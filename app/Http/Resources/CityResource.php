<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'city-ar'   => $this->{"city-ar"},
            'city-en'   => $this->{"city-en"}
        ];
    }
}
