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
                            <li class="breadcrumb-item active" aria-current="page">{{ __('All Trips') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('All Passenger Trips') }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('passengers.all-trips') }}" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="passenger_id">{{ __('Passenger') }}</label>
                                <select class="form-control" id="passenger_id" name="passenger_id">
                                    <option value="">{{ __('All Passengers') }}</option>
                                    @foreach($passengers as $passenger)
                                        <option value="{{ $passenger->id }}" {{ request('passenger_id') == $passenger->id ? 'selected' : '' }}>
                                            {{ $passenger->{'user-first-name'} }} {{ $passenger->{'user-last-name'} }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="driver_id">{{ __('Driver') }}</label>
                                <select class="form-control" id="driver_id" name="driver_id">
                                    <option value="">{{ __('All Drivers') }}</option>
                                    @foreach($drivers as $driver)
                                        <option value="{{ $driver->id }}" {{ request('driver_id') == $driver->id ? 'selected' : '' }}>
                                            {{ $driver->{'user-first-name'} }} {{ $driver->{'user-last-name'} }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_from">{{ __('From Date') }}</label>
                                <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_to">{{ __('To Date') }}</label>
                                <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">{{ __('Filter') }}</button>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <a href="{{ route('passengers.all-trips') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Trip Statistics -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $immediateTrips->total() }}</h5>
                            <p class="card-text">{{ __('Immediate Trips') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $dailyTrips->total() }}</h5>
                            <p class="card-text">{{ __('Daily Trips') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $weeklyTrips->total() }}</h5>
                            <p class="card-text">{{ __('Weekly Trips') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Immediate Trips -->
            @if($immediateTrips->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Immediate Trips') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Passenger') }}</th>
                                <th>{{ __('Driver') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($immediateTrips as $trip)
                                <tr>
                                    <td>{{ $trip->id }}</td>
                                    <td>
                                        <a href="{{ route('passengers.show', $trip->passenger->id) }}" class="text-primary">
                                            {{ optional($trip->passenger)->{'user-first-name'} }} {{ optional($trip->passenger)->{'user-last-name'} }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('drivers.show', $trip->driver->id) }}" class="text-primary">
                                            {{ optional($trip->driver)->{'user-first-name'} }} {{ optional($trip->driver)->{'user-last-name'} }}
                                        </a>
                                    </td>
                                    <td>{{ optional($trip->{'date-of-add'})->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td><span class="badge badge-info">{{ __('Immediate') }}</span></td>
                                    <td>
                                        <a href="{{ route('passengers.show', $trip->passenger->id) }}" class="btn btn-sm btn-info">{{ __('View Passenger') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $immediateTrips->links('dashboard.pagination.custom') }}
                    </div>
                </div>
            @endif

            <!-- Daily Trips -->
            @if($dailyTrips->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Daily Trips') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Passenger') }}</th>
                                <th>{{ __('Driver') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($dailyTrips as $trip)
                                <tr>
                                    <td>{{ $trip->id }}</td>
                                    <td>
                                        <a href="{{ route('passengers.show', $trip->passenger->id) }}" class="text-primary">
                                            {{ optional($trip->passenger)->{'user-first-name'} }} {{ optional($trip->passenger)->{'user-last-name'} }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('drivers.show', $trip->driver->id) }}" class="text-primary">
                                            {{ optional($trip->driver)->{'user-first-name'} }} {{ optional($trip->driver)->{'user-last-name'} }}
                                        </a>
                                    </td>
                                    <td>{{ optional($trip->{'date-of-add'})->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td><span class="badge badge-success">{{ __('Daily') }}</span></td>
                                    <td>
                                        <a href="{{ route('passengers.show', $trip->passenger->id) }}" class="btn btn-sm btn-info">{{ __('View Passenger') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $dailyTrips->links('dashboard.pagination.custom') }}
                    </div>
                </div>
            @endif

            <!-- Weekly Trips -->
            @if($weeklyTrips->count() > 0)
                <div class="card mb-3">
                    <div class="card-header">
                        <h4 class="card-title">{{ __('Weekly Trips') }}</h4>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>{{ __('ID') }}</th>
                                <th>{{ __('Passenger') }}</th>
                                <th>{{ __('Driver') }}</th>
                                <th>{{ __('Date') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($weeklyTrips as $trip)
                                <tr>
                                    <td>{{ $trip->id }}</td>
                                    <td>
                                        <a href="{{ route('passengers.show', $trip->passenger->id) }}" class="text-primary">
                                            {{ optional($trip->passenger)->{'user-first-name'} }} {{ optional($trip->passenger)->{'user-last-name'} }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('drivers.show', $trip->driver->id) }}" class="text-primary">
                                            {{ optional($trip->driver)->{'user-first-name'} }} {{ optional($trip->driver)->{'user-last-name'} }}
                                        </a>
                                    </td>
                                    <td>{{ optional($trip->{'date-of-add'})->format('Y-m-d H:i') ?? '-' }}</td>
                                    <td><span class="badge badge-primary">{{ __('Weekly') }}</span></td>
                                    <td>
                                        <a href="{{ route('passengers.show', $trip->passenger->id) }}" class="btn btn-sm btn-info">{{ __('View Passenger') }}</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        {{ $weeklyTrips->links('dashboard.pagination.custom') }}
                    </div>
                </div>
            @endif

            @if($immediateTrips->count() == 0 && $dailyTrips->count() == 0 && $weeklyTrips->count() == 0)
                <div class="alert alert-info">
                    {{ __('No trips found matching your criteria.') }}
                </div>
            @endif

            <a href="{{ route('passengers.index') }}" class="btn btn-secondary">
                {{ __('Back to Passengers') }}
            </a>
        </div>
    </div>
@endsection
