@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }} </a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{  __('Drivers') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0"> {{ __('Drivers') }} </h1>
                </div>
                <!-- <a href="{{ route('drivers.create') }}" class="btn btn-success ml-3">{{ __('Create') }} <i class="material-icons">add</i></a> -->
            </div>
        </div>

        <div class="container-fluid page__container">

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Filters') }}</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('drivers.index') }}" class="row align-items-end">
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label for="name">{{ __('Name') }}</label>
                                <input type="text" class="form-control w-100" id="name" name="name" value="{{ request('name') }}" placeholder="{{ __('Search by name') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label for="email">{{ __('Email') }}</label>
                                <input type="email" class="form-control w-100" id="email" name="email" value="{{ request('email') }}" placeholder="{{ __('Search by email') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label for="phone">{{ __('Phone') }}</label>
                                <input type="text" class="form-control w-100" id="phone" name="phone" value="{{ request('phone') }}" placeholder="{{ __('Search by phone') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label for="approval">{{ __('Approval Status') }}</label>
                                <select class="form-control w-100" id="approval" name="approval">
                                    <option value="">{{ __('All') }}</option>
                                    <option value="0" {{ request('approval') === '0' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="1" {{ request('approval') === '1' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                    <option value="2" {{ request('approval') === '2' ? 'selected' : '' }}>{{ __('Under Review') }}</option>
                                    <option value="3" {{ request('approval') === '3' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                    <option value="4" {{ request('approval') === '4' ? 'selected' : '' }}>{{ __('Absher Update Required') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label for="university-id">{{ __('University') }}</label>
                                <select class="form-control w-100" id="university-id" name="university-id">
                                    <option value="">{{ __('All') }}</option>
                                    @foreach($universities as $university)
                                        <option value="{{ $university->id }}" @selected(request('university-id') == $university->id)>
                                            {{ $university->{"name-ar"} ?? $university->{"name-en"} }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label for="user-stage-id">{{ __('Stage') }}</label>
                                <select class="form-control w-100" id="user-stage-id" name="user-stage-id">
                                    <option value="">{{ __('All') }}</option>
                                    @foreach($stages as $stage)
                                        <option value="{{ $stage->id }}" @selected(request('user-stage-id') == $stage->id)>
                                            {{ $stage->{"name-ar"} ?? $stage->{"name-en"} }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label for="min_rate">{{ __('Minimum Rate') }}</label>
                                <input type="number" step="0.01" min="0" max="5" class="form-control w-100" id="min_rate" name="min_rate" value="{{ request('min_rate') }}" placeholder="{{ __('Minimum Rate') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label for="max_rate">{{ __('Maximum Rate') }}</label>
                                <input type="number" step="0.01" min="0" max="5" class="form-control w-100" id="max_rate" name="max_rate" value="{{ request('max_rate') }}" placeholder="{{ __('Maximum Rate') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label for="min_dues">{{ __('Minimum Dues (SAR)') }}</label>
                                <input type="number" step="0.01" min="0" class="form-control w-100" id="min_dues" name="min_dues" value="{{ request('min_dues') }}" placeholder="{{ __('Minimum Dues (SAR)') }}">
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6 mb-3 mb-lg-0">
                            <div class="form-group mb-0">
                                <label for="sort">{{ __('Sort By') }}</label>
                                <select class="form-control w-100" id="sort" name="sort">
                                    <option value="newest" @selected(request('sort', 'newest') === 'newest')>{{ __('Newest First') }}</option>
                                    <option value="oldest" @selected(request('sort') === 'oldest')>{{ __('Oldest First') }}</option>
                                    <option value="highest_dues" @selected(request('sort') === 'highest_dues')>{{ __('Highest Dues') }}</option>
                                    <option value="lowest_dues" @selected(request('sort') === 'lowest_dues')>{{ __('Lowest Dues') }}</option>
                                    <option value="highest_rate" @selected(request('sort') === 'highest_rate')>{{ __('Highest Rate') }}</option>
                                    <option value="lowest_rate" @selected(request('sort') === 'lowest_rate')>{{ __('Lowest Rate') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">{{ __('Apply') }}</button>
                            <a href="{{ route('drivers.index') }}" class="btn btn-secondary">{{ __('Clear Filters') }}</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive" data-toggle="lists" data-lists-values='["js-lists-values-employee-name"]'>

                    <table class="table mb-0 thead-border-top-0 table-striped drivers-index-table">
                        <thead class="drivers-index-table__head">
                            <tr>
                            <th class="drivers-col-index"> {{ __('#') }} </th>
                            <th class="drivers-col-name"> {{ __('Full Name') }} </th>
                            <th class="drivers-col-email"> {{ __('Email') }} </th>
                            <th class="drivers-col-phone"> {{ __('Phone Number') }} </th>
                            <th class="drivers-col-university"> {{ __('University') }} </th>
                            <th class="drivers-col-rate"> {{ __('Rate') }} </th>
                            <th class="drivers-col-dues"> {{ __('Dues') }} </th>
                            <th class="drivers-col-approval"> {{ __('Approval') }} </th>
                            <th class="drivers-col-action"> {{ __('Action') }} </th>
                            </tr>
                        </thead>
                        <tbody class="list" id="companies">
                        @forelse ($drivers as $index => $driver)
                        <tr>

                            <td class="drivers-col-index">
                                <div class="badge badge-soft-dark"> {{ $drivers->firstItem() + $index }} </div>
                            </td>

                            <td class="drivers-col-name">
                                {{ $driver->fullName }}
                            </td>

                            <td class="drivers-col-email">
                                <a href="mailto:{{ $driver->email }}" class="drivers-email-link">{{ $driver->email }}</a>
                            </td>

                            <td class="drivers-col-phone">
                                <span class="drivers-phone">{{ $driver->fullPhoneNumber }}</span>
                            </td>

                            <td class="drivers-col-university">
                                {{ $driver->university->{"name-ar"} ?? __('Not Specified') }}
                            </td>

                            <td class="drivers-col-rate">
                                {{ $driver->driverInfo->{"driver-rate"} ?? __('Not Specified') }}
                            </td>

                            <td class="drivers-col-dues">
                                {{ number_format($driver->current_dues ?? 0, 2) }} {{ __('SAR') }}
                            </td>

                            <td class="drivers-col-approval">
                                @if($driver->approval == 0)
                                    <span class="badge badge-warning">{{ __('Pending') }}</span>
                                @elseif($driver->approval == 1)
                                    <span class="badge badge-success">{{ __('Approved') }}</span>
                                @elseif($driver->approval == 2)
                                    <span class="badge badge-info">{{ __('Under Review') }}</span>
                                @elseif($driver->approval == 3)
                                    <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                @elseif($driver->approval == 4)
                                    <span class="badge badge-warning">{{ __('Absher Update Required') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Unknown') }}</span>
                                @endif
                            </td>
                            <td class="drivers-col-action text-nowrap">
                                <div class="drivers-actions" role="group">
                                <a href="{{ route('drivers.show', $driver->id) }}" class="btn btn-link px-1" title="{{ __('View') }}">
                                    <i class="fa fa-eye fa-lg"></i>
                                </a>

                                <a href="{{ route('drivers.driverTrips', $driver->id) }}" class="btn btn-link px-1" title="{{ __('Trips') }}">
                                    <i class="fa fa-route fa-lg"></i>
                                </a>

                                <a href="{{ route('drivers.earnings', $driver->id) }}" class="btn btn-link px-1" title="{{ __('Driver Earnings') }}">
                                    <i class="fa fa-coins fa-lg text-success"></i>
                                </a>

                                @if($driver->email)
                                    <form action="{{ route('drivers.sendPaymentReminder', $driver->id) }}" method="post" class="d-inline-block" onsubmit="return confirm('{{ __('Send dues reminder to this driver?') }}');">
                                        @csrf
                                        <button type="submit" class="btn btn-link px-1" title="{{ __('Remind') }}">
                                            <i class="fa fa-bell fa-lg text-warning"></i>
                                        </button>
                                    </form>
                                @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    @php
                                        $hasActiveFilters = count(array_filter(request()->only([
                                            'name', 'email', 'phone', 'approval', 'university-id', 'user-stage-id',
                                            'min_rate', 'max_rate', 'min_dues',
                                        ]))) > 0 || (request('sort') && request('sort') !== 'newest');
                                    @endphp
                                    @if($hasActiveFilters)
                                        <p class="text-muted mb-0">{{ __('No Results matched') }}</p>
                                    @else
                                        <h5 class="mb-0">{{ __('No records') }}</h5>
                                    @endif
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="mt-4">
                {{ $drivers->links('dashboard.pagination.custom') }}
            </div>
        </div>
        <!-- // END drawer-layout__content -->
    </div>

    <style>
        .drivers-index-table {
            width: 100%;
            min-width: 1100px;
        }

        .drivers-index-table__head th {
            font-size: 0.95rem;
            font-weight: 700;
            padding: 0.85rem 0.65rem;
            vertical-align: middle;
            white-space: nowrap;
        }

        .drivers-index-table tbody td {
            font-size: 0.95rem;
            padding: 0.75rem 0.65rem;
            vertical-align: middle;
            white-space: normal;
            word-break: break-word;
            overflow-wrap: anywhere;
            line-height: 1.45;
        }

        .drivers-index-table .badge {
            font-size: 0.85rem;
            padding: 0.35em 0.55em;
            white-space: normal;
            word-break: break-word;
        }

        .drivers-col-index { width: 48px; min-width: 48px; }
        .drivers-col-name { min-width: 150px; }
        .drivers-col-email { min-width: 180px; }
        .drivers-col-phone { min-width: 130px; white-space: nowrap !important; }
        .drivers-col-university { min-width: 140px; }
        .drivers-col-rate { min-width: 70px; white-space: nowrap !important; }
        .drivers-col-dues { min-width: 100px; white-space: nowrap !important; }
        .drivers-col-approval { min-width: 120px; }
        .drivers-col-action { min-width: 150px; white-space: nowrap !important; }

        .drivers-email-link {
            color: inherit;
            text-decoration: none;
        }

        .drivers-email-link:hover {
            color: #007bff;
            text-decoration: underline;
        }

        .drivers-phone {
            display: inline-block;
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
            background-color: #e3f2fd;
            color: #1565c0;
            font-size: 0.875rem;
            white-space: nowrap;
        }

        .drivers-actions .btn-link {
            line-height: 1;
        }
    </style>
@endsection
