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
            'contant-ar'    => $this->{"contant-ar"},
            'contant-eng'   => $this->{"contant-eng"},
            'date-of-add'   => $this->{"date-of-add"}
        ];
    }
}
