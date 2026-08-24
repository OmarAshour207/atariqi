@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card card-body">
            <h1>{{ __('Edit Employee Data') }}</h1>
            <form method="POST" action="{{ route('employees.update', $employee) }}">
                @csrf @method('PUT')
                @include('dashboard.employees._form', ['employee' => $employee])
                @adminCan('update')
                <button class="btn btn-primary">{{ __('Save') }}</button>
                @endadminCan
                <a href="{{ route('employees.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
