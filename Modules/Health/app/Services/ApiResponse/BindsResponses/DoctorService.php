<?php


namespace Modules\Health\Services\ApiResponse\BindsResponses;


use Illuminate\Database\Eloquent\Builder;
use Modules\Health\Http\Requests\ApiBindsRequest;
use Modules\Health\Services\ApiResponse\ApiBindsResponseAbstract;
use Modules\Health\Services\VariationsCalculators\DoctorVariationsCalculator;
use Modules\Health\Services\CollectionTransformer;

class DoctorService extends ApiBindsResponseAbstract
{

    public function answer()
    {

        //выбрать в основном и вложенных коллекциях только необходимые поля
        //для вариаций нужно выбрать pivot данные для докторов и iservices
        //из pivot убрать ids оставить только необходимые данные
        //причем сделать это еще на этапе запроса в бд

        $this->query->addSelect(['id']);

        //calc min price for every set variations
        //here absolutely need price, custom price and other data && pivot data
        //this is achieved by adding fields or additional queries
        //to distinguish variations set specials labels : f.e minPrice = true, maxPrice = true, use = true and another
        $target = $this->request->getTargetMethod(); //doctors or services
        $relations = [ ];

        foreach ([$target, $target.'.variations'] as $relation){
            $relations[$relation] = function ($query) {
                $primaryKey = $query->getQuery()->getModel()->getKeyName();
                $primaryKey = $query->getQuery()->from.'.'.$primaryKey;
                $query->select($primaryKey);
            };
        }
        if($relations){
            $this->query->with($relations);
        }


        $data = $this->query->get();
        $data = (new CollectionTransformer())->byKeys($data);

        //из коллекции, выбрать ids докторов и ids вариаций
        //для выбранных ids сделать запрос в бд, для выборки связок доктор-вариация с pivot data
        //структура не задана точно, поэтому, сначала определяем где
        $calcData = (new DoctorVariationsCalculator())->forCollection($data)->mergeCalcData();

        if(!$responseClass = $this->getResponseClass()) return $data->toArray(); //<<<<<<<<<<<<<<<<<<<<<<<<

        return $responseClass::collection( $data );


    }

}
