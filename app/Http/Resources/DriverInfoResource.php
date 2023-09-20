<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverInfoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'driver'            => new UserSampleResource($this->driver),
            'car-brand'         => $this->{"car-brand"},
            'car-model'         => $this->{"car-model"},
            'car-number'        => $this->{"car-number"},
            'car-letters'       => $this->{"car-letters"},
            'car-color'         => $this->{"car-color"},
            'driver-neighborhood'   => $this->{"driver-neighborhood"},
            'driver-rate'           => $this->{"driver-rate"},
            'driver-license-link'   => url($this->{"driver-license-link"}),
            'allow-disabilities'    => $this->{"allow-disabilities"},
            'date-of-add'           => $this->{"date-of-add"},
            'date-of-edit'          => $this->{"date-of-edit"}
        ];
    }
}
