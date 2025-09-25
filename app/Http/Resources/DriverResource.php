<?php

namespace App\Http\Resources;

use App\Http\Resources\Driver\PackageResource;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'                => $this->id,
            'user-first-name'   => $this->{"user-first-name"},
            'user-last-name'    => $this->{"user-last-name"},
            'phone-no'          => $this->{"phone-no"},
            'gender'            => $this->gender,
            'email'             => $this->email,
            'approval'          => $this->approval,
            'user-type'         => $this->{"user-type"},
            'user-stage'        => new StageResource($this->stage),
            'call-key'          => new CallingKeyResource($this->callingKey),
            'university'        => new UniversityResource($this->university),
            'image'             => url("uploads/$this->id/$this->image"),
            'date-of-add'       => $this->{"date-of-add"},
            'date-of-edit'      => $this->{"date-of-edit"},
            'driver_info'       => new DriverInfoResource($this->driverInfo),
            'driver_car'        => new DriverCarResource($this->driverCar),
            'neighbourhoods'    => NeighbourResource::collection($this->university->cityUni->neighbours),
            // 'package'           => new PackageResource($this->whenLoaded('activePackage'))
        ];
    }
}
