<?php

namespace App\Http\Resources\Driver\Wasl;

use Illuminate\Http\Resources\Json\JsonResource;

class EligibilityCheckResource extends JsonResource
{
    public function toArray($request): array
    {
        $identityNumber = trim((string) ($this->driverInfo?->identity_number ?? ''));

        return [
            'driverIds' => [
                ['id' => $identityNumber],
            ],
        ];
    }
}
