@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__heading-container">
        <div class="page__heading d-flex align-items-center">
            <div class="flex"><h1 class="m-0">{{ __('Universities') }}</h1></div>
            <a href="{{ route('universities.create') }}" class="btn btn-success">{{ __('Add University') }}</a>
        </div>
    </div>
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <div class="card table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>{{ __('University Name') }}</th>
                    <th>{{ __('City') }}</th>
                    <th>{{ __('Location') }}</th>
                    <th>{{ __('Services') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($universities as $university)
                    <tr>
                        <td>{{ $university->{'name-ar'} ?? $university->{'name-eng'} }}</td>
                        <td>{{ $university->cityUni?->{'city-ar'} ?? '-' }}</td>
                        <td>{{ $university->location ?? '-' }}</td>
                        <td>
                            <a href="{{ route('universities.services', $university) }}" class="btn btn-sm btn-info">{{ __('View') }}</a>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteUni{{ $university->id }}">{{ __('Delete') }}</button>
                        </td>
                    </tr>
                    @include('dashboard.partials.delete_modal', [
                        'id' => 'deleteUni'.$university->id,
                        'action' => route('universities.destroy', $university),
                        'title' => __('Delete University'),
                    ])
                @empty
                    <tr><td colspan="5" class="text-center py-4">{{ __('No universities found.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
            <div class="card-footer">{{ $universities->links('dashboard.pagination.custom') }}</div>
        </div>
    </div>
</div>
@endsection
