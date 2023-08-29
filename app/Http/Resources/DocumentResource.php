<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'title-ar'      => $this->{"title-ar"},
            'title-eng'     => $this->{"title-eng"},
            'file-link'     => url($this->{"file-link"}),
            'date-of-add'   => $this->{"date-of-add"},
            'date-of-edit'  => $this->{"date-of-edit"}
        ];
    }
}
