@component('mail::message')
# {{ __('Package Assignment') }}

{{ __('Hello') }} {{ $driver->{'user-first-name'} }},

{{ __('We have successfully assigned a package to your account.') }}

**{{ __('Package Details:') }}**
- {{ __('Package Name') }}: {{ $package->name_en }}
- {{ __('Interval') }}: {{ ucfirst($interval) }}
- {{ __('Assigned Date') }}: {{ now()->format('Y-m-d H:i') }}

{{ __('Please log in to your account to view more details about your package and its benefits.') }}

{{ __('If you have any questions, please contact our support team.') }}

@component('mail::button', ['url' => config('app.url')])
{{ __('View Your Account') }}
@endcomponent

{{ __('Best Regards') }},<br>
{{ setting('title') }}
@endcomponent
