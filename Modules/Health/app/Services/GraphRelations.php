<?php


namespace Modules\Health\Services;
//use Modules\Reviews\Models\DoctorReview;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Modules\Doctors\Models\Doctor;
use Modules\Health\Models\Seo;
use Modules\Health\Models\Variation;


class GraphRelations
{
    private const TARGET_MAP = [
        'doctor' => Doctor::class,
        'doctors' => Doctor::class,
        'variation' => Variation::class,
        'variations' => Variation::class,
        'seo' => Seo::class,
    ];

    private const MODEL_INFO_MAP = [
        Doctor::class => ['alias' => 'doctor', 'name' => 'Doctor'],
        Variation::class => ['alias' => 'variation', 'name' => 'Variation'],
        Seo::class => ['alias' => 'seo', 'name' => 'Seo'],
    ];


    private const RELATIONS_METHODS = [
        Variation::class => 'variations',
        Doctor::class => 'doctors',
        Seo::class => 'seo',
    ];

    public function getModels():array {

        return array_keys(self::MODEL_INFO_MAP);
    }

    public function getModelByAlias(string $alias) {
        return self::TARGET_MAP[$alias];
    }

    public function getAliasByModel( $model ) {
        return self::MODEL_INFO_MAP[$model]['alias'];
    }

    public function getRelationsMethod(string $model){
        return self::RELATIONS_METHODS[$model];
    }

    public function getRelationName(string $model){
        return self::RELATIONS_METHODS[$model];
    }


    public function getClassNameByModel(string $model):string {
        return self::MODEL_INFO_MAP[$model]['name'];
    }

    public function getRelationsMethods():array {
        return array_values( self::RELATIONS_METHODS );
    }

    public function getPathOfCollection(Collection $collection, $prefix = ''):array {
        $outKeys = [];
        if($collection->isEmpty()) return [];
        foreach ($collection as $model){
            $relations = $model->getRelations();
            if(!$relations) continue;
            foreach ($relations as $relationName => $relatedCollection) {
                if ($relatedCollection instanceof Collection) {
                    $key = ($prefix) ? $prefix . '.' . $relationName : $relationName;
                    $outKeys[$key] = $key;
                    if($relatedCollection->isEmpty()) continue;
                    $relKeys = $this->getPathOfCollection($relatedCollection, $relationName);
                    $outKeys = array_merge($outKeys, $relKeys);
                }

            }
        }
        return $outKeys;
    }


    public function addBaseToPaths( array $paths, $collection):array {
        $outPaths = [];
        $baseClass = $collection->first();
        if(!$baseClass) return $paths;
        $baseModelName = $this->getRelationsMethod(get_class($baseClass));
        if(!$baseModelName) return $paths;
        foreach ($paths as $path){
            $outPaths[] = $baseModelName.'.'.$path;
        }

        return $outPaths;
    }

    public function removeBaseFromPaths( array $paths):array {
        $outPaths = [];
        foreach ($paths as $path){
            $pathArray = explode('.', $path);
            if($pathArray) unset($pathArray[0]);

            $outPaths[] = implode('.', $pathArray);
        }
        $outPaths = array_filter($outPaths);
        $outPaths = array_unique($outPaths);
        return $outPaths;
    }

    public function getPathsByTargets(array $targets, array $paths):array {
        $outPaths = [];
        foreach ($paths as $path){
            $beginOffset = false;
            $offset = false;
            foreach ($targets as $target){
                $tstrpos = strpos($path, $target, $offset);
                if($beginOffset === false) $beginOffset = $tstrpos;
                if($tstrpos === false) {
                    $offset = false;
                    break;
                }
                $offset += $tstrpos;
            }
            if($offset !== false && $beginOffset !== false){
                $lastTarget = $targets[array_key_last($targets)];
                $outPaths[] = substr($path, $beginOffset, $offset + strlen($lastTarget));
            }
        }

        return $outPaths;
    }

    public function getIdsByPaths(array $paths, Collection $collection):Collection{
        $outCollection = collect([]);
        if(!$paths) return $collection->pluck('id')->flatten();
        foreach ($paths as $path){
            $path = explode('.', $path);
            $path = implode('.*.', $path);
            $outCollection = $outCollection->merge($collection->pluck($path.'.*.id')->flatten());
        }
        return $outCollection->unique();
    }




}
