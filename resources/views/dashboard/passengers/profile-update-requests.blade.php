@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('passengers.index') }}">{{ __('Passengers') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Profile Update Requests') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('Profile Update Requests') }}</h1>
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

            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ __('Pending Profile Updates') }}</h4>
                    <p class="card-subtitle">{{ __('Passengers who have requested profile changes and are waiting for approval') }}</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>{{ __('Current Name') }}</th>
                            <th>{{ __('New Name') }}</th>
                            <th>{{ __('Phone') }}</th>
                            <th>{{ __('Email') }}</th>
                            <th>{{ __('University') }}</th>
                            <th>{{ __('Request Date') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @forelse($passengers as $index => $passenger)
                            <tr>
                                <td>{{ $passengers->firstItem() + $index }}</td>
                                <td>
                                    {{ $passenger->user->{'user-first-name'} }} {{ $passenger->user->{'user-last-name'} }}
                                </td>
                                <td>
                                    <strong>{{ $passenger->{'user-first-name'} }} {{ $passenger->{'user-last-name'} }}</strong>
                                    <i class="fas fa-edit text-info" title="{{ __('Updated') }}"></i>
                                </td>
                                <td>
                                    +{{ optional($passenger->callingKey)->{'call-key'} }}{{ $passenger->{'phone-no'} }}
                                    @if($passenger->newUserInfo && $passenger->newUserInfo->{'phone-no'} !== $passenger->{'phone-no'})
                                        <br><small class="text-info">
                                            <i class="fas fa-arrow-right"></i>
                                            +{{ optional($passenger->newUserInfo->callingKey)->{'call-key'} }}{{ $passenger->newUserInfo->{'phone-no'} }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    {{ $passenger->email }}
                                    @if($passenger->newUserInfo && $passenger->newUserInfo->email !== $passenger->email)
                                        <br><small class="text-info">
                                            <i class="fas fa-arrow-right"></i>
                                            {{ $passenger->newUserInfo->email }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    {{ optional($passenger->university)->{'name-ar'} ?? optional($passenger->university)->{'name-en'} ?? '-' }}
                                    @if($passenger->newUserInfo && $passenger->newUserInfo->{'university-id'} !== $passenger->{'university-id'})
                                        <br><small class="text-info">
                                            <i class="fas fa-arrow-right"></i>
                                            {{ optional($passenger->newUserInfo->university)->{'name-ar'} ?? optional($passenger->newUserInfo->university)->{'name-en'} ?? '-' }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    @if($passenger->newUserInfo)
                                        {{ optional($passenger->newUserInfo->{'date-of-add'})->format('Y-m-d H:i') ?? '-' }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('passengers.show', $passenger->user->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> {{ __('Review Details') }}
                                        </a>
                                        <form action="{{ route('passengers.approve-profile-update', $passenger->user->id) }}" method="post" class="d-inline-block ml-1">
                                            @csrf
                                            @method('post')
                                            <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('{{ __('Are you sure you want to approve this profile update?') }}');">
                                                <i class="fas fa-check"></i> {{ __('Approve') }}
                                            </button>
                                        </form>
                                        <form id="reject-form-{{ $passenger->user->id }}" action="{{ route('passengers.reject-profile-update', $passenger->user->id) }}" method="post" class="d-inline-block ml-1 profile-reject-form">
                                            @csrf
                                            @method('post')
                                            <input type="hidden" name="rejection_reason" value="">
                                            <button type="button" class="btn btn-sm btn-danger profile-reject-btn" data-form-id="reject-form-{{ $passenger->user->id }}">
                                                <i class="fas fa-times"></i> {{ __('Reject') }}
                                            </button>
                                        </form>
                                        <form id="assign-form-{{ $passenger->user->id }}" action="{{ route('passengers.assign-to-admin', $passenger->user->id) }}" method="post" class="d-inline-block ml-1 profile-assign-form">
                                            @csrf
                                            @method('post')
                                            <input type="hidden" name="assign_note" value="">
                                            <input type="hidden" name="assigned_admin" value="">
                                            <button type="button" class="btn btn-sm btn-primary profile-assign-btn" data-form-id="assign-form-{{ $passenger->user->id }}">
                                                <i class="fas fa-level-up-alt"></i> {{ __('Escalate') }}
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                        <h5>{{ __('No Pending Requests') }}</h5>
                                        <p class="text-muted">{{ __('All profile update requests have been processed.') }}</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                @if($passengers->hasPages())
                    <div class="card-footer">
                        {{ $passengers->links('dashboard.pagination.custom') }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="profileRejectModal" tabindex="-1" role="dialog" aria-labelledby="profileRejectModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileRejectModalLabel">{{ __('Reject Profile Update') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="profile-reject-reason">{{ __('Rejection Reason') }} <span class="text-danger">*</span></label>
                        <textarea id="profile-reject-reason" class="form-control" rows="4" placeholder="{{ __('Enter the reason for rejecting this profile update') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-danger" id="confirm-profile-reject">{{ __('Confirm Rejection') }}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="profileAssignModal" tabindex="-1" role="dialog" aria-labelledby="profileAssignModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="profileAssignModalLabel">{{ __('Assign Request') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="profile-assigned-admin">{{ __('Assign To Admin') }} <span class="text-danger">*</span></label>
                        <select id="profile-assigned-admin" class="form-control">
                            <option value="">{{ __('Select an admin') }}</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }} @if($admin->email) ({{ $admin->email }}) @endif</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="profile-assign-note">{{ __('Assignment Note') }} <span class="text-danger">*</span></label>
                        <textarea id="profile-assign-note" class="form-control" rows="4" placeholder="{{ __('Enter a note for this assignment') }}"></textarea>
                        <small class="form-text text-muted">{{ __('Example: Phone number review required') }}</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                    <button type="button" class="btn btn-primary" id="confirm-profile-assign">{{ __('Assign Request') }}</button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .modal-backdrop {
            z-index: 9998 !important;
            pointer-events: none !important;
            background-color: transparent !important;
        }
        .modal-backdrop.show {
            opacity: 0 !important;
            z-index: 9998 !important;
            pointer-events: none !important;
        }
        .modal.show {
            z-index: 9999 !important;
            pointer-events: auto !important;
        }
        .modal.show .modal-dialog {
            pointer-events: auto !important;
        }
    </style>
@endsection

@push('admin_scripts')
    <script>
        (function () {
            let activeRejectForm = null;
            let activeAssignForm = null;

            document.querySelectorAll('.profile-reject-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    activeRejectForm = document.getElementById(button.getAttribute('data-form-id'));
                    document.getElementById('profile-reject-reason').value = '';
                    $('#profileRejectModal').modal('show');
                });
            });

            document.querySelectorAll('.profile-assign-btn').forEach(function (button) {
                button.addEventListener('click', function () {
                    activeAssignForm = document.getElementById(button.getAttribute('data-form-id'));
                    document.getElementById('profile-assign-note').value = '';
                    document.getElementById('profile-assigned-admin').value = '';
                    $('#profileAssignModal').modal('show');
                });
            });

            document.getElementById('confirm-profile-reject').addEventListener('click', function () {
                const reason = document.getElementById('profile-reject-reason').value.trim();

                if (!reason) {
                    alert('{{ __('Please enter a rejection reason') }}');
                    return;
                }

                if (!activeRejectForm) {
                    return;
                }

                activeRejectForm.querySelector('input[name="rejection_reason"]').value = reason;
                activeRejectForm.submit();
            });

            document.getElementById('confirm-profile-assign').addEventListener('click', function () {
                const note = document.getElementById('profile-assign-note').value.trim();
                const assignedAdmin = document.getElementById('profile-assigned-admin').value;

                if (!assignedAdmin) {
                    alert('{{ __('Please select an admin') }}');
                    return;
                }

                if (!note) {
                    alert('{{ __('Please enter an assignment note') }}');
                    return;
                }

                if (!activeAssignForm) {
                    return;
                }

                activeAssignForm.querySelector('input[name="assign_note"]').value = note;
                activeAssignForm.querySelector('input[name="assigned_admin"]').value = assignedAdmin;
                activeAssignForm.submit();
            });
        })();
    </script>
@endpush
