<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\UserSampleResource;
use App\Http\Resources\WeekRideBookingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SugWeeklyDriverResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"},
            'viewed'        => $this->viewed,
//            'passenger'     => new UserSampleResource($this->whenLoaded('passenger')),
            'trip'          => new WeekRideBookingResource($this->booking),
            'delivery_info' => $this->whenLoaded('deliveryInfo'),
            'general_passenger_rate' => $this->whenLoaded('rate'),
        ];
    }
}
