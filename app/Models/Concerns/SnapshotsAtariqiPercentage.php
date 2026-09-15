<?php

namespace App\Models\Concerns;

use App\Models\Subscription;

trait SnapshotsAtariqiPercentage
{
    protected static function bootSnapshotsAtariqiPercentage(): void
    {
        static::creating(function ($model) {
            if ($model->getAttribute('atariqi_percentage') === null) {
                $model->setAttribute(
                    'atariqi_percentage',
                    Subscription::generalDuesPercentageValue()
                );
            }

            if ($model->getAttribute('trip_cost') === null) {
                $model->setAttribute('trip_cost', static::resolveTripCostSnapshot($model));
            }
        });
    }

    protected static function resolveTripCostSnapshot($model): ?float
    {
        $bookingId = $model->getAttribute('booking-id');
        if (!$bookingId) {
            return null;
        }

        $booking = $model->booking()->with('service')->first();
        if ($booking?->service?->cost === null) {
            return null;
        }

        return (float) $booking->service->cost;
    }

    public function snapshottedTripCost(): float
    {
        if ($this->trip_cost !== null) {
            return (float) $this->trip_cost;
        }

        return (float) ($this->booking?->service?->cost ?? 0);
    }

    public function snapshottedAtariqiPercentage(): float
    {
        if ($this->atariqi_percentage !== null) {
            return (float) $this->atariqi_percentage;
        }

        return Subscription::generalDuesPercentageValue();
    }

    public function companyAmount(): float
    {
        return ($this->snapshottedTripCost() * $this->snapshottedAtariqiPercentage()) / 100;
    }

    public function driverAmount(): float
    {
        return $this->snapshottedTripCost() - $this->companyAmount();
    }

    public function duesAmountForCost(?float $cost = null): float
    {
        $cost ??= $this->snapshottedTripCost();

        return ((float) $cost * $this->snapshottedAtariqiPercentage()) / 100;
    }
}
