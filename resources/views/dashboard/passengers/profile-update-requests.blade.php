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
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Profile Update Requests') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Profile Update Requests') }}</h1>
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

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Pending Profile Updates') }}</h4>
                    <p class="card-subtitle">{{ __('Passengers who have requested profile changes and are waiting for approval') }}</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Current Name') }}</th>
                            <th>{{ __('New Name') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('University') }}</th>
                            <th>{{ __('Request Date') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($passengers as $index => $passenger)
                            <tr>
                                <td>{{ $passengers->firstItem() + $index }}</td>
                                <td>
                                    {{ $passenger->{'user-first-name'} }} {{ $passenger->{'user-last-name'} }}
                                </td>
                                <td>
                                    @if($passenger->newUserInfo)
                                        <strong>{{ $passenger->newUserInfo->{'user-first-name'} }} {{ $passenger->newUserInfo->{'user-last-name'} }}</strong>
                                        <i class="fas fa-edit text-info" title="{{ __('Updated') }}"></i>
                                    @else
                                        <span class="text-muted">{{ __('No changes') }}</span>
                                    @endif
                                </td>
                                <td>
                                    +{{ optional($passenger->callingKey)->{'call-key'} }}{{ $passenger->{'phone-no'} }}
                                    @if($passenger->newUserInfo && $passenger->newUserInfo->{'phone-no'} !== $passenger->{'phone-no'})
                                        <br><small class="text-info">
                                            <i class="fas fa-arrow-right"></i>
                                            +{{ optional($passenger->newUserInfo->callingKey)->{'call-key'} }}{{ $passenger->newUserInfo->{'phone-no'} }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    {{ $passenger->email }}
                                    @if($passenger->newUserInfo && $passenger->newUserInfo->email !== $passenger->email)
                                        <br><small class="text-info">
                                            <i class="fas fa-arrow-right"></i>
                                            {{ $passenger->newUserInfo->email }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    {{ optional($passenger->university)->{'name-ar'} ?? optional($passenger->university)->{'name-en'} ?? '-' }}
                                    @if($passenger->newUserInfo && $passenger->newUserInfo->{'university-id'} !== $passenger->{'university-id'})
                                        <br><small class="text-info">
                                            <i class="fas fa-arrow-right"></i>
                                            {{ optional($passenger->newUserInfo->university)->{'name-ar'} ?? optional($passenger->newUserInfo->university)->{'name-en'} ?? '-' }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($passenger->newUserInfo)
                                        {{ optional($passenger->newUserInfo->{'date-of-add'})->format('Y-m-d H:i') ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('passengers.show', $passenger->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{ __('Review Details') }}
                                        </a>
                                        <form action="{{ route('passengers.approve-profile-update', $passenger->id) }}" method="post" class="d-inline-block ml-1">
                                            @csrf
                                            @method('post')
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __('Are you sure you want to approve this profile update?') }}');">
                                                <i class="fas fa-check"></i> {{ __('Approve') }}
                                            </button>
                                        </form>
                                        <form action="{{ route('passengers.reject-profile-update', $passenger->id) }}" method="post" class="d-inline-block ml-1">
                                            @csrf
                                            @method('post')
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('{{ __('Are you sure you want to reject this profile update?') }}');">
                                                <i class="fas fa-times"></i> {{ __('Reject') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <h5>{{ __('No Pending Requests') }}</h5>
                                        <p class="text-muted">{{ __('All profile update requests have been processed.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($passengers->hasPages())
                    <div class="card-footer">
                        {{ $passengers->links('dashboard.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
