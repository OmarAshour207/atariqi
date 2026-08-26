@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <h1>{{ __('Delivery Services') }}</h1>
        <div class="card table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>{{ __('Service Name') }}</th>
                    <th>{{ __('Price') }}</th>
                    <th>{{ __('Trip Direction') }}</th>
                    <th>{{ __('Name (AR)') }}</th>
                    <th>{{ __('Name (EN)') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($services as $service)
                    <tr>
                        <td>{{ $service->service }}</td>
                        <td>{{ $service->cost }}</td>
                        <td>{{ $service->{'road-way'} }}</td>
                        <td>{{ $service->{'service-ar'} }}</td>
                        <td>{{ $service->{'service-eng'} }}</td>
                        <td>
                            @adminCan('update')
                            <a href="{{ route('delivery-services.edit', $service) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                            @endadminCan
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center">{{ __('No services found.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="card-footer">{{ $services->links('dashboard.pagination.custom') }}</div>
        </div>
    </div>
</div>
@endsection
