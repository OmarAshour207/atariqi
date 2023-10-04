<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DayRideBookingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'passenger_id'      => $this->{"passenger-id"},
            'neighborhood_id'   => $this->{"neighborhood-id"},
            'service_id'        => new ServiceResource($this->service),
            'date_of_ser'       => $this->{"date-of-ser"},
            'road_way'          => $this->{"road-way"},
            'time_go'           => $this->{"time-go"},
            'time_back'         => $this->{"time-back"},
            'action'            => $this->action,
            'date_of_add'       => $this->{"date-of-add"}
        ];
    }
}
