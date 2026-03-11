@push('admin_styles')
        <!-- Quill Theme -->
    <!-- <link type="text/css" href="{{ asset('dashboard/css/vendor-quill.css') }}" rel="stylesheet"> -->
    <link type="text/css" href="{{ asset('dashboard/css/vendor-quill.rtl.css') }}" rel="stylesheet">
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
                            <li class="breadcrumb-item">{{ request()->segments()[2] ?? '' }}</li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0"> {{ request()->segments()[2] ?? '' }} </h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">

            <div class="card card-form__body card-body">
                <form method="post" action="{{ route('homepage-sections.update', $section->id) }}" enctype="multipart/form-data">

                    @csrf
                    @method('put')

                    @include('dashboard.partials._errors')

                    <div class="form-group">
                        <label for="title_ar"> {{ __("Title Arabic") }}</label>
                        <input id="title_ar" name="title_ar" dir="auto" type="text" class="form-control" placeholder="{{ __("Title Arabic") }}" value="{{ old("title_ar", $section->title_ar) }}">
                    </div>

                    <div class="form-group">
                        <label for="title"> {{ __("Title") }}</label>
                        <input id="title" name="title" dir="auto" type="text" class="form-control" placeholder="{{ __("Title") }}" value="{{ old("title", $section->title) }}">
                    </div>

                    <div class="form-group">
                        <label for="content_ar">{{ __("Content Arabic") }}</label>
                        <textarea id="content_ar" name="content_ar" class="form-control" style="display: none;">{!! old("content_ar", $section->content_ar) !!}</textarea>
                        <div id="quill-editor-ar" style="height: 150px;">{!! old("content_ar", $section->content_ar) !!}</div>
                    </div>

                    <div class="form-group">
                        <label for="content">{{ __("Content") }}</label>
                        <textarea id="content" name="content" class="form-control" style="display: none;">{!! old("content", $section->content) !!}</textarea>
                        <div id="quill-editor" style="height: 150px;">{!! old("content", $section->content) !!}</div>
                    </div>

                    <div class="form-group">
                        <label for="icon"> {{ __("Icon") }}</label>
                        <input id="icon" name="icon" dir="auto" type="file" class="form-control" placeholder="{{ __("Icon") }}" value="{{ old("icon") }}">
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

@push('admin_scripts')
    <!-- Quill JS -->
    <script src="{{ asset('dashboard/vendor/quill.min.js') }}"></script>
    <script src="{{ asset('dashboard/js/quill.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize Quill for content
            var quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: 'Content',
            });

            var textarea = document.querySelector('textarea[name="content"]');

            if (textarea && textarea.value) {
                quill.clipboard.dangerouslyPasteHTML(textarea.value);
            }

            quill.on('text-change', function() {
                if (textarea) {
                    textarea.value = quill.root.innerHTML;
                    console.log('Synced content:', textarea.value.substring(0, 50) + '...');
                }
            });

            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function (e) {
                    console.log('Form submitting...');
                    if (textarea) {
                        textarea.value = quill.root.innerHTML;
                        console.log('Final content set:', textarea.value.length, 'chars');
                    }
                });
            }

            // Initialize Quill for content_ar
            var quillAr = new Quill('#quill-editor-ar', {
                theme: 'snow',
                placeholder: 'Content Arabic',
            });

            var textareaAr = document.querySelector('textarea[name="content_ar"]');

            if (textareaAr && textareaAr.value) {
                quillAr.clipboard.dangerouslyPasteHTML(textareaAr.value);
            }

            quillAr.on('text-change', function() {
                if (textareaAr) {
                    textareaAr.value = quillAr.root.innerHTML;
                    console.log('Synced content_ar:', textareaAr.value.substring(0, 50) + '...');
                }
            });

            if (form) {
                form.addEventListener('submit', function (e) {
                    console.log('Form submitting...');
                    if (textareaAr) {
                        textareaAr.value = quillAr.root.innerHTML;
                        console.log('Final content_ar set:', textareaAr.value.length, 'chars');
                    }
                });
            }
        });
    </script>
@endpush
