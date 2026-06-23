<div class="trip-endpoint-details">
    <div class="trip-endpoint-type text-muted small">{{ $endpoint['label'] }}</div>
    <div class="trip-endpoint-name font-weight-bold">{{ $endpoint['name'] }}</div>
    @if(!empty($endpoint['address']))
        <div class="trip-endpoint-address text-muted small">{{ $endpoint['address'] }}</div>
    @endif
    @if(!empty($endpoint['lat']) && !empty($endpoint['lng']))
        <div class="trip-endpoint-coords text-muted small">
            {{ __('Coordinates') }}: {{ $endpoint['lat'] }}, {{ $endpoint['lng'] }}
        </div>
        <a href="https://www.google.com/maps?q={{ $endpoint['lat'] }},{{ $endpoint['lng'] }}" target="_blank" rel="noopener noreferrer" class="small">
            <i class="fa fa-map-marker-alt"></i> {{ __('View on map') }}
        </a>
    @endif
</div>
