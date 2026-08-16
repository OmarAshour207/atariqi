@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__heading-container">
        <div class="page__heading"><h1 class="m-0">{{ __('Logs Management') }}</h1></div>
    </div>
    <div class="container-fluid page__container">
        @foreach($sections as $key => $section)
            @php
                $rows = $section['rows'];
                $filterKeys = ["{$key}_employee", "{$key}_date", "{$key}_action", "{$key}_sort", "{$key}_page"];
            @endphp
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <strong>{{ $section['title'] }}</strong>
                    @if($rows->total() > 0)
                        <small class="text-muted">
                            {{ __('Showing') }} {{ $rows->firstItem() }}–{{ $rows->lastItem() }} {{ __('of') }} {{ $rows->total() }}
                        </small>
                    @endif
                </div>
                <div class="card-body">
                    <form method="GET" class="row mb-3">
                        @foreach(request()->except($filterKeys) as $param => $value)
                            @if(is_array($value))
                                @foreach($value as $item)
                                    <input type="hidden" name="{{ $param }}[]" value="{{ $item }}">
                                @endforeach
                            @else
                                <input type="hidden" name="{{ $param }}" value="{{ $value }}">
                            @endif
                        @endforeach
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
                        <div class="col-md-1"><button type="submit" class="btn btn-primary">{{ __('Search') }}</button></div>
                    </form>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped">
                            <thead><tr>@foreach($section['columns'] as $col)<th>{{ ucwords(str_replace('_', ' ', $col)) }}</th>@endforeach<th></th></tr></thead>
                            <tbody>
                            @forelse($rows as $row)
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
                    @if($rows->hasPages())
                        <div class="card-footer bg-white px-0 pb-0">
                            {{ $rows->links('dashboard.pagination.logs-table') }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
