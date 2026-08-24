@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <h1>{{ __('University Services') }} - {{ $university->{'name-ar'} }}</h1>
            <table class="table table-striped">
                <thead><tr><th>{{ __('University Name') }}</th><th>{{ __('Service Name') }}</th></tr></thead>
                <tbody>
                @forelse($university->uniDrivingServices as $link)
                    <tr>
                        <td>{{ $university->{'name-ar'} }}</td>
                        <td>{{ $link->service?->{'service-ar'} ?? $link->service?->service }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2">{{ __('No services found.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
            <hr>
            <form method="POST" action="{{ route('universities.services.store', $university) }}">
                @csrf
                <label>{{ __('Add Service') }}</label>
                @foreach($services as $service)
                    <div class="form-check">
                        <input type="checkbox" name="service_ids[]" value="{{ $service->id }}" id="svc{{ $service->id }}" {{ in_array($service->id, $linkedIds) ? 'checked' : '' }}>
                        <label for="svc{{ $service->id }}">{{ $service->{'service-ar'} ?? $service->service }}</label>
                    </div>
                @endforeach
                @adminCan('update')
                <button class="btn btn-primary mt-3">{{ __('Save') }}</button>
                @endadminCan
                <a href="{{ route('universities.index') }}" class="btn btn-secondary mt-3">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
