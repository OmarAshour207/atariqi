@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }} </a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Profile') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0"> {{ __('Profile') }} </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">

            <div class="card card-form__body card-body">
                <form method="post" action="{{ route('profile.update') }}" >

                    @method('post')
                    @csrf
                    @include('dashboard.partials._errors')

                    <div class="row no-gutters">
                        <div class="col-lg-4 card-body">
                            <p><strong class="headings-color">{{ __('Profile Info') }}</strong></p>
                            <p class="text-muted">{{ __('Edit Profile') }}</p>
                        </div>
                        
                    </div>

                    <div class="row no-gutters">
                        <div class="col-lg-4 card-body">
                            <p><strong class="headings-color">{{ __('Update Password') }}</strong></p>
                            <p class="text-muted">{{ __('Change Password') }}</p>
                        </div>
                        <div class="col-lg-8 card-form__body card-body">
                            <div class="form-group">
                                <label for="opass">{{ __('Old Password') }}</label>
                                <input style="width: 270px;" id="opass" name="old_password" type="password" class="form-control">
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <label for="npass">{{ __('New Password') }}</label>
                                    <input style="width: 270px;" id="npass" name="new_password" type="password" class="form-control" placeholder="{{ __('New Password') }}">
                                </div>
                                <div class="form-group col">
                                    <label for="cpass">{{ __('Confirm New Password') }}</label>
                                    <input style="width: 270px;" id="cpass" name="confirm_new_password" type="password" class="form-control" placeholder="{{ __('Confirm New Password') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-right mb-5">
                        <input type="submit" class="btn btn-success" value="{{ __('Update') }}">
                    </div>
                </form>
            </div>
        </div>
        <!-- // END drawer-layout__content -->
    </div>
@endsection
