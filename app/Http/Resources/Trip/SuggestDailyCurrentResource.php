<?php

namespace App\Http\Resources\Trip;

use App\Http\Resources\DayRideBookingResource;
use App\Http\Resources\DriverInfoResource;
use App\Http\Resources\UserSampleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestDailyCurrentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $roadWay = $this->{"road-way"};

        $to['ar'] = $roadWay == 'from' ? $this->booking->neighborhood->{"neighborhood-ar"} : $this->booking->university->{"name-ar"};
        $to['en'] = $roadWay == 'from' ? $this->booking->neighborhood->{"neighborhood-eng"} : $this->booking->university->{"name-eng"};
        $from['ar'] = $roadWay == 'from' ? $this->booking->university->{"name-ar"} : $this->booking->neighborhood->{"neighborhood-ar"};
        $from['en'] = $roadWay == 'from' ? $this->booking->university->{"name-eng"} : $this->booking->neighborhood->{"neighborhood-eng"};

        return [
            'sug_day_driver' => [
                'id'            => $this->id,
                'action'        => $this->action,
                'date-of-add'   => $this->{"date-of-add"},
                'viewed'        => $this->viewed,
                'trip'          => new DayRideBookingResource($this->booking),
                'driver'        => new UserSampleResource($this->driver),
                'delivery_info' => $this->whenLoaded('deliveryInfo'),
                'driverinfo'    => new DriverInfoResource($this->whenLoaded('driverinfo'))
            ],
            'to' => $to,
            'from' => $from,
            'destination_lat' => $roadWay == 'from' ? $this->booking->lat : $this->booking->university->lat,
            'destination_lng' => $roadWay == 'from' ? $this->booking->lng : $this->booking->university->lng,
            'source_lat'    => $roadWay == 'from' ? $this->booking->university->lat : $this->booking->lat,
            'source_lng'    => $roadWay == 'from' ? $this->booking->university->lng : $this->booking->lng,
            'estimated_time' => 0,
            'action' => 'daily/transport/trip',
        ];
    }
}
