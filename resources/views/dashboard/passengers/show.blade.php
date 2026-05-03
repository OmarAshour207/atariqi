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
                            <li class="breadcrumb-item active" aria-current="page">{{ __('View Passenger') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Passenger Details') }}</h1>
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

            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h3 class="card-title">{{ __('Personal Information') }}</h3>
                            @if($passenger->newUserInfo)
                                <span class="badge badge-warning">{{ __('Update Requested') }}</span>
                            @endif
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ __('First Name') }}</h6>
                                    <p>
                                        {{ $passenger->{'user-first-name'} }}
                                        @if($passenger->newUserInfo && $passenger->newUserInfo->{'user-first-name'} !== $passenger->{'user-first-name'})
                                            <br><small class="text-info">
                                                <i class="fas fa-arrow-right"></i> {{ $passenger->newUserInfo->{'user-first-name'} }}
                                            </small>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ __('Last Name') }}</h6>
                                    <p>
                                        {{ $passenger->{'user-last-name'} }}
                                        @if($passenger->newUserInfo && $passenger->newUserInfo->{'user-last-name'} !== $passenger->{'user-last-name'})
                                            <br><small class="text-info">
                                                <i class="fas fa-arrow-right"></i> {{ $passenger->newUserInfo->{'user-last-name'} }}
                                            </small>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ __('Email') }}</h6>
                                    <p>
                                        {{ $passenger->email }}
                                        @if($passenger->newUserInfo && $passenger->newUserInfo->email !== $passenger->email)
                                            <br><small class="text-info">
                                                <i class="fas fa-arrow-right"></i> {{ $passenger->newUserInfo->email }}
                                            </small>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ __('Phone') }}</h6>
                                    <p>
                                        +{{ optional($passenger->callingKey)->{'call-key'} }}{{ $passenger->{'phone-no'} }}
                                        @if($passenger->newUserInfo && $passenger->newUserInfo->{'phone-no'} !== $passenger->{'phone-no'})
                                            <br><small class="text-info">
                                                <i class="fas fa-arrow-right"></i>
                                                +{{ optional($passenger->newUserInfo->callingKey)->{'call-key'} }}{{ $passenger->newUserInfo->{'phone-no'} }}
                                            </small>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ __('University') }}</h6>
                                    <p>
                                        {{ optional($passenger->university)->{'name-ar'} ?? optional($passenger->university)->{'name-en'} ?? '-' }}
                                        @if($passenger->newUserInfo && $passenger->newUserInfo->{'university-id'} !== $passenger->{'university-id'})
                                            <br><small class="text-info">
                                                <i class="fas fa-arrow-right"></i>
                                                {{ optional($passenger->newUserInfo->university)->{'name-ar'} ?? optional($passenger->newUserInfo->university)->{'name-en'} ?? '-' }}
                                            </small>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ __('Stage') }}</h6>
                                    <p>
                                        {{ optional($passenger->stage)->{'name-ar'} ?? optional($passenger->stage)->{'name-en'} ?? '-' }}
                                        @if($passenger->newUserInfo && $passenger->newUserInfo->{'user-stage-id'} !== $passenger->{'user-stage-id'})
                                            <br><small class="text-info">
                                                <i class="fas fa-arrow-right"></i>
                                                {{ optional($passenger->newUserInfo->stage)->{'name-ar'} ?? optional($passenger->newUserInfo->stage)->{'name-en'} ?? '-' }}
                                            </small>
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ __('ID') }}</h6>
                                    <p>{{ $passenger->id }}</p>
                                </div>
                                <div class="col-md-6">
                                    <h6 class="text-muted">{{ __('Registration Date') }}</h6>
                                    <p>{{ optional($passenger->{'date-of-add'})->format('Y-m-d H:i') ?? '-' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Rating & Status') }}</h3>
                        </div>
                        <div class="card-body">
                            @if($passenger->passengerRate)
                                <div class="alert alert-info">
                                    <h6>{{ __('Overall Rating') }}</h6>
                                    <h3 class="text-{{ $passenger->passengerRate->rate < 2 ? 'danger' : ($passenger->passengerRate->rate < 3 ? 'warning' : 'success') }}">
                                        {{ number_format($passenger->passengerRate->rate, 2) }}
                                    </h3>
                                    @if($passenger->passengerRate->rate < 2)
                                        <p class="text-danger"><i class="fas fa-exclamation-circle"></i> {{ __('Warning: Possibility of banning') }}</p>
                                    @endif
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    {{ __('No rating yet') }}
                                </div>
                            @endif

                            <div class="alert alert-secondary">
                                <h6>{{ __('Approval Status') }}</h6>
                                @if($passenger->approval == 1)
                                    <span class="badge badge-success badge-lg">{{ __('Approved') }}</span>
                                @elseif($passenger->approval == 2)
                                    <span class="badge badge-warning badge-lg"><i class="fas fa-exclamation-triangle"></i> {{ __('Pending Review') }}</span>
                                @else
                                    <span class="badge badge-danger badge-lg">{{ __('Banned') }}</span>
                                @endif
                            </div>

                            <div class="alert alert-info">
                                <h6>{{ __('Total Trips') }}</h6>
                                <h3>{{ $passenger->total_trips }}</h3>
                                <small>Immediate: {{ $passenger->immediate_trips_count }}, Daily: {{ $passenger->daily_trips_count }}, Weekly: {{ $passenger->weekly_trips_count }}</small>
                            </div>

                            <form action="{{ route('passengers.updateApproval', $passenger->id) }}" method="post">
                                @csrf
                                @method('post')
                                <div class="form-group">
                                    <label for="approval">{{ __('Update Status') }}</label>
                                    <select name="approval" id="approval" class="form-control">
                                        <option value="1" {{ $passenger->approval == 1 ? 'selected' : '' }}>{{ __('Approve') }}</option>
                                        <option value="2" {{ $passenger->approval == 2 ? 'selected' : '' }}>{{ __('Pending Review') }}</option>
                                        <option value="3" {{ $passenger->approval == 3 ? 'selected' : '' }}>{{ __('Ban') }}</option>
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">{{ __('Update Status') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">{{ __('Actions') }}</h3>
                        </div>
                        <div class="card-body">
                            @if($passenger->newUserInfo && $passenger->approval == 2)
                                <div class="alert alert-info mb-3">
                                    <h6><i class="fas fa-user-edit"></i> {{ __('Profile Update Request') }}</h6>
                                    <p>{{ __('This passenger has requested profile changes that need approval.') }}</p>
                                    <div class="btn-group">
                                        <form action="{{ route('passengers.approve-profile-update', $passenger->id) }}" method="post" class="d-inline-block">
                                            @csrf
                                            @method('post')
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __('Are you sure you want to approve this profile update?') }}');">
                                                <i class="fas fa-check"></i> {{ __('Approve Update') }}
                                            </button>
                                        </form>
                                        <form action="{{ route('passengers.reject-profile-update', $passenger->id) }}" method="post" class="d-inline-block ml-1">
                                            @csrf
                                            @method('post')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to reject this profile update?') }}');">
                                                <i class="fas fa-times"></i> {{ __('Reject Update') }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endif

                            <a href="{{ route('passengers.trips', $passenger->id) }}" class="btn btn-primary">
                                <i class="fas fa-route"></i> {{ __('View Trips') }}
                            </a>
                            <a href="{{ route('passengers.all-trips') }}" class="btn btn-info">
                                <i class="fas fa-list"></i> {{ __('All Passenger Trips') }}
                            </a>
                            <a href="{{ route('passengers.profile-update-requests') }}" class="btn btn-secondary">
                                <i class="fas fa-user-edit"></i> {{ __('Profile Update Requests') }}
                            </a>
                            <button type="button" class="btn btn-warning" onclick="alert('{{ __('Complaints feature to be implemented') }}')">
                                <i class="fas fa-exclamation-circle"></i> {{ __('View Complaints') }}
                            </button>
                            @if($passenger->passengerRate && $passenger->passengerRate->rate < 2)
                                <form action="{{ route('passengers.ban', $passenger->id) }}" method="post" class="d-inline-block" onsubmit="return confirm('{{ __('Are you sure you want to ban this passenger?') }}');">
                                    @csrf
                                    @method('post')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fas fa-ban"></i> {{ __('Ban Passenger') }}
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('passengers.index') }}" class="btn btn-secondary">
                                {{ __('Back to List') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
