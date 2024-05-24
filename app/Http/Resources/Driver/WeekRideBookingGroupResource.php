<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\NeighbourResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\UniversityResource;
use App\Http\Resources\UserSampleResource;
use Illuminate\Http\Resources\Json\JsonResource;

class WeekRideBookingGroupResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'group-id'  => $this->resource->first()->{"group-id"},
            'data'      => $this->transformData($this->resource)
        ];
    }

    private function transformData($data)
    {
        return $data->map(function ($item) {
            return [
                'id'            => $item->id,
                'group_id'      => $item->{"group-id"},
                'date_of_ser'   => $item->{"date-of-ser"},
                'road_way'      => $item->{"road-way"},
                'time_go'       => $item->{"time-go"},
                'time_back'     => $item->{"time-back"},
                'lat'           => $item->{"lat"},
                'lng'           => $item->{"lng"},
                'action'        => $item->{"action"},
                'status'        => $item->status,
                'neighborhood'  => new NeighbourResource($item->neighborhood),
                'passenger'     => new UserSampleResource($item->passenger),
                'university'    => new UniversityResource($item->university),
                'service_id'    => new ServiceResource($item->service),
                'general_passenger_rate' => $item->rate,
                'delivery_info' => $item->sugDriver?->deliveryInfo
            ];
        });
    }
}
