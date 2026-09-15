<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\ResolvesTripCost;
use Illuminate\Http\Resources\Json\JsonResource;

class SugDayDrivingResource extends JsonResource
{
    use ResolvesTripCost;

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"},
            'viewed'        => $this->viewed,
            'trip'          => $this->bookingResourceWithTripCost(DayRideBookingResource::class),
            'driver'        => new UserSampleResource($this->driver),
            'delivery_info' => $this->whenLoaded('deliveryInfo'),
            'driverinfo'    => new DriverInfoResource($this->whenLoaded('driverinfo'))
        ];
    }
}
