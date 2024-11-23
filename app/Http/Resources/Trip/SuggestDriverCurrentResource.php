<?php

namespace App\Http\Resources\Trip;

use App\Http\Resources\DayRideBookingResource;
use App\Http\Resources\DriverInfoResource;
use App\Http\Resources\RideBookingResource;
use App\Http\Resources\UserSampleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class SuggestDriverCurrentResource extends JsonResource
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
            'drivers'       => [
                new DriverInfoResource($this->driverinfo)
            ],
            'trip'          => new RideBookingResource($this->booking),
            'destination_lat' => $roadWay == 'from' ? $this->booking->lat : $this->booking->university->lat,
            'destination_lng' => $roadWay == 'from' ? $this->booking->lng : $this->booking->university->lng,
            'source_lat'    => $roadWay == 'from' ? $this->booking->university->lat : $this->booking->lat,
            'source_lng'    => $roadWay == 'from' ? $this->booking->university->lng : $this->booking->lng,
            'to'            => $to,
            'from'          => $from,
            'estimated_time' => $this->deliveryInfo?->{"expect-arrived"},
            'action'        => 'immediate/transport/trips',
        ];
    }
}
