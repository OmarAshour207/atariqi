@extends('dashboard.layouts.app')

@section('title', __('Driver Trips'))

@section('content')
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
                                    <th>{{ __('Driver') }}</th>
                                    <th>{{ __('Passenger') }}</th>
                                    <th>{{ __('Trip Type') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Revenue (SAR)') }}</th>
                                    <th>{{ __('Status') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($paginatedTrips as $index => $trip)
                                    <tr>
                                        <td>{{ $paginatedTrips->firstItem() + $index }}</td>
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
                                        <td>{{ number_format($trip->revenue, 2) }}</td>
                                        <td>
                                            @if($trip->action == 5)
                                                <span class="badge badge-success">{{ __('Completed') }}</span>
                                            @elseif($trip->action == 1)
                                                <span class="badge badge-warning">{{ __('Pending') }}</span>
                                            @elseif($trip->action == 2)
                                                <span class="badge badge-info">{{ __('Accepted') }}</span>
                                            @else
                                                <span class="badge badge-secondary">{{ __('Other') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($trip->driver && $trip->revenue > 0)
                                                <button type="button" class="btn btn-sm btn-warning" data-toggle="modal" data-target="#reminderModal{{ $trip->id }}">
                                                    {{ __('Send Reminder') }}
                                                </button>
                                            @endif
                                        </td>
                                    </tr>

                                    <!-- Payment Reminder Modal -->
                                    @if($trip->driver && $trip->revenue > 0)
                                    <div class="modal fade" id="reminderModal{{ $trip->id }}" tabindex="-1" role="dialog" aria-labelledby="reminderModalLabel{{ $trip->id }}" aria-hidden="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="reminderModalLabel{{ $trip->id }}">{{ __('Send Payment Reminder') }}</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form method="POST" action="{{ route('drivers.sendPaymentReminder', $trip->driver->id) }}">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="form-group">
                                                            <label for="amount{{ $trip->id }}">{{ __('Amount (SAR)') }}</label>
                                                            <input type="number" step="0.01" class="form-control" id="amount{{ $trip->id }}" name="amount" value="{{ $trip->revenue }}" required>
                                                        </div>
                                                        <p>{{ __('Send payment reminder to') }}: {{ $trip->driver->{'user-first-name'} . ' ' . $trip->driver->{'user-last-name'} }}</p>
                                                        <p>{{ __('Trip Type') }}: {{ ucfirst($trip->trip_type) }}</p>
                                                        <p>{{ __('Trip Date') }}: {{ \Carbon\Carbon::parse($trip->{'date-of-add'})->format('Y-m-d H:i') }}</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                                                        <button type="submit" class="btn btn-primary">{{ __('Send Reminder') }}</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
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
@endsection
