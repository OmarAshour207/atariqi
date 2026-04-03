@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Driver Packages') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Driver Packages') }}</h1>
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
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Full Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Current Package') }}</th>
                            <th>{{ __('Package Interval') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($drivers as $index => $driver)
                            <tr>
                                <td>{{ $drivers->firstItem() + $index }}</td>
                                <td>{{ $driver->fullName }}</td>
                                <td>{{ $driver->email }}</td>
                                <td>{{ optional($driver->activePackage)->name_en ?? __('No active package') }}</td>
                                <td>{{ optional($driver->packages()->active()->first())->interval ?? __('unknown') }}</td>
                                <td>{{ optional($driver->packages()->active()->first())->statusText ?? __('None') }}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary" type="button" data-toggle="collapse" data-target="#plan-list-{{ $driver->id }}" aria-expanded="false" aria-controls="plan-list-{{ $driver->id }}">
                                        {{ __('Show Plans') }}
                                    </button>
                                    <button class="btn btn-sm btn-success" type="button" onclick="document.getElementById('assign-{{ $driver->id }}').style.display='block'">{{ __('Assign Package') }}</button>
                                </td>
                            </tr>
                            <tr class="collapse" id="plan-list-{{ $driver->id }}">
                                <td colspan="7">
                                    <div>
                                        <h6>{{ __('Available Packages for') }} {{ $driver->fullName }}</h6>
                                        <div class="row">
                                            @foreach($packages as $pkg)
                                                <div class="col-md-3 mb-2">
                                                    <div class="card border-secondary h-100">
                                                        <div class="card-body p-2">
                                                            <h6 class="card-title">{{ $pkg->name_en }}</h6>
                                                            <p class="card-text mb-1">{{ __('Monthly') }}: {{ $pkg->price_monthly }} SAR</p>
                                                            <p class="card-text mb-1">{{ __('Annual') }}: {{ $pkg->price_annual }} SAR</p>
                                                            <p>{{ $pkg->statusText }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <div id="assign-{{ $driver->id }}" style="display:none; margin-top: 12px;">
                                        <form action="{{ route('drivers.assignPackage', $driver->id) }}" method="post">
                                            @csrf
                                            <div class="form-row align-items-end">
                                                <div class="col-md-4">
                                                    <label for="package_id_{{ $driver->id }}" class="form-label">{{ __('Choose Package') }}</label>
                                                    <select name="package_id" id="package_id_{{ $driver->id }}" class="form-control">
                                                        @foreach($packages as $pkg)
                                                            <option value="{{ $pkg->id }}">{{ $pkg->name_en }} ({{ $pkg->statusText }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <label for="interval_{{ $driver->id }}" class="form-label">{{ __('Interval') }}</label>
                                                    <select name="interval" id="interval_{{ $driver->id }}" class="form-control">
                                                        <option value="monthly">{{ __('Monthly') }}</option>
                                                        <option value="yearly">{{ __('Yearly') }}</option>
                                                    </select>
                                                </div>
                                                <div class="col-md-3">
                                                    <button type="submit" class="btn btn-sm btn-primary">{{ __('Apply') }}</button>
                                                    <button type="button" class="btn btn-sm btn-secondary" onclick="document.getElementById('assign-{{ $driver->id }}').style.display='none'">{{ __('Cancel') }}</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $drivers->links('dashboard.pagination.custom') }}</div>
        </div>
    </div>
@endsection
