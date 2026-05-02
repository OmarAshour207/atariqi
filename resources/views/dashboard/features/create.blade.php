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
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Create Feature') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Create Feature') }}</h1>
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
                <form action="{{ route('features.store') }}" method="post">
                    @csrf
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="name_en">{{ __('Name (EN)') }}</label>
                            <input type="text" name="name_en" id="name_en" class="form-control" value="{{ old('name_en') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="name_ar">{{ __('Name (AR)') }}</label>
                            <input type="text" name="name_ar" id="name_ar" class="form-control" value="{{ old('name_ar') }}" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="service_id">{{ __('Service') }}</label>
                            <select name="service_id" id="service_id" class="form-control">
                                <option value="">{{ __('No service') }}</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>{{ $service->service }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="description_en">{{ __('Description (EN)') }}</label>
                            <textarea name="description_en" id="description_en" rows="3" class="form-control">{{ old('description_en') }}</textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description_ar">{{ __('Description (AR)') }}</label>
                        <textarea name="description_ar" id="description_ar" rows="3" class="form-control">{{ old('description_ar') }}</textarea>
                    </div>

                    <div class="text-right">
                        <a href="{{ route('features.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                        <button type="submit" class="btn btn-primary">{{ __('Save Feature') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
