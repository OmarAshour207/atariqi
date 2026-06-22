@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }} </a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Show Driver') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0"> {{ __('Drivers') }} </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">


            @if ($banned)
                <div class="alert alert-danger">
                    <strong>
                        <i class="fas fa-ban"></i> {{ __('Warning: This driver is banned before!') }}
                    </strong>
                    @if($banned->note)
                        <br>{{ __('Reason') }}: {{ $banned->note }}
                    @endif
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if($driver->approval == 0 && $waslEligibility['is_valid'] !== null)
                <div class="alert alert-{{ $waslEligibility['is_valid'] ? 'success' : 'danger' }}">
                    <strong>{{ __('Ministry Request Status') }}:</strong>
                    {{ $waslEligibility['display_status'] }}
                    @if($waslEligibility['is_valid'])
                        @if(!empty($waslEligibility['driver_expiry_date']))
                            <br><span>{{ __('Driver eligibility expires on') }}: {{ $waslEligibility['driver_expiry_date'] }}</span>
                        @endif
                        @if(!empty($waslEligibility['vehicle_plate']))
                            <br><span>{{ __('Vehicle') }}: {{ $waslEligibility['vehicle_plate'] }}
                                @if(!empty($waslEligibility['vehicle_eligibility']))
                                    ({{ $waslEligibility['vehicle_eligibility'] }})
                                @endif
                            </span>
                            @if(!empty($waslEligibility['vehicle_expiry_date']))
                                <br><span>{{ __('Vehicle eligibility expires on') }}: {{ $waslEligibility['vehicle_expiry_date'] }}</span>
                            @endif
                        @endif
                    @elseif(!empty($waslEligibility['message']))
                        <br><span>{{ $waslEligibility['message'] }}</span>
                    @endif
                </div>
            @endif

            @if($driver->approval == 4)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    {{ __('This driver must update their data on Absher.') }}
                    @if($driver->{'reject-reason'})
                        <br>{{ $driver->{'reject-reason'} }}
                    @endif
                </div>
            @endif

            @if($hasPendingUpdate)
                <div class="alert alert-warning">
                    <i class="fas fa-user-edit"></i> {{ __('This driver has a pending profile update request.') }}
                </div>
            @endif

            @if($driver->approval != 0)
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card card-body text-center">
                            <h6 class="text-muted mb-2">{{ __('Dues') }}</h6>
                            <h3 class="mb-0 text-{{ $currentDues > 50 ? 'danger' : 'success' }}">
                                {{ number_format($currentDues, 2) }} {{ __('SAR') }}
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-body text-center">
                            <h6 class="text-muted mb-2">{{ __('Driver Rate') }}</h6>
                            <h3 class="mb-0">
                                {{ $driver->driverInfo?->{'driver-rate'} ?? __('Not Specified') }}
                            </h3>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-body text-center">
                            <h6 class="text-muted mb-2">{{ __('Update Alert') }}</h6>
                            <h3 class="mb-0">
                                @if($hasPendingUpdate)
                                    <span class="badge badge-warning badge-lg">{{ __('Update Requested') }}</span>
                                @else
                                    <span class="badge badge-success badge-lg">{{ __('No Updates') }}</span>
                                @endif
                            </h3>
                        </div>
                    </div>
                </div>
            @endif

            @if($driver->approval != 0)
                @if($driver->email)
                    <div class="mb-3 text-right">
                        <form action="{{ route('drivers.sendPaymentReminder', $driver->id) }}" method="post" class="d-inline-block" onsubmit="return confirm('{{ __('Send dues reminder to this driver?') }}');">
                            @csrf
                            <button type="submit" class="btn btn-warning">
                                <i class="fa fa-bell"></i> {{ __('Remind') }}
                            </button>
                        </form>
                        <a href="{{ route('drivers.driverTrips', $driver->id) }}" class="btn btn-primary">
                            <i class="fas fa-route"></i> {{ __('View Trips') }}
                        </a>
                        <a href="{{ route('drivers.earnings', $driver->id) }}" class="btn btn-success">
                            <i class="fas fa-coins"></i> {{ __('Driver Earnings') }}
                        </a>
                    </div>
                @else
                    <div class="mb-3 text-right">
                        <a href="{{ route('drivers.driverTrips', $driver->id) }}" class="btn btn-primary">
                            <i class="fas fa-route"></i> {{ __('View Trips') }}
                        </a>
                        <a href="{{ route('drivers.earnings', $driver->id) }}" class="btn btn-success">
                            <i class="fas fa-coins"></i> {{ __('Driver Earnings') }}
                        </a>
                    </div>
                @endif
            @endif

            <div class="card card-form__body card-body">
                <form method="post" action="{{ route('drivers.updateStatus', $driver->id) }}" class="submit-form">

                    @include('dashboard.partials._errors')

                    @csrf
                    @method('post')

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
                        <div class="tab-pane active show fade" id="driver-data">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="user-first-name"> {{ __("First name") }}</label>
                                        <input id="user-first-name" name="user-first-name" dir="auto" type="text" class="form-control" placeholder="{{ __("First Name") }}" value="{{ old("user-first-name", $driver->{"user-first-name"}) }}" disabled>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="user-last-name"> {{ __("Last name") }}</label>
                                        <input id="user-last-name" name="user-last-name" dir="auto" type="text" class="form-control" placeholder="{{ __("Last Name") }}" value="{{ old("user-last-name", $driver->{"user-last-name"}) }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="email"> {{ __("Email") }}</label>
                                        <input id="email" name="email" type="text" dir="auto" class="form-control" placeholder="{{ __("Email") }}" value="{{ old("email", $driver->email) }}" disabled>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="phone"> {{ __("Phone Number") }}</label>
                                        <input id="phone" name="phone-no" dir="auto" type="text" class="form-control" placeholder="{{ __("Phone Number") }}" value="{{ old("phone-no", $driver->{"phone-no"}) }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 form-group">
                                    <label for="gender"> {{ __("Gender") }}</label>
                                    <input id="gender" name="gender" dir="auto" type="text" class="form-control" placeholder="{{ __("Gender") }}" value="{{ old("gender", $driver->gender) }}" disabled>
                                </div>

                                <div class="col-6 form-group">
                                    <label for="identity_number"> {{ __("Identity Number") }}</label>
                                    <input id="identity_number" name="identity_number" dir="auto" type="text" class="form-control" placeholder="{{ __("Identity Number") }}" value="{{ old("identity_number", $driver->driverInfo ? $driver->driverInfo->identity_number : '') }}" disabled>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6 form-group">
                                    <label for="date_of_birth"> {{ __("Date of Birth") }}</label>
                                    <input id="date_of_birth" name="date_of_birth" dir="auto" type="text" class="form-control" placeholder="{{ __("Date of Birth") }}" value="{{ old("date_of_birth", $driver->driverInfo ? $driver->driverInfo->date_of_birth : '') }}" disabled>
                                </div>

                                <div class="col-6 form-group">
                                    <label for="date_of_birth_hajri"> {{ __("Date of Birth (Hijri)") }}</label>
                                    <input id="date_of_birth_hajri" name="date_of_birth_hajri" dir="auto" type="text" class="form-control" placeholder="{{ __("Date of Birth (Hijri)") }}" value="{{ old("date_of_birth_hajri", $driver->driverInfo ? $driver->driverInfo->date_of_birth_hijri : '') }}" disabled>
                                </div>
                            </div>

                            <div class="row no-gutters">
                                <div class="col card-form__body card-body">
                                    <div class="row">
                                        <div class="form-group col">
                                            <label for="university-id">{{ __('University') }}</label>
                                            <select id="university-id" name="university-id" class="form-control" disabled>
                                                @foreach ($universities as $university)
                                                    <option value="{{ $university->id }}"
                                                        {{ old('university-id', $driver->{"university-id"}) == $university->id ? 'selected' : '' }}>
                                                            {{ $university->{"name-ar"} }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col">
                                            <label for="stage-id">{{ __('Stage') }}</label>
                                            <select id="stage-id" name="user-stage-id" class="form-control select2" disabled>
                                                <option value="" selected> {{ __('Stage') }} </option>
                                                @foreach ($stages as $stage)
                                                    <option value="{{ $stage->id }}" {{ old('user-stage-id', $driver->{"user-stage-id"}) == $stage->id ? 'selected' : '' }}> {{ $stage->{"name-ar"} }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-lg-6">
                                <label for="approval"> {{ __('Approval') }}</label> <br>
                                <select id="approval" name="approval" class="form-control select2" disabled>
                                    <option value="" selected> {{ __('Approval') }} </option>
                                    <option value="0" {{ old('approval', $driver->approval) == 0 ? 'selected' : '' }}> {{ __('Pending') }} </option>
                                    <option value="1" {{ old('approval', $driver->approval) == 1 ? 'selected' : '' }}> {{ __('Approved') }} </option>
                                    <option value="2" {{ old('approval', $driver->approval) == 2 ? 'selected' : '' }}> {{ __('Under Review') }} </option>
                                    <option value="3" {{ old('approval', $driver->approval) == 3 ? 'selected' : '' }}> {{ __('Rejected') }} </option>
                                    <option value="4" {{ old('approval', $driver->approval) == 4 ? 'selected' : '' }}> {{ __('Absher Update Required') }} </option>
                                </select>
                            </div>

                            <input type="hidden" id="reject-reason" name="reject-reason" value="{{ old('reject-reason', $driver->{"reject-reason"}) }}">

                            <div class="form-group">
                                <label for="image"> {{ __('User Image') }}</label>
                                @if($driver->image)
                                    <a href="{{ url('uploads/' . $driver->id . '/' . $driver->image) }}" data-lightbox="profile-image" data-title="profile-image">
                                        <img id="img-preview" class="img-fluid d-block mb-2" style="max-width: 150px;" src="{{ url('uploads/' . $driver->id . '/' . $driver->image) }}"/>
                                    </a>
                                @else
                                    <img id="img-preview" src="https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png" width="100px" />
                                @endif
                            </div>
                        </div>

                        <div class="tab-pane show fade" id="driver-info">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car-brand"> {{ __("Car Brand") }}</label>
                                        <input id="car-brand" name="car-brand" dir="auto" type="text" class="form-control" placeholder="{{ __("Car Brand") }}" value="{{ old("car-brand", $driver->driverInfo ? $driver->driverInfo->{"car-brand"} : '') }}" disabled>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car-model"> {{ __("Car Model") }}</label>
                                        <input id="car-model" name="car-model" dir="auto" type="text" class="form-control" placeholder="{{ __("Car Model") }}" value="{{ old("car-model", $driver->driverInfo ? $driver->driverInfo->{"car-model"} : '') }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car-number"> {{ __("Car Number") }}</label>
                                        <input id="car-number" name="car-number" dir="auto" type="text" class="form-control" placeholder="{{ __("Car Number") }}" value="{{ old("car-number", $driver->driverInfo ? $driver->driverInfo->{"car-number"} : '') }}" disabled>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car-letters"> {{ __("Car Letters") }}</label>
                                        <input id="car-letters" name="car-letters" dir="auto" type="text" class="form-control" placeholder="{{ __("Car Letters") }}" value="{{ old("car-letters", $driver->driverInfo ? $driver->driverInfo->{"car-letters"} : '') }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car-color"> {{ __("Car Color") }}</label>
                                        <input id="car-color" name="car-color" dir="auto" type="text" class="form-control" placeholder="{{ __("Car Color") }}" value="{{ old("car-color", $driver->driverInfo ? $driver->driverInfo->{"car-color"} : '') }}" disabled>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="driver-rate"> {{ __("Driver Rate") }}</label>
                                        <input id="driver-rate" name="driver-rate" dir="auto" type="text" class="form-control" placeholder="{{ __("Driver Rate") }}" value="{{ old("driver-rate", $driver->driverInfo ? $driver->driverInfo->{"driver-rate"} : '') }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="sequence-number"> {{ __("Sequence Number") }}</label>
                                <input id="sequence-number" name="sequence-number" dir="auto" type="text" class="form-control" placeholder="{{ __("Sequence Number") }}" value="{{ old("sequence-number", $driver->driverInfo ? $driver->driverInfo->{"sequence-number"} : '') }}" disabled>
                            </div>

                            @if($driver->approval != 0)
                                <div class="form-group">
                                    <label for="neighborhoods-from"> {{ __('Neighborhoods From') }}</label>
                                    <input id="neighborhoods-from" type="text" class="form-control" value="{{ $neighborhoodFromNames }}" disabled>
                                </div>

                                <div class="form-group">
                                    <label for="neighborhoods-to"> {{ __('Neighborhoods To') }}</label>
                                    <input id="neighborhoods-to" type="text" class="form-control" value="{{ $neighborhoodToNames }}" disabled>
                                </div>
                            @endif

                            <div class="card mb-3">
                                <div class="card-header">
                                    <strong>{{ __('Current Subscription') }}</strong>
                                </div>
                                <div class="card-body">
                                    @if($currentUserPackage)
                                        <div class="row">
                                            <div class="col-md-6 form-group">
                                                <label>{{ __('Package') }}</label>
                                                <input type="text" class="form-control" value="{{ $currentUserPackage->package?->name_ar ?? $currentUserPackage->package?->name_en ?? __('Not Specified') }}" disabled>
                                            </div>
                                            <div class="col-md-6 form-group">
                                                <label>{{ __('Interval') }}</label>
                                                <input type="text" class="form-control" value="{{ $currentUserPackage->interval ?? '-' }}" disabled>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('Start Date') }}</label>
                                                <input type="text" class="form-control" value="{{ $currentUserPackage->start_date ? \Carbon\Carbon::parse($currentUserPackage->start_date)->format('Y-m-d') : '-' }}" disabled>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('End Date') }}</label>
                                                <input type="text" class="form-control" value="{{ $currentUserPackage->end_date ? \Carbon\Carbon::parse($currentUserPackage->end_date)->format('Y-m-d') : '-' }}" disabled>
                                            </div>
                                            <div class="col-md-4 form-group">
                                                <label>{{ __('Status') }}</label>
                                                <input type="text" class="form-control" value="{{
                                                    match($currentUserPackage->status) {
                                                        \App\Models\UserPackage::STATUS_ACTIVE => __('Active'),
                                                        \App\Models\UserPackage::STATUS_EXPIRED => __('Expired'),
                                                        \App\Models\UserPackage::STATUS_CANCELLED => __('Cancelled'),
                                                        default => __('Unknown'),
                                                    }
                                                }}" disabled>
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-muted mb-0">{{ __('None') }}</p>
                                    @endif
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="driver-wasl-status"> {{ __("Driver Wasl Status") }}</label>
                                        <input id="driver-wasl-status" name="driver-wasl-status" dir="auto" type="text" class="form-control" placeholder="{{ __("Driver Wasl Status") }}" value="{{ $waslEligibility['driver_eligibility'] ?? $waslEligibility['display_status'] ?? __('Unknown') }}" disabled>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="driver-wasl-reason"> {{ __("Driver Wasl Reason") }}</label>
                                        <input id="driver-wasl-reason" name="driver-wasl-reason" dir="auto" type="text" class="form-control" value="{{ $waslEligibility['is_valid'] && !empty($waslEligibility['driver_expiry_date']) ? $waslEligibility['driver_expiry_date'] : ($waslEligibility['message'] ?? __('Unknown')) }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="vehicle-wasl-status"> {{ __("Vehicle Wasl Status") }}</label>
                                        <input id="vehicle-wasl-status" name="vehicle-wasl-status" dir="auto" type="text" class="form-control" placeholder="{{ __("Vehicle Wasl Status") }}" value="{{ $waslEligibility['vehicle_eligibility'] ?? __('Unknown') }}" disabled>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="vehicle-reason"> {{ __("Vehicle Wasl Reason") }}</label>
                                        <input id="vehicle-reason" name="vehicle-reason" dir="auto" type="text" class="form-control" placeholder="{{ __("Vehicle Wasl Reason") }}" value="{{ $waslEligibility['is_valid'] && !empty($waslEligibility['vehicle_expiry_date']) ? $waslEligibility['vehicle_expiry_date'] : ($waslEligibility['message'] ?? __('Unknown')) }}" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane show fade" id="driver-car">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="driver-type"> {{ __("Driver Type") }}</label>
                                        <select id="driver-type" name="driver-type-id" class="form-control select2" disabled>
                                            <option value=""> {{ __('Driver Type') }} </option>
                                            @foreach ($driverTypes as $driverType)
                                                <option value="{{ $driverType->id }}" {{ old('driver-type-id', $driver->driverCar ? $driver->driverCar->{"driver-type-id"} : '') == $driverType->id ? 'selected' : '' }}> {{ $driverType->{"name-ar"} }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="car_form_img"> {{ __('Car Form Image') }}</label>
                                @if($driver->driverCar && $driver->driverCar->{"car_form_img"})
                                    <a href="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_form_img"}) }}" data-lightbox="car-form-image" data-title="{{ __('Car Form Image') }}">
                                        <img id="car-form-img" class="img-fluid d-block mb-2" style="max-width: 150px;" src="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_form_img"}) }}"/>
                                    </a>
                                @else
                                    <img id="car-form-img" src="https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png" width="100px" />
                                @endif
                            </div>

                            <div class="form-group">
                                <label for="license_img"> {{ __('License Image') }}</label>
                                @if($driver->driverCar && $driver->driverCar->{"license_img"})
                                    <a href="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"license_img"}) }}" data-lightbox="license-image" data-title="{{ __('License Image') }}">
                                        <img id="license-img" class="img-fluid d-block mb-2" style="max-width: 150px;" src="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"license_img"}) }}"/>
                                    </a>
                                @else
                                    <img id="license-img" src="https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png" width="100px" />
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_front_img"> {{ __('Car Front Image') }}</label>
                                        @if($driver->driverCar && $driver->driverCar->{"car_front_img"})
                                            <a href="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_front_img"}) }}" data-lightbox="car-front-image" data-title="{{ __('Car Front Image') }}">
                                                <img id="car-front-img" class="img-fluid d-block mb-2" style="max-width: 150px;" src="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_front_img"}) }}"/>
                                            </a>
                                        @else
                                            <img id="car-front-img" src="https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png" width="100px" />
                                        @endif
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_back_img"> {{ __('Car Back Image') }}</label>
                                        @if($driver->driverCar && $driver->driverCar->{"car_back_img"})
                                            <a href="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_back_img"}) }}" data-lightbox="car-back-image" data-title="{{ __('Car Back Image') }}">
                                                <img id="car-back-img" class="img-fluid d-block mb-2" style="max-width: 150px;" src="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_back_img"}) }}"/>
                                            </a>
                                        @else
                                            <img id="car-back-img" src="https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png" width="100px" />
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_rside_img"> {{ __('Car Right Side Image') }}</label>
                                        @if($driver->driverCar && $driver->driverCar->{"car_rside_img"})
                                            <a href="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_rside_img"}) }}" data-lightbox="car-right-side-image" data-title="{{ __('Car Right Side Image') }}">
                                                <img id="car-rside-img" class="img-fluid d-block mb-2" style="max-width: 150px;" src="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_rside_img"}) }}"/>
                                            </a>
                                        @else
                                            <img id="car-rside-img" src="https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png" width="100px" />
                                        @endif
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_lside_img"> {{ __('Car Left Side Image') }}</label>
                                        @if($driver->driverCar && $driver->driverCar->{"car_lside_img"})
                                            <a href="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_lside_img"}) }}" data-lightbox="car-left-side-image" data-title="{{ __('Car Left Side Image') }}">
                                                <img id="car-lside-img" class="img-fluid d-block mb-2" style="max-width: 150px;" src="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_lside_img"}) }}"/>
                                            </a>
                                        @else
                                            <img id="car-lside-img" src="https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png" width="100px" />
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_insideFront_img"> {{ __('Car Inside Front Image') }}</label>
                                        @if($driver->driverCar && $driver->driverCar->{"car_insideFront_img"})
                                            <a href="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_insideFront_img"}) }}" data-lightbox="car-inside-front-image" data-title="{{ __('Car Inside Front Image') }}">
                                                <img id="car-inside-front-img" class="img-fluid d-block mb-2" style="max-width: 150px;" src="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_insideFront_img"}) }}"/>
                                            </a>
                                        @else
                                            <img id="car-inside-front-img" src="https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png" width="100px" />
                                        @endif
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_insideBack_img"> {{ __('Car Inside Back Image') }}</label>
                                        @if($driver->driverCar && $driver->driverCar->{"car_insideBack_img"})
                                            <a href="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_insideBack_img"}) }}" data-lightbox="car-inside-back-image" data-title="{{ __('Car Inside Back Image') }}">
                                                <img id="car-inside-back-img" class="img-fluid d-block mb-2" style="max-width: 150px;" src="{{ url('uploads/' . $driver->id . '/' . $driver->driverCar->{"car_insideBack_img"}) }}"/>
                                            </a>
                                        @else
                                            <img id="car-inside-back-img" src="https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png" width="100px" />
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($driver->approval == 0)
                        <div class="text-right mb-5">
                            @if($waslEligibility['is_valid'] !== false)
                                <button type="submit" name="approval" value="1" class="btn btn-success">{{ __('Accept') }}</button>
                            @endif
                            <button type="button" class="btn btn-primary" onclick="showAssignModal()">{{ __('Assign') }}</button>
                            <button type="button" class="btn btn-danger" onclick="showRejectModal()">{{ __('Reject') }}</button>
                        </div>
                    @endif

                    @if($driver->driverInfo && is_numeric($driver->driverInfo->{"driver-rate"}) && floatval($driver->driverInfo->{"driver-rate"}) < 1)
                        <div class="text-right mb-5">
                            <button type="button" class="btn btn-danger" onclick="showBanModal()">{{ __('Ban Driver') }}</button>
                        </div>
                    @endif

                </form>
            </div>
        </div>
        <!-- // END drawer-layout__content -->
    </div>

    <!-- Reject Reason Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-labelledby="rejectModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="rejectModalLabel">{{ __('Reject Driver') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="modal-reject-reason">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                        <textarea id="modal-reject-reason" class="form-control" rows="4" placeholder="{{ __('Enter the reason for rejecting this driver') }}"></textarea>
                        <small class="form-text text-muted">{{ __('Please provide a clear reason for the rejection') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" onclick="confirmRejectDriver()">{{ __('Confirm Rejection') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Assign Driver Modal -->
    <div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="assignModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="assign-form" action="{{ route('drivers.assignToAdmin', $driver->id) }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="assignModalLabel">{{ __('Assign Driver') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="assign-note">{{ __('Assignment Note') }} <span class="text-danger">*</span></label>
                            <textarea id="assign-note" name="assign_note" class="form-control" rows="4" placeholder="{{ __('Enter a note for this assignment') }}"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="assigned-admin">{{ __('Assign To Admin') }} <span class="text-danger">*</span></label>
                            <select id="assigned-admin" name="assigned_admin" class="form-control select2">
                                @foreach($admins as $admin)
                                    <option value="{{ $admin->id }}">{{ $admin->name }} @if($admin->email) ({{ $admin->email }}) @endif</option>
                                @endforeach
                            </select>
                            <small class="form-text text-muted">{{ __('Select an admin to assign this driver to.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="button" class="btn btn-primary" onclick="confirmAssignDriver()">{{ __('Ok') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Ban Driver Modal -->
    <div class="modal fade" id="banModal" tabindex="-1" role="dialog" aria-labelledby="banModalLabel" aria-hidden="true" style="z-index: 9999;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="ban-form" action="{{ route('drivers.ban', $driver->id) }}" method="post">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="banModalLabel">{{ __('Ban Driver') }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="ban-reason">{{ __('Ban Reason') }} <span class="text-danger">*</span></label>
                            <textarea id="ban-reason" name="ban_reason" class="form-control" rows="4" placeholder="{{ __('Enter the reason for banning this driver') }}"></textarea>
                            <small class="form-text text-muted">{{ __('Provide a reason for this ban.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                        <button type="button" class="btn btn-danger" onclick="confirmBanDriver()">{{ __('Confirm Ban') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .modal-backdrop {
            z-index: 9998 !important;
            pointer-events: none !important;
            background-color: transparent !important;
        }
        .modal-backdrop.show {
            opacity: 0 !important;
            z-index: 9998 !important;
            pointer-events: none !important;
        }
        .modal.show {
            z-index: 9999 !important;
            pointer-events: auto !important;
        }
        .modal.show .modal-dialog {
            pointer-events: auto !important;
        }
    </style>

    <script>
        function showRejectModal() {
            const modal = document.getElementById('rejectModal');
            if (modal) {
                $('#rejectModal').modal('show');
                document.getElementById('modal-reject-reason').value = '';
                document.getElementById('modal-reject-reason').focus();
            }
        }

        function showBanModal() {
            const modal = document.getElementById('banModal');
            if (modal) {
                $('#banModal').modal('show');
                document.getElementById('ban-reason').value = '';
                document.getElementById('ban-reason').focus();
            }
        }

        function confirmRejectDriver() {
            const reason = document.getElementById('modal-reject-reason').value.trim();

            if (!reason) {
                alert('{{ __("Please enter a rejection reason") }}');
                document.getElementById('modal-reject-reason').focus();
                return false;
            }

            // Fill the hidden field with the reason
            document.getElementById('reject-reason').value = reason;

            // Set approval to 3 (rejected)
            const approvalInput = document.createElement('input');
            approvalInput.type = 'hidden';
            approvalInput.name = 'approval';
            approvalInput.value = '3';

            // Get the form and submit it
            const form = document.querySelector('.submit-form');
            form.appendChild(approvalInput);

            // Close modal and submit
            $('#rejectModal').modal('hide');
            form.submit();
        }

        function showAssignModal() {
            const modal = document.getElementById('assignModal');
            if (modal) {
                $('#assignModal').modal('show');
                document.getElementById('assign-note').value = '';
                const assignedAdmins = document.getElementById('assigned-admin');
                if (assignedAdmins) {
                    Array.from(assignedAdmins.options).forEach(option => option.selected = false);
                    if (window.$ && $.fn.select2) {
                        $('#assigned-admin').val([]).trigger('change');
                    }
                }
                document.getElementById('assign-note').focus();
            }
        }

        function confirmAssignDriver() {
            const note = document.getElementById('assign-note').value.trim();
            const assignedAdmins = document.getElementById('assigned-admin');
            const selectedAdmins = assignedAdmins ? Array.from(assignedAdmins.selectedOptions).map(option => option.value) : [];

            if (!note) {
                alert('{{ __("Please enter an assignment note") }}');
                document.getElementById('assign-note').focus();
                return false;
            }

            if (!selectedAdmins.length) {
                alert('{{ __("Please select at least one admin") }}');
                assignedAdmins.focus();
                return false;
            }

            document.getElementById('assign-form').submit();
        }

        function confirmBanDriver() {
            const reason = document.getElementById('ban-reason').value.trim();

            if (!reason) {
                alert('{{ __("Please enter a ban reason") }}');
                document.getElementById('ban-reason').focus();
                return false;
            }

            // Fill the hidden field with the reason
            document.getElementById('ban-reason').value = reason;

            // Set approval to 4 (banned)
            const approvalInput = document.createElement('input');
            approvalInput.type = 'hidden';
            approvalInput.name = 'approval';
            approvalInput.value = '4';

            // Get the form and submit it
            const form = document.querySelector('.submit-form');
            form.appendChild(approvalInput);

            // Close modal and submit
            $('#banModal').modal('hide');
            form.submit();
        }

        function confirmAssignDriver() {
            const note = document.getElementById('assign-note').value.trim();
            const assignedAdmins = document.getElementById('assigned-admin');
            const selectedAdmins = assignedAdmins ? Array.from(assignedAdmins.selectedOptions).map(option => option.value) : [];

            if (!note) {
                alert('{{ __("Please enter an assignment note") }}');
                document.getElementById('assign-note').focus();
                return false;
            }

            if (!selectedAdmins.length) {
                alert('{{ __("Please select at least one admin") }}');
                assignedAdmins.focus();
                return false;
            }

            document.getElementById('assign-form').submit();
        }
    </script>
@endsection
