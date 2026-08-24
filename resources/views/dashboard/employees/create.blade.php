@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <h1>{{ __('Add Employee') }}</h1>
            <form method="POST" action="{{ route('employees.store') }}">
                @csrf
                @include('dashboard.employees._form', ['employee' => null])
                <hr>
                <h5>{{ __('Actions') }}</h5>
                <p class="text-muted">{{ __('Choose view, update and delete. Create, approve, reject and replace use update.') }}</p>
                @foreach($permissions as $permission)
                    <div class="form-check">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm{{ $permission->id }}" class="form-check-input" {{ in_array($permission->name, old('permissions', ['view']), true) ? 'checked' : '' }}>
                        <label for="perm{{ $permission->id }}" class="form-check-label">{{ __(ucfirst($permission->name)) }}</label>
                    </div>
                @endforeach
                <hr>
                <h5>{{ __('Allowed Pages') }}</h5>
                @foreach($pages as $page)
                    <div class="form-check">
                        <input type="checkbox" name="page_ids[]" value="{{ $page->id }}" id="page{{ $page->id }}" class="form-check-input" {{ in_array($page->id, old('page_ids', [])) ? 'checked' : '' }}>
                        <label for="page{{ $page->id }}" class="form-check-label">{{ __($page->name) }}</label>
                    </div>
                @endforeach
                @adminCan('update')
                <button class="btn btn-primary mt-3">{{ __('Save') }}</button>
                @endadminCan
                <a href="{{ route('employees.index') }}" class="btn btn-secondary mt-3">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
