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

        // Remove 966 prefix from phone number if it exists
        $phoneNo = $this->{"phone-no"};
        if (strpos($phoneNo, '966') === 0) {
            $phoneNo = substr($phoneNo, 3);
        }

        $mobileNumber = '+' . $this->callingKey->{"call-key"} . $phoneNo;

        return [
            "driver" => [
                "identityNumber" => $this->driverInfo->identity_number,
                "dateOfBirthHijri" => $this->driverInfo->date_of_birth_hijri,
                "dateOfBirthGregorian" => $this->driverInfo->date_of_birth,
                "emailAddress" => $this->email,
                "mobileNumber" => $mobileNumber,
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
        $str = str_replace(' ', '', $str);
        return mb_str_split($str);
    }
}
