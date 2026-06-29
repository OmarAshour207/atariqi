@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Passengers') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Passengers') }}</h1>
                </div>
                <a href="{{ route('passengers.all-trips') }}" class="btn btn-info">{{ __('All Trips') }}</a>
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
                    <form method="GET" action="{{ route('passengers.index') }}" class="row align-items-end">
                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="name">{{ __('Name/Email') }}</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}" placeholder="{{ __('Search...') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="phone">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ request('phone') }}" placeholder="{{ __('Phone number...') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="university_id">{{ __('University') }}</label>
                                <select class="form-control" id="university_id" name="university_id">
                                    <option value="">{{ __('All') }}</option>
                                    @foreach($universities as $university)
                                        <option value="{{ $university->id }}" {{ request('university_id') == $university->id ? 'selected' : '' }}>
                                            {{ $university->{'name-ar'} ?? $university->{'name-en'} }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="stage_id">{{ __('Stage') }}</label>
                                <select class="form-control" id="stage_id" name="stage_id">
                                    <option value="">{{ __('All') }}</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}" {{ request('stage_id') == $stage->id ? 'selected' : '' }}>
                                            {{ $stage->{'name-ar'} ?? $stage->{'name-en'} }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-0">
                                <label for="rating_filter">{{ __('Rating') }}</label>
                                <select class="form-control" id="rating_filter" name="rating_filter">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="warning" {{ request('rating_filter') == 'warning' ? 'selected' : '' }}>{{ __('Warning (< 2)') }}</option>
                                    <option value="no_rating" {{ request('rating_filter') == 'no_rating' ? 'selected' : '' }}>{{ __('No Rating') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-0 d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                                <a href="{{ route('passengers.index') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('University') }}</th>
                            <th>{{ __('Stage') }}</th>
                            <th>{{ __('Rating') }}</th>
                            <th>{{ __('Trips') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($passengers as $index => $passenger)
                            <tr>
                                <td>{{ $passengers->firstItem() + $index }}</td>
                                <td>
                                    {{ $passenger->{'user-first-name'} }} {{ $passenger->{'user-last-name'} }}
                                    @if($passenger->approval == 2)
                                        <i class="fas fa-exclamation-triangle text-warning" title="{{ __('Modification request under review') }}"></i>
                                    @endif
                                </td>
                                <td>{{ $passenger->email }}</td>
                                <td>+{{ optional($passenger->callingKey)->{'call-key'} }}{{ $passenger->{'phone-no'} }}</td>
                                <td>{{ optional($passenger->university)->{'name-ar'} ?? optional($passenger->university)->{'name-en'} ?? '-' }}</td>
                                <td>{{ optional($passenger->stage)->{'name-ar'} ?? optional($passenger->stage)->{'name-en'} ?? '-' }}</td>
                                <td>
                                    @if($passenger->passengerRate)
                                        <span class="badge badge-{{ $passenger->passengerRate->rate < 2 ? 'danger' : ($passenger->passengerRate->rate < 3 ? 'warning' : 'success') }}">
                                            {{ number_format($passenger->passengerRate->rate, 2) }}
                                        </span>
                                        @if($passenger->passengerRate->rate < 2)
                                            <i class="fas fa-exclamation-circle text-danger" title="{{ __('Ban warning') }}"></i>
                                        @endif
                                    @else
                                        <span class="text-muted">{{ __('No rating') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $passenger->total_trips }}</span>
                                </td>
                                <td>
                                    @if($passenger->approval == 1)
                                        <span class="badge badge-success">{{ __('Approved') }}</span>
                                    @elseif($passenger->approval == 2)
                                        <span class="badge badge-warning">{{ __('Pending') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ __('Banned') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('passengers.show', $passenger->id) }}" class="btn btn-sm btn-info" title="{{ __('View') }}">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('passengers.trips', $passenger->id) }}" class="btn btn-sm btn-primary" title="{{ __('Trips') }}">
                                            <i class="fas fa-route"></i>
                                        </a>
                                        @if($passenger->passengerRate && $passenger->passengerRate->rate < 2)
                                            <button type="button" class="btn btn-sm btn-danger" title="{{ __('Ban') }}" onclick="showBanModal('{{ route('passengers.ban', $passenger->id) }}')">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">{{ __('No passengers found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $passengers->links('dashboard.pagination.custom') }}</div>
        </div>
    </div>

    <div class="modal fade" id="banModal" tabindex="-1" role="dialog" aria-labelledby="banModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="ban-form" action="#" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="banModalLabel">{{ __('Ban Passenger') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-warning mb-3">{{ __('Are you sure you want to ban this passenger?') }}</div>
                        <div class="form-group mb-0">
                            <label for="ban-reason">{{ __('Ban Reason') }} <span class="text-danger">*</span></label>
                            <textarea id="ban-reason" name="ban_reason" class="form-control" rows="4" placeholder="{{ __('Enter the reason for banning this passenger') }}" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="button" class="btn btn-danger" onclick="confirmBanPassenger()">{{ __('Confirm Ban') }}</button>
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
@endsection

@push('admin_scripts')
    <script>
        function showBanModal(banUrl) {
            const modal = $('#banModal');
            if (modal.parent()[0] !== document.body) {
                modal.appendTo('body');
            }
            document.getElementById('ban-form').action = banUrl;
            document.getElementById('ban-reason').value = '';
            modal.modal('show');
            document.getElementById('ban-reason').focus();
        }

        function confirmBanPassenger() {
            const reason = document.getElementById('ban-reason').value.trim();

            if (!reason) {
                alert('{{ __('Please enter a ban reason') }}');
                document.getElementById('ban-reason').focus();
                return;
            }

            document.getElementById('ban-form').submit();
        }
    </script>
@endpush
