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
                <button class="btn btn-primary mt-3">{{ __('Save') }}</button>
                <a href="{{ route('employees.index') }}" class="btn btn-secondary mt-3">{{ __('Back') }}</a>
            </form>
        </div>
    </div>
</div>
@endsection
