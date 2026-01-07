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
                dictDefaultMessage: '{{ __('Upload Photo') }}',
                dictRemoveFile: '<button class="btn btn-danger"> <i class="fa fa-trash center"></i></button>',
                params: {
                    _token: '{{ csrf_token() }}',
                    path: path,
                    width: 165,
                    height: 165
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
                    @else
                        var mock = { name: '{{ $user->name }}', size: 2};
                        this.emit('addedfile', mock);
                        this.emit('thumbnail', mock, '{{ $user->thumbImage }}');
                        this.emit('complete', mock);
                    @endif
                    $('.dz-progress').remove();

                    this.on("success", function (file, image) {
                        $('.image_name').val(image);
                    })
                }
            });
        });
    </script>
    <style type="text/css">

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
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Edit Profile') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0"> {{ __('Profile') }} </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">

            <div class="card card-form__body card-body">
                <form method="post" action="{{ route('update.profile') }}" enctype="multipart/form-data">

                    @method('post')
                    @csrf
                    @include('dashboard.partials._errors')

                    <div class="row no-gutters">
                        <div class="col-lg-4 card-body">
                            <p><strong class="headings-color">{{ __('Profile Info') }}</strong></p>
                            <p class="text-muted">{{ __('Edit Profile') }}</p>
                        </div>
                        <div class="col-lg-8 card-form__body card-body">
                            <div class="form-group">
                                <label for="name">{{ __('Full Name') }}</label>
                                <input id="name" type="text" name="name" class="form-control" placeholder="{{ __('Full Name') }}" value="{{ old('name', $user->name) }}">
                            </div>

                            <div class="form-group">
                                <label for="address">{{ __('Address') }}</label>
                                <input id="address" type="text" name="address" class="form-control" placeholder="{{ __('Address') }}" value="{{ old('address', $user->address) }}">
                            </div>

{{--                            <div class="row">--}}
{{--                                <div class="col-lg-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="lat">{{ __('Lat') }}</label>--}}
{{--                                        <input id="lat" type="text" name="lat" class="form-control" placeholder="{{ __('Lat') }}" value="{{ old('lat', $user->lat) }}">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                                <div class="col-lg-6">--}}
{{--                                    <div class="form-group">--}}
{{--                                        <label for="lng">{{ __('Lng') }}</label>--}}
{{--                                        <input id="lng" type="text" name="lng" class="form-control" placeholder="{{ __('Lng') }}" value="{{ old('lng', $user->lng) }}">--}}
{{--                                    </div>--}}
{{--                                </div>--}}
{{--                            </div>--}}

                            <div class="form-group">
                                <label for="phonenumber">{{ __('Phone Number') }}</label>
                                <input id="phonenumber" type="text" name="phonenumber" class="form-control" placeholder="{{ __('Phone Number') }}" value="{{ old('phonenumber', $user->phonenumber) }}">
                            </div>

                        </div>
                    </div>

                    <div class="row no-gutters">
                        <div class="col-lg-4 card-body">
                            <p><strong class="headings-color">{{ __('Update Password') }}</strong></p>
                            <p class="text-muted">{{ __('Change Password') }}</p>
                        </div>
                        <div class="col-lg-8 card-form__body card-body">
                            <div class="form-group">
                                <label for="opass">{{ __('Old Password') }}</label>
                                <input style="width: 270px;" id="opass" name="old_password" type="password" class="form-control">
                            </div>
                            <div class="row">
                                <div class="form-group col">
                                    <label for="npass">{{ __('New Password') }}</label>
                                    <input style="width: 270px;" id="npass" name="new_password" type="password" class="form-control" placeholder="{{ __('New Password') }}">
                                </div>
                                <div class="form-group col">
                                    <label for="cpass">{{ __('Confirm New Password') }}</label>
                                    <input style="width: 270px;" id="cpass" name="confirm_new_password" type="password" class="form-control" placeholder="{{ __('Confirm New Password') }}">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row no-gutters">
                        <div class="col-lg-4 card-body">
                            <p><strong class="headings-color">{{ __('Profile Image') }}</strong></p>
                        </div>
                        <div class="form-group">
                            <input class="image_name" type="hidden" name="image">
                        </div>
                        <div class="form-group">
                            <label> {{ __('Photo') }} </label>
                            <div class="dropzone" id="mainphoto"></div>
                        </div>
                    </div>

                    <div class="text-right mb-5">
                        <input type="submit" class="btn btn-success" value="{{ __('Update') }}">
                    </div>
                </form>
            </div>
        </div>
        <!-- // END drawer-layout__content -->
    </div>
@endsection
