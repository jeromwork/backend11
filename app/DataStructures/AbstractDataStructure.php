<?php


namespace App\DataStructures;

abstract class AbstractDataStructure
{
    //public $id = null;

    public function __construct( array $data = [] ) {
        if($data){
            $this->fromArray($data);
            $this->isFill();
        }
    }

    public function fromArray(array $data ):self {
        foreach ($data as $key => $value){
            $this->$key = $value;
        }
        return $this;
    }


    public function isFill():bool {
        $reflectionClass = new \ReflectionClass($this);
        $defaultFields = $reflectionClass->getDefaultProperties();
        if($defaultFields){
            foreach ($defaultFields as $field => $value){
                if($value === null && $this->$field === null){
                    throw new \Exception("Not fill '$field' in structure $reflectionClass->name");
                }
            }
        }
        return true;
    }

//    public abstract function getId():int;




    public function toArray( array $possibleFields = []) {
        $toArray = [];
        foreach ($this as $key => $val){
            if($possibleFields && !array_search($key, $possibleFields)) continue;
            if (is_array($val)){
                foreach ($val as $k => $v){
                    if(is_object($v)){
                        $toArray[$key][$k] = $v->toArray();
                    }
                }
            }else{
                $toArray[$key] = $val;
            }
        }
        return $toArray;
    }

//    public function setAttach(string $attachName, $idOrAray, ?AbstractDataStructure $structure = null ):self {
//        global $modx;
//        if(is_array($idOrAray)){
//            $this->$attachName = $idOrAray;
//        }elseif (( is_int($idOrAray*1) || is_string($idOrAray) ) && $structure && $structure instanceof self){
//            $this->$attachName[$idOrAray] = $structure;
//        }
//        return $this;
//    }
//
//    public function getAttach(string $attachName, $id = null)  {
//        if($id && $this->$attachName && $this->$attachName[$id]){
//            return $this->$attachName[$id];
//        }elseif ( !$id ){
//            return $this->$attachName;
//        }
//    }
//
//    public function removeAttach( string $attachName, $id = null ):self {
//        global $modx;
//        if(isset($this->$attachName) && $id){
//            if($id){
//                unset($this->$attachName[$id]);
//            }else{
//                unset($this->$attachName);
//            }
//
//        }
//        return $this;
//    }
//
//
//    public function getId(){
//        return (int)$this->id;
//    }

}

