<?php

namespace App\Http\Resources\Driver;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPackageResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'status' => $this->statusText,
            'interval' => $this->interval,
            'package' => new PackageResource($this->package),
            'user'  => $this->whenLoaded('user'),
        ];
    }
}
