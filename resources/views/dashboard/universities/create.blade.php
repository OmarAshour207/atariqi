@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <h1>{{ __('Add University') }}</h1>
            <form method="POST" action="{{ route('universities.store') }}">
                @csrf
                <div class="form-group"><label>{{ __('University Name') }} (AR)</label><input name="name-ar" class="form-control" required></div>
                <div class="form-group"><label>{{ __('University Name') }} (EN)</label><input name="name-eng" class="form-control" required></div>
                <div class="form-group">
                    <label>{{ __('City') }}</label>
                    <select name="city_id" class="form-control" required>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->{'city-ar'} }} / {{ $city->{'city-en'} }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group"><label>{{ __('Location') }}</label><input name="location" class="form-control"></div>
                <div class="row">
                    <div class="col-md-6 form-group"><label>{{ __('Latitude') }}</label><input name="lat" class="form-control"></div>
                    <div class="col-md-6 form-group"><label>{{ __('Longitude') }}</label><input name="lng" class="form-control"></div>
                </div>
                <div class="form-group">
                    <label>{{ __('Services') }}</label>
                    @foreach($services as $service)
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="service_ids[]" value="{{ $service->id }}" id="service{{ $service->id }}">
                            <label class="form-check-label" for="service{{ $service->id }}">{{ $service->{'service-ar'} ?? $service->service }}</label>
                        </div>
                    @endforeach
                </div>
                @adminCan('add-delete')
                <button class="btn btn-primary">{{ __('Save') }}</button>
                @endadminCan
                <a href="{{ route('universities.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
