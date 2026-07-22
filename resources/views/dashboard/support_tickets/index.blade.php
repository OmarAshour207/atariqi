@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item">
                                <a href="{{ route('dashboard.index') }}">
                                    <i class="material-icons icon-20pt">home</i> {{ __('Home') }}
                                </a>
                            </li>
                            <li class="breadcrumb-item">{{ __('Technical Support') }}</li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $pageTitle }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ $pageTitle }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('support-tickets.index', $page) }}" class="row align-items-end">
                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label for="ticket_number">{{ __('Ticket Number') }}</label>
                                <input type="text" class="form-control" id="ticket_number" name="ticket_number"
                                       value="{{ $filters['ticket_number'] ?? '' }}"
                                       placeholder="{{ __('Ticket Number') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label for="email">{{ __('Customer Email') }}</label>
                                <input type="text" class="form-control" id="email" name="email"
                                       value="{{ $filters['email'] ?? '' }}"
                                       placeholder="{{ __('Email') }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label for="status">{{ __('Status') }}</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="">{{ __('All') }}</option>
                                    @foreach($statuses as $value => $label)
                                        <option value="{{ $value }}" {{ ($filters['status'] ?? '') === $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label for="date_from">{{ __('Date From') }}</label>
                                <input type="date" class="form-control" id="date_from" name="date_from"
                                       value="{{ $filters['date_from'] ?? '' }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label for="date_to">{{ __('Date To') }}</label>
                                <input type="date" class="form-control" id="date_to" name="date_to"
                                       value="{{ $filters['date_to'] ?? '' }}">
                            </div>
                        </div>

                        <div class="col-md-2">
                            <div class="form-group mb-2">
                                <label for="sort">{{ __('Sort By') }}</label>
                                <select class="form-control" id="sort" name="sort">
                                    <option value="newest" {{ ($filters['sort'] ?? 'newest') === 'newest' ? 'selected' : '' }}>{{ __('Newest') }}</option>
                                    <option value="oldest" {{ ($filters['sort'] ?? '') === 'oldest' ? 'selected' : '' }}>{{ __('Oldest') }}</option>
                                    <option value="status" {{ ($filters['sort'] ?? '') === 'status' ? 'selected' : '' }}>{{ __('By Status') }}</option>
                                </select>
                            </div>
                        </div>

                        <div class="col-12 text-right mt-2">
                            <button type="submit" class="btn btn-primary">{{ __('Filter') }}</button>
                            <a href="{{ route('support-tickets.index', $page) }}" class="btn btn-secondary">{{ __('Clear Filters') }}</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="table-responsive">
                    <table class="table table-striped mb-0">
                        <thead>
                        <tr>
                            <th>{{ __('Ticket Number') }}</th>
                            <th>{{ __('Customer Email') }}</th>
                            <th>{{ __('Ticket Type') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Created At') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>
                                    <a href="{{ route('support-tickets.show', [$page, $ticket]) }}">
                                        {{ $ticket->ticket_number }}
                                    </a>
                                </td>
                                <td>{{ $ticket->customer_email }}</td>
                                <td>{{ $ticket->subtype_label }}</td>
                                <td>
                                    <span class="badge
                                        @if($ticket->status === 'new') badge-info
                                        @elseif($ticket->status === 'in_progress') badge-warning
                                        @elseif($ticket->status === 'closed') badge-secondary
                                        @else badge-success
                                        @endif">
                                        {{ $ticket->status_label }}
                                    </span>
                                </td>
                                <td>{{ $ticket->created_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <a href="{{ route('support-tickets.show', [$page, $ticket]) }}" class="btn btn-sm btn-outline-primary">
                                        {{ __('View') }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    {{ __('No tickets match the selected filters.') }}
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                @if($tickets->hasPages())
                    <div class="card-footer">
                        {{ $tickets->links('dashboard.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
