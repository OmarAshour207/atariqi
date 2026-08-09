@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        <div class="card card-body">
            <h1>{{ $title }}</h1>
            <table class="table table-bordered">
                @foreach((array) $row as $field => $value)
                    <tr>
                        <th>{{ ucwords(str_replace('_', ' ', $field)) }}</th>
                        <td>
                            @if(is_array($value) || is_object($value))
                                <pre class="mb-0">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @else
                                {{ $value }}
                            @endif
                        </td>
                    </tr>
                @endforeach
            </table>
            <a href="{{ route('logs.index') }}" class="btn btn-secondary">{{ __('Back') }}</a>
        </div>
    </div>
</div>
@endsection
