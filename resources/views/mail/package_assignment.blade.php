<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Package Assignment Notification') }}</title>
</head>
<body>
    <h1>{{ __('Hello') }}, {{ $driver->fullName ?? $driver->{'user-first-name'} }}</h1>
    <p>{{ __('Your subscription has been upgraded successfully.') }}</p>

    <p><strong>{{ __('Package Details:') }}</strong></p>
    <ul>
        <li>{{ __('Package Name') }}: {{ app()->getLocale() === 'ar' ? ($package->name_ar ?? $package->name_en) : ($package->name_en ?? $package->name_ar) }}</li>
        <li>{{ __('Interval') }}: {{ $interval === 'yearly' ? __('Yearly') : __('Monthly') }}</li>
        <li>{{ __('Assigned Date') }}: {{ now()->format('Y-m-d H:i') }}</li>
    </ul>

    <p>{{ __('Please log in to your account to view more details about your package and its benefits.') }}</p>
    <p>{{ __('If you have any questions, please contact our support team.') }}</p>

    <p>{{ __('Best Regards') }},<br>{{ config('app.name') }}</p>
</body>
</html>
