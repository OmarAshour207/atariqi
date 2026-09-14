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
        });
    }

    public function duesAmountForCost(?float $cost = null): float
    {
        $cost ??= (float) ($this->booking?->service?->cost ?? 0);
        $percentage = $this->atariqi_percentage;

        if ($percentage === null) {
            $percentage = Subscription::generalDuesPercentageValue();
        }

        return ((float) $cost * (float) $percentage) / 100;
    }
}
