<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
class DriverCarResource extends JsonResource
{
    public function toArray($request)
    {
        $driverId = $this->{"driver-id"};

        return [
            'id'            => $this->id,
            'driver-id'     => $this->{"driver-id"},
            'driver-type'   => new DriverTypeResource($this->driverType),
            'car_form_img'  => url("uploads/$driverId/$this->car_form_img"),
            'license_img'   => url("uploads/$driverId/$this->license_img"),
            'car_front_img' => url("uploads/$driverId/$this->car_front_img"),
            'car_back_img'  => url("uploads/$driverId/$this->car_back_img"),
            'car_rside_img' => url("uploads/$driverId/$this->car_rside_img"),
            'car_lside_img' => url("uploads/$driverId/$this->car_lside_img"),
            'car_insideFront_img'  => url("uploads/$driverId/$this->car_insideFront_img"),
            'car_insideBack_img'  => url("uploads/$driverId/$this->car_insideBack_img"),
            'date_of_add'  => $this->date_of_add
        ];
    }
}
