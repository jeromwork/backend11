<?php

namespace Modules\Reviews\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewContentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'url' => $this->url,
            'confirm' => (bool)$this->confirm,
            'published' => (bool)$this->published,
            'typeFile' => $this->typeFile,
        ];
    }
}
