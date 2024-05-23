<?php

namespace App\Transformers\Control;

use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Content\Transformers\ContentResource;
use Modules\Doctors\Http\Resources\Admin\DiplomResource;

class DoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'surname' => $this->surname,
            'name' => $this->name,
            'middlename' => $this->middlename,
            'fullname' => $this->surname.' '.$this->name.' '.$this->middlename,
            'contentOriginal' => ContentResource::collection($this->whenLoaded('contentOriginal')),
            'content' => ($this->content_cache)? json_decode($this->content_cache) :null,
            'diplomsOriginal' => DiplomResource::collection($this->whenLoaded('diplomsOriginal')),
        ];

    }
}
