<?php

namespace Modules\Reviews\Transformers\Control;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Reviews\Transformers\ReviewContentResource;

class ReviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            'id' => $this->id,
            'text' => $this->text,
            'rating' => $this->rating,
            'reviewable_id' => $this->reviewable_id,
            'reviewable_type' => $this->reviewable_type,
            'author' => $this->author,
            'source' => $this->source,
            'content' => ReviewContentResource::collection($this->whenLoaded('content'))
        ];

    }
}
