@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('drivers.index') }}">{{ __('Drivers') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('drivers.show', $driver->id) }}">{{ $driver->{'user-first-name'} }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Trips') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Driver Trips') }} - {{ $driver->{'user-first-name'} }} {{ $driver->{'user-last-name'} }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $immediateTrips->count() }}</h5>
                            <p class="card-text">{{ __('Immediate Trips') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $dailyTrips->count() }}</h5>
                            <p class="card-text">{{ __('Daily Trips') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $weeklyTrips->count() }}</h5>
                            <p class="card-text">{{ __('Weekly Trips') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $immediateTrips->count() + $dailyTrips->count() + $weeklyTrips->count() }}</h5>
                            <p class="card-text">{{ __('Total Trips') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($immediateTrips->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Immediate Trips') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('Booking ID') }}</th>
                                <th>{{ __('Passenger') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Cost') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($immediateTrips as $trip)
                                <tr>
                                    <td>{{ $trip->{'booking-id'} }}</td>
                                    <td>
                                        @if($trip->passenger)
                                            {{ $trip->passenger->{'user-first-name'} }} {{ $trip->passenger->{'user-last-name'} }}
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </td>
                                    <td>{{ $trip->{'date-of-add'} ? \Carbon\Carbon::parse($trip->{'date-of-add'})->format('Y-m-d H:i') : '-' }}</td>
                                    <td>{{ $trip->booking?->service?->cost ?? 0 }} {{ __('SAR') }}</td>
                                    <td>
                                        @if($trip->booking)
                                            {{ $trip->booking->{'road-way'} == 'from' ? ($trip->booking->university?->{'name-ar'} ?? '-') : ($trip->booking->neighborhood?->{'neighborhood-ar'} ?? '-') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td><span class="badge badge-info">{{ __('Immediate') }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($dailyTrips->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Daily Trips') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('Booking ID') }}</th>
                                <th>{{ __('Passenger') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Cost') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dailyTrips as $trip)
                                <tr>
                                    <td>{{ $trip->{'booking-id'} }}</td>
                                    <td>
                                        @if($trip->passenger)
                                            {{ $trip->passenger->{'user-first-name'} }} {{ $trip->passenger->{'user-last-name'} }}
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </td>
                                    <td>{{ $trip->{'date-of-add'} ? \Carbon\Carbon::parse($trip->{'date-of-add'})->format('Y-m-d H:i') : '-' }}</td>
                                    <td>{{ $trip->booking?->service?->cost ?? 0 }} {{ __('SAR') }}</td>
                                    <td>
                                        @if($trip->booking)
                                            {{ $trip->booking->{'road-way'} == 'from' ? ($trip->booking->university?->{'name-ar'} ?? '-') : ($trip->booking->neighborhood?->{'neighborhood-ar'} ?? '-') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td><span class="badge badge-success">{{ __('Daily') }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($weeklyTrips->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Weekly Trips') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('Booking ID') }}</th>
                                <th>{{ __('Passenger') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Cost') }}</th>
                                <th>{{ __('Location') }}</th>
                                <th>{{ __('Status') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($weeklyTrips as $trip)
                                <tr>
                                    <td>{{ $trip->{'booking-id'} }}</td>
                                    <td>
                                        @if($trip->passenger)
                                            {{ $trip->passenger->{'user-first-name'} }} {{ $trip->passenger->{'user-last-name'} }}
                                        @else
                                            {{ __('N/A') }}
                                        @endif
                                    </td>
                                    <td>{{ $trip->{'date-of-add'} ? \Carbon\Carbon::parse($trip->{'date-of-add'})->format('Y-m-d H:i') : '-' }}</td>
                                    <td>{{ $trip->booking?->service?->cost ?? 0 }} {{ __('SAR') }}</td>
                                    <td>
                                        @if($trip->booking)
                                            {{ $trip->booking->{'road-way'} == 'from' ? ($trip->booking->university?->{'name-ar'} ?? '-') : ($trip->booking->neighborhood?->{'neighborhood-ar'} ?? '-') }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td><span class="badge badge-primary">{{ __('Weekly') }}</span></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($immediateTrips->count() == 0 && $dailyTrips->count() == 0 && $weeklyTrips->count() == 0)
                <div class="alert alert-info">
                    {{ __('No trips found for this driver.') }}
                </div>
            @endif

            <a href="{{ route('drivers.show', $driver->id) }}" class="btn btn-secondary">
                {{ __('Back to Driver Details') }}
            </a>
        </div>
    </div>
@endsection
