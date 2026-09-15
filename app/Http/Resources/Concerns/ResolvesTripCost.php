<?php

namespace App\Http\Resources\Concerns;

trait ResolvesTripCost
{
    protected function resolveTripCost(): float
    {
        $explicit = $this->resource->getAttribute('trip_cost');
        if ($explicit !== null) {
            return (float) $explicit;
        }

        if (method_exists($this->resource, 'sugDriver') && $this->sugDriver) {
            return $this->sugDriver->snapshottedTripCost();
        }

        return (float) ($this->service?->cost ?? 0);
    }

    /**
     * Inject snapshotted trip cost from the sug/driver resource onto its booking.
     */
    protected function bookingResourceWithTripCost(string $resourceClass, $booking = null)
    {
        $booking = $booking ?? $this->booking;

        if ($booking && method_exists($this->resource, 'snapshottedTripCost')) {
            $booking->setAttribute('trip_cost', $this->resource->snapshottedTripCost());
        }

        return new $resourceClass($booking);
    }

    protected function tripCostFromSug($sug, $fallbackService = null): float
    {
        if ($sug && method_exists($sug, 'snapshottedTripCost')) {
            return $sug->snapshottedTripCost();
        }

        return (float) ($fallbackService?->cost ?? 0);
    }
}
