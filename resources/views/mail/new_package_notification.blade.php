<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('New Package Available') }}</title>
</head>
<body>
    <h1>{{ __('New Package Available') }}</h1>
    <p>{{ __('Hello') }} {{ $customer->{"user-first-name"} }} {{ $customer->{"user-last-name"} }},</p>
    <p>{{ __('We have just launched a new package. Here are the details:') }}</p>
    <ul>
        <li><strong>{{ __('Package Name (Arabic)') }}:</strong> {{ $package->name_ar }}</li>
        <li><strong>{{ __('Package Name (English)') }}:</strong> {{ $package->name_en }}</li>
        <li><strong>{{ __('Monthly Price') }}:</strong> {{ $package->price_monthly }} SAR</li>
        <li><strong>{{ __('Annual Price') }}:</strong> {{ $package->price_annual }} SAR</li>
    </ul>
    <p>{{ __('Visit our platform to learn more and subscribe.') }}</p>
    <p>{{ __('Thank you,') }}<br>{{ config('app.name') }}</p>
</body>
</html>
