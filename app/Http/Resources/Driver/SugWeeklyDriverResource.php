<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\UserSampleResource;
use App\Http\Resources\WeekRideBookingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SugWeeklyDriverResource extends JsonResource
{
    public function toArray($request)
    {
        $roadWay = $this->booking->{"road-way"};
        return [
            'id'            => $this->id,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"},
            'viewed'        => $this->viewed,
            'destination_lat' => $roadWay == 'from' ? $this->booking->lat : $this->booking->university->lat,
            'destination_lng' => $roadWay == 'from' ? $this->booking->lng : $this->booking->university->lng,
            'source_lat'    => $roadWay == 'from' ? $this->booking->university->lat : $this->booking->lat,
            'source_lng'    => $roadWay == 'from' ? $this->booking->university->lng : $this->booking->lng,
//            'passenger'     => new UserSampleResource($this->whenLoaded('passenger')),
            'trip'          => new WeekRideBookingResource($this->booking),
            'delivery_info' => $this->whenLoaded('deliveryInfo'),
            'general_passenger_rate' => $this->whenLoaded('rate'),
        ];
    }
}
