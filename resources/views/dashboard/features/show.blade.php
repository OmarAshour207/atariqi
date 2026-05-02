@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('features.index') }}">{{ __('Features') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('View Feature') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('View Feature') }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h4>{{ __('Name (EN)') }}</h4>
                            <p>{{ $feature->name_en }}</p>
                        </div>
                        <div class="col-md-6">
                            <h4>{{ __('Name (AR)') }}</h4>
                            <p>{{ $feature->name_ar }}</p>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <h4>{{ __('Service') }}</h4>
                            <p>{{ optional($feature->service)->service ?? __('None') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h4>{{ __('Created At') }}</h4>
                            <p>{{ optional($feature->created_at)->format('Y-m-d H:i') ?? __('N/A') }}</p>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <h4>{{ __('Description (EN)') }}</h4>
                            <p>{{ $feature->description_en ?? __('-') }}</p>
                        </div>
                        <div class="col-md-6">
                            <h4>{{ __('Description (AR)') }}</h4>
                            <p>{{ $feature->description_ar ?? __('-') }}</p>
                        </div>
                    </div>
                    <div class="text-right mt-4">
                        <a href="{{ route('features.edit', $feature->id) }}" class="btn btn-primary">{{ __('Edit') }}</a>
                        <a href="{{ route('features.index') }}" class="btn btn-secondary">{{ __('Back to list') }}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
