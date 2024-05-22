<?php


namespace App\DataStructures\Content;

class CreateReplicaContentStructure extends \App\DataStructures\AbstractDataStructure
{
    public ?string $file = null;
    public ?string $url = null;
    public ?string $type = null;
    public ?string $typeFile = null;
    public ?bool $confirm = null;
    public ?bool $published = null;
    public ?string $contentable_type = null;
    public ?int $contentable_id = null;
    public ?string $parent_id = null;
    public string $is_preview_for = '';
    public ?string $mime = null;
    public string $alt = '';
    public int $file_size = 0;
    public string $file_extension = '';
    public string $original_file_name = '';
}
