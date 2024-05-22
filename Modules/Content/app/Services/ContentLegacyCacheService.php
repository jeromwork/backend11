<?php

namespace Modules\Content\Services;

use Illuminate\Database\Eloquent\Collection;

class ContentLegacyCacheService
{

    protected ?Collection $content = null;
    public function forContent(  $content):self   {
        $this->content = $content;
        return $this;
    }

    public function getLegacyContentData():array    {
        $contentCache = [];
        if(!$this->content) return $contentCache;
        foreach ($this->content as $content){

            //fill legacy
            $contentLegacy = [
                "id" => ($content->type !== 'kinescope')?$content->id : $content->file,
                "type" => $content->type,
                "typeFile" => $content->typeFile,
                "alt" => $content->alt,
                "url" => $content->url,
            ];
            if($content->preview){ //add preview
                $contentLegacy['preview'] = [
                    "id" => $content->preview->id,
                    "type" => $content->preview->type,
                    "typeFile" => $content->preview->typeFile,
                    "url" => $content->preview->url,
                ];
            }
            $contentCache[] = $contentLegacy;
        }


        return $contentCache;
    }
}
