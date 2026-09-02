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
                                                <img src="{{ user_upload_url($oldDriver->id, $oldDriver->image) }}" alt="{{ $oldDriver->{"user-first-name"} }}" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
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
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->{'user-first-name'}, $newDriverInfo->{'user-first-name'}) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Last Name") }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->{'user-last-name'}, $newDriverInfo->{'user-last-name'}) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Email") }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->email, $newDriverInfo->email) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Phone Number") }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->{'phone-no'}, $newDriverInfo->{'phone-no'}) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Gender") }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->gender, $newDriverInfo->gender) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __('University') }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->{'university-id'}, $newDriverInfo->{'university-id'}) ? optional($newDriverInfo->university)->{'name-ar'} : '' }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __('Stage') }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->{'user-stage-id'}, $newDriverInfo->{'user-stage-id'}) ? optional($newDriverInfo->stage)->{'name-ar'} : '' }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    @php($pendingUserImage = pending_image_filename($oldDriver->image, $newDriverInfo->image))
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="new-label">{{ __('User Image') }}</label>
                                                @if($pendingUserImage)
                                                    <img src="{{ user_upload_url($newDriverInfo->{'user-id'}, $pendingUserImage) }}" alt="{{ $newDriverInfo->{'user-first-name'} }}" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                                @else
                                                    <p class="text-muted mb-0">{{ __('No change') }}</p>
                                                @endif
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

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label class="old-label">{{ __('License Image') }}</label>
                                                <a href="{{ user_upload_url($oldDriver->id, $oldDriver->driverInfo?->{'driver-license-link'} ?? $oldDriver->driverCar?->license_img) }}" data-lightbox="old-license-info" data-title="{{ __('License Image') }}">
                                                    <img src="{{ user_upload_url($oldDriver->id, $oldDriver->driverInfo?->{'driver-license-link'} ?? $oldDriver->driverCar?->license_img) }}" alt="{{ __('License Image') }}" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                                </a>
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
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->driverInfo?->{'car-brand'}, $newDriverInfoRecord?->{'car-brand'}) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Car Model") }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->driverInfo?->{'car-model'}, $newDriverInfoRecord?->{'car-model'}) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Car Number") }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->driverInfo?->{'car-number'}, $newDriverInfoRecord?->{'car-number'}) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Car Letters") }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->driverInfo?->{'car-letters'}, $newDriverInfoRecord?->{'car-letters'}) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Car Color") }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->driverInfo?->{'car-color'}, $newDriverInfoRecord?->{'car-color'}) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Driver Rate") }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->driverInfo?->{'driver-rate'}, $newDriverInfoRecord?->{'driver-rate'}) }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $oldLicenseImage = $oldDriver->driverInfo?->{'driver-license-link'} ?? $oldDriver->driverCar?->license_img;
                                        $newLicenseImage = pending_image_filename(
                                            $oldLicenseImage,
                                            $newDriverInfoRecord?->{'driver-license-link'} ?? $newDriverCarRecord?->license_img
                                        );
                                    @endphp
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __('License Image') }}</label>
                                                @if($newLicenseImage)
                                                    <a href="{{ user_upload_url($newDriverInfo->{'user-id'}, $newLicenseImage) }}" data-lightbox="new-license-info" data-title="{{ __('License Image') }}">
                                                        <img src="{{ user_upload_url($newDriverInfo->{'user-id'}, $newLicenseImage) }}" alt="{{ __('License Image') }}" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                                    </a>
                                                @else
                                                    <p class="text-muted mb-0">{{ __('No change') }}</p>
                                                @endif
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
                                        <img src="{{ user_upload_url($oldDriver->id, $oldDriver->driverCar?->{"car_form_img"}) }}" alt="Car Form" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('License Image') }}</label>
                                        <img src="{{ user_upload_url($oldDriver->id, $oldDriver->driverCar?->{"license_img"}) }}" alt="License" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Front Image') }}</label>
                                        <img src="{{ user_upload_url($oldDriver->id, $oldDriver->driverCar?->{"car_front_img"}) }}" alt="Car Front" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Back Image') }}</label>
                                        <img src="{{ user_upload_url($oldDriver->id, $oldDriver->driverCar?->{"car_back_img"}) }}" alt="Car Back" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Right Side Image') }}</label>
                                        <img src="{{ user_upload_url($oldDriver->id, $oldDriver->driverCar?->{"car_rside_img"}) }}" alt="Car Right" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Left Side Image') }}</label>
                                        <img src="{{ user_upload_url($oldDriver->id, $oldDriver->driverCar?->{"car_lside_img"}) }}" alt="Car Left" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Inside Front Image') }}</label>
                                        <img src="{{ user_upload_url($oldDriver->id, $oldDriver->driverCar?->{"car_insideFront_img"}) }}" alt="Inside Front" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>

                                    <div class="form-group">
                                        <label class="old-label">{{ __('Car Inside Back Image') }}</label>
                                        <img src="{{ user_upload_url($oldDriver->id, $oldDriver->driverCar?->{"car_insideBack_img"}) }}" alt="Inside Back" class="img-fluid d-block mb-2" style="max-width: 150px; border-radius: 5px;">
                                    </div>
                                </div>

                                <!-- NEW DATA (HIGHLIGHTED) -->
                                <div class="new-data-section">
                                    <div class="section-title new-label"><i class="material-icons" style="vertical-align: middle; margin-right: 5px;">new_releases</i>{{ __("Requested Changes") }}</div>

                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group highlight-new">
                                                <label class="new-label">{{ __("Driver Type") }}</label>
                                                <input type="text" class="form-control" value="{{ pending_field_value($oldDriver->driverCar?->{'driver-type-id'}, $newDriverCarRecord?->{'driver-type-id'}) ? optional($newDriverCarRecord?->driverType)->{'name-ar'} : '' }}" disabled style="background-color: transparent; border: none;">
                                            </div>
                                        </div>
                                    </div>

                                    @php
                                        $carImageFields = [
                                            'car_form_img' => __('Car Form Image'),
                                            'license_img' => __('License Image'),
                                            'car_front_img' => __('Car Front Image'),
                                            'car_back_img' => __('Car Back Image'),
                                            'car_rside_img' => __('Car Right Side Image'),
                                            'car_lside_img' => __('Car Left Side Image'),
                                            'car_insideFront_img' => __('Car Inside Front Image'),
                                            'car_insideBack_img' => __('Car Inside Back Image'),
                                        ];
                                    @endphp

                                    @foreach($carImageFields as $field => $label)
                                        @php($pendingCarImage = pending_image_filename($oldDriver->driverCar?->{$field}, $newDriverCarRecord?->{$field}))
                                        <div class="form-group">
                                            <label class="new-label">{{ $label }}</label>
                                            @if($pendingCarImage)
                                                <img src="{{ user_upload_url($newDriverInfo->{'user-id'}, $pendingCarImage) }}" alt="{{ $label }}" class="img-fluid d-block mb-2" style="max-width: 150px; border: 2px solid #ffc107; border-radius: 5px;">
                                            @else
                                                <p class="text-muted mb-0">{{ __('No change') }}</p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden field for rejection reason -->
                    <input type="hidden" id="rejection-reason-input" name="rejection-reason" value="">

                    @if($waslEligibility['is_valid'] === false)
                        <div class="alert alert-danger">
                            <strong><i class="fas fa-exclamation-triangle"></i> {{ __('WASL Eligibility: Invalid') }}</strong>
                            @if(!empty($waslEligibility['message']))
                                <br><span>{{ $waslEligibility['message'] }}</span>
                            @endif
                        </div>
                    @elseif($waslEligibility['is_valid'] === true)
                        <div class="alert alert-success">
                            <strong><i class="fas fa-check-circle"></i> {{ __('WASL Eligibility: Valid') }}</strong>
                        </div>
                    @endif

                    <div class="text-right mb-5">
                        @adminCan('decide')
                        @if($waslEligibility['is_valid'] !== false)
                            <button type="submit" name="approval" value="1" class="btn btn-success">{{ __('Accept') }}</button>
                        @endif
                        <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#rejectReasonModal">{{ __('Reject') }}</button>
                        @endadminCan
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
