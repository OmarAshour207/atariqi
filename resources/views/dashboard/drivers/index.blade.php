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

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Filters') }}</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('drivers.index') }}" class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="name">{{ __('Name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" value="{{ request('name') }}" placeholder="{{ __('Search by name or email') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="email">{{ __('Email') }}</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ request('email') }}" placeholder="{{ __('Search by email') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="phone">{{ __('Phone') }}</label>
                                <input type="text" class="form-control" id="phone" name="phone" value="{{ request('phone') }}" placeholder="{{ __('Search by phone') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="approval">{{ __('Approval Status') }}</label>
                                <select class="form-control" id="approval" name="approval">
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

                    <table class="table mb-0 thead-border-top-0 table-striped">
                        <thead>
                            <tr>

                            <th style="width: 5%;"> # </th>
                            <th style="width: 15%;"> {{ __('Full Name') }} </th>
                            <th style="width: 15%;"> {{ __('Email') }} </th>
                            <th style="width: 12%;"> {{ __('Phone Number') }} </th>
                            <th style="width: 12%;"> {{ __('University') }} </th>
                            <th style="width: 12%;"> {{ __('Stage') }} </th>
                            <th style="width: 10%;"> {{ __('Approval') }} </th>
                            <th style="width: 17%;"> {{ __('Action') }} </th>
                            </tr>
                        </thead>
                        <tbody class="list" id="companies">
                        @forelse ($drivers as $index => $driver)
                        <tr>

                            <td>
                                <div class="badge badge-soft-dark"> {{ $index+1 }} </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        {{ mb_substr($driver->fullName, 0, 20) }}
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        {{ mb_substr($driver->email, 0, 20) }}
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center  btn btn-info">
                                    <div class="d-flex align-items-center">
                                        {{ $driver->fullPhoneNumber }}
                                    </div>
                                </div>
                             </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        {{ $driver->university->{"name-ar"} ?? __('Not Specified') }}
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        {{ $driver->stage->{"name-ar"} ?? __('Not Specified') }}
                                    </div>
                                </div>
                            </td>

                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="d-flex align-items-center">
                                        @if($driver->approval == 2)
                                            <span class="badge badge-warning">{{ __('Pending') }}</span>
                                        @elseif($driver->approval == 1)
                                            <span class="badge badge-success">{{ __('Approved') }}</span>
                                        @elseif($driver->approval == 3)
                                            <span class="badge badge-danger">{{ __('Rejected') }}</span>
                                        @else
                                            <span class="badge badge-secondary">{{ __('Unknown') }}</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('drivers.show', $driver->id) }}" class="btn btn-sm btn-link">
                                    <i class="fa fa-eye fa-2x"></i>
                                </a>

                                <a href="{{ route('drivers.edit', $driver->id) }}" class="btn btn-sm btn-link">
                                    <i class="fa fa-edit fa-2x"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                            <h1> {{ __('No records') }} </h1>
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
@endsection
