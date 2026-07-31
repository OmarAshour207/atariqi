@extends('layouts.app')

@section('content')
<section class="py-5">
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                    <div>
                        <h1 class="fw-bold mb-2">{{ __('Technical Support') }}</h1>
                        <p class="text-secondary mb-0">{{ __('Submit an inquiry, complaint, or technical issue and we will get back to you.') }}</p>
                    </div>
                    <a href="{{ route('support.track') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-search me-1"></i>{{ __('Track Request') }}
                    </a>
                </div>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                        @if(session('ticket_number'))
                            <br><strong>{{ __('Ticket Number') }}:</strong> {{ session('ticket_number') }}
                        @endif
                    </div>
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

                <div class="card border-0 shadow-soft">
                    <div class="card-body p-4">
                        <ul class="nav nav-pills mb-4 gap-2" id="supportTypeTabs" role="tablist">
                            @foreach($types as $key => $label)
                                <li class="nav-item" role="presentation">
                                    <button
                                        class="nav-link {{ $loop->first ? 'active' : '' }}"
                                        id="tab-{{ $key }}"
                                        data-bs-toggle="pill"
                                        data-bs-target="#panel-{{ $key }}"
                                        type="button"
                                        role="tab"
                                    >{{ $label }}</button>
                                </li>
                            @endforeach
                        </ul>

                        <div class="tab-content">
                            @foreach($types as $key => $label)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="panel-{{ $key }}" role="tabpanel">
                                    <form method="post" action="{{ route('support.store') }}" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="type" value="{{ $key }}">

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Name') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                                                <input type="text" name="name" class="form-control" value="{{ old('type') === $key ? old('name') : '' }}">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Email') }} <span class="text-danger">*</span></label>
                                                <input type="email" name="email" class="form-control" value="{{ old('type') === $key ? old('email') : '' }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label">{{ __('Phone Number') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                                                <input type="text" name="phone" class="form-control" value="{{ old('type') === $key ? old('phone') : '' }}">
                                            </div>

                                            @if($key === 'inquiry')
                                                <div class="col-12">
                                                    <label class="form-label">{{ __('Inquiry Subject') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="subject" class="form-control" value="{{ old('type') === $key ? old('subject') : '' }}" required>
                                                </div>
                                            @endif

                                            @if($key === 'complaint')
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('Complaint Type') }} <span class="text-danger">*</span></label>
                                                    <input type="text" name="complaint_type" class="form-control" value="{{ old('type') === $key ? old('complaint_type') : '' }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('Trip / Service Reference') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                                                    <input type="text" name="trip_reference" class="form-control" value="{{ old('type') === $key ? old('trip_reference') : '' }}">
                                                </div>
                                            @endif

                                            @if($key === 'technical')
                                                <div class="col-md-6">
                                                    <label class="form-label">{{ __('Device Type') }} <span class="text-danger">*</span></label>
                                                    <select name="device_type" class="form-select" required>
                                                        <option value="">{{ __('Select device type') }}</option>
                                                        <option value="android" {{ old('type') === $key && old('device_type') === 'android' ? 'selected' : '' }}>Android</option>
                                                        <option value="ios" {{ old('type') === $key && old('device_type') === 'ios' ? 'selected' : '' }}>iOS</option>
                                                        <option value="web" {{ old('type') === $key && old('device_type') === 'web' ? 'selected' : '' }}>Web</option>
                                                    </select>
                                                </div>
                                            @endif

                                            <div class="col-12">
                                                <label class="form-label">{{ __('Description') }} <span class="text-danger">*</span></label>
                                                <textarea name="description" rows="5" class="form-control" required>{{ old('type') === $key ? old('description') : '' }}</textarea>
                                            </div>

                                            <div class="col-12">
                                                <label class="form-label">{{ __('Attachments') }} <span class="text-muted">({{ __('Optional') }})</span></label>
                                                <input type="file" name="attachments[]" class="form-control" multiple accept="{{ $key === 'technical' ? 'image/*' : 'image/*,.pdf' }}">
                                                <small class="text-muted">{{ __('You can attach screenshots or supporting files.') }}</small>
                                            </div>
                                        </div>

                                        <div class="{{ session('locale', app()->getLocale()) === 'ar' ? 'text-end' : 'text-start' }} mt-4">
                                            <button type="submit" class="btn btn-brand px-4">
                                                <i class="bi bi-send me-1"></i>{{ __('Submit') }}
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const oldType = @json(old('type'));
        if (oldType) {
            const tab = document.getElementById('tab-' + oldType);
            if (tab) {
                new bootstrap.Tab(tab).show();
            }
        }
    });
</script>
@endpush
