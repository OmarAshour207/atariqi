<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RideBookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'passenger'     => new UserSampleResource($this->passenger),
            'neighborhood'  => new NeighbourResource($this->neighborhood),
            'service'       => new ServiceResource($this->service),
            'university'    => new UniversityResource($this->university),
            'road_way'      => $this->{"road-way"},
            'lat'           => $this->lat,
            'lng'           => $this->lng,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"}
        ];
    }
}
