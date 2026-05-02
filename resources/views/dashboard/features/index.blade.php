@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Features') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Features') }}</h1>
                </div>
                <a href="{{ route('features.create') }}" class="btn btn-success">{{ __('Create Feature') }}</a>
            </div>
        </div>

        <div class="container-fluid page__container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('features.index') }}" class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="name">{{ __('Feature Name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}" placeholder="{{ __('Search by name...') }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="service_id">{{ __('Service') }}</label>
                                <select class="form-control" id="service_id" name="service_id">
                                    <option value="">{{ __('All Services') }}</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" {{ request('service_id') == $service->id ? 'selected' : '' }}>{{ $service->service }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="sort_by">{{ __('Sort By') }}</label>
                                <select class="form-control" id="sort_by" name="sort_by">
                                    <option value="id" {{ request('sort_by', 'id') == 'id' ? 'selected' : '' }}>{{ __('ID') }}</option>
                                    <option value="name_en" {{ request('sort_by') == 'name_en' ? 'selected' : '' }}>{{ __('Name (EN)') }}</option>
                                    <option value="name_ar" {{ request('sort_by') == 'name_ar' ? 'selected' : '' }}>{{ __('Name (AR)') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="sort_direction">{{ __('Direction') }}</label>
                                <select class="form-control" id="sort_direction" name="sort_direction">
                                    <option value="desc" {{ request('sort_direction', 'desc') == 'desc' ? 'selected' : '' }}>{{ __('Descending') }}</option>
                                    <option value="asc" {{ request('sort_direction') == 'asc' ? 'selected' : '' }}>{{ __('Ascending') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12 text-right">
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                            <a href="{{ route('features.index') }}" class="btn btn-secondary">{{ __('Clear Filters') }}</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Name (EN)') }}</th>
                            <th>{{ __('Name (AR)') }}</th>
                            <th>{{ __('Service') }}</th>
                            <th>{{ __('Description (EN)') }}</th>
                            <th>{{ __('Description (AR)') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($features as $index => $feature)
                            <tr>
                                <td>{{ $features->firstItem() + $index }}</td>
                                <td>{{ $feature->name_en }}</td>
                                <td>{{ $feature->name_ar }}</td>
                                <td>{{ optional($feature->service)->service ?? __('None') }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($feature->description_en, 60) }}</td>
                                <td>{{ \Illuminate\Support\Str::limit($feature->description_ar, 60) }}</td>
                                <td>
                                    <a href="{{ route('features.edit', $feature->id) }}" class="btn btn-sm btn-primary">{{ __('Edit') }}</a>
                                    <form action="{{ route('features.destroy', $feature->id) }}" method="post" class="d-inline-block">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="btn btn-sm btn-danger delete">{{ __('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">{{ __('No features found.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $features->links('dashboard.pagination.custom') }}</div>
        </div>
    </div>
@endsection
