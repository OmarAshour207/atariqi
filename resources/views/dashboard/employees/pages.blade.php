@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <h1>{{ __('Edit Pages') }} - {{ $employee->name }}</h1>
            <form method="POST" action="{{ route('employees.pages.update', $employee) }}">
                @csrf @method('PUT')
                @foreach($pages as $page)
                    <div class="form-check">
                        <input type="checkbox" name="page_ids[]" value="{{ $page->id }}" id="page{{ $page->id }}" class="form-check-input" {{ in_array($page->id, $selected) ? 'checked' : '' }}>
                        <label for="page{{ $page->id }}">{{ __($page->name) }} <small class="text-muted">({{ $page->route }})</small></label>
                    </div>
                @endforeach
                <button class="btn btn-primary mt-3">{{ __('Save') }}</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary mt-3">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
