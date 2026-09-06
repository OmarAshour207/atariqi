<?php

namespace App\Models\Support\QueryFilters;

use App\Models\DayRideBooking;
use App\Models\WeekRideBooking;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortByDate implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        if($property == 'daily') {
            $query->orderBy(
                DayRideBooking::select('date-of-ser')->whereColumn('day-ride-booking.id', 'sug-day-drivers.booking-id'),
                $direction);
        } elseif ($property == 'weekly') {
            $query->orderBy(
                WeekRideBooking::select('date-of-ser')
                    ->whereColumn('week-ride-booking.id', 'sug-week-drivers.booking-id'),
                $direction);
        } else {
            $query->orderBy('date-of-add', $direction);
        }
    }
}
