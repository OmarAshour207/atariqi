@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Driver Passenger Rates') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Driver Passenger Rates') }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Driver Name') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Average Rating') }}</th>
                            <th>{{ __('Total Ratings') }}</th>
                            <th>{{ __('Rating Breakdown') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($drivers as $index => $driver)
                            <tr>
                                <td>{{ $drivers->firstItem() + $index }}</td>
                                <td>{{ $driver->fullName }}</td>
                                <td>{{ $driver->email }}</td>
                                <td>{{ $driver->fullPhoneNumber ?? $driver->{'phone-no'} }}</td>
                                <td>
                                    @if($driver->average_rating)
                                        <span class="badge badge-success">
                                            {{ $driver->average_rating }}/5
                                        </span>
                                    @else
                                        <span class="text-muted">{{ __('No ratings') }}</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $driver->total_ratings }}</span>
                                </td>
                                <td>
                                    @if($driver->total_ratings > 0)
                                        <small>
                                            {{ __('Daily') }}: {{ $driver->rating_breakdown['daily'] }}<br>
                                            {{ __('Weekly') }}: {{ $driver->rating_breakdown['weekly'] }}<br>
                                            {{ __('Immediate') }}: {{ $driver->rating_breakdown['immediate'] }}
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('drivers.show', $driver->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fa fa-eye"></i> {{ __('View Details') }}
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4">{{ $drivers->links('dashboard.pagination.custom') }}</div>
        </div>
    </div>
@endsection
