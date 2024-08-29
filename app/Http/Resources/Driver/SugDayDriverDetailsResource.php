<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\DayRideBookingResource;
use App\Http\Resources\UserSampleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SugDayDriverDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"},
            'viewed'        => $this->viewed,
            'passenger'     => new UserSampleResource($this->passenger),
            'trip'          => new DayRideBookingResource($this->booking),
            'delivery_info' => $this->whenLoaded('deliveryInfo'),
            'general_passenger_rate' => $this->whenLoaded('rate'),
            'destination_lat' => $this->booking->{"road-way"} == 'from' ? $this->booking->lat : $this->booking->university->lat,
            'destination_lng' => $this->booking->{"road-way"} == 'from' ? $this->booking->lng : $this->booking->university->lng,
            'source_lat' => $this->booking->{"road-way"} == 'from' ? $this->booking->university->lat : $this->booking->lat,
            'source_lng' => $this->booking->{"road-way"} == 'from' ? $this->booking->university->lng : $this->booking->lng,
        ];
    }
}
