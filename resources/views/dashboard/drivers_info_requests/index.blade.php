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
                    <h1 class="m-0"> {{ __('Edit Info Requests') }} </h1>
                </div>
                <!-- <a href="{{ route('drivers.create') }}" class="btn btn-success ml-3">{{ __('Create') }} <i class="material-icons">add</i></a> -->
            </div>
        </div>

        <div class="container-fluid page__container">

            <div class="card">
                <div class="table-responsive" data-toggle="lists" data-lists-values='["js-lists-values-employee-name"]'>

                    <table class="table mb-0 thead-border-top-0 table-striped">
                        <thead>
                            <tr>

                            <th style="width: 5%;"> # </th>
                            <th style="width: 20%;"> {{ __('Full Name') }} </th>
                            <th style="width: 20%;"> {{ __('Email') }} </th>
                            <th style="width: 15%;"> {{ __('Phone Number') }} </th>
                            <th style="width: 20%;"> {{ __('Action') }} </th>
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
                                        {{ $driver->email }}
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
                                <a href="{{ route('edit-info-request.show', $driver->{"user-id"}) }}" class="btn btn-sm btn-link">
                                    <i class="fa fa-eye fa-2x"></i>
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
