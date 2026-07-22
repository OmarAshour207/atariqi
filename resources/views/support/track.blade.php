@extends('layouts.app')

@section('content')
<section class="py-5">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h1 class="fw-bold mb-2">{{ __('Track Request') }}</h1>
                        <p class="text-secondary mb-0">{{ __('Enter your ticket number and email to view the status of your request.') }}</p>
                    </div>
                    <a href="{{ route('support') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-right me-1"></i>{{ __('Back to Support') }}
                    </a>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card border-0 shadow-soft mb-4">
                    <div class="card-body p-4">
                        <form method="post" action="{{ route('support.lookup') }}">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Ticket Number') }} <span class="text-danger">*</span></label>
                                    <input type="text" name="ticket_number" class="form-control" value="{{ old('ticket_number', $ticket->ticket_number ?? '') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                                    <input type="email" name="email" class="form-control" value="{{ old('email', $ticket->customer_email ?? '') }}" required>
                                </div>
                            </div>
                            <div class="text-end mt-4">
                                <button type="submit" class="btn btn-brand px-4">
                                    <i class="bi bi-search me-1"></i>{{ __('Search') }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @isset($ticket)
                    <div class="card border-0 shadow-soft mb-4">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">{{ __('Ticket Details') }}</h5>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="small text-secondary">{{ __('Ticket Number') }}</div>
                                    <div class="fw-semibold">{{ $ticket->ticket_number }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-secondary">{{ __('Type') }}</div>
                                    <div class="fw-semibold">{{ $ticket->type_label }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-secondary">{{ __('Status') }}</div>
                                    <div>
                                        <span class="badge badge-soft">{{ $ticket->status_label }}</span>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small text-secondary">{{ __('Last Updated') }}</div>
                                    <div class="fw-semibold">{{ $ticket->updated_at->format('Y-m-d H:i') }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="small text-secondary">{{ __('Ticket Title') }}</div>
                                    <div class="fw-semibold">{{ $ticket->title }}</div>
                                </div>
                                <div class="col-12">
                                    <div class="small text-secondary">{{ __('Description') }}</div>
                                    <div>{{ $ticket->description }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-soft">
                        <div class="card-body p-4">
                            <h5 class="fw-bold mb-4">{{ __('Response History') }}</h5>
                            @forelse($ticket->replies as $reply)
                                <div class="p-3 bg-light rounded mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>
                                            {{ $reply->sender_type === 'employee' ? __('Support Response') : __('Customer') }}
                                        </strong>
                                        <span class="small text-secondary">{{ $reply->created_at?->format('Y-m-d H:i') }}</span>
                                    </div>
                                    <div style="white-space: pre-wrap;">{{ $reply->message }}</div>
                                </div>
                            @empty
                                <div class="alert alert-info mb-0">{{ __('No response yet. Our team will contact you soon.') }}</div>
                            @endforelse
                        </div>
                    </div>
                @endisset
            </div>
        </div>
    </div>
</section>
@endsection
