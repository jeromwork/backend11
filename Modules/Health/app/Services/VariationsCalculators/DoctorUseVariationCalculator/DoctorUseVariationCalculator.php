<?php


namespace Modules\Health\Services\VariationsCalculators\DoctorUseVariationCalculator;


use Illuminate\Support\Collection;
use Modules\Health\Entities\Doctor;
use Modules\Health\Entities\Variation;

class DoctorUseVariationCalculator
{
    protected array $doctorsIds;
    protected array $variationsIds;
    protected Collection $collection;
    protected bool $mark = false;
    protected bool $merge = false;
    protected bool $filter = false;
    protected array $calculatorsClasses = [
        UseBySkill::class,
//        UseByAlwaysMark::class
    ];
    protected array $calculators = [];


    public function forDoctorsIds(array $doctorsIds):self {
        $this->doctorsIds = $doctorsIds;
        return $this;
    }

    public function forVariationsIds(array $variationsIds):self {
        $this->variationsIds = $variationsIds;
        return $this;
    }

    public function mark():self {
        $this->mark = true;
        return $this;
    }

    public function withCalculators():self{
        //метод выставляте калькуляторы для обработки вариаций
        //он добавляет калькуляторы в начало массивов калькуляторов
        //дубли калькуляторов удаляются из массива

        return $this;
    }

    public function get():array {
        if(!$this->calculators) {
            $this->calculators = $this->getCalculators();
        }

        if( !$this->variationsIds || !$this->calculators ) return [];



        //выбираем коллекцию докторов и вариаций распределенных по доктора
        //для доктора нужен только id и  skill
        //для вариаций нужны ids , skills, pivot данные
        //какие pivot данные нужны, будут решать множество калькуляторов из массива калькуляторов
        //эти калькуляторы настраивают какие еще нужны данные в запросе
        //а потом получают коллекцию сфорированную запросом, и производят какие то действия
        //в цикле обходим калькуляторы для каждого доктора,
        //калькулятор производит вычисления с переданными данными доктора, вариаций и связок
        //калькулятор не может ограничивать выборку вариаций или докторов, т.к. возможно другие вариации ожидают полный список
        //калькулятор может добавлять select  поля в запрос, или доп запросы, к другим таблицам

        //затем в цикле опрашиваются калькуляторы в порядке очередности
        //если калькулятор обрабатывает вариации, он добавляет какие то данные
        //калькулятор может удалить вариацию если посчитает нужным
        //следующие калькуляторы тоже могут удалить вариацию, если посчитают нужным
        //можно использовать метод onlyMark, что бы не удалять вариации, а проставлять метки
        //какую метку ставить решает сам калькулятор

        //межно дать возможность, извне управлять очередность и номенклатуру срабатывания калькуляторов
        $query = $this->getCalculatorQuery();

        foreach ($this->calculators as $calculator){
            $query = $calculator->buildQuery($query);
        }

        $collectionFromDb = $query->get();
        //получены доктора с вариациями, и с другими необходимыми данными
        //каждому калькулятору передаем исходную коллекцию,
        $outData = [];
        foreach ($this->calculators as $calc){
            $calc->mergeData($this->merge)->filterVariations($this->filter)->calculate($collectionFromDb, $outData );
        }



        return [];
    }


    protected function getCalculators():array {
        if(!$this->calculatorsClasses) return [];
        $calculators = [];
        foreach ($this->calculatorsClasses as $calculatorClass){
            $calculators[$calculatorClass] = new $calculatorClass();
        }
        return $calculators;
    }

    protected function getCalculatorQuery(){
        if($this->doctorsIds){
            $query = Doctor::whereIn('id', $this->doctorsIds)->
            with('variations', function ($query){
                $primaryKey = $query->getQuery()->from.'.'.$query->getQuery()->getModel()->getKeyName();
                $query->whereIn($primaryKey, $this->variationsIds);
            });
        }else{
            $query = Variation::whereIn('id', $this->variationsIds);

        }
        return $query;
    }

    public function mergeData(bool $merge = true):self{
        $this->merge = $merge;
        return $this;
    }

    public function filterVariations(bool $filter = true):self {
        $this->filter = $filter;
        return $this;
    }

}
