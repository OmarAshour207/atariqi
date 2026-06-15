@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('passengers.index') }}">{{ __('Passengers') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Complaints') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Complaints') }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['immediate_count'] }}</h5>
                            <p class="card-text">{{ __('Immediate Complaints') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['daily_count'] }}</h5>
                            <p class="card-text">{{ __('Daily Complaints') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['weekly_count'] }}</h5>
                            <p class="card-text">{{ __('Weekly Complaints') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $stats['total_complaints_rates'] }}</h5>
                            <p class="card-text">{{ __('Total Complaints') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            @if($stats['immediateTrips']->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Immediate Complaints') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Driver') }}</th>
                                <th>{{ __('Comment') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stats['immediateTrips'] as $trip)
                                <tr>
                                    <td>{{ $trip->{"sug-id"} }}</td>
                                    <td>{{ $trip->ride->driver->{'user-first-name'} }} {{ $trip->ride->driver->{'user-last-name'} }}</td>
                                    <td>{{ $trip->comment }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($stats['dailyTrips']->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Daily Complaints') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Driver') }}</th>
                                <th>{{ __('Comment') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stats['dailyTrips'] as $trip)
                                <tr>
                                    <td>{{ $trip->{"sug-id"} }}</td>
                                    <td>{{ $trip->ride->driver->{'user-first-name'} }} {{ $trip->ride->driver->{'user-last-name'} }}</td>
                                    <td>{{ $trip->comment }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($stats['weeklyTrips']->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Weekly Complaints') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Driver') }}</th>
                                <th>{{ __('Comment') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($stats['weeklyTrips'] as $trip)
                                <tr>
                                    <td>{{ $trip->{"sug-id"} }}</td>
                                    <td>{{ $trip->ride->driver->{'user-first-name'} }} {{ $trip->ride->driver->{'user-last-name'} }}</td>
                                    <td>{{ $trip->comment }}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if($stats['total_complaints_rates'] == 0)
                <div class="alert alert-info">
                    {{ __('No Complaints found for this passenger.') }}
                </div>
            @endif

            @if ($stats['total_complaints_rates'] > 5)

            @endif

        </div>
    </div>
@endsection
