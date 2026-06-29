@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }}</a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('General Dues Percentage') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __('General Dues Percentage Management') }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @if(!$subscription)
                <div class="alert alert-danger mb-0">
                    {{ __('General dues percentage record not found.') }}
                </div>
            @else
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card card-form__body card-body" id="view-panel">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label>{{ __('Name (AR)') }}</label>
                            <p class="form-control-plaintext border rounded px-3 py-2 bg-light mb-0">{{ $subscription->{'name-ar'} }}</p>
                        </div>
                        <div class="form-group col-md-6">
                            <label>{{ __('Name (EN)') }}</label>
                            <p class="form-control-plaintext border rounded px-3 py-2 bg-light mb-0">{{ $subscription->{'name-eng'} }}</p>
                        </div>
                    </div>

                    <div class="form-group col-md-6 px-0">
                        <label>{{ __('Current Percentage') }}</label>
                        <p class="form-control-plaintext border rounded px-3 py-2 bg-light mb-0">{{ $subscription->cost }}%</p>
                    </div>

                    <div class="text-right">
                        <button type="button" class="btn btn-primary" id="btn-edit">{{ __('Edit') }}</button>
                    </div>
                </div>

                <div class="card card-form__body card-body d-none" id="edit-panel">
                    <form action="{{ route('general-dues-percentage.update') }}" method="post">
                        @csrf
                        @method('patch')

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label>{{ __('Name (AR)') }}</label>
                                <input type="text" class="form-control" value="{{ $subscription->{'name-ar'} }}" readonly>
                            </div>
                            <div class="form-group col-md-6">
                                <label>{{ __('Name (EN)') }}</label>
                                <input type="text" class="form-control" value="{{ $subscription->{'name-eng'} }}" readonly>
                            </div>
                        </div>

                        <div class="form-group col-md-6 px-0">
                            <label for="cost">{{ __('Dues Percentage') }} (%)</label>
                            <input
                                type="number"
                                name="cost"
                                id="cost"
                                class="form-control @error('cost') is-invalid @enderror"
                                value="{{ old('cost', $subscription->cost) }}"
                                min="0"
                                max="100"
                                step="0.01"
                                required
                            >
                            @error('cost')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="text-right">
                            <button type="button" class="btn btn-secondary" id="btn-cancel">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('admin_scripts')
    <script>
        (function () {
            var viewPanel = document.getElementById('view-panel');
            var editPanel = document.getElementById('edit-panel');
            var btnEdit = document.getElementById('btn-edit');
            var btnCancel = document.getElementById('btn-cancel');

            if (!viewPanel || !editPanel) {
                return;
            }

            btnEdit.addEventListener('click', function () {
                viewPanel.classList.add('d-none');
                editPanel.classList.remove('d-none');
            });

            btnCancel.addEventListener('click', function () {
                editPanel.classList.add('d-none');
                viewPanel.classList.remove('d-none');
            });

            @if($errors->any() || old('cost') !== null)
                viewPanel.classList.add('d-none');
                editPanel.classList.remove('d-none');
            @endif
        })();
    </script>
@endpush
