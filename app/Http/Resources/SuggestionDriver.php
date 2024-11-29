<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SuggestionDriver extends JsonResource
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
            'trip'          => new DayRideBookingResource($this->booking),
            'driver'        => new UserSampleResource($this->whenLoaded('driver')),
            'driverinfo'    => new DriverInfoResource($this->whenLoaded('driverinfo')),
            'delivery_info' => $this->whenLoaded('deliveryInfo'),
            'general_passenger_rate' => $this->whenLoaded('rate')
        ];
    }
}
