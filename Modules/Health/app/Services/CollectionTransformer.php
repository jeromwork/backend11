<?php


namespace Modules\Health\Services;


use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Collection;

class collectionTransformer
{

    public function byKeys($collection){
        if(!$collection instanceof EloquentCollection) return $collection;//<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<
        if(!$collection->first()) return $collection;
        $primaryKey = $collection->first()->getKeyName();
        if($primaryKey){
            $collection = $collection->keyBy($primaryKey);
        }

        foreach ($collection as $key => $item){
            $relations = $item->getRelations();
            if(!$relations) continue;
            foreach ($relations as $relationName => $relation) {
                $relationByKey = $this->byKeys($relation);
                $item->setRelation($relationName, $relationByKey);
            }
        }





        return $collection;
    }

}
