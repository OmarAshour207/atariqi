<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="icon" type="image/x-icon" href="{{ asset('dashboard/images/logos/logo.jpg') }}">
    <title>{{ __('Login') }}</title>

    <!-- Prevent the demo from appearing in search engines -->
    <meta name="robots" content="noindex">

    <!-- App CSS -->
    <!-- <link type="text/css" href="{{ asset('dashboard/css/app.css') }} " rel="stylesheet"> -->
    <link type="text/css" href="{{ asset('dashboard/css/app.rtl.css') }} " rel="stylesheet">

    <!-- Material Design Icons -->
    <!-- <link type="text/css" href="{{ asset('dashboard/css/vendor-material-icons.css') }} " rel="stylesheet"> -->
    <link type="text/css" href="{{ asset('dashboard/css/vendor-material-icons.rtl.css') }}" rel="stylesheet">

    <!-- Font Awesome FREE Icons -->
    <!-- <link type="text/css" href="{{ asset('dashboard/css/vendor-fontawesome-free.css') }} " rel="stylesheet"> -->
    <link type="text/css" href="{{ asset('dashboard/css/vendor-fontawesome-free.rtl.css') }} " rel="stylesheet">


</head>

<body class="layout-login">





    <div class="layout-login__overlay"></div>
    <div class="layout-login__form bg-white" data-perfect-scrollbar>
        <div class="d-flex justify-content-center mt-2 mb-5 navbar-light">
            <a href="{{ route('dashboard.index') }}" class="navbar-brand" style="min-width: 0">
                <img class="navbar-brand-icon" src="{{ asset('dashboard/images/logos/logo_2.jpg') }}" width="25" alt="{{ setting('title') }}">
                <span>{{ setting('title') }}</span>
            </a>
        </div>

        <h4 class="m-0">{{ __('Welcome back') }}!</h4>

        <form method="post" action="{{ route('login') }}" novalidate>
            @csrf
            @include('dashboard.partials._errors')

            <div class="form-group">
                <label class="text-label" for="email_2">{{ __('Email Address') }}:</label>
                <div class="input-group input-group-merge">
                    <input id="email_2" name="email" type="email" value="{{ old('email') }}" required="" class="form-control form-control-prepended" placeholder="example@example.com">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="far fa-envelope"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="text-label" for="password_2">{{ __('Password') }}:</label>
                <div class="input-group input-group-merge">
                    <input id="password_2" name="password" type="password" required="" class="form-control form-control-prepended" placeholder="{{ __('Enter your password') }}">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            <span class="fa fa-key"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-group mb-5">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" name="remember" class="custom-control-input" checked="" id="remember">
                    <label class="custom-control-label" for="remember">{{ __('Remember me') }}</label>
                </div>
            </div>
            <div class="form-group text-center">
                <button class="btn btn-primary mb-5" type="submit">{{ __('Login') }}</button><br>
{{--                <a href="#">{{ __('Forgot password') }}</a>--}}
            </div>
        </form>
    </div>


    <!-- jQuery -->
    <script src="{{ asset('dashboard/vendor/jquery.min.js') }}"></script>

    <!-- Bootstrap -->
    <script src="{{ asset('dashboard/vendor/popper.min.js') }}"></script>
    <script src="{{ asset('dashboard/vendor/bootstrap.min.js') }}"></script>

    <!-- DOM Factory -->
    <script src="{{ asset('dashboard/vendor/dom-factory.js') }}"></script>

    <!-- MDK -->
    <script src="{{ asset('dashboard/vendor/material-design-kit.js') }}"></script>

    <!-- App -->
    <script src="{{ asset('dashboard/js/app.js') }}"></script>

</body>
</html>
