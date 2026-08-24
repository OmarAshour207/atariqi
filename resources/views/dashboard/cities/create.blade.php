@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <h1>{{ __('Add City') }}</h1>
            <form method="POST" action="{{ route('cities.store') }}" id="cityForm">
                @csrf
                <div class="form-group"><label>{{ __('City') }} (AR)</label><input name="city-ar" class="form-control" required></div>
                <div class="form-group"><label>{{ __('City') }} (EN)</label><input name="city-en" class="form-control" required></div>
                <hr>
                <h5>{{ __('Neighborhoods') }}</h5>
                <div id="neighborhoods">
                    <div class="form-row mb-2 neighborhood-row">
                        <div class="col-md-5"><input name="neighborhoods[0][ar]" class="form-control" placeholder="AR"></div>
                        <div class="col-md-5"><input name="neighborhoods[0][eng]" class="form-control" placeholder="EN"></div>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-secondary" onclick="addNeighborhood()">{{ __('Add Neighborhood') }}</button>
                <hr>
                @adminCan('update')
                <button class="btn btn-primary">{{ __('Save') }}</button>
                @endadminCan
                <a href="{{ route('cities.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@push('admin_scripts')
<script>
let neighborhoodIndex = 1;
function addNeighborhood() {
    const html = `<div class="form-row mb-2 neighborhood-row">
        <div class="col-md-5"><input name="neighborhoods[${neighborhoodIndex}][ar]" class="form-control" placeholder="AR"></div>
        <div class="col-md-5"><input name="neighborhoods[${neighborhoodIndex}][eng]" class="form-control" placeholder="EN"></div>
    </div>`;
    document.getElementById('neighborhoods').insertAdjacentHTML('beforeend', html);
    neighborhoodIndex++;
}
</script>
@endpush
@endsection
