@php
    $type = $tripType ?? ($trip->trip_type ?? 'daily');
@endphp
<span class="badge badge-{{ trip_action_badge_class($trip->action, $type) }}">
    {{ trip_action_label($trip->action, $type) }}
    <small>({{ $trip->action }})</small>
</span>
@if($type === 'weekly' && isset($trip->booking) && $trip->booking)
    <div class="mt-1">
        <span class="badge badge-{{ weekly_group_status_badge_class($trip->booking->status) }}">
            {{ __('Group status') }}: {{ weekly_group_status_label($trip->booking->status) }}
            <small>({{ $trip->booking->status }})</small>
        </span>
    </div>
@endif
