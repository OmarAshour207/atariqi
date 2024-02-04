<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DriverTypeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'        => $this->id,
            'name-ar'   => $this->{"name-ar"},
            'name-eng'  => $this->{"name-eng"}
        ];
    }
}
