<?php

namespace Modules\Doctors\Transformers\Control;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiplomResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
