<?php

namespace App\Http\Resources;

use App\Http\Resources\Concerns\ResolvesTripCost;
use Illuminate\Http\Resources\Json\JsonResource;

class RideBookingResource extends JsonResource
{
    use ResolvesTripCost;

    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'passenger'     => new UserSampleResource($this->passenger),
            'neighborhood'  => new NeighbourResource($this->neighborhood),
            'service'       => new ServiceResource($this->service),
            'service_id'    => new ServiceResource($this->service),
            'university'    => new UniversityResource($this->university),
            'delivery_info' => $this->sugDriver?->deliveryInfo,
            'road_way'      => $this->{"road-way"},
            'lat'           => $this->lat,
            'lng'           => $this->lng,
            'action'        => $this->action,
            'date-of-add'   => $this->{"date-of-add"},
            'trip_cost'     => $this->resolveTripCost(),
        ];
    }
}
