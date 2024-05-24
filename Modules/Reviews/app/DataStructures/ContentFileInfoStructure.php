<?php


namespace Modules\Reviews\DataStructures;
use App\DataStructures\AbstractDataStructure;


class ContentFileInfoStructure extends AbstractDataStructure
{
    public ?string $file = null;
    public ?string $url = null;
    public string $type = '';
    public ?string $typeFile = null;
}
