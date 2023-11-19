<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WeekRideBookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'group_id'      => $this->{"group-id"},
            'date_of_ser'   => $this->{"date-of-ser"},
            'road_way'      => $this->{"road-way"},
            'time_go'       => $this->{"time-go"},
            'time_back'     => $this->{"time-back"},
            'lat'           => $this->{"lat"},
            'lng'           => $this->{"lng"},
            'action'        => $this->{"action"},
            'neighborhood'  => new NeighbourResource($this->neighborhood),
            'passenger'     => new UserSampleResource($this->passenger),
            'university'    => new UniversityResource($this->university),
            'service_id'    => new ServiceResource($this->service),
        ];
    }
}
