<?php

namespace App\Http\Resources\Driver;

use App\Http\Resources\ServiceResource;
use Illuminate\Http\Resources\Json\JsonResource;

class FeatureResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name_ar'       => $this->name_ar,
            'name_en'       => $this->name_en,
            'description_ar' => $this->description_ar,
            'description_en' => $this->description_en,
            'service'       => new ServiceResource($this->service),
        ];
    }
}
