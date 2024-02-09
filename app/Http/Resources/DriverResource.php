<?php

namespace App\Http\Resources;

use App\Models\DriverInfo;
use App\Models\DriversCar;
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
            'call-key'          => new CallingKeyResource($this->callingKey),
            'university'        => new UniversityResource($this->university),
            'image'             => url($this->image),
            'date-of-add'       => $this->{"date-of-add"},
            'date-of-edit'      => $this->{"date-of-edit"},
            'driver_info'       => new DriverInfo($this->driverInfo),
            'driver_car'        => new DriversCar($this->driverCar)
        ];
    }
}