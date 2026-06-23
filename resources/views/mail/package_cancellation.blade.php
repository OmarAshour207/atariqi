<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ __('Package Cancellation') }}</title>
</head>
<body>
    <h1>{{ __('Hello') }}, {{ $driver->fullName ?? $driver->{'user-first-name'} }}</h1>
    <p>{{ __('Your paid subscription has been cancelled and your account has been moved to the free subscription.') }}</p>

    <p><strong>{{ __('Previous Package') }}:</strong>
        {{ app()->getLocale() === 'ar' ? ($oldPackage?->name_ar ?? $oldPackage?->name_en ?? __('N/A')) : ($oldPackage?->name_en ?? $oldPackage?->name_ar ?? __('N/A')) }}
    </p>
    <p><strong>{{ __('Current Package') }}:</strong>
        {{ app()->getLocale() === 'ar' ? ($freePackage->name_ar ?? $freePackage->name_en) : ($freePackage->name_en ?? $freePackage->name_ar) }}
    </p>
    <p><strong>{{ __('Cancellation Date') }}:</strong> {{ now()->format('Y-m-d H:i') }}</p>

    <p>{{ __('Please log in to your account to view your current subscription details.') }}</p>
    <p>{{ __('If you have any questions, please contact our support team.') }}</p>

    <p>{{ __('Best Regards') }},<br>{{ config('app.name') }}</p>
</body>
</html>
