@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__heading-container">
        <div class="page__heading"><h1 class="m-0">{{ __('Logs Management') }}</h1></div>
    </div>
    <div class="container-fluid page__container">
        @foreach($sections as $key => $section)
            <div class="card mb-4">
                <div class="card-header"><strong>{{ $section['title'] }}</strong></div>
                <div class="card-body">
                    <form method="GET" class="row mb-3">
                        <div class="col-md-3">
                            <select name="{{ $key }}_employee" class="form-control">
                                <option value="">{{ __('Employee') }}</option>
                                @foreach($employees as $employee)
                                    <option value="{{ $employee->id }}" {{ ($section['filters'][$key.'_employee'] ?? '') == $employee->id ? 'selected' : '' }}>{{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3"><input type="date" name="{{ $key }}_date" class="form-control" value="{{ $section['filters'][$key.'_date'] ?? '' }}"></div>
                        <div class="col-md-3"><input type="text" name="{{ $key }}_action" class="form-control" placeholder="{{ __('Action Type') }}" value="{{ $section['filters'][$key.'_action'] ?? '' }}"></div>
                        <div class="col-md-2">
                            <select name="{{ $key }}_sort" class="form-control">
                                <option value="desc" {{ ($section['filters'][$key.'_sort'] ?? 'desc') === 'desc' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                                <option value="asc" {{ ($section['filters'][$key.'_sort'] ?? '') === 'asc' ? 'selected' : '' }}>{{ __('Oldest') }}</option>
                            </select>
                        </div>
                        <div class="col-md-1"><button class="btn btn-primary">{{ __('Search') }}</button></div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead><tr>@foreach($section['columns'] as $col)<th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>@endforeach<th></th></tr></thead>
                            <tbody>
                            @forelse($section['rows'] as $row)
                                <tr>
                                    @foreach($section['columns'] as $col)
                                        <td>{{ data_get($row, $col) }}</td>
                                    @endforeach
                                    <td><a href="{{ route('logs.show', [$key, data_get($row, 'id')]) }}" class="btn btn-sm btn-outline-primary">{{ __('View') }}</a></td>
                                </tr>
                            @empty
                                <tr><td colspan="{{ count($section['columns']) + 1 }}" class="text-center">{{ __('No records found.') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
