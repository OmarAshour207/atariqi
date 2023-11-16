<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SugWeekDriverResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"},
            'viewed'        => $this->viewed,
            'trip'          => new WeekRideBookingResource($this->booking),
            'driver'        => new UserSampleResource($this->whenLoaded('driver')),
            'driverinfo'    => new DriverInfoResource($this->whenLoaded('driverinfo'))
        ];
    }
}
