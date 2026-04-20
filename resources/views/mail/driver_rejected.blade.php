<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Driver Application Rejected') }}</title>
</head>
<body>
    <h1>{{ __('Hello') }}, {{ $driver->fullName ?? $driver->email }}</h1>
    <p>{{ __('Your driver application has been rejected.') }}</p>

    <p><strong>{{ __('Reason:') }}</strong></p>
    <p>{{ $reason }}</p>

    <p>{{ __('If you believe this is a mistake, please contact support.') }}</p>
</body>
</html>
