<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Driver Request Assignment') }}</title>
</head>
<body>
    <h1>{{ __('Hello') }}, {{ $admin->name }}</h1>
    <p>{{ __('A driver registration request has been assigned to you.') }}</p>

    <p><strong>{{ __('Driver') }}:</strong> {{ $driver->{'user-first-name'} }} {{ $driver->{'user-last-name'} }} ({{ $driver->email }})</p>
    <p><strong>{{ __('Assigned By') }}:</strong> {{ $assignedBy->name }}</p>
    <p><strong>{{ __('Note') }}:</strong></p>
    <p>{{ $note }}</p>

    <p>{{ __('Please review the request in the dashboard.') }}</p>
</body>
</html>
