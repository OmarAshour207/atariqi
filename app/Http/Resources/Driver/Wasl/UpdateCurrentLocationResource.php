<?php

namespace App\Http\Resources\Driver\Wasl;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateCurrentLocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "locations" => [
                [
                    "driverIdentityNumber" => $this->driverInfo->identity_number,
                    "vehicleSequenceNumber" => $this->driverInfo->date_of_birth_hijri,
                    "latitude" => $this->{"current-lat"},
                    "longitude" => $this->{"current-lng"},
                    "hasCustomer" => true,
                    "updatedWhen" => Carbon::now()
                ]
            ]
        ];
    }

}
