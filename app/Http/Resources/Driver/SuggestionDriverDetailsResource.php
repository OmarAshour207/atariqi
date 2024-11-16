<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\DayRideBookingResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestionDriverDetailsResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"},
            'viewed'        => $this->viewed,
            'trip'          => new DayRideBookingResource($this->booking),
            'delivery_info' => $this->deliveryInfo,
            'general_passenger_rate' => $this->rate,
            'source_lat' => $this->{"road-way"} == 'from' ? $this->booking->university->lat : $this->booking->lat,
            'source_lng' => $this->{"road-way"} == 'from' ? $this->booking->university->lng : $this->booking->lng,
            'destination_lat' => $this->{"road-way"} == 'to' ? $this->booking->lat : $this->booking->university->lat,
            'destination_lng' => $this->{"road-way"} == 'to' ? $this->booking->lat : $this->booking->university->lng
        ];
    }

}
