@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__heading-container">
        <div class="page__heading d-flex align-items-center">
            <div class="flex"><h1 class="m-0">{{ __('Employee Management') }}</h1></div>
            <a href="{{ route('employees.create') }}" class="btn btn-success">{{ __('Add Employee') }}</a>
        </div>
    </div>
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Name') }}</th>
                    <th>{{ __('Email') }}</th>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Status') }}</th>
                    <th>{{ __('Pages') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->id }}</td>
                        <td>{{ $employee->name }}</td>
                        <td>{{ $employee->email }}</td>
                        <td>{{ \App\Models\Admin::roleLabel($employee->role ?? 'agent') }}</td>
                        <td>{{ $employee->is_active ? __('Active') : __('Inactive') }}</td>
                        <td>{{ $employee->pages->count() }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-primary">{{ __('Edit Data') }}</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4">{{ __('No employees found.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="card-footer">{{ $employees->links('dashboard.pagination.custom') }}</div>
        </div>
    </div>
</div>
@endsection
