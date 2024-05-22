<?php


namespace App\DataStructures\Content;

class ContentFileInfoStructure extends \App\DataStructures\AbstractDataStructure
{
    public ?string $file = null;
    public ?string $url = null;
    public ?string $type = null;
    public string $typeFile = '';
    public bool $confirm = false;
    public bool $published = false;

    public string $contentable_type = '';
    public int $contentable_id = 0;
    public ?string $parent_id = null;
    public string $is_preview_for = '';
    public ?string $mime = null;
}
