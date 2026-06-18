@component('mail::message')
# {{ __('Package Cancellation') }}

{{ __('Hello') }} {{ $driver->{'user-first-name'} }},

{{ __('Your paid subscription has been cancelled and your account has been moved to the free subscription.') }}

**{{ __('Previous Package') }}:** {{ $oldPackage?->name_en ?? __('N/A') }}

**{{ __('Current Package') }}:** {{ $freePackage->name_en }}

{{ __('Cancellation Date') }}: {{ now()->format('Y-m-d H:i') }}

{{ __('Please log in to your account to view your current subscription details.') }}

{{ __('If you have any questions, please contact our support team.') }}

@component('mail::button', ['url' => config('app.url')])
{{ __('View Your Account') }}
@endcomponent

{{ __('Best Regards') }},<br>
{{ setting('title') }}
@endcomponent
