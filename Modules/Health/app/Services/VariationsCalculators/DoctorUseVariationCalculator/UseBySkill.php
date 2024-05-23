<?php


namespace Modules\Health\Services\VariationsCalculators\DoctorUseVariationCalculator;
use Illuminate\Support\Collection;

class UseBySkill
{
    protected bool $merge = false;
    protected bool $filter = false;
    protected Collection $collection;


    public function buildQuery($query) {
        //this calc use skills doctor and variation
        $query->with('variations', function ($query){
            $alias = $query->getQuery()->from;
            $query->addSelect($alias.'.id', $alias.'.skill');
        });
        $query->with('info', function($query){
            $alias = $query->getQuery()->from;
            $query->addSelect($alias.'.id', $alias.'.skill');

        });

        return $query;
    }




    public function calculate(Collection $collectionDoctorsVariations, array $outData):array   {
        foreach ( $collectionDoctorsVariations as $doctor ){
            if(!isset($outData[$doctor->id])) $outData[$doctor->id] = ['variations' => [] ];
            if(!$doctor->info || !$doctor->variations) continue;
            foreach ($doctor->variations as $variation){
                if(!$this->filter && !isset($outData[$doctor->id]['variations'][$variation->id])){
                    $outData[$doctor->id]['variations'][$variation->id] = [];
                }
                if($variation->skill && $doctor->info->skill === $variation->skill){
                    if($this->merge){
                        $outData[$doctor->id]['variations'][$variation->id] += ($outData[$doctor->id]['variations'][$variation->id])
                            ? $outData[$doctor->id]['variations'][$variation->id] + ['id' => $variation->id, 'useBySkill' => true]
                            : ['id' => $variation->id, 'useBySkill' => true];
                    }else $outData[$doctor->id]['variations'][$variation->id] = ['id' => $variation->id];
                }
            }
        }

        return $outData;
    }

    public function mergeData(bool $merge = true):self{
        $this->merge = $merge;
        return $this;
    }

    public function filterVariations(bool $filter = true):self {
        $this->filter = $filter;
        return $this;
    }

    public function filterAndMerge(Collection $collectionFromDB, array $outData = []) {
        $this->merge = true;
        $this->filter = true;

    }



}
