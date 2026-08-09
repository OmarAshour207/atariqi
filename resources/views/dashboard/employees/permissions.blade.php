@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <h1>{{ __('Edit Permissions') }} - {{ $employee->name }}</h1>
            <form method="POST" action="{{ route('employees.permissions.update', $employee) }}">
                @csrf @method('PUT')
                @foreach($permissions as $permission)
                    <div class="form-check">
                        <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" id="perm{{ $permission->id }}" class="form-check-input" {{ in_array($permission->id, $selected) ? 'checked' : '' }}>
                        <label for="perm{{ $permission->id }}">{{ __($permission->name) }}</label>
                    </div>
                @endforeach
                <button class="btn btn-primary mt-3">{{ __('Save') }}</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary mt-3">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
