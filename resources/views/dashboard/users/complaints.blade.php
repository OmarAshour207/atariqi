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

            @if($stats['total_complaints_rates'] >= 5)
                <button type="button"
                        class="btn btn-danger"
                        onclick="showBanModal()">
                    <i class="fa fa-ban"></i> {{ __('Ban Passenger') }}
                </button>
            @endif

        </div>
    </div>

    <div class="modal fade" id="banModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="ban-form"
                  action="{{ route('passengers.ban', $stats['passenger']->id) }}"
                  method="POST">
                @csrf

                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ __('Ban Passenger') }}
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="alert alert-warning">
                        {{ __('Are you sure you want to ban this passenger?') }}
                    </div>

                    <div class="form-group">
                        <label>
                            {{ __('Ban Reason') }}
                            <span class="text-danger">*</span>
                        </label>

                        <textarea id="ban-reason"
                                  name="ban_reason"
                                  class="form-control"
                                  rows="4"
                                  required></textarea>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        {{ __('Cancel') }}
                    </button>

                    <button type="button"
                            class="btn btn-danger"
                            onclick="confirmBanPassenger()">
                        {{ __('Confirm Ban') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
    <style>
        .modal-backdrop {
            z-index: 9998 !important;
            pointer-events: none !important;
            background-color: transparent !important;
        }
        .modal-backdrop.show {
            opacity: 0 !important;
            z-index: 9998 !important;
            pointer-events: none !important;
        }
        .modal.show {
            z-index: 9999 !important;
            pointer-events: auto !important;
        }
        .modal.show .modal-dialog {
            pointer-events: auto !important;
        }
    </style>
<script>
    function showBanModal() {
    $('#banModal').modal('show');

    $('#ban-reason').val('');
}

function confirmBanPassenger() {
    const reason = $('#ban-reason').val().trim();

    if (!reason) {
        alert('{{ __("Please enter a ban reason.") }}');
        return;
    }

    $('#ban-form').submit();
}
</script>
@endsection
