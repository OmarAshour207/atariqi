@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__container py-4">
            <div class="row justify-content-center">
                <div class="col-lg-8 col-xl-7">
                    <div class="card border-0 shadow-sm overflow-hidden">
                        <div class="card-body text-center p-5" style="background: linear-gradient(180deg, #f8fffe 0%, #ffffff 45%);">
                            <img
                                src="{{ asset('dashboard/images/logos/main-logo.png') }}"
                                alt="{{ __('Atariqi') }}"
                                class="mb-4"
                                style="max-height: 72px; width: auto;"
                            >

                            <h1 class="h3 font-weight-bold mb-2" style="color: #2F3A40;">
                                {{ __('Welcome, :name', ['name' => $admin->name]) }}
                            </h1>

                            <p class="text-muted mb-1">{{ __('Atariqi Employee Dashboard') }}</p>
                            <p class="text-muted mb-4">{{ __('Use the sidebar menu to access the sections assigned to you.') }}</p>

                            <div class="d-inline-flex align-items-center px-3 py-2 rounded bg-light text-muted small">
                                <i class="material-icons mr-2" style="font-size: 18px;">mail_outline</i>
                                {{ $admin->email }}
                            </div>
                        </div>

                        <div class="card-footer bg-white border-top-0 px-5 pb-5 pt-0">
                            <div class="row text-center">
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="p-3 rounded bg-light h-100">
                                        <i class="material-icons text-primary mb-2">dashboard</i>
                                        <div class="font-weight-medium">{{ __('Dashboard') }}</div>
                                        <small class="text-muted">{{ __('Your starting point') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4 mb-3 mb-md-0">
                                    <div class="p-3 rounded bg-light h-100">
                                        <i class="material-icons text-primary mb-2">menu</i>
                                        <div class="font-weight-medium">{{ __('Assigned Pages') }}</div>
                                        <small class="text-muted">{{ __('Available from the sidebar') }}</small>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="p-3 rounded bg-light h-100">
                                        <i class="material-icons text-primary mb-2">account_circle</i>
                                        <div class="font-weight-medium">{{ __('Profile') }}</div>
                                        <small class="text-muted">
                                            <a href="{{ route('profile.edit') }}">{{ __('Edit Profile') }}</a>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
