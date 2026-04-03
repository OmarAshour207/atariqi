@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('drivers.packages') }}">{{ __('Driver Packages') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $driver->fullName }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Plans for') }} {{ $driver->fullName }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            <div class="card p-3">
                <h4>{{ __('Current Package') }}: {{ optional($driver->activePackage)->name_en ?? __('None') }}</h4>
                <p>{{ __('Current status') }}: {{ optional($driver->packages()->active()->first())->statusText ?? __('No active subscription') }}</p>
                <p>{{ __('Ends at') }}: {{ optional($driver->packages()->active()->first())->end_date?->format('Y-m-d') ?? __('-') }}</p>
            </div>

            <div class="row mt-3">
                @foreach($packages as $pkg)
                    <div class="col-md-4">
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5>{{ $pkg->name_en }}</h5>
                                <p>{{ __('Monthly') }}: {{ $pkg->price_monthly }} SAR</p>
                                <p>{{ __('Annual') }}: {{ $pkg->price_annual }} SAR</p>
                                <p>{{ $pkg->statusText }}</p>
                                <form method="post" action="{{ route('drivers.assignPackage', $driver->id) }}">
                                    @csrf
                                    <input type="hidden" name="package_id" value="{{ $pkg->id }}" />
                                    <select name="interval" class="form-control mb-2">
                                        <option value="monthly">{{ __('Monthly') }}</option>
                                        <option value="yearly">{{ __('Yearly') }}</option>
                                    </select>
                                    <button class="btn btn-sm btn-success" type="submit">{{ __('Switch to this plan') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <a href="{{ route('drivers.packages') }}" class="btn btn-secondary">{{ __('Back to driver packages') }}</a>
        </div>
    </div>
@endsection
