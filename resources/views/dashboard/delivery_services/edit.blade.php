@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <h1>{{ __('Edit Service') }}</h1>
            <form method="POST" action="{{ route('delivery-services.update', $service) }}">
                @csrf @method('PUT')
                <div class="form-group"><label>{{ __('Service Name') }}</label><input name="service" class="form-control" value="{{ old('service', $service->service) }}" required></div>
                <div class="form-group"><label>{{ __('Name (AR)') }}</label><input name="service-ar" class="form-control" value="{{ old('service-ar', $service->{'service-ar'}) }}" required></div>
                <div class="form-group"><label>{{ __('Name (EN)') }}</label><input name="service-eng" class="form-control" value="{{ old('service-eng', $service->{'service-eng'}) }}" required></div>
                <div class="form-group"><label>{{ __('Price') }}</label><input name="cost" type="number" step="0.01" class="form-control" value="{{ old('cost', $service->cost) }}" required></div>
                <div class="form-group"><label>{{ __('Trip Direction') }}</label><input name="road-way" class="form-control" value="{{ old('road-way', $service->{'road-way'}) }}"></div>
                @adminCan('update')
                <button class="btn btn-primary">{{ __('Save') }}</button>
                @endadminCan
                <a href="{{ route('delivery-services.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
