<?php

namespace App\Http\Resources\Driver\Wasl;

use App\Http\Resources\ServiceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class RegisterResource extends JsonResource
{
    public function toArray($request)
    {
        $carLetters = $this->driverInfo->{"car-letters"};
        $plateLetters = $this->splitChars($carLetters);

        return [
            "driver" => [
                "identityNumber" => $this->driverInfo->identity_number,
                "dateOfBirthHijri" => $this->driverInfo->date_of_birth_hijri,
                "dateOfBirthGregorian" => $this->driverInfo->date_of_birth,
                "emailAddress" => $this->email,
                "mobileNumber" => '+' . $this->callingKey->{"call-key"} . $this->{"phone-no"},
            ],
            "vehicle" => [
                "sequenceNumber" => $this->driverInfo->{"sequence-number"},
                "plateLetterRight" => $plateLetters[0] ?? '',
                "plateLetterMiddle" => $plateLetters[1] ?? '',
                "plateLetterLeft" => $plateLetters[2] ?? '',
                "plateNumber" => (string) $this->driverInfo->{"car-number"},
                "plateType" => "1"
            ]
        ];
    }

    function splitChars(string $str): array
    {
        mb_internal_encoding('UTF-8');
        return mb_str_split($str);
    }
}
