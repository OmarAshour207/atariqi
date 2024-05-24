<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\NeighbourResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UniversityResource;
use App\Http\Resources\UserSampleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WeekRideBookingGroupDetails extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'group_id'      => $this->{"group-id"},
            'date_of_ser'   => $this->{"date-of-ser"},
            'action'        => $this->{"action"},
            'neighborhood'  => new NeighbourResource($this->neighborhood),
            'passenger'     => new UserSampleResource($this->passenger),
            'university'    => new UniversityResource($this->university),
            'general_passenger_rate' => $this->whenLoaded('rate'),
            'delivery_info' => $this->sugDriver?->deliveryInfo,
            'source_lat' => $this->{"road-way"} == 'from' ? $this->university->lat : $this->lat,
            'source_lng' => $this->{"road-way"} == 'from' ? $this->university->lng : $this->lng,
            'destination_lat' => $this->{"road-way"} == 'from' ? $this->lat : $this->university->lat,
            'destination_lng' => $this->{"road-way"} == 'from' ? $this->lat : $this->university->lng
        ];
    }
}
