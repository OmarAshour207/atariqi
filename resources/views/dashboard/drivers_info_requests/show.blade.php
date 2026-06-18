@extends('dashboard.layouts.app')

@section('content')
    <style>
        .highlight-new {
            background-color: #fff3cd !important;
            border-left: 4px solid #ffc107 !important;
        }
        .comparison-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .old-data-section, .new-data-section {
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e0e0e0;
        }
        .old-data-section {
            background-color: #f8f9fa;
        }
        .new-data-section {
            background-color: #fff3cd;
            border: 2px solid #ffc107;
        }
        .section-title {
            font-weight: 600;
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dee2e6;
        }
        .old-label { color: #6c757d; font-weight: 500; }
        .new-label { color: #856404; font-weight: 600; }
        @media (max-width: 1024px) {
            .comparison-container {
                grid-template-columns: 1fr;
            }
        }

        /* Ensure modal sits above other layout layers (fix typing being blocked) */
        .modal {
            z-index: 200000 !important;
            pointer-events: auto !important;
        }
        .modal-backdrop {
            z-index: 199999 !important;
        }
    </style>
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }} </a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Driver Info Request') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0"> {{ __('Edit Driver Info Request') }} </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">

            <div class="card card-form__body card-body">
                <form id="edit-info-form" method="post" action="{{ route('edit-info-request.update', ['edit_info_request' => $newDriverInfo->{"user-id"}]) }}">

                    @csrf
                    @method('put')

                    @include('dashboard.partials._errors')

                    <div class="card-header card-header-tabs-basic nav" role="tablist">
                        <a href="#driver-data" class="active" data-toggle="tab" role="tab" aria-controls="step1" aria-selected="true">
                            {{ __('Driver Data') }} <br>
                        </a>
                        <a href="#driver-info" data-toggle="tab" role="tab" aria-selected="false">
                            {{ __('Driver Info') }} <br>
                        </a>
                        <a href="#driver-car" data-toggle="tab" role="tab" aria-selected="false">
                            {{ __('Driver Car') }} <br>
                        </a>
                    </div>

                    <div class="card-body tab-content">
                        <!-- Driver Data Tab -->
                        <div class="tab-pane active show fade" id="driver-data">
                            <div class="comparison-container">
                                <!-- OLD DATA -->
                                <div class="old-data-section">
                                    <div class="section-title old-label"><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">history</i>{{ __("Current Data") }}</div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("First Name") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->{"user-first-name"} }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Last Name") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->{"user-last-name"} }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Email") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->email }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Phone Number") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->{"phone-no"} }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Gender") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->gender }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __('University') }}</label>
                                                <input type="text" class="form-control" value="{{ optional($oldDriver->university)->{"name-ar"} }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __('Stage') }}</label>
                                                <input type="text" class="form-control" value="{{ optional($oldDriver->stage)->{"name-ar"} }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __('User Image') }}</label>
                                                <img src="{{ $oldDriver->image ? url('uploads/' . $oldDriver->id . '/' . $oldDriver->image) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $oldDriver->{"user-first-name"} }}" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- NEW DATA (HIGHLIGHTED) -->
                                <div class="new-data-section">
                                    <div class="section-title new-label"><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">new_releases</i>{{ __("Requested Changes") }}</div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("First Name") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfo->{"user-first-name"} }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Last Name") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfo->{"user-last-name"} }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Email") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfo->email }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Phone Number") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfo->{"phone-no"} }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Gender") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfo->gender }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __('University') }}</label>
                                                <input type="text" class="form-control" value="{{ optional($newDriverInfo->university)->{"name-ar"} }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __('Stage') }}</label>
                                                <input type="text" class="form-control" value="{{ optional($newDriverInfo->stage)->{"name-ar"} }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="new-label">{{ __('User Image') }}</label>
                                                <img src="{{ $newDriverInfo->image ? url('uploads/' . $newDriverInfo->{"user-id"} . '/' . $newDriverInfo->image) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $newDriverInfo->{"user-first-name"} }}" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Driver Info Tab -->
                        <div class="tab-pane show fade" id="driver-info">
                            <div class="comparison-container">
                                <!-- OLD DATA -->
                                <div class="old-data-section">
                                    <div class="section-title old-label"><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">history</i>{{ __("Current Data") }}</div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Car Brand") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->driverInfo ? $oldDriver->driverInfo->{"car-brand"} : '' }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Car Model") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->driverInfo ? $oldDriver->driverInfo->{"car-model"} : '' }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Car Number") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->driverInfo ? $oldDriver->driverInfo->{"car-number"} : '' }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Car Letters") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->driverInfo ? $oldDriver->driverInfo->{"car-letters"} : '' }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Car Color") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->driverInfo ? $oldDriver->driverInfo->{"car-color"} : '' }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Driver Rate") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->driverInfo ? $oldDriver->driverInfo->{"driver-rate"} : '' }}" disabled>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- NEW DATA (HIGHLIGHTED) -->
                                <div class="new-data-section">
                                    <div class="section-title new-label"><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">new_releases</i>{{ __("Requested Changes") }}</div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Car Brand") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfoRecord?->{'car-brand'} ?? '' }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Car Model") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfoRecord?->{'car-model'} ?? '' }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Car Number") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfoRecord?->{'car-number'} ?? '' }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Car Letters") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfoRecord?->{'car-letters'} ?? '' }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Car Color") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfoRecord?->{'car-color'} ?? '' }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Driver Rate") }}</label>
                                                <input type="text" class="form-control" value="{{ $newDriverInfoRecord?->{'driver-rate'} ?? '' }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Driver Car Tab -->
                        <div class="tab-pane show fade" id="driver-car">
                            <div class="comparison-container">
                                <!-- OLD DATA -->
                                <div class="old-data-section">
                                    <div class="section-title old-label"><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">history</i>{{ __("Current Data") }}</div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __("Driver Type") }}</label>
                                                <input type="text" class="form-control" value="{{ $oldDriver->driverCar ? optional($oldDriver->driverCar->driverType)->{"name-ar"} : '' }}" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Form Image') }}</label>
                                        <img src="{{ $oldDriver->driverCar && $oldDriver->driverCar->{"car_form_img"} ? url('uploads/' . $oldDriver->id . '/' . $oldDriver->driverCar->{"car_form_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Car Form" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('License Image') }}</label>
                                        <img src="{{ $oldDriver->driverCar && $oldDriver->driverCar->{"license_img"} ? url('uploads/' . $oldDriver->id . '/' . $oldDriver->driverCar->{"license_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="License" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Front Image') }}</label>
                                        <img src="{{ $oldDriver->driverCar && $oldDriver->driverCar->{"car_front_img"} ? url('uploads/' . $oldDriver->id . '/' . $oldDriver->driverCar->{"car_front_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Car Front" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Back Image') }}</label>
                                        <img src="{{ $oldDriver->driverCar && $oldDriver->driverCar->{"car_back_img"} ? url('uploads/' . $oldDriver->id . '/' . $oldDriver->driverCar->{"car_back_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Car Back" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Right Side Image') }}</label>
                                        <img src="{{ $oldDriver->driverCar && $oldDriver->driverCar->{"car_rside_img"} ? url('uploads/' . $oldDriver->id . '/' . $oldDriver->driverCar->{"car_rside_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Car Right" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Left Side Image') }}</label>
                                        <img src="{{ $oldDriver->driverCar && $oldDriver->driverCar->{"car_lside_img"} ? url('uploads/' . $oldDriver->id . '/' . $oldDriver->driverCar->{"car_lside_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Car Left" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Inside Front Image') }}</label>
                                        <img src="{{ $oldDriver->driverCar && $oldDriver->driverCar->{"car_insideFront_img"} ? url('uploads/' . $oldDriver->id . '/' . $oldDriver->driverCar->{"car_insideFront_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Inside Front" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Inside Back Image') }}</label>
                                        <img src="{{ $oldDriver->driverCar && $oldDriver->driverCar->{"car_insideBack_img"} ? url('uploads/' . $oldDriver->id . '/' . $oldDriver->driverCar->{"car_insideBack_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Inside Back" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>
                                </div>

                                <!-- NEW DATA (HIGHLIGHTED) -->
                                <div class="new-data-section">
                                    <div class="section-title new-label"><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">new_releases</i>{{ __("Requested Changes") }}</div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Driver Type") }}</label>
                                                <input type="text" class="form-control" value="{{ optional($newDriverCarRecord?->driverType)->{"name-ar"} ?? '' }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="new-label">{{ __('Car Form Image') }}</label>
                                        <img src="{{ $newDriverCarRecord && $newDriverCarRecord->{"car_form_img"} ? url('uploads/' . $newDriverInfo->{"user-id"} . '/' . $newDriverCarRecord->{"car_form_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Car Form" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="new-label">{{ __('License Image') }}</label>
                                        <img src="{{ $newDriverCarRecord && $newDriverCarRecord->{"license_img"} ? url('uploads/' . $newDriverInfo->{"user-id"} . '/' . $newDriverCarRecord->{"license_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="License" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="new-label">{{ __('Car Front Image') }}</label>
                                        <img src="{{ $newDriverCarRecord && $newDriverCarRecord->{"car_front_img"} ? url('uploads/' . $newDriverInfo->{"user-id"} . '/' . $newDriverCarRecord->{"car_front_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Car Front" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="new-label">{{ __('Car Back Image') }}</label>
                                        <img src="{{ $newDriverCarRecord && $newDriverCarRecord->{"car_back_img"} ? url('uploads/' . $newDriverInfo->{"user-id"} . '/' . $newDriverCarRecord->{"car_back_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Car Back" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="new-label">{{ __('Car Right Side Image') }}</label>
                                        <img src="{{ $newDriverCarRecord && $newDriverCarRecord->{"car_rside_img"} ? url('uploads/' . $newDriverInfo->{"user-id"} . '/' . $newDriverCarRecord->{"car_rside_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Car Right" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="new-label">{{ __('Car Left Side Image') }}</label>
                                        <img src="{{ $newDriverCarRecord && $newDriverCarRecord->{"car_lside_img"} ? url('uploads/' . $newDriverInfo->{"user-id"} . '/' . $newDriverCarRecord->{"car_lside_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Car Left" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="new-label">{{ __('Car Inside Front Image') }}</label>
                                        <img src="{{ $newDriverCarRecord && $newDriverCarRecord->{"car_insideFront_img"} ? url('uploads/' . $newDriverInfo->{"user-id"} . '/' . $newDriverCarRecord->{"car_insideFront_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Inside Front" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="new-label">{{ __('Car Inside Back Image') }}</label>
                                        <img src="{{ $newDriverCarRecord && $newDriverCarRecord->{"car_insideBack_img"} ? url('uploads/' . $newDriverInfo->{"user-id"} . '/' . $newDriverCarRecord->{"car_insideBack_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="Inside Back" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden field for rejection reason -->
                    <input type="hidden" id="rejection-reason-input" name="rejection-reason" value="">

                    <div class="text-right mb-5">
                        <button type="submit" name="approval" value="1" class="btn btn-success">{{ __('Accept') }}</button>
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectReasonModal">{{ __('Reject') }}</button>
                    </div>

                </form>
            </div>
        </div>
        <!-- // END drawer-layout__content -->
    </div>

@push('admin_scripts')
    <!-- Rejection Reason Modal (moved to layout stack to avoid overlay issues) -->
    <div class="modal fade" id="rejectReasonModal" tabindex="-1" role="dialog" aria-labelledby="rejectReasonModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectReasonModalLabel">{{ __('Rejection Reason') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="rejectForm">
                        <div class="form-group">
                            <label for="reason">{{ __('Please provide a reason for rejection') }}</label>
                            <textarea class="form-control" id="reason" name="reason" rows="4" placeholder="{{ __('Enter rejection reason...') }}" required></textarea>
                            <small class="form-text text-muted">{{ __('This reason will be sent to the driver.') }}</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmRejectBtn">{{ __('Confirm Rejection') }}</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(function(){
            var $reasonInput = $('#reason');
            var $rejectionReasonHidden = $('#rejection-reason-input');
            var $confirmBtn = $('#confirmRejectBtn');
            var $modal = $('#rejectReasonModal');
            var $mainForm = $('#edit-info-form');

            $confirmBtn.on('click', function(){
                var reason = $reasonInput.val() ? $reasonInput.val().trim() : '';
                if(!reason){
                    alert('{{ __('Please enter a rejection reason') }}');
                    return;
                }

                // set hidden field
                $rejectionReasonHidden.val(reason);

                // ensure hidden approval input exists and set to 2
                var $approvalInput = $mainForm.find('input[name="approval"][type="hidden"]');
                if($approvalInput.length === 0){
                    $approvalInput = $('<input>').attr({type: 'hidden', name: 'approval'}).appendTo($mainForm);
                }
                $approvalInput.val('3');

                // submit form
                $mainForm.submit();

                // hide modal
                $modal.modal('hide');
            });

            $modal.on('hidden.bs.modal', function(){
                $reasonInput.val('');
            });
        });
    </script>
@endpush
@endsection
