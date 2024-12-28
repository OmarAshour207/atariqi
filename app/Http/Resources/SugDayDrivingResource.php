<?php

namespace App\Http\Resources;

use App\Models\DriverInfo;
use Illuminate\Http\Resources\Json\JsonResource;

class SugDayDrivingResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"},
            'viewed'        => $this->viewed,
            'trip'          => new DayRideBookingResource($this->booking),
            'driver'        => new UserSampleResource($this->driver),
            'delivery_info' => $this->whenLoaded('deliveryInfo'),
            'driverinfo'    => new DriverInfoResource($this->whenLoaded('driverinfo')),
            'diver_arrived' => $this->deliveryInfo?->{"arrived-location"} ? true : false
        ];
    }
}
