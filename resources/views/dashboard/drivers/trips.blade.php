@extends('dashboard.layouts.app')

@section('title', __('Driver Trips'))

@section('content')
    <div class="mdk-drawer-layout__content page">
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Driver Trips') }}</h4>
                </div>
                <div class="card-body">
                    <!-- Filters -->
                    <div class="row mb-3">
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('drivers.trips') }}">
                                <div class="form-group">
                                    <label for="driver_id">{{ __('Filter by Driver') }}</label>
                                    <select name="driver_id" id="driver_id" class="form-control">
                                        <option value="">{{ __('All Drivers') }}</option>
                                        @foreach($drivers as $driver)
                                            <option value="{{ $driver->id }}" {{ $driverId == $driver->id ? 'selected' : '' }}>
                                                {{ $driver->{'user-first-name'} . ' ' . $driver->{'user-last-name'} }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="trip_type">{{ __('Filter by Trip Type') }}</label>
                                <select name="trip_type" id="trip_type" class="form-control">
                                    <option value="">{{ __('All Types') }}</option>
                                    <option value="immediate" {{ $tripType == 'immediate' ? 'selected' : '' }}>{{ __('Immediate') }}</option>
                                    <option value="daily" {{ $tripType == 'daily' ? 'selected' : '' }}>{{ __('Daily') }}</option>
                                    <option value="weekly" {{ $tripType == 'weekly' ? 'selected' : '' }}>{{ __('Weekly') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                                    <a href="{{ route('drivers.trips') }}" class="btn btn-secondary">{{ __('Clear Filters') }}</a>
                                </div>
                            </div>
                        </div>
                            </form>
                    </div>

                    <!-- Trips Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{{ __('#') }}</th>
                                    <th>{{ __('Booking ID') }}</th>
                                    <th>{{ __('Driver') }}</th>
                                    <th>{{ __('Passenger') }}</th>
                                    <th>{{ __('Trip Type') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Cost') }}</th>
                                    <th>{{ __('Location') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedTrips as $index => $trip)
                                    <tr>
                                        <td>{{ $paginatedTrips->firstItem() + $index }}</td>
                                        <td>{{ $trip->{"booking-id"} }}</td>
                                        <td>
                                            @if($trip->driver)
                                                <a href="{{ route('drivers.show', $trip->driver->id) }}">
                                                    {{ $trip->driver->{'user-first-name'} . ' ' . $trip->driver->{'user-last-name'} }}
                                                </a>
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </td>
                                        <td>
                                            @if($trip->passenger)
                                                {{ $trip->passenger->{'user-first-name'} . ' ' . $trip->passenger->{'user-last-name'} }}
                                            @else
                                                {{ __('N/A') }}
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge badge-{{ $trip->trip_type == 'immediate' ? 'primary' : ($trip->trip_type == 'daily' ? 'success' : 'info') }}">
                                                {{ ucfirst($trip->trip_type) }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($trip->{'date-of-add'})->format('Y-m-d H:i') }}</td>
                                        <td>{{ $trip->booking->service->cost ?? 0 }}</td>
                                        <td>{{ $trip->booking->{"road-way"} == 'from' ? $trip->booking->university->{"name-ar"} : $trip->booking->neighborhood->{"neighborhood-ar"} }}</td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">{{ __('No trips found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="mt-4">
                        {{ $paginatedTrips->links('dashboard.pagination.custom') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </div>
@endsection
