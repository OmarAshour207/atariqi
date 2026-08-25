@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <h1>{{ __('Edit Role') }} - {{ __(ucfirst(str_replace('-', ' ', $role->name))) }}</h1>
            <form method="POST" action="{{ route('roles.update', $role) }}">
                @csrf @method('PUT')
                <div class="form-group">
                    <label>{{ __('Role Name') }}</label>
                    <input
                        name="name"
                        class="form-control"
                        value="{{ old('name', $role->name) }}"
                        {{ $role->name === \App\Models\Admin::ROLE_ADMIN ? 'readonly' : 'required' }}
                    >
                    @if($role->name === \App\Models\Admin::ROLE_ADMIN)
                        <small class="form-text text-muted">{{ __('The admin role name cannot be changed and always has full access.') }}</small>
                    @endif
                </div>
                <hr>
                <h5>{{ __('Page Permissions') }}</h5>
                <p class="text-muted">{{ __('Select pages and actions allowed for this role.') }}</p>
                @include('dashboard.roles._permissions_matrix', [
                    'matrix' => $matrix,
                    'selected' => old('permissions', $selected),
                    'readonly' => $role->name === \App\Models\Admin::ROLE_ADMIN,
                ])
                <button class="btn btn-primary mt-3">{{ __('Save') }}</button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary mt-3">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
