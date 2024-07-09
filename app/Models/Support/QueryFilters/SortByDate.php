<?php

namespace App\Models\Support\QueryFilters;

use App\Models\DayRideBooking;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortByDate implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        if($property == 'daily') {
            $query->orderBy('date-of-add', $direction);
//                DayRideBooking::select('date-of-add')->whereColumn('day-ride-booking.id', 'sug-day-drivers.booking-id'),
//                $direction);
        } elseif ($property == 'weekly') {
            $query->orderBy('date-of-ser', $direction);
        } else {
            $query->orderBy('date-of-add', $direction);
        }
    }
}
