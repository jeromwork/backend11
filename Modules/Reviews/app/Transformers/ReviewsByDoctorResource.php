<?php

namespace Modules\Reviews\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReviewsByDoctorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'text' => $this->text,
            'body' => $this->text,
            'rating' => $this->rating,
            'ozenka' => round($this->rating/10, 1),
            'author' => $this->author,
            'patient' => $this->author,
            'posle' => $this->after_course,
            'dateOtziv' => $this->created_at,
            'reply' => $this->otvet_legacy,
            'dateReply' => ($this->otvet_date_legacy)? date('Y-m-d', $this->otvet_date_legacy):'',
            'content' => ReviewContentResource::collection($this->whenLoaded('content'))
        ];
    }
}
