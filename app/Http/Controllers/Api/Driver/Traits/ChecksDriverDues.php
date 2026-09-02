<?php

namespace App\Http\Controllers\Api\Driver\Traits;

use App\Services\DriverDuesService;
use Symfony\Component\HttpFoundation\JsonResponse;

trait ChecksDriverDues
{
    protected function blockIfDriverCannotAcceptTripsDueToDues(): ?JsonResponse
    {
        if (app(DriverDuesService::class)->canAcceptTrips()) {
            return null;
        }

        return $this->sendError(
            __('Please pay your dues to activate your services again. Note: you can deliver your previously accepted rides'),
            [__('Please pay your dues to activate your services again. Note: you can deliver your previously accepted rides')]
        );
    }
}
