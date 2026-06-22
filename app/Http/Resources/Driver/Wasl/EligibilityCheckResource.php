<?php

namespace App\Http\Resources\Driver\Wasl;

use Illuminate\Http\Resources\Json\JsonResource;

class EligibilityCheckResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'driverIds' => [
                ['id' => (string) $this->driverInfo->identity_number],
            ],
        ];
    }
}
