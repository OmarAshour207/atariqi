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
                    <form method="GET" action="{{ route('packages.index') }}" class="row">
                        <div class="col-md-3">
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
                                <label for="min_price">{{ __('Min Price') }}</label>
                                <input type="number" step="0.01" class="form-control" id="min_price" name="min_price" value="{{ request('min_price') }}" placeholder="0.00">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="has_features">{{ __('Has Features') }}</label>
                                <select class="form-control" id="has_features" name="has_features">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="1" {{ request('has_features') == '1' ? 'selected' : '' }}>{{ __('With Features') }}</option>
                                    <option value="0" {{ request('has_features') == '0' ? 'selected' : '' }}>{{ __('Without Features') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="sort_by">{{ __('Sort By') }}</label>
                                <select class="form-control" id="sort_by" name="sort_by">
                                    <option value="created_at" {{ request('sort_by', 'created_at') == 'created_at' ? 'selected' : '' }}>{{ __('Created Date') }}</option>
                                    <option value="name_en" {{ request('sort_by') == 'name_en' ? 'selected' : '' }}>{{ __('Name (EN)') }}</option>
                                    <option value="price_monthly" {{ request('sort_by') == 'price_monthly' ? 'selected' : '' }}>{{ __('Monthly Price') }}</option>
                                    <option value="price_annual" {{ request('sort_by') == 'price_annual' ? 'selected' : '' }}>{{ __('Annual Price') }}</option>
                                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>{{ __('Status') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-block">{{ __('Filter') }}</button>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <a href="{{ route('packages.index') }}" class="btn btn-secondary">{{ __('Clear Filters') }}</a>
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
                                        <button type="button" class="btn btn-sm btn-info" onclick="showFeatures({{ $package->id }})">
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

            <!-- Features Overlays -->
            @foreach($packages as $package)
                @if($package->features->count() > 0)
                <div id="featuresOverlay{{ $package->id }}" class="features-overlay" style="display: none;">
                    <div class="features-modal">
                        <div class="features-header">
                            <h5>{{ __('Features for') }} {{ $package->name_en }}</h5>
                            <button type="button" class="close-btn" onclick="hideFeatures({{ $package->id }})">&times;</button>
                        </div>
                        <div class="features-body">
                            <div class="row">
                                @foreach($package->features as $feature)
                                    <div class="col-md-6 mb-3">
                                        <div class="card h-100">
                                            <div class="card-body">
                                                <h6 class="card-title">{{ $feature->name_en }}</h6>
                                                @if($feature->name_ar)
                                                    <h6 class="card-subtitle mb-2 text-muted">{{ $feature->name_ar }}</h6>
                                                @endif
                                                @if($feature->description_en)
                                                    <p class="card-text small">{{ $feature->description_en }}</p>
                                                @endif
                                                @if($feature->description_ar)
                                                    <p class="card-text small text-right">{{ $feature->description_ar }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="features-footer">
                            <button type="button" class="btn btn-secondary" onclick="hideFeatures({{ $package->id }})">{{ __('Close') }}</button>
                        </div>
                    </div>
                </div>
                @endif
            @endforeach

            <div class="mt-4">{{ $packages->links('dashboard.pagination.custom') }}</div>
        </div>
    </div>
@endsection

@push('admin_styles')
<style>
.features-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 9999;
    display: flex;
    justify-content: center;
    align-items: center;
}

.features-modal {
    background: white;
    border-radius: 8px;
    max-width: 90%;
    max-height: 90%;
    width: 800px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    display: flex;
    flex-direction: column;
}

.features-header {
    padding: 20px;
    border-bottom: 1px solid #dee2e6;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.features-header h5 {
    margin: 0;
    font-size: 1.25rem;
    font-weight: 500;
}

.close-btn {
    background: none;
    border: none;
    font-size: 24px;
    cursor: pointer;
    color: #6c757d;
    padding: 0;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.close-btn:hover {
    color: #000;
}

.features-body {
    padding: 20px;
    overflow-y: auto;
    max-height: 400px;
}

.features-footer {
    padding: 20px;
    border-top: 1px solid #dee2e6;
    text-align: right;
}
</style>
@endpush

@push('admin_scripts')
<script>
function showFeatures(packageId) {
    document.getElementById('featuresOverlay' + packageId).style.display = 'flex';
    document.body.style.overflow = 'hidden'; // Prevent background scrolling
}

function hideFeatures(packageId) {
    document.getElementById('featuresOverlay' + packageId).style.display = 'none';
    document.body.style.overflow = 'auto'; // Restore background scrolling
}

// Close modal when clicking on overlay background
document.addEventListener('click', function(event) {
    if (event.target.classList.contains('features-overlay')) {
        event.target.style.display = 'none';
        document.body.style.overflow = 'auto';
    }
});

// Close modal on escape key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        var overlays = document.querySelectorAll('.features-overlay[style*="display: flex"]');
        overlays.forEach(function(overlay) {
            overlay.style.display = 'none';
            document.body.style.overflow = 'auto';
        });
    }
});
</script>
@endpush
