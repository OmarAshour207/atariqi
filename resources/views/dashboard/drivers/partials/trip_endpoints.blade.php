@php
    $endpoints = \App\Support\TripLocationFormatter::resolveFromBooking($trip->booking ?? null);
@endphp

@if($endpoints)
    <div class="trip-endpoints">
        <div class="trip-endpoint trip-endpoint--from mb-2">
            <span class="badge badge-secondary mb-1">{{ __('From') }}</span>
            @include('dashboard.drivers.partials.trip_endpoint', ['endpoint' => $endpoints['from']])
        </div>
        <div class="trip-endpoint trip-endpoint--to">
            <span class="badge badge-secondary mb-1">{{ __('To') }}</span>
            @include('dashboard.drivers.partials.trip_endpoint', ['endpoint' => $endpoints['to']])
        </div>
    </div>
@else
    <span class="text-muted">-</span>
@endif
