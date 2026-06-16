<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Passenger Profile Update Assignment') }}</title>
</head>
<body>
    <h1>{{ __('Hello') }}, {{ $admin->name }}</h1>
    <p>{{ __('A passenger profile update request has been assigned to you.') }}</p>

    <p><strong>{{ __('Passenger') }}:</strong> {{ $passenger->{'user-first-name'} }} {{ $passenger->{'user-last-name'} }} ({{ $passenger->email }})</p>
    <p><strong>{{ __('Assigned By') }}:</strong> {{ $assignedBy->name }}</p>
    <p><strong>{{ __('Note') }}:</strong></p>
    <p>{{ $note }}</p>

    <p>{{ __('Please review the request in the dashboard.') }}</p>
</body>
</html>
