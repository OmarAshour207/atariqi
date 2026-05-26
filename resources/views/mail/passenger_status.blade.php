<div style="font-family: Arial, sans-serif;">
    <h2>{{ __('Account Status Update') }}</h2>
    <p>{{ __('Dear') }} {{ $passenger->{'user-first-name'} }} {{ $passenger->{'user-last-name'} }},</p>
    <p>
        @if($status === 'approved')
            {{ __('Congratulations! Your account has been approved.') }}
        @elseif($status === 'rejected')
            {{ __('We regret to inform you that your account has been rejected.') }}
        @endif
    </p>
    @if($info)
        <p>{{ $info }}</p>
    @endif
    <p>{{ __('Thank you for using our service.') }}</p>
</div>
