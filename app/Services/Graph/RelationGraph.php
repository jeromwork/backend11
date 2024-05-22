<?php


namespace App\Services\Graph;


use Illuminate\Support\Facades\Log;
use Modules\Health\Entities\Doctor;
use Modules\Health\Entities\Service;
use Modules\Health\Entities\Variation;

class RelationGraph
{

    protected array $graph = [['seoServices', 'services', 'variation', 'doctor']];
    protected array $graphData = [];
    protected array $responseModels = [];
    protected array $graphFiltered = [];
    protected array $graphModels = [];
    protected array $graphIds = [];
    protected array $mapModelToAlias = [];
    protected array $mapAliasToModel = [];
    protected bool $groupById = false;

    protected array $modelsWhere = [];


//    protected array         $modelAliases = [
//        'doctor' => Doctor::class,
//        'service' => Service::class,
//        'variation' => Variation::class,
//        //'seoResource' => SeoResource::class,
//
//
//
//
//    ];

    protected array $modelClassToAlias = [
        'Modules\Health\Entities\Doctor' => 'doctor',
        'Modules\Health\Entities\Service' => 'service',
        'Modules\Health\Entities\Variation' => 'variation',
    ];

    public function __construct()    {

    }


    public function withResponseModels( array $models):self    {
        $this->responseModels = $models;
        $this->composeGraphData($models);
        return $this;
    }



    public function withModel($builder):self {
        $modelClass = get_class($builder->getModel());
        $this->modelsWhere[$modelClass] = $builder;
        return $this;
    }

    public function groupById():self   {
        $this->groupById = true;
        return $this;
    }
    protected function composeGraphData($models):self {
        $this->graphModels = $this->composeGraph($models);
        if(!$this->graphModels) return $this; //<<<<<<<<<<<<<<<<<
        foreach ($this->graphModels as $graphModel => $graphRelations){
            $this->mapModelToAlias[$graphModel] = $graphModel::MODEL_RELATION_ALIAS;
            $this->mapAliasToModel[$graphModel::MODEL_RELATION_ALIAS] = $graphModel;
        }
        return $this;
    }
    protected function composeGraph(array $models):array {
        $modelsWithRelations = [];

    //можно не использовать ветки и вообще граф!
    //у нас есть допустим массив по которому нужно построить ответ
    //[ 'Modules\Health\Entities\Doctor', 'Modules\Health\Entities\Service', 'Modules\Health\Entities\Variation'];
    //такой массив приходит уже от контроллера, который знает как преобразовать request данные в необходимые модели
    //может быть какой то спец класс будет этим заниматься
    //для каждой модели, ищем, relation  методы, которые возвращают коллекцию нужного типа
    //т.е для модели Modules\Health\Entities\Doctor нашли метод variations() который возвращает коллекцию Modules\Health\Entities\Variation
    //записываем в массив отфильтрованных моделей
    //[Doctor, 'Modules\Health\Entities\Variation']
    //[ Doctor::class=>['variations' => Variation::class], Service::class, Variation::class]
    //для Doctor::class проверяем есть ли Service::class, Variation::class relation methods
    //нашли 'variations'
    //заполняем массив
    //[ Doctor::class=>['variations' => Variation::class]]
    //из массива $responseModels обработаны классы Doctor::class и Variation::class, остался Service::class
    //для класса Service::class находим 'variations' => Variation::class

        foreach ($models as $model){

            $modelsWithRelations[$model] = ($model::RELATIONS_METHODS) ? $model::RELATIONS_METHODS : [];
        }


        return  $modelsWithRelations;
    }


    public function get():array{
        //fill data from db
        $this->graphData = $this->graphFillFromDB();
        //полученные избыточные данные, можно преобразовать необходимый вид


        //$this->graphData = $this->graphToIds( $this->graphData );




        $st = $this->stalker(array_fill_keys(array_keys($this->graphData), null));
//        $this->graphIds = $this->graphToIds();
        if($this->groupById) {
            return [];
        }

        //в цикле обходим первую модель по которой будет строится ответ
        //для каждой сущности запускать сталкера

        //полученный результат преобразовать в необходимый формат



//        Log::info(print_r($this->graphData,1));
        return [];
    }



    protected function graphFillFromDB() : array {
        $graphCollections = [];
        $graphModels = [];
        $graphModelIds = array_fill_keys(array_keys($this->graphModels), []);
        foreach ($this->graphModels as $model => $relations){
            $q = ( isset($this->modelsWhere[$model]) ) ?
                $this->modelsWhere[$model] :
                $model::query();
            //if select fields filled add id
            $selectFields = $q->getQuery()->columns;
            if(!$selectFields || !in_array('id', $selectFields)){
                $q->addSelect('id'); //always use ids
            }


            if( $relations ) {
                foreach ($relations as $relationModel => $relation ) {

                    if(isset($graphCollections[$relationModel]) ){
                        //если уже есть коллекция с данными из базы
                        //todo нужно взять ids и распределить в текущую коллекцию с заданными ключами
                    }
                    //todo select $relationModels, only ids with pivot data
                    //todo проверить что запрос уже может быть настроен извне, на фильтрацию по текущему pivot
                    $q -> with([$relation => function ($query) use($relation) {
                        $prefix = $query->getQuery()->from;
                        $query->addSelect($prefix.'.id as id');
                    }]);
//
                }
            }
            if(isset($graphModelIds[$model]) && $graphModelIds[$model]){
                $q->whereIn('id', $graphModelIds[$model]);
            }

            $s = $q->getQuery()->toSql();
            $c = $q->get();
            $graphCollections[$model] = $c;


            $modelArray = $this->collectionToKeyArray($c);


            $graphModels[$this->mapModelToAlias[$model]] = $modelArray;
            //add where relations models by get relations items ids
            if(isset($this->graphModels[$model])){
                $currentRelationsIds = $this->getRelationsIds($modelArray, $this->graphModels[$model]);
                if($currentRelationsIds){
                    $graphModelIds = array_merge_recursive($graphModelIds, $currentRelationsIds);
                }
            }
        }
        //revers filter models by ids for consistent relations
        //if necessary only fill relations of models
        //$graphCollections = $this->reversFilterByIds($graphModelIds, $graphCollections);

        //$graphCollections = $this->setRelationsById($graphCollections);
        return $graphModels;
        return $graphCollections;
    }
    protected function reversFilterByIds( array $graphModelIds, array $graphFromDb ):array {
        $graphModelCollections = $graphFromDb;
        foreach ($graphModelIds as $model => $collectionIds){
            if(isset($graphModelCollections[$model])){
                $collectionIds = ($collectionIds)  ? $collectionIds : collect([]);
                $graphModelCollections[$model] = $graphModelCollections[$model]->whereIn('id', $collectionIds);
            }
        }
        return $graphModelCollections;
    }

    protected function collectionToKeyArray($modelCollection):array {
        if( $modelCollection->count() < 1 ) return []; //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        $modelArray = $modelCollection->toArray();
        if( !$modelArray ) return []; //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        $modelArray = array_combine(array_column($modelArray, 'id'), $modelArray);
        foreach ($modelArray as $id => $item){
            if(!$item) continue;
            foreach ($item as $field=> $val){
                if(is_array($val)){
                    $modelArray[$id][$field] = array_combine(array_column($val, 'id'), $val);
                }
            }
        }
        return $modelArray;
    }

    protected function getRelationsIds($modelArray, $relationModels):array {
        $arrayIds = [];
        if( !$modelArray ) return []; //<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        foreach ($relationModels as $model => $field){
            foreach ($modelArray as $id => $item){
                if(isset($item[$field]) && $item[$field] && is_array($item[$field])) {
                    $arrayIds[$model] = (isset($arrayIds[$model]) && $arrayIds[$model] ) ? $arrayIds[$model] + array_keys($item[$field]) : array_keys($item[$field]);
                }
            }
        }

        return $arrayIds;
    }


//
//    protected function graphToIds(array $graphCollections):array {
//
//        $graphByIds = [];
//        foreach ($graphCollections as $model => $collection){
//            $relationsFields = [];
//            //get relations fields
//            if(isset($this->graphModels[$model]) && $this->graphModels[$model]){
//                $relationsFields = array_values($this->graphModels[$model]);
//            }
//            if($relationsFields){
//                foreach ($relationsFields as $field){
//                    $f = $collection->variations;
//                    $collection->setRelation($field, $collection->$field()->keyBy('id'));
//                }
//            }
//
//            $r = $collection->keyBy('id')->toArray();
//            $e = 98;
////            if(!$relations && isset($graphCollections[$model])) {
////                $graphByIds[$model] = $graphCollections[$model]->pluck('id');
////                continue;
////            }
////
////            foreach ($relations as $relationModel => $relation) {
////
////            }
//
////            if(is_array($relations)) {
////                //если node это массив запускаем рекурсию этой функции
////                $modelIds[$node] = $this->collectionsToIds($path, $collections);
////            }else{
////                $modelIds[$node] = $collections[$node]->pluck('id');
////            }
//
//        }
//        return $graphByIds;
//    }




    protected function stalker(array $discoveryMap, $cityBegin = null, array $roadsBegin = null, int $level = 0): array    {
        $terrain = $this->graphData;


        if($cityBegin && $roadsBegin){
            $roadsBegin = array_combine($roadsBegin, $roadsBegin);
        }

        foreach ($terrain as $city => $roads) {
            if((is_array($discoveryMap[$city]) && !$cityBegin) || !$roads) continue;
            if($cityBegin && $city !== $cityBegin) continue;
            foreach ($roads as $street => $nearCities) {
                if($roadsBegin && !isset($roadsBegin[$street])) continue;
                if (!$nearCities || !$openCities = $this->getRelationsByAlias($city)) {
                    $discoveryMap[$city][$street] = [];
                    continue;
                }
                foreach ($openCities as $nearOpenCity){

                    //next postfilter revers relation by exist models

                    $nearOpenCityRoads = $nearCities[$nearOpenCity];
                    if(!isset($discoveryMap[$city][$street])){
                        $discoveryMap[$city][$street] = $nearCities;
                    }

                    if( !$nearOpenCityRoads && !$discoveryMap[$nearOpenCity]){
                        $discoveryMap[$nearOpenCity] = [];
                    }

                    if( !$nearOpenCityRoads || $discoveryMap[$nearOpenCity]) continue;

                    $discoveryMap = $this->stalker($discoveryMap, $nearOpenCity, array_keys($nearOpenCityRoads),  $level + 1);

                    //если сейчас верхний уровень стека вызова
                    if($this->groupById && $level === 0 && array_search(null, $discoveryMap, true) === false){
                        //и заполнены все необходимые модели
                        $f = 89;
                        //можно заполнять вложенные модели
                        //т.к. одна текущая верхняя начальная модель
                        $discoveryMap = $this->distributeGraph($discoveryMap);

                    }

                }
            }
        }

        return $discoveryMap;
    }

    protected function distributeGraph(array $discoveryMap):array {
        //можно заполнять вложенные модели
        //т.к. одна текущая верхняя начальная модель
        //но для этого случая, нужно будет уничтожать $discoveryMap, что бы следующее путишествие, было с чистого листа


        //pivot data нужны только крайним моделям, т.е если встречается до этого, предыдуший можно удалить
        $map = [];

        $mapKeys = array_combine(array_keys($discoveryMap), array_keys($discoveryMap));
        //revers loop map
        end($discoveryMap);
        while (($relation = key($discoveryMap)) !== null) {
            prev($discoveryMap);
            $prevKey = key($discoveryMap);
            if(!$prevKey || !$discoveryMap[$prevKey]) continue;
            if(!isset($map[$prevKey])) $map[$prevKey] = $discoveryMap[$prevKey];
            //обходим текущие модели,
            //выбираем для каждой модели ids предыдущих моделей
            //для текущей модели:
            //забираем все данные не реляции
            //если предыдущая реляция, пропускаем
            //если другие реляции, забираем только те, которые совпадают ids
            foreach ($discoveryMap[$relation] as $modelId => $modelData){

                if(!isset($modelData[$prevKey]) || !isset($discoveryMap[$prevKey])) continue;
                $clearModelData = [];

                foreach ($modelData as $modelField => $val){
                    if($modelField === $prevKey) continue;
                    $clearModelData[$modelField] = (isset($discoveryMap[$modelField]) && $discoveryMap[$modelField]) ? array_intersect_key($val, $discoveryMap[$modelField]) : $val;
                }
                //$ee =
                //$prevModelIds = array_keys( array_intersect_key($map[$prevKey], $modelData[$prevKey]) );
                //$f = $clearModelData;
                foreach ($modelData[$prevKey] as $prevModelId => $prevModel){
                    if(!isset($discoveryMap[$prevKey][$prevModelId])) continue;
                    $map[$prevKey][$prevModelId][$relation][$modelId] += $clearModelData;
                }
            }



//            foreach ($map[$prevKey] as $prevId => $prevData){
//                foreach ($mapKeys as $rel){
//                    if(!isset($prevData[$rel]) || !is_array($prevData[$rel]) || !is_array($discoveryMap[$rel])) continue;
//                    //get items for merge relations and pivot
//                    $relArray = array_intersect_key($map[$rel], $prevData[$rel]);
//                    foreach ($prevData[$rel] as $k => $data){
//                        //нужно не мержить, а передавать только необходимые данные
//                        //т.е. соседние реляции не мержить, а остальные передавать только ids
//                        foreach($relArray[$k] as $modelField => $modelData){
//                            if($modelField === $prevKey)    continue;
//                            if(!isset($map[$modelField]))   continue;
//                            //нужны только те реляции для которых есть модели
//                            $modelArray = array_intersect_key($modelData, $map[$modelField]);
//                            $map[$prevKey][$prevId][$rel][$k] = array_merge($data, $relArray[$k]) ;
//                        }
//
//                    }
//                }
//            }
        }
        return $map;
    }



    protected function getModelByAlias(string $alias){
        return $this->mapAliasToModel[$alias];
    }

    protected function getRelationsByAlias(string $alias) : array {
        if(!$model = $this->getModelByAlias($alias)) return  [];
        return $this->graphModels[$model];
    }



}
