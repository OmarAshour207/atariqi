@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__heading-container">
        <div class="page__heading d-flex align-items-center">
            <div class="flex"><h1 class="m-0">{{ __('Roles Management') }}</h1></div>
            <a href="{{ route('roles.create') }}" class="btn btn-success">{{ __('Add Role') }}</a>
        </div>
    </div>
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>{{ __('Role') }}</th>
                    <th>{{ __('Permissions') }}</th>
                    <th>{{ __('Employees') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td>{{ __(ucfirst(str_replace('-', ' ', $role->name))) }}</td>
                        <td>{{ $role->permissions->count() }}</td>
                        <td>{{ $role->users_count }}</td>
                        <td class="text-nowrap">
                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                            @if($role->name !== \App\Models\Admin::ROLE_ADMIN)
                                <form action="{{ route('roles.destroy', $role) }}" method="post" class="d-inline-block" onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">{{ __('Delete') }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="text-center py-4">{{ __('No roles found.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
