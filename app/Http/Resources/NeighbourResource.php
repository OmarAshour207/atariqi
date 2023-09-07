<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NeighbourResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'neighborhood-ar'   => $this->{"neighborhood-ar"},
            'neighborhood-eng'  => $this->{"neighborhood-eng"}
        ];
    }
}
