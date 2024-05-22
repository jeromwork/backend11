<?php


namespace App\DataStructures\Content;

class ContentUpdateStructure extends \App\DataStructures\AbstractDataStructure
{
    public ?string $id= null;
    public ?string $contentable_type = null;
    public ?int $contentable_id = null;

    public string $file = '';
    public string $url = '';

//    public string $original_banner_id = '';
    public string $type = '';
    public string $typeFile = '';
    public int $confirm = 0;
    public bool $published = false;
    public int $isDeleted = 0;
    public ?ContentUpdateStructure $previewOriginal = null; //un neccesarally


    public function isFill():bool {
        $reflectionClass = new \ReflectionClass($this);
        $defaultFields = $reflectionClass->getDefaultProperties();
        if($defaultFields){
            foreach ($defaultFields as $field => $value){
                if($value === null && $this->$field === null && $field !== 'previewOriginal'){
                    throw new \Exception("Not fill '$field' in structure $reflectionClass->name");
                }
            }
        }
        return true;
    }

}
