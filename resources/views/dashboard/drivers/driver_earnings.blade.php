@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('drivers.index') }}">{{ __('Drivers') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('drivers.show', $driver->id) }}">{{ $driver->{'user-first-name'} }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Driver Earnings') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Driver Earnings') }} - {{ $driver->{'user-first-name'} }} {{ $driver->{'user-last-name'} }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-body text-center">
                        <h6 class="text-muted mb-2">{{ __('Total Earnings') }}</h6>
                        <h3 class="mb-0 text-success">{{ number_format($revenueBreakdown['total'], 2) }} {{ __('SAR') }}</h3>
                        <small class="text-muted">{{ __('Since registration') }}</small>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-body text-center">
                        <h6 class="text-muted mb-2">{{ __('Dues Percentage') }}</h6>
                        <h3 class="mb-0">{{ number_format($duesPercentage, 2) }}%</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-body text-center">
                        <h6 class="text-muted mb-2">{{ __('Total Dues') }}</h6>
                        <h3 class="mb-0 text-primary">{{ number_format($totalDues, 2) }} {{ __('SAR') }}</h3>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card card-body text-center">
                        <h6 class="text-muted mb-2">{{ __('Total Paid') }}</h6>
                        <h3 class="mb-0 text-info">{{ number_format($totalPaid, 2) }} {{ __('SAR') }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-body text-center">
                        <h6 class="text-muted mb-2">{{ __('Remaining Balance') }}</h6>
                        <h3 class="mb-0 text-warning">{{ number_format($remainingDues, 2) }} {{ __('SAR') }}</h3>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-body text-center">
                        <h6 class="text-muted mb-2">{{ __('Current Unpaid Dues') }}</h6>
                        <h3 class="mb-0 text-{{ $currentUnpaidDues > 50 ? 'danger' : 'success' }}">
                            {{ number_format($currentUnpaidDues, 2) }} {{ __('SAR') }}
                        </h3>
                    </div>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <strong>{{ __('Earnings Breakdown by Trip Type') }}</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr>
                            <th>{{ __('Trip Type') }}</th>
                            <th>{{ __('Finished Trips') }}</th>
                            <th>{{ __('Revenue') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td><span class="badge badge-info">{{ __('Immediate') }}</span></td>
                            <td>{{ $revenueBreakdown['immediate']['count'] }}</td>
                            <td>{{ number_format($revenueBreakdown['immediate']['revenue'], 2) }} {{ __('SAR') }}</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-success">{{ __('Daily') }}</span></td>
                            <td>{{ $revenueBreakdown['daily']['count'] }}</td>
                            <td>{{ number_format($revenueBreakdown['daily']['revenue'], 2) }} {{ __('SAR') }}</td>
                        </tr>
                        <tr>
                            <td><span class="badge badge-primary">{{ __('Weekly') }}</span></td>
                            <td>{{ $revenueBreakdown['weekly']['count'] }}</td>
                            <td>{{ number_format($revenueBreakdown['weekly']['revenue'], 2) }} {{ __('SAR') }}</td>
                        </tr>
                        <tr class="font-weight-bold">
                            <td>{{ __('Total') }}</td>
                            <td>{{ $revenueBreakdown['immediate']['count'] + $revenueBreakdown['daily']['count'] + $revenueBreakdown['weekly']['count'] }}</td>
                            <td>{{ number_format($revenueBreakdown['total'], 2) }} {{ __('SAR') }}</td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card mb-4">
                <div class="card-header">
                    <strong>{{ __('Payment History') }}</strong>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr>
                            <th>{{ __('#') }}</th>
                            <th>{{ __('Amount') }}</th>
                            <th>{{ __('Date') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($payments as $index => $payment)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ number_format($payment->amount, 2) }} {{ __('SAR') }}</td>
                                <td>{{ $payment->{'date-of-add'} ? \Carbon\Carbon::parse($payment->{'date-of-add'})->format('Y-m-d H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">{{ __('No payments recorded yet.') }}</td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <a href="{{ route('drivers.show', $driver->id) }}" class="btn btn-secondary">
                {{ __('Back to Driver Details') }}
            </a>
        </div>
    </div>
@endsection
