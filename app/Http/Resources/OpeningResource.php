<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OpeningResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'title-ar'      => $this->{"title-ar"},
            'title-eng'     => $this->{"title-eng"},
            'content-ar'    => $this->{"title-ar"},
            'content-eng'   => $this->{"title-eng"},
        ];
    }
}
