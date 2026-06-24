@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Packages') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Packages') }}</h1>
                </div>
                <a href="{{ route('packages.create') }}" class="btn btn-success">{{ __('Create Package') }}</a>
            </div>
        </div>

        <div class="container-fluid page__container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Summary Cards -->
            <div class="row mb-3">
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ $packages->total() }}</h5>
                            <p class="card-text">{{ __('Total Packages') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ \App\Models\Package::where('status', \App\Models\Package::FREE)->count() }}</h5>
                            <p class="card-text">{{ __('Free Packages') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ \App\Models\Package::where('status', \App\Models\Package::NEW)->count() }}</h5>
                            <p class="card-text">{{ __('New Packages') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ \App\Models\Package::has('features')->count() }}</h5>
                            <p class="card-text">{{ __('Packages with Features') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ \App\Models\Feature::count() }}</h5>
                            <p class="card-text">{{ __('Total Features') }}</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="card text-center">
                        <div class="card-body">
                            <h5 class="card-title">{{ \App\Models\Package::withCount('features')->get()->sum('features_count') }}</h5>
                            <p class="card-text">{{ __('Total Feature Assignments') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('packages.index') }}">
                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="name">{{ __('Package Name') }}</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}" placeholder="{{ __('Search by name...') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status">{{ __('Status') }}</label>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">{{ __('All Status') }}</option>
                                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>{{ __('Free') }}</option>
                                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>{{ __('Coming Soon') }}</option>
                                        <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>{{ __('New') }}</option>
                                        <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>{{ __('Discount') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="monthly_price">{{ __('Monthly Price') }}</label>
                                    <input type="number" step="0.01" class="form-control" id="monthly_price" name="monthly_price" value="{{ request('monthly_price') }}" placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="annual_price">{{ __('Annual Price') }}</label>
                                    <input type="number" step="0.01" class="form-control" id="annual_price" name="annual_price" value="{{ request('annual_price') }}" placeholder="0.00">
                                </div>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <label>&nbsp;</label>
                                    <div class="d-flex">
                                        <button type="submit" class="btn btn-primary mr-2">{{ __('Filter') }}</button>
                                        <a href="{{ route('packages.index') }}" class="btn btn-secondary">{{ __('Clear Filters') }}</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name (EN)') }}</th>
                            <th>{{ __('Name (AR)') }}</th>
                            <th>{{ __('Monthly Price') }}</th>
                            <th>{{ __('Annual Price') }}</th>
                            <th>{{ __('Features Count') }}</th>
                            <th>{{ __('Features') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($packages as $index => $package)
                            <tr>
                                <td>{{ $packages->firstItem() + $index }}</td>
                                <td>{{ $package->name_en }}</td>
                                <td>{{ $package->name_ar }}</td>
                                <td>{{ number_format($package->price_monthly, 2) }} {{ __('SAR') }}</td>
                                <td>{{ number_format($package->price_annual, 2) }} {{ __('SAR') }}</td>
                                <td>
                                    <span class="badge badge-secondary">{{ $package->features->count() }}</span>
                                </td>
                                <td>
                                    @if($package->features->count() > 0)
                                        <button type="button" class="btn btn-sm btn-info" data-toggle="modal" data-target="#featuresModal{{ $package->id }}">
                                            {{ __('View Features') }} ({{ $package->features->count() }})
                                        </button>
                                    @else
                                        <span class="text-muted">{{ __('No features') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $package->status == 0 ? 'success' : ($package->status == 1 ? 'warning' : ($package->status == 2 ? 'info' : 'primary')) }}">
                                        {{ $package->statusText }}
                                    </span>
                                </td>
                                <td>{{ optional($package->created_at)->format('Y-m-d') ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('packages.edit', $package->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-edit"></i> {{ __('Edit') }}
                                    </a>
                                    <form action="{{ route('packages.destroy', $package->id) }}" method="post" class="d-inline-block">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-danger delete">
                                            <i class="fa fa-trash"></i> {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">{{ __('No packages found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $packages->links('dashboard.pagination.custom') }}</div>
        </div>
    </div>

    @foreach($packages as $package)
        @if($package->features->count() > 0)
            <div class="modal fade package-features-modal" id="featuresModal{{ $package->id }}" tabindex="-1" role="dialog" aria-labelledby="featuresModalLabel{{ $package->id }}" aria-hidden="true" data-backdrop="true" data-keyboard="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="featuresModalLabel{{ $package->id }}">
                                {{ __('Features for') }}:
                                {{ app()->getLocale() === 'ar' ? ($package->name_ar ?: $package->name_en) : ($package->name_en ?: $package->name_ar) }}
                                <span class="badge badge-info ml-2">{{ $package->features->count() }}</span>
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @php
                                $featuresByService = $package->features->groupBy(function ($feature) {
                                    return $feature->service?->{'service-ar'}
                                        ?? $feature->service?->{'service-eng'}
                                        ?? $feature->service?->service
                                        ?? __('General');
                                });
                            @endphp

                            @foreach($featuresByService as $serviceName => $serviceFeatures)
                                <div class="package-features-group mb-4">
                                    <h6 class="package-features-service-title">
                                        <i class="material-icons icon-16pt align-middle">local_offer</i>
                                        {{ $serviceName }}
                                    </h6>
                                    <div class="list-group">
                                        @foreach($serviceFeatures as $feature)
                                            <div class="list-group-item package-feature-item">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div class="flex-grow-1">
                                                        <div class="font-weight-bold mb-1">
                                                            {{ app()->getLocale() === 'ar' ? ($feature->name_ar ?: $feature->name_en) : ($feature->name_en ?: $feature->name_ar) }}
                                                        </div>
                                                        @if($feature->name_ar && $feature->name_en && $feature->name_ar !== $feature->name_en)
                                                            <div class="text-muted small mb-1">
                                                                {{ app()->getLocale() === 'ar' ? $feature->name_en : $feature->name_ar }}
                                                            </div>
                                                        @endif
                                                        @if($feature->description_ar || $feature->description_en)
                                                            <p class="mb-0 text-muted small">
                                                                {{ app()->getLocale() === 'ar' ? ($feature->description_ar ?: $feature->description_en) : ($feature->description_en ?: $feature->description_ar) }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <span class="badge badge-light border ml-2">{{ __('Feature') }}</span>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection

@push('admin_styles')
<style>
    .package-features-modal.modal {
        z-index: 10050 !important;
    }

    .package-features-modal .modal-dialog {
        max-width: min(1140px, 96vw);
        width: 100%;
        margin: 1.5rem auto;
    }

    .package-features-modal .modal-content {
        max-height: 92vh;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.25);
    }

    .package-features-modal .modal-body {
        max-height: calc(92vh - 130px);
        overflow-y: auto;
        background-color: #f8f9fa;
    }

    body.package-features-modal-open .modal-backdrop {
        z-index: 10040 !important;
        opacity: 0.45 !important;
        pointer-events: auto !important;
        background-color: #000 !important;
    }

    body.package-features-modal-open .modal-backdrop.show {
        opacity: 0.45 !important;
    }

    .package-features-service-title {
        color: #1e88e5;
        font-weight: 600;
        margin-bottom: 0.75rem;
        padding-bottom: 0.35rem;
        border-bottom: 1px solid #dee2e6;
    }

    .package-feature-item {
        border-left: 3px solid #1e88e5;
        margin-bottom: 0.5rem;
        background: #fff;
    }

    .package-feature-item:last-child {
        margin-bottom: 0;
    }
</style>
@endpush

@push('admin_scripts')
<script>
    $(function () {
        $('.package-features-modal').on('show.bs.modal', function () {
            $(this).appendTo('body');
            $('body').addClass('package-features-modal-open');
        });

        $('.package-features-modal').on('hidden.bs.modal', function () {
            if (!$('.package-features-modal.show').length) {
                $('body').removeClass('package-features-modal-open');
            }
        });
    });
</script>
@endpush
