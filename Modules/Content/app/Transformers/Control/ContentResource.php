<?php

namespace Modules\Content\Transformers\Control;

use Illuminate\Http\Resources\Json\JsonResource;

class ContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request = null)
    {
        if($this->mime === 'hyperlink') $url = $this->url;
        else $url = (empty($_SERVER['HTTPS']) ? 'http' : 'https').'://'.$_SERVER['HTTP_HOST'].$this->url;

         return [
            'id' => $this->id,
            'url' => $url,
            'title' => (string)$this->title,
            'confirm' => (bool)$this->confirm,
            'published' => (bool)$this->published,
            'typeFile' => $this->typeFile,
             'preview' => new ContentResource($this->whenLoaded('preview')),
             'previewOriginal' => new ContentResource($this->whenLoaded('previewOriginal')),
             'original_file_name' => $this->original_file_name,
             'alt' => $this->alt,
//             'is_preview_for' => (string)$this->is_preview_for,
            'handledReplicas' => ($this->left_handle_replicas < 1)

        ];
    }
}
