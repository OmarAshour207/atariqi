<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Captain Application Approved') }}</title>
</head>
<body>
    <h1>{{ __('Hello') }}, {{ $driver->fullName ?? $driver->email }}</h1>
    <p>{{ __('Congratulations! Your captain application has been approved.') }}</p>
    <p>{{ __('You can now log in to the captain app and start accepting trips.') }}</p>
    <p>{{ __('If you have any questions, please contact support.') }}</p>
</body>
</html>
