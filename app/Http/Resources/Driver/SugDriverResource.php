<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\Concerns\ResolvesTripCost;
use App\Http\Resources\DayRideBookingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SugDriverResource extends JsonResource
{
    use ResolvesTripCost;

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"},
            'viewed'        => $this->viewed,
//            'passenger'     => new UserSampleResource($this->whenLoaded('passenger')),
            'trip'          => $this->bookingResourceWithTripCost(DayRideBookingResource::class),
            'delivery_info' => $this->whenLoaded('deliveryInfo'),
            'rate'          => $this->whenLoaded('rate')
        ];
    }
}
