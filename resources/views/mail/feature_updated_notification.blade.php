<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ __('Feature Updated Notification') }}</title>
</head>
<body>
    <h1>{{ __('Feature Updated Notification') }}</h1>
    <p>{{ __('Hello') }} {{ $customer->{"user-first-name"} }} {{ $customer->{"user-last-name"} }},</p>
    <p>{{ __('The feature you are interested in has been updated. Here are the details:') }}</p>
    <ul>
        <li><strong>{{ __('Feature Name (Arabic)') }}:</strong> {{ $feature->name_ar }}</li>
        <li><strong>{{ __('Feature Name (English)') }}:</strong> {{ $feature->name_en }}</li>
        <li><strong>{{ __('Description (Arabic)') }}:</strong> {{ $feature->description_ar }}</li>
        <li><strong>{{ __('Description (English)') }}:</strong> {{ $feature->description_en }}</li>
    </ul>
    <p>{{ __('Visit our platform to learn more and subscribe.') }}</p>
    <p>{{ __('Thank you,') }}<br>{{ config('app.name') }}</p>
</body>
</html>
