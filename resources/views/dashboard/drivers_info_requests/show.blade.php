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
                    <h1 class="m-0"> {{ __('Edit Driver Info Request') }} </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">

            <div class="card card-form__body card-body">
                <form method="post" action="{{ route('edit-info-request.update', ['edit_info_request' => $newDriverInfo->{"user-id"}]) }}">

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
                        <div class="tab-pane active show fade" id="driver-data">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="user-first-name"> {{ __("First Name") }}</label>
                                        <input id="user-first-name" name="user-first-name" dir="auto" type="text" class="form-control" placeholder="{{ __("First Name") }}" value="{{ old("user-first-name", $newDriverInfo->{"user-first-name"}) }}" disabled>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="user-last-name"> {{ __("Last Name") }}</label>
                                        <input id="user-last-name" name="user-last-name" dir="auto" type="text" class="form-control" placeholder="{{ __("Last Name") }}" value="{{ old("user-last-name", $newDriverInfo->{"user-last-name"}) }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="email"> {{ __("Email") }}</label>
                                        <input id="email" name="email" type="text" dir="auto" class="form-control" placeholder="{{ __("Email") }}" value="{{ old("email", $newDriverInfo->email) }}" disabled>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="phone"> {{ __("Phone Number") }}</label>
                                        <input id="phone" name="phone-no" dir="auto" type="text" class="form-control" placeholder="{{ __("Phone Number") }}" value="{{ old("phone-no", $newDriverInfo->{"phone-no"}) }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="gender"> {{ __("Gender") }}</label>
                                <input id="gender" name="gender" dir="auto" type="text" class="form-control" placeholder="{{ __("Gender") }}" value="{{ old("gender", $newDriverInfo->gender) }}" disabled>
                            </div>

                            <div class="row no-gutters">
                                <div class="col card-form__body card-body">
                                    <div class="row">
                                        <div class="form-group col">
                                            <label for="university-id">{{ __('University') }}</label>
                                            <select id="university-id" name="university-id" data-toggle="select" class="form-control select2" disabled>
                                                <option value="" selected> {{ __('University') }} </option>
                                                @foreach ($universities as $university)
                                                    <option value="{{ $university->id }}" {{ old('university-id', $newDriverInfo->{"university-id"}) == $university->id ? 'selected' : '' }}> {{ $university->{"name-ar"} }} </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="form-group col">
                                            <label for="stage-id">{{ __('Stage') }}</label>
                                            <select id="stage-id" name="user-stage-id" data-toggle="select" class="form-control select2" disabled>
                                                <option value="" selected> {{ __('Stage') }} </option>
                                                @foreach ($stages as $stage)
                                                    <option value="{{ $stage->id }}" {{ old('user-stage-id', $newDriverInfo->{"user-stage-id"}) == $stage->id ? 'selected' : '' }}> {{ $stage->{"name-ar"} }} </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="form-group col-lg-6">
                                <label for="approval"> {{ __('Approval') }}</label> <br>
                                <select id="approval" name="approval" data-toggle="select" class="form-control select2" disabled>
                                    <option value="" selected> {{ __('approval') }} </option>
                                    <option value="0" {{ old('approval', $newDriverInfo->approval) == 0 ? 'selected' : '' }}> {{ __('Pending') }} </option>
                                    <option value="1" {{ old('approval', $newDriverInfo->approval) == 1 ? 'selected' : '' }}> {{ __('Approved') }} </option>
                                    <option value="2" {{ old('approval', $newDriverInfo->approval) == 2 ? 'selected' : '' }}> {{ __('Rejected') }} </option>
                                </select>
                            </div> -->

                            <div class="form-group">
                                <label for="image"> {{ __('User Image') }}</label>
                                <img src="{{ $newDriverInfo->image ? url($newDriverInfo->image) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $newDriverInfo->fullName }}" class="img-fluid d-block mb-2" style="max-width: 150px;">
                            </div>
                        </div>

                        <div class="tab-pane show fade" id="driver-info">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car-brand"> {{ __("Car Brand") }}</label>
                                        <input id="car-brand" name="car-brand" dir="auto" type="text" class="form-control" placeholder="{{ __("Car Brand") }}" value="{{ old("car-brand", $newDriverInfo->driverInfo ? $newDriverInfo->driverInfo->{"car-brand"} : '') }}" disabled>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car-model"> {{ __("Car Model") }}</label>
                                        <input id="car-model" name="car-model" dir="auto" type="text" class="form-control" placeholder="{{ __("Car Model") }}" value="{{ old("car-model", $newDriverInfo->driverInfo ? $newDriverInfo->driverInfo->{"car-model"} : '') }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car-number"> {{ __("Car Number") }}</label>
                                        <input id="car-number" name="car-number" dir="auto" type="text" class="form-control" placeholder="{{ __("Car Number") }}" value="{{ old("car-number", $newDriverInfo->driverInfo ? $newDriverInfo->driverInfo->{"car-number"} : '') }}" disabled>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car-letters"> {{ __("Car Letters") }}</label>
                                        <input id="car-letters" name="car-letters" dir="auto" type="text" class="form-control" placeholder="{{ __("Car Letters") }}" value="{{ old("car-letters", $newDriverInfo->driverInfo ? $newDriverInfo->driverInfo->{"car-letters"} : '') }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car-color"> {{ __("Car Color") }}</label>
                                        <input id="car-color" name="car-color" dir="auto" type="text" class="form-control" placeholder="{{ __("Car Color") }}" value="{{ old("car-color", $newDriverInfo->driverInfo ? $newDriverInfo->driverInfo->{"car-color"} : '') }}" disabled>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="driver-rate"> {{ __("Driver Rate") }}</label>
                                        <input id="driver-rate" name="driver-rate" dir="auto" type="text" class="form-control" placeholder="{{ __("Driver Rate") }}" value="{{ old("driver-rate", $newDriverInfo->driverInfo ? $newDriverInfo->driverInfo->{"driver-rate"} : '') }}" disabled>
                                    </div>
                                </div>
                            </div>

                            <!-- <div class="form-group">
                                <label for="sequence-number"> {{ __("Sequence Number") }}</label>
                                <input id="sequence-number" name="sequence-number" dir="auto" type="text" class="form-control" placeholder="{{ __("Sequence Number") }}" value="{{ old("sequence-number", $newDriverInfo->driverInfo ? $newDriverInfo->driverInfo->{"sequence-number"} : '') }}" disabled>
                            </div> -->


                        </div>

                        <div class="tab-pane show fade" id="driver-car">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="driver-type"> {{ __("Driver Type") }}</label>
                                        <select id="driver-type" name="driver-type-id" data-toggle="select" class="form-control select2">
                                            <option value=""> {{ __('Driver Type') }} </option>
                                            @foreach ($driverTypes as $newDriverInfoType)
                                                <option value="{{ $newDriverInfoType->id }}" {{ old('driver-type-id', $newDriverInfo->driverCar ? $newDriverInfo->driverCar->{"driver-type-id"} : '') == $newDriverInfoType->id ? 'selected' : '' }}> {{ $newDriverInfoType->{"name-ar"} }} </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="car_form_img"> {{ __('Car Form Image') }}</label>
                                <img src="{{ $newDriverInfo->driverCar && $newDriverInfo->driverCar->{"car_form_img"} ? url($newDriverInfo->driverCar->{"car_form_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $newDriverInfo->fullName }}" class="img-fluid d-block mb-2" style="max-width: 150px;">
                            </div>

                            <div class="form-group">
                                <label for="license_img"> {{ __('License Image') }}</label>
                                <img src="{{ $newDriverInfo->driverCar && $newDriverInfo->driverCar->{"license_img"} ? url($newDriverInfo->driverCar->{"license_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $newDriverInfo->fullName }}" class="img-fluid d-block mb-2" style="max-width: 150px;">
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_front_img"> {{ __('Car Front Image') }}</label>
                                        <img src="{{ $newDriverInfo->driverCar && $newDriverInfo->driverCar->{"car_front_img"} ? url($newDriverInfo->driverCar->{"car_front_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $newDriverInfo->fullName }}" class="img-fluid d-block mb-2" style="max-width: 150px;">
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_back_img"> {{ __('Car Back Image') }}</label>
                                        <img src="{{ $newDriverInfo->driverCar && $newDriverInfo->driverCar->{"car_back_img"} ? url($newDriverInfo->driverCar->{"car_back_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $newDriverInfo->fullName }}" class="img-fluid d-block mb-2" style="max-width: 150px;">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_rside_img"> {{ __('Car Right Side Image') }}</label>
                                        <img src="{{ $newDriverInfo->driverCar && $newDriverInfo->driverCar->{"car_rside_img"} ? url($newDriverInfo->driverCar->{"car_rside_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $newDriverInfo->fullName }}" class="img-fluid d-block mb-2" style="max-width: 150px;">
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_lside_img"> {{ __('Car Left Side Image') }}</label>
                                        <img src="{{ $newDriverInfo->driverCar && $newDriverInfo->driverCar->{"car_lside_img"} ? url($newDriverInfo->driverCar->{"car_lside_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $newDriverInfo->fullName }}" class="img-fluid d-block mb-2" style="max-width: 150px;">
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_insideFront_img"> {{ __('Car Inside Front Image') }}</label>
                                        <img src="{{ $newDriverInfo->driverCar && $newDriverInfo->driverCar->{"car_insideFront_img"} ? url($newDriverInfo->driverCar->{"car_insideFront_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $newDriverInfo->fullName }}" class="img-fluid d-block mb-2" style="max-width: 150px;">
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="car_insideBack_img"> {{ __('Car Inside Back Image') }}</label>
                                        <img src="{{ $newDriverInfo->driverCar && $newDriverInfo->driverCar->{"car_insideBack_img"} ? url($newDriverInfo->driverCar->{"car_insideBack_img"}) : 'https://ami-sni.com/wp-content/themes/consultix/images/no-image-found-360x250.png' }}" alt="{{ $newDriverInfo->fullName }}" class="img-fluid d-block mb-2" style="max-width: 150px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if ($newDriverInfo->approval == 0)
                        <div class="text-right mb-5">
                            <button type="submit" name="approval" value="1" class="btn btn-success">{{ __('Accept') }}</button>
                            <button type="submit" name="approval" value="2" class="btn btn-danger">{{ __('Reject') }}</button>
                        </div>
                    @endif

                </form>
            </div>
        </div>
        <!-- // END drawer-layout__content -->
    </div>
@endsection
