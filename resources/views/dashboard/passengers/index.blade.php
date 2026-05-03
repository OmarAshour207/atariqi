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
                    <form method="GET" action="{{ route('passengers.index') }}" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">{{ __('Name/Email') }}</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}" placeholder="{{ __('Search...') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="phone">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ request('phone') }}" placeholder="{{ __('Phone number...') }}">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
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
                            <div class="form-group">
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
                            <div class="form-group">
                                <label for="rating_filter">{{ __('Rating') }}</label>
                                <select class="form-control" id="rating_filter" name="rating_filter">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="warning" {{ request('rating_filter') == 'warning' ? 'selected' : '' }}>{{ __('Warning (< 2)') }}</option>
                                    <option value="no_rating" {{ request('rating_filter') == 'no_rating' ? 'selected' : '' }}>{{ __('No Rating') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" class="btn btn-primary btn-block">{{ __('Filter') }}</button>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <a href="{{ route('passengers.index') }}" class="btn btn-secondary">{{ __('Clear') }}</a>
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
                                            <form action="{{ route('passengers.ban', $passenger->id) }}" method="post" class="d-inline-block" onsubmit="return confirm('{{ __('Are you sure?') }}');">
                                                @csrf
                                                @method('post')
                                                <button type="submit" class="btn btn-sm btn-danger delete" title="{{ __('Ban') }}">
                                                    <i class="fas fa-ban"></i>
                                                </button>
                                            </form>
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
@endsection
