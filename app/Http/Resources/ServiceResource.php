<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'service-ar'    => $this->{"service-ar"},
            'service-eng'   => $this->{"service-eng"},
            'cost'          => $this->cost,
            'date-of-add'   => $this->{"date-of-add"},
            'date-of-edit'  => $this->{"date-of-edit"},
        ];
    }
}
