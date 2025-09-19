<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'            => $this->id,
            'name_ar'       => $this->name_ar,
            'name_en'       => $this->name_en,
            'price_monthly' => $this->price_monthly,
            'price_annual'  => $this->price_annual,
            'status'        => $this->statusText,
            'features'      => FeatureResource::collection($this->features),
        ];
    }
}
