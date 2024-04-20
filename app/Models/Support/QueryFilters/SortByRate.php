<?php

namespace App\Models\Support\QueryFilters;

use App\Models\DeliveryInfo;
use Spatie\QueryBuilder\Sorts\Sort;
use Illuminate\Database\Eloquent\Builder;

class SortByRate implements Sort
{
    public function __invoke(Builder $query, bool $descending, string $property)
    {
        $direction = $descending ? 'DESC' : 'ASC';

        $query->orderBy(
            DeliveryInfo::select('passenger-rate')->whereColumn('delivery-info.sug-id', 'suggestions-drivers.id')
            , $direction);
    }
}
