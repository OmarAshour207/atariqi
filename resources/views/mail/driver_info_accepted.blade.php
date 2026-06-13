<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Driver Information Update Accepted') }}</title>
</head>
<body>
    <h1>{{ __('Hello') }}, {{ $driver->fullName ?? $driver->email }}</h1>
    <p>{{ __('Your driver information has been accepted.') }}</p>

    <p>{{ __('If you believe this is a mistake, please contact support.') }}</p>
</body>
</html>
