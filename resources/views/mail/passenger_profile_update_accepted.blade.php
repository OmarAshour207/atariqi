<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Profile Update Accepted') }}</title>
</head>
<body>
    <h1>{{ __('Hello') }}, {{ $passenger->fullName ?? $passenger->email }}</h1>
    <p>{{ __('Your profile update has been accepted.') }}</p>

    <p>{{ __('If you believe this is a mistake, please contact support.') }}</p>
</body>
</html>
