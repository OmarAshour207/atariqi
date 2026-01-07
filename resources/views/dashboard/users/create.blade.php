@push('admin_scripts')
<script type="text/javascript">
    var path = 'users';
    Dropzone.autoDiscover = false;
    $(document).ready(function () {
        $('#mainphoto').dropzone({
            url: '{{ route('upload.image') }}',
            paramName:'image',
            autoDiscover: false,
            uploadMultiple: false,
            maxFiles: 1,
            acceptedFiles: 'image/*',
            dictDefaultMessage: '{{ __('Upload Image') }}',
            dictRemoveFile: '<button class="btn btn-danger"> <i class="fa fa-trash center"></i></button>',
            params: {
                _token: '{{ csrf_token() }}',
                path: path,
                width: 500,
                height: 600
            },
            addRemoveLinks: true,
            removedfile:function (file) {
                var imageName = $('.image_name').val();
                $.ajax({
                    dataType: 'json',
                    type: 'POST',
                    url: '{{ route('remove.image') }}',
                    data: {
                        _token: '{{ csrf_token() }}',
                        image: imageName,
                        path: path
                    }
                });
                var fmock;
                return (fmock = file.previewElement) != null ? fmock.parentNode.removeChild(file.previewElement): void 0;
            },
            init: function () {
                @if(!empty(old('image')))
                    @php $image = \App\Models\Media::where('id', old('image'))->first(); @endphp
                    var mock = { name: '{{ $image->name }}', size: 2};
                    this.emit('addedfile', mock);
                    this.emit('thumbnail', mock, '{{ $image->tempMediaImage }}');
                    this.emit('complete', mock);
                    $('.dz-progress').remove();
                @endif

                    this.on("success", function (file, image) {
                    $('.image_name').val(image);
                })
            }
        });
    });
</script>
<style>
    .dropzone {
        width: 200px;
        height: 90px;
        min-height: 0px !important;
        background-color: #1C2260;
        border: #1C2260;
    }
</style>
@endpush

@extends('dashboard.layouts.app')

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }} </a></li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Create') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0"> {{ __('Users') }} </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">

            <div class="card card-form__body card-body">
                <form method="post" action="{{ route('users.store') }}">

                    @csrf
                    @include('dashboard.partials._errors')

                    <div class="form-group">
                        <label for="name"> {{ __("Name") }}</label>
                        <input id="name" name="name" dir="auto" type="text" class="form-control" placeholder="{{ __("Name") }}" value="{{ old("name") }}">
                    </div>

                    <div class="form-group">
                        <label for="email"> {{ __("Email") }}</label>
                        <input id="email" name="email" type="text" class="form-control" placeholder="{{ __("Email") }}" value="{{ old("email") }}">
                    </div>

                    <div class="row no-gutters">
                        <div class="col card-form__body card-body">
                            <div class="row">
                                <div class="form-group col">
                                    <label for="npass">{{ __('Password') }}</label>
                                    <input id="npass" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}">
                                </div>
                                <div class="form-group col">
                                    <label for="cpass">{{ __('Confirm Password') }}</label>
                                    <input id="cpass" name="password_confirmation" type="password" class="form-control" placeholder="{{ __('Confirm Password') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="phone"> {{ __("Phone Number") }}</label>
                        <input id="phone" name="phone" dir="auto" type="text" class="form-control" placeholder="{{ __("Phone Number") }}" value="{{ old("phone") }}">
                    </div>

                    <div class="row">
                        <div class="form-group col-lg-6">
                            <label for="role"> {{ __('Role') }}</label> <br>
                            <select id="role" name="role" data-toggle="select" class="form-control select2">
                                <option value="" selected> {{ __('Role') }} </option>
                                <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}> {{ __('Driver') }} </option>
                                <option value="supervisor" {{ old('role') == 'supervisor' ? 'selected' : '' }}> {{ __('Supervisor') }} </option>
                                <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}> {{ __('User') }} </option>
                                <option value="1" {{ old('role') == 'admin' ? 'selected' : '' }}> {{ __('admin') }} </option>
                            </select>
                        </div>

                        <div class="form-group col-lg-6">
                            <label for="status"> {{ __('Status') }}</label> <br>
                            <select id="status" name="status" data-toggle="select" class="form-control select2">
                                <option value="" selected> {{ __('Status') }} </option>
                                <option value="0" {{ old('status') == 0 ? 'selected' : '' }}> {{ __('InActive') }} </option>
                                <option value="1" {{ old('status') == 1 ? 'selected' : '' }}> {{ __('Active') }} </option>
                            </select>
                        </div>

                    </div>

                    <div class="form-group">
                        <input class="image_name" type="hidden" name="image" value="">
                    </div>

                    <div class="form-group">
                        <label> {{ __('Image') }} </label>
                        <div class="dropzone" id="mainphoto"></div>
                    </div>

                    <div class="text-right mb-5">
                        <input type="submit" class="btn btn-success" value="{{ __('Add') }}">
                    </div>
                </form>
            </div>
        </div>
        <!-- // END drawer-layout__content -->
    </div>
@endsection
