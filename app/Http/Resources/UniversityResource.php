<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UniversityResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name-ar'       => $this->{"name-ar"},
            'name-eng'      => $this->{"name-eng"},
            'country'       => $this->country,
            'city'          => new CityResource($this->cityUni),
            'location'      => $this->location,
            'date-of-add'   => $this->{"date-of-add"},
            'date-of-edit'  => $this->{"date-of-edit"}
        ];
    }
}
