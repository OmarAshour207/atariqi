<?php

namespace App\Models\Support\QueryFilters;

use App\Models\PassengerRate;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortByRate implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        if($property == 'daily') {
            $query->orderBy(
                PassengerRate::select('rate')->whereColumn('passenger-rate.user-id', 'sug-day-drivers.passenger-id')
                , $direction);
        } elseif ($property == 'weekly') {
            $query->orderBy(
                PassengerRate::select('rate')->whereColumn('passenger-rate.user-id', 'week-ride-booking.passenger-id')
                , $direction);
        } else {
            $query->orderBy(
                PassengerRate::select('rate')->whereColumn('passenger-rate.user-id', 'suggestions-drivers.passenger-id')
                , $direction);
        }
    }
}
