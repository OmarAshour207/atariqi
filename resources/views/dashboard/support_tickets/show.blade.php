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
                            <li class="breadcrumb-item">
                                <a href="{{ route('support-tickets.index', $page) }}">{{ $pageTitle }}</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{ $ticket->ticket_number }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Ticket Details') }}</h1>
                </div>
                <a href="{{ route('support-tickets.index', $page) }}" class="btn btn-secondary">
                    {{ __('Back') }}
                </a>
            </div>
        </div>

        <div class="container-fluid page__container">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>{{ $ticket->ticket_number }}</strong>
                            <span class="badge float-right
                                @if($ticket->status === 'new') badge-info
                                @elseif($ticket->status === 'in_progress') badge-warning
                                @else badge-secondary
                                @endif">
                                {{ $ticket->status_label }}
                            </span>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="text-muted small">{{ __('Customer Email') }}</div>
                                    <div class="font-weight-bold">{{ $ticket->customer_email }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small">{{ __('Customer Name') }}</div>
                                    <div class="font-weight-bold">{{ $ticket->customer_name ?: '-' }}</div>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="text-muted small">{{ __('Phone') }}</div>
                                    <div class="font-weight-bold">{{ $ticket->customer_phone ?: '-' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="text-muted small">{{ __('Ticket Type') }}</div>
                                    <div class="font-weight-bold">{{ $ticket->subtype_label }}</div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">{{ __('Ticket Title') }}</div>
                                <div class="font-weight-bold">{{ $ticket->title }}</div>
                            </div>

                            @if($ticket->related_service_reference)
                                <div class="mb-3">
                                    <div class="text-muted small">{{ __('Trip / Service Reference') }}</div>
                                    <div class="font-weight-bold">{{ $ticket->related_service_reference }}</div>
                                </div>
                            @endif

                            <div class="mb-0">
                                <div class="text-muted small">{{ __('Description') }}</div>
                                <div class="p-3 bg-light rounded" style="white-space: pre-wrap;">{{ $ticket->description }}</div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>{{ __('Attachments') }}</strong>
                        </div>
                        <div class="card-body">
                            @if($ticket->attachments->count())
                                <ul class="list-unstyled mb-0">
                                    @foreach($ticket->attachments as $index => $attachment)
                                        <li class="mb-2">
                                            <a href="{{ asset($attachment->file_path) }}" target="_blank" rel="noopener">
                                                {{ __('Attachment') }} {{ $index + 1 }}
                                                ({{ $attachment->uploaded_by === 'employee' ? __('Employee') : __('Customer') }})
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-muted mb-0">{{ __('No attachments') }}</p>
                            @endif
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>{{ __('Response History') }}</strong>
                        </div>
                        <div class="card-body">
                            @forelse($ticket->replies as $reply)
                                <div class="border rounded p-3 mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>
                                            @if($reply->sender_type === 'employee')
                                                {{ __('Support Response') }}
                                                @if($reply->employee)
                                                    - {{ $reply->employee->name }}
                                                @endif
                                            @else
                                                {{ __('Customer') }}
                                            @endif
                                        </strong>
                                        <span class="text-muted small">{{ $reply->created_at?->format('Y-m-d H:i') }}</span>
                                    </div>
                                    <div style="white-space: pre-wrap;">{{ $reply->message }}</div>
                                </div>
                            @empty
                                <p class="text-muted mb-0">{{ __('No responses yet.') }}</p>
                            @endforelse
                        </div>
                    </div>

                    @if(!$ticket->isClosed())
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>{{ __('Add Reply') }}</strong>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('support-tickets.reply', [$page, $ticket]) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <label for="message">{{ __('Reply') }}</label>
                                        <textarea id="message" name="message" class="form-control" rows="5" required>{{ old('message') }}</textarea>
                                    </div>
                                    <div class="form-group">
                                        <label for="attachments">{{ __('Attachments') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                                        <input type="file" id="attachments" name="attachments[]" class="form-control" multiple accept=".jpg,.jpeg,.png,.pdf">
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ __('Send Reply') }}</button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <div class="card mb-3">
                        <div class="card-header">
                            <strong>{{ __('Current Status') }}</strong>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="text-muted small">{{ __('Status') }}</div>
                                <div class="font-weight-bold">{{ $ticket->status_label }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">{{ __('Assigned Employee') }}</div>
                                <div class="font-weight-bold">{{ $ticket->assignedEmployee->name ?? __('Unassigned') }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">{{ __('Created At') }}</div>
                                <div class="font-weight-bold">{{ $ticket->created_at?->format('Y-m-d H:i') }}</div>
                            </div>
                            <div class="mb-3">
                                <div class="text-muted small">{{ __('Last Updated') }}</div>
                                <div class="font-weight-bold">{{ $ticket->updated_at?->format('Y-m-d H:i') }}</div>
                            </div>
                            @if($ticket->closed_at)
                                <div class="mb-0">
                                    <div class="text-muted small">{{ __('Closed At') }}</div>
                                    <div class="font-weight-bold">{{ $ticket->closed_at->format('Y-m-d H:i') }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if(!$ticket->isClosed())
                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>{{ __('Assign Ticket') }}</strong>
                            </div>
                            <div class="card-body">
                                <form method="POST" action="{{ route('support-tickets.assign', [$page, $ticket]) }}">
                                    @csrf
                                    <div class="form-group">
                                        <label for="assigned_employee_id">{{ __('Support Employee') }}</label>
                                        <select id="assigned_employee_id" name="assigned_employee_id" class="form-control" required>
                                            <option value="">{{ __('Select employee') }}</option>
                                            @foreach($employees as $employee)
                                                <option value="{{ $employee->id }}" {{ (int) old('assigned_employee_id', $ticket->assigned_employee_id) === (int) $employee->id ? 'selected' : '' }}>
                                                    {{ $employee->name }} ({{ $employee->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-info btn-block">{{ __('Assign Ticket') }}</button>
                                </form>
                            </div>
                        </div>

                        <div class="card mb-3">
                            <div class="card-header">
                                <strong>{{ __('Close Ticket') }}</strong>
                            </div>
                            <div class="card-body">
                                <p class="text-muted small">{{ __('Ticket can be closed only after at least one reply has been sent.') }}</p>
                                <form method="POST" action="{{ route('support-tickets.close', [$page, $ticket]) }}" onsubmit="return confirm('{{ __('Are you sure you want to close this ticket?') }}')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-block" {{ $ticket->hasEmployeeReply() ? '' : 'disabled' }}>
                                        {{ __('Close Ticket') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
