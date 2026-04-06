@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('packages.index') }}">{{ __('Packages') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Package') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Edit Package') }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="card card-form__body card-body">
                <form action="{{ route('packages.update', $package->id) }}" method="post">
                    @csrf
                    @method('put')
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name_en">{{ __('Name (EN)') }}</label>
                            <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en', $package->name_en) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name_ar">{{ __('Name (AR)') }}</label>
                            <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar', $package->name_ar) }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="price_monthly">{{ __('Monthly Price') }}</label>
                            <input type="number" step="0.01" name="price_monthly" id="price_monthly" class="form-control" value="{{ old('price_monthly', $package->price_monthly) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="price_annual">{{ __('Annual Price') }}</label>
                            <input type="number" step="0.01" name="price_annual" id="price_annual" class="form-control" value="{{ old('price_annual', $package->price_annual) }}" required>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="status">{{ __('Status') }}</label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="">{{ __('Select status') }}</option>
                            <option value="0" {{ old('status', $package->status) == '0' ? 'selected' : '' }}>{{ __('Free') }}</option>
                            <option value="1" {{ old('status', $package->status) == '1' ? 'selected' : '' }}>{{ __('Coming Soon') }}</option>
                            <option value="2" {{ old('status', $package->status) == '2' ? 'selected' : '' }}>{{ __('New') }}</option>
                            <option value="3" {{ old('status', $package->status) == '3' ? 'selected' : '' }}>{{ __('Discount') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="features">{{ __('Features') }}</label>
                        <select name="features[]" id="features" class="form-control" multiple style="height: 200px;">
                            @php
                                $features = \App\Models\Feature::with('service')->get()->groupBy('service.service');
                                $selectedFeatures = old('features', $package->features->pluck('id')->toArray());
                            @endphp
                            @foreach($features as $serviceName => $serviceFeatures)
                                @if($serviceName)
                                    <optgroup label="{{ $serviceName }}">
                                        @foreach($serviceFeatures as $feature)
                                            <option value="{{ $feature->id }}" {{ in_array($feature->id, $selectedFeatures) ? 'selected' : '' }}>
                                                {{ $feature->name_en }} @if($feature->name_ar) ({{ $feature->name_ar }}) @endif
                                            </option>
                                        @endforeach
                                    </optgroup>
                                @else
                                    @foreach($serviceFeatures as $feature)
                                        <option value="{{ $feature->id }}" {{ in_array($feature->id, $selectedFeatures) ? 'selected' : '' }}>
                                            {{ $feature->name_en }} @if($feature->name_ar) ({{ $feature->name_ar }}) @endif
                                        </option>
                                    @endforeach
                                @endif
                            @endforeach
                        </select>
                        <small class="form-text text-muted">{{ __('Hold Ctrl (or Cmd on Mac) to select multiple features') }}</small>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('packages.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Update Package') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('admin_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Enhance multi-select with better UX
    const featuresSelect = document.getElementById('features');

    if (featuresSelect) {
        // Add selected features counter
        const selectedCounter = document.createElement('small');
        selectedCounter.className = 'form-text text-info';
        selectedCounter.style.display = 'none';
        featuresSelect.parentNode.appendChild(selectedCounter);

        function updateCounter() {
            const selectedOptions = Array.from(featuresSelect.selectedOptions);
            if (selectedOptions.length > 0) {
                selectedCounter.textContent = selectedOptions.length + ' features selected';
                selectedCounter.style.display = 'block';
            } else {
                selectedCounter.style.display = 'none';
            }
        }

        featuresSelect.addEventListener('change', updateCounter);
        updateCounter(); // Initial count
    }
});
</script>
