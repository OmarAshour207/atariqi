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
                                    <option value="2" {{ request('approval') === '2' ? 'selected' : '' }}>{{ __('Pending') }}</option>
                                    <option value="1" {{ request('approval') === '1' ? 'selected' : '' }}>{{ __('Approved') }}</option>
                                    <option value="3" {{ request('approval') === '3' ? 'selected' : '' }}>{{ __('Rejected') }}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                            <a href="{{ route('drivers.index') }}" class="btn btn-secondary">{{ __('Clear Filters') }}</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive" data-toggle="lists" data-lists-values='["js-lists-values-employee-name"]'>

                    <table class="table mb-0 thead-border-top-0 table-striped drivers-index-table" style="table-layout: fixed; width: 100%;">
                        <thead class="drivers-index-table__head">
                            <tr>

                            <th style="width: 4%;"> {{ __('#') }} </th>
                            <th style="width: 12%;"> {{ __('Full Name') }} </th>
                            <th style="width: 14%;"> {{ __('Email') }} </th>
                            <th style="width: 12%;"> {{ __('Phone Number') }} </th>
                            <th style="width: 12%;"> {{ __('University') }} </th>
                            <th style="width: 8%;"> {{ __('Rate') }} </th>
                            <th style="width: 8%;"> {{ __('Dues') }} </th>
                            <th style="width: 10%;"> {{ __('Approval') }} </th>
                            <th style="width: 20%;"> {{ __('Action') }} </th>
                            </tr>
                        </thead>
                        <tbody class="list" id="companies">
                        @forelse ($drivers as $index => $driver)
                        <tr>

                            <td>
                                <div class="badge badge-soft-dark"> {{ $index+1 }} </div>
                            </td>

                            <td class="text-truncate" title="{{ $driver->fullName }}">
                                {{ $driver->fullName }}
                            </td>

                            <td class="text-truncate" title="{{ $driver->email }}">
                                {{ $driver->email }}
                            </td>

                            <td>
                                <span class="badge badge-info">{{ $driver->fullPhoneNumber }}</span>
                            </td>

                            <td class="text-truncate" title="{{ $driver->university->{"name-ar"} ?? __('Not Specified') }}">
                                {{ $driver->university->{"name-ar"} ?? __('Not Specified') }}
                            </td>

                            <td>
                                {{ $driver->driverInfo->{"driver-rate"} ?? __('Not Specified') }}
                            </td>

                            <td>
                                {{ number_format($driver->current_dues ?? 0, 2) }} {{ __('SAR') }}
                            </td>

                            <td>
                                @if(in_array($driver->approval, [0, 2], true))
                                    <span class="badge badge-warning">{{ __('Pending') }}</span>
                                @elseif($driver->approval == 1)
                                    <span class="badge badge-success">{{ __('Approved') }}</span>
                                @elseif($driver->approval == 3)
                                    <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                @else
                                    <span class="badge badge-secondary">{{ __('Unknown') }}</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <div class="btn-group" role="group">
                                <a href="{{ route('drivers.show', $driver->id) }}" class="btn btn-link px-2" title="{{ __('View') }}">
                                    <i class="fa fa-eye fa-2x"></i>
                                </a>

                                <a href="{{ route('drivers.driverTrips', $driver->id) }}" class="btn btn-link px-2" title="{{ __('Trips') }}">
                                    <i class="fa fa-route fa-2x"></i>
                                </a>

                                <a href="{{ route('drivers.earnings', $driver->id) }}" class="btn btn-link px-2" title="{{ __('Driver Earnings') }}">
                                    <i class="fa fa-coins fa-2x text-success"></i>
                                </a>

                                @if (in_array($driver->approval, [0, 2], true))
                                    <a href="{{ route('drivers.edit', $driver->id) }}" class="btn btn-link px-2" title="{{ __('Edit') }}">
                                        <i class="fa fa-edit fa-2x"></i>
                                    </a>
                                @endif

                                @if($driver->email)
                                    <form action="{{ route('drivers.sendPaymentReminder', $driver->id) }}" method="post" class="d-inline-block" onsubmit="return confirm('{{ __('Send dues reminder to this driver?') }}');">
                                        @csrf
                                        <button type="submit" class="btn btn-link px-2" title="{{ __('Remind') }}">
                                            <i class="fa fa-bell fa-2x text-warning"></i>
                                        </button>
                                    </form>
                                @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            <h1> {{ __('No Results matched') }} </h1>
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
        .drivers-index-table__head th {
            font-size: 1.1rem;
            font-weight: 700;
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .drivers-index-table tbody td {
            font-size: 1rem;
            padding: 0.9rem 0.75rem;
            vertical-align: middle;
        }

        .drivers-index-table .badge {
            font-size: 0.95rem;
            padding: 0.45em 0.65em;
        }
    </style>
@endsection
