@extends('dashboard.layouts.app')

@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__heading-container">
        <div class="page__heading d-flex align-items-center">
            <div class="flex">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}">{{ __('Home') }}</a></li>
                        <li class="breadcrumb-item active">{{ __('Announcements') }}</li>
                    </ol>
                </nav>
                <h1 class="m-0">{{ __('Announcements') }}</h1>
            </div>
            <a href="{{ route('announcements.create') }}" class="btn btn-success">{{ __('Add Announcement') }}</a>
        </div>
    </div>

    <div class="container-fluid page__container">
        @include('dashboard.partials.session')

        <div class="card table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('Title') }}</th>
                    <th>{{ __('Content') }}</th>
                    <th>{{ __('App Type') }}</th>
                    <th>{{ __('Created At') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($announcements as $item)
                    <tr>
                        <td>{{ $item['id'] }}</td>
                        <td>{{ $item['title'] }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($item['content'], 80) }}</td>
                        <td>{{ $item['target_app'] }}</td>
                        <td>{{ $item['created_at'] }}</td>
                        <td>
                            <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteModal{{ $item['source'] }}{{ $item['id'] }}">{{ __('Delete') }}</button>
                        </td>
                    </tr>
                    @include('dashboard.partials.delete_modal', [
                        'id' => 'deleteModal'.$item['source'].$item['id'],
                        'action' => route('announcements.destroy', [$item['source'], $item['id']]),
                        'title' => __('Delete Announcement'),
                    ])
                @empty
                    <tr><td colspan="6" class="text-center py-4">{{ __('No announcements found.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
