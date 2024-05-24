<?php

namespace Modules\Profile\Transformers;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Modules\Orders\Transformers\OrderResource;

class ProfileSlotsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'time_begin' => $this->time_begin,
            'time_end' => $this->time_end,
            'clinic_id' => $this->clinic_id,
            'cabinet_id' => $this->cabinet_id,
            'variation_id' => $this->service_id,
            'price' => $this->price,
            'count' => $this->count,
            'doctor_id' => $this->doctor_id,




//            'service' => SeoResource::collection($this->whenLoaded('seoService')),
            //todo create order
            'order' => OrderResource::collection($this->whenLoaded('order')),

//            'doctor' => DoctorResource::collection($this->whenLoaded('doctor')),
            'doctor' => $this->whenLoaded('doctor') ? [
                'id' => $this->doctor->id,
                'fullname' => $this->doctor->fullname,
            ] : null,
            //todo create clinic
//            'clinic' => ClinicResource::collection($this->whenLoaded('clinic')),



        ];

    }
}
