<?php


namespace App\DataStructures\Kinescope;

class KinescopeReplicaStructure extends \App\DataStructures\AbstractDataStructure
{

    public ?string$id = null;
    public ?string $original_name = null;
    public  ?int $file_size = null;
    public ?string $filetype = null;
    public ?string $quality = null;
    public ?string $resolution = null;
    public ?string $url = null;
    public ?string $download_link = null;
}
