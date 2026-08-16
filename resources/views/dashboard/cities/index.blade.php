@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__heading-container">
        <div class="page__heading d-flex align-items-center">
            <div class="flex"><h1 class="m-0">{{ __('Cities & Neighborhoods') }}</h1></div>
            @adminCan('add')
            <a href="{{ route('cities.create') }}" class="btn btn-success">{{ __('Add City') }}</a>
            @endadminCan
        </div>
    </div>
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        @forelse($cities as $city)
            <div class="card mb-3">
                <div class="card-header d-flex justify-content-between">
                    <strong>{{ $city->{'city-ar'} }} / {{ $city->{'city-en'} }}</strong>
                </div>
                <div class="card-body">
                    @if($city->neighbours->count())
                        <table class="table table-sm">
                            <thead><tr><th>{{ __('Neighborhood') }}</th><th>{{ __('Actions') }}</th></tr></thead>
                            <tbody>
                            @foreach($city->neighbours as $neighborhood)
                                <tr>
                                    <td>
                                        @adminCan('edit')
                                        <form method="POST" action="{{ route('neighborhoods.update', $neighborhood) }}" class="form-inline">
                                            @csrf @method('PUT')
                                            <input name="neighborhood-ar" class="form-control form-control-sm mr-2" value="{{ $neighborhood->{'neighborhood-ar'} }}">
                                            <input name="neighborhood-eng" class="form-control form-control-sm mr-2" value="{{ $neighborhood->{'neighborhood-eng'} }}">
                                            <button type="submit" class="btn btn-sm btn-primary">{{ __('Edit') }}</button>
                                        </form>
                                        @else
                                        {{ $neighborhood->{'neighborhood-ar'} }} / {{ $neighborhood->{'neighborhood-eng'} }}
                                        @endadminCan
                                    </td>
                                    <td>
                                        @adminCan('delete')
                                        <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#deleteNb{{ $neighborhood->id }}">{{ __('Delete') }}</button>
                                        @endadminCan
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>

                        @adminCan('delete')
                        @foreach($city->neighbours as $neighborhood)
                            @include('dashboard.partials.delete_modal', [
                                'id' => 'deleteNb'.$neighborhood->id,
                                'action' => route('neighborhoods.destroy', $neighborhood),
                                'title' => __('Delete Neighborhood'),
                            ])
                        @endforeach
                        @endadminCan
                    @else
                        <p class="text-muted">{{ __('No neighborhoods found.') }}</p>
                    @endif
                    @adminCan('add')
                    <form method="POST" action="{{ route('cities.neighborhoods.store', $city) }}" class="mt-3">
                        @csrf
                        <div class="form-row">
                            <div class="col-md-4"><input name="neighborhood-ar" class="form-control" placeholder="{{ __('Neighborhood') }} AR" required></div>
                            <div class="col-md-4"><input name="neighborhood-eng" class="form-control" placeholder="{{ __('Neighborhood') }} EN" required></div>
                            <div class="col-md-4"><button class="btn btn-info">{{ __('Add Neighborhood') }}</button></div>
                        </div>
                    </form>
                    @endadminCan
                </div>
            </div>
        @empty
            <div class="alert alert-info">{{ __('No cities found.') }}</div>
        @endforelse
    </div>
</div>
@endsection
