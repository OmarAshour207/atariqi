@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <h1>{{ __('Add Role') }}</h1>
            <form method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div class="form-group">
                    <label>{{ __('Role Name') }}</label>
                    <input name="name" class="form-control" value="{{ old('name') }}" placeholder="sales-agent" required>
                    <small class="form-text text-muted">{{ __('Use lowercase letters, numbers and dashes only.') }}</small>
                </div>
                <hr>
                <h5>{{ __('Page Permissions') }}</h5>
                <p class="text-muted">{{ __('Select pages and actions allowed for this role.') }}</p>
                @include('dashboard.roles._permissions_matrix', ['matrix' => $matrix, 'selected' => old('permissions', $selected)])
                <button class="btn btn-primary mt-3">{{ __('Save') }}</button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary mt-3">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
