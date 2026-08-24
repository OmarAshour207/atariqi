@extends('dashboard.layouts.app')

@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__heading-container">
        <div class="page__heading">
            <h1 class="m-0">{{ __('Add Announcement') }}</h1>
        </div>
    </div>
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <form method="POST" action="{{ route('announcements.store') }}">
                @csrf
                <div class="form-group">
                    <label>{{ __('Title') }} (AR)</label>
                    <input type="text" name="title-ar" class="form-control" value="{{ old('title-ar') }}" required>
                </div>
                <div class="form-group">
                    <label>{{ __('Title') }} (EN)</label>
                    <input type="text" name="title-eng" class="form-control" value="{{ old('title-eng') }}" required>
                </div>
                <div class="form-group">
                    <label>{{ __('Content') }} (AR)</label>
                    <textarea name="content-ar" class="form-control" rows="4" required>{{ old('content-ar') }}</textarea>
                </div>
                <div class="form-group">
                    <label>{{ __('Content') }} (EN)</label>
                    <textarea name="content-eng" class="form-control" rows="4" required>{{ old('content-eng') }}</textarea>
                </div>
                <div class="form-group">
                    <label>{{ __('App Type') }}</label>
                    <select name="target_app" class="form-control" required>
                        <option value="passengers">{{ __('Passengers') }}</option>
                        <option value="drivers">{{ __('Drivers') }}</option>
                        <option value="both">{{ __('Both') }}</option>
                    </select>
                </div>
                @adminCan('update')
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                @endadminCan
                <a href="{{ route('announcements.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
