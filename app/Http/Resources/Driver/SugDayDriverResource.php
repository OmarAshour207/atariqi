<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\DayRideBookingResource;
use App\Http\Resources\UserSampleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SugDayDriverResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"},
            'viewed'        => $this->viewed,
            'passenger'     => new UserSampleResource($this->whenLoaded('passenger')),
            'trip'          => new DayRideBookingResource($this->booking),
            'delivery_info' => $this->whenLoaded('deliveryInfo'),
            'general_passenger_rate' => $this->whenLoaded('rate'),
        ];
    }
}
