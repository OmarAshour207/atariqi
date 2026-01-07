@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">

        <div class="container-fluid page__container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="#">{{ __('Home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Stat') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Home') }}</h1>
                </div>
            </div>
        </div>


        <div class="container-fluid page__container">

            <div class="card-group">
                <div class="card card-body text-center">
                    <div class="d-flex flex-row align-items-center">
                        <div class="card-header__title m-0">
                            <i class="material-icons icon-muted icon-30pt">assessment</i>
                            {{ __('Total Orders') }}
                        </div>
                        <div class="text-amount ml-auto"> 15 </div>
                    </div>
                </div>
            </div>

            <div class="card-group">
                <div class="card card-body text-center">
                    <div class="d-flex flex-row align-items-center">
                        <div class="card-header__title m-0">
                            <i class="material-icons icon-muted icon-30pt">assessment</i>
                            {{  'Proccess' }}
                        </div>
                        <div class="text-amount ml-auto">15</div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header card-header-large bg-white d-flex align-items-center">
                    <h4 class="card-header__title flex m-0">{{ __('Export Reports in Excel') }}</h4>
                </div>

                <div class="card-form d-flex flex-column flex-sm-row">

                    <form method="post" action="#">

                        @csrf
                        @method('post')

                        <div class="card-form__body card-body-form-group flex">
                            <div class="row">
                                <div class="col-sm-auto">
                                    <div class="form-group">
                                        <label for="filter_name">{{ __('Search') }}</label>
                                        <input id="filter_name" value="{{ request()->get('search') }}" name="search" type="text" class="form-control" placeholder="{{ __('Search by name or Phone Number') }}" style="width: 250px;">
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="form-group">
                                        <label for="filter_category">{{ __('Type') }}</label><br>
                                        <select id="filter_category" class="custom-select" style="width: 150px;" name="type">
                                            <option value="ticket">{{ __('Tickets') }}</option>
                                            <option value="supply">{{ __('Supplies') }}</option>
                                            <option value="request">{{ __('Requests') }}</option>
                                        </select>
                                    </div>
                                </div>
                                @php
                                    // 1 => on hold, 2 => processing, 3 => Delivered, 4 => rejected, 5 => delayed
                                    $status = [
                                        1     => __('On hold'),
                                        2     => __('Under Processing'),
                                        3     => __('Delivered'),
                                        4     => __('Rejected'),
                                        5     => __('Delayed')
                                    ];
                                @endphp
                                <div class="col-sm-auto">
                                    <div class="form-group">
                                        <label for="filter_status">{{ __('Status') }}</label><br>
                                        <select id="filter_status" class="custom-select" style="width: 150px;" name="status">
                                            <option value="all">{{ __('All') }}</option>
                                            @foreach($status as $key => $state)
                                                <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}> {{ $state }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="form-group" style="width: 175px;">
                                        <label for="filter_date">{{ __('From') }}</label>
                                        <input id="filter_date" type="text" name="from" class="form-control date_from" placeholder="{{ __('Select date') }}" value="{{ request()->get('from') ?? '01/01/2023' }}" data-toggle="flatpickr" data-flatpickr-alt-format="d/m/Y" data-flatpickr-date-format="d/m/Y">
                                    </div>
                                </div>
                                <div class="col-sm-auto">
                                    <div class="form-group" style="width: 175px;">
                                        <label for="filter_date_to">{{ __('To') }}</label>
                                        <input id="filter_date_to" type="text" name="to" class="form-control date_to" placeholder="{{ __('Select date') }}" value="{{ request()->get('to') ?? now() }}" data-toggle="flatpickr" data-flatpickr-alt-format="d/m/Y" data-flatpickr-date-format="d/m/Y">
                                    </div>
                                </div>

                                <div class="ml-auto">
                                    <button class="btn btn-primary border-left border-top border-top-sm-0 rounded-top-0 rounded-top-sm rounded-left-sm-0" type="submit" style="margin-top: 27px">
                                        {{ __('Export Data') }} <i class="material-icons">file_download</i>
                                    </button>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header card-header-large bg-white d-flex align-items-center">
                    <h4 class="card-header__title flex m-0">{{ __('Recent Orders') }}</h4>
                </div>

                <div class="table-responsive" data-toggle="lists" data-lists-values='["js-lists-values-employee-name"]'>

                    <table class="table mb-0 thead-border-top-0 table-striped">
                        <thead>
                            <tr>
                                <th style="width: 5%;" class="text-center">#</th>

                                <th style="width: 20%;">{{ __('Username') }}</th>

                                <th style="width: 20%;">
                                    {{ __('Type') }}
                                </th>

                                <th style="width: 15%;">{{ __('Status') }}</th>

                                <th style="width: 20%;">{{ __('Reason in rejection') }}</th>

                                <th style="width: 20%;" class="text-right">
                                    <i class="material-icons icon-16pt text-muted-light mr-1">today</i>
                                    {{ __('Created at') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="list" id="companies">
                            <tr>
                                <td style="width: 5%;" class="text-center">
                                    <div class="badge badge-soft-dark">
                                        <a href="#">
                                            <i class="material-icons icon-muted ml-3">arrow_forward</i>
                                            1
                                        </a>
                                    </div>
                                </td>

                                <td style="width: 20%;">
                                </td>

                                <td style="width: 20%;">
                                </td>

                                <td class="text-center" style="width: 15%;">
                                    <div class="d-flex align-items-center btn btn-info">
                                    </div>
                                </td>
                                <td style="width: 20%;">
                                </td>
                                <td class="text-right" style="width: 20%;">
                                    <strong> </strong>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>

        </div>
        <!-- // END drawer-layout__content -->
    </div>
@endsection
@push('admin_scripts')
    <script>
        $(".date_from").flatpickr({
            locale: "{{ session()->get('locale') }}",
            defaultDate: '{{ request()->get('from') }}'
        });

        $(".date_to").flatpickr({
            locale: "{{ session()->get('locale') }}",
            defaultDate: '{{ request()->get('to') }}'
        });
    </script>
@endpush
