<?php

namespace App\Http\Resources\Driver\Wasl;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UpdateCurrentLocationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'locations' => [
                app(\App\Services\WaslService::class)->buildLocationEntry(
                    (string) ($this->resource['driverIdentityNumber'] ?? ''),
                    (string) ($this->resource['vehicleSequenceNumber'] ?? ''),
                    (float) ($this->resource['latitude'] ?? 0),
                    (float) ($this->resource['longitude'] ?? 0),
                    (bool) ($this->resource['hasCustomer'] ?? false),
                    isset($this->resource['updatedWhen'])
                        ? Carbon::parse($this->resource['updatedWhen'])
                        : null
                ),
            ],
        ];
    }
}
