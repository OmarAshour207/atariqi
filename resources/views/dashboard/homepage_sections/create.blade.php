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
                    <h1 class="m-0"> {{ __('Sections') }} </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">

            <div class="card card-form__body card-body">
                <form method="post" action="{{ route('homepage-sections.store') }}" enctype="multipart/form-data">

                    @csrf
                    @include('dashboard.partials._errors')

                    <input type="hidden" name="section_key" value="{{ request('section') }}">

                    <div class="form-group">
                        <label for="title"> {{ __("Title") }}</label>
                        <input id="title" name="title" dir="auto" type="text" class="form-control" placeholder="{{ __("Title") }}" value="{{ old("title") }}">
                    </div>

                    <div class="form-group">
                        <label for="content"> {{ __("Content") }}</label>
                        <textarea id="content" name="content" dir="auto" class="form-control" placeholder="{{ __("Content") }}" rows="4">{{ old("content") }}</textarea>
                    </div>

                    <div style="height: 150px;" data-toggle="quill" data-quill-placeholder="Quill WYSIWYG editor">
                        <h1>Hello World!</h1>
                        <p>Some initial <strong>bold</strong> text</p>
                        <p><br></p>
                    </div>

                    <div class="form-group">
                        <label for="icon"> {{ __("Icon") }}</label>
                        <input id="icon" name="icon" dir="auto" type="file" class="form-control" placeholder="{{ __("Icon") }}" value="{{ old("icon") }}">
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
