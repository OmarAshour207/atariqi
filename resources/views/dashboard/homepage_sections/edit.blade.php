@extends('dashboard.layouts.app')

@push('admin_styles')
    @if(app()->getLocale() === 'ar')
        <link type="text/css" href="{{ asset('dashboard/css/vendor-quill.rtl.css') }}" rel="stylesheet">
    @else
        <link type="text/css" href="{{ asset('dashboard/css/vendor-quill.css') }}" rel="stylesheet">
    @endif
    <style>
        .ql-editor[dir="rtl"] {
            direction: rtl;
            text-align: right;
        }
        .ql-editor[dir="ltr"] {
            direction: ltr;
            text-align: left;
        }
        .homepage-section-form .form-actions {
            display: flex;
            justify-content: flex-end;
        }
    </style>
@endpush

@section('content')
    <div class="mdk-drawer-layout__content page">
        <div class="container-fluid page__heading-container">
            <div class="page__heading d-flex align-items-center">
                <div class="flex">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}"><i class="material-icons icon-20pt">home</i> {{ __('Home') }} </a></li>
                            <li class="breadcrumb-item">{{ __(ucwords(str_replace('_', ' ', $section->section_key))) }}</li>
                            <li class="breadcrumb-item active" aria-current="page">{{ __('Edit') }}</li>
                        </ol>
                    </nav>
                    <h1 class="m-0">{{ __(ucwords(str_replace('_', ' ', $section->section_key))) }}</h1>
                </div>
            </div>
        </div>

        <div class="container-fluid page__container">
            <div class="card card-form__body card-body">
                <form method="post" action="{{ route('homepage-sections.update', $section->id) }}" enctype="multipart/form-data" class="homepage-section-form">
                    @csrf
                    @method('put')

                    @include('dashboard.partials._errors')

                    <div class="form-group">
                        <label for="title_ar">{{ __('Title Arabic') }}</label>
                        <input id="title_ar" name="title_ar" dir="rtl" type="text" class="form-control" placeholder="{{ __('Title Arabic') }}" value="{{ old('title_ar', $section->title_ar) }}">
                    </div>

                    <div class="form-group">
                        <label for="title">{{ __('Title') }}</label>
                        <input id="title" name="title" dir="ltr" type="text" class="form-control" placeholder="{{ __('Title') }}" value="{{ old('title', $section->title) }}">
                    </div>

                    <div class="form-group">
                        <label for="content_ar">{{ __('Content Arabic') }}</label>
                        <textarea id="content_ar" name="content_ar" class="form-control" style="display: none;">{{ old('content_ar', $section->content_ar) }}</textarea>
                        <div id="quill-editor-ar" style="height: 150px;"></div>
                    </div>

                    <div class="form-group">
                        <label for="content">{{ __('Content') }}</label>
                        <textarea id="content" name="content" class="form-control" style="display: none;">{{ old('content', $section->content) }}</textarea>
                        <div id="quill-editor" style="height: 150px;"></div>
                    </div>

                    <div class="form-group">
                        <label for="icon">{{ __('Icon') }}</label>
                        @if(!empty($section->icon))
                            <div class="mb-2">
                                <img src="{{ url($section->icon) }}" alt="{{ $section->title }}" style="max-width: 120px; max-height: 120px; border-radius: 6px;">
                            </div>
                        @endif
                        <input id="icon" name="icon" type="file" class="form-control" accept="image/*">
                    </div>

                    <div class="form-actions mb-5">
                        @adminCan('update')
                        <input type="submit" class="btn btn-success" value="{{ __('Update') }}">
                        @endadminCan
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('admin_scripts')
    <script src="{{ asset('dashboard/vendor/quill.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function bindQuill(editorSelector, textareaName, direction) {
                var editorEl = document.querySelector(editorSelector);
                var textarea = document.querySelector('textarea[name="' + textareaName + '"]');
                if (!editorEl || !textarea || typeof Quill === 'undefined') {
                    return null;
                }

                var quill = new Quill(editorSelector, {
                    theme: 'snow',
                    placeholder: direction === 'rtl' ? 'Content Arabic' : 'Content',
                });

                quill.root.setAttribute('dir', direction);

                if (textarea.value) {
                    quill.clipboard.dangerouslyPasteHTML(textarea.value);
                }

                quill.on('text-change', function () {
                    textarea.value = quill.root.innerHTML;
                });

                return quill;
            }

            var quillEn = bindQuill('#quill-editor', 'content', 'ltr');
            var quillAr = bindQuill('#quill-editor-ar', 'content_ar', 'rtl');

            var form = document.querySelector('.homepage-section-form');
            if (form) {
                form.addEventListener('submit', function () {
                    if (quillEn) {
                        document.querySelector('textarea[name="content"]').value = quillEn.root.innerHTML;
                    }
                    if (quillAr) {
                        document.querySelector('textarea[name="content_ar"]').value = quillAr.root.innerHTML;
                    }
                });
            }
        });
    </script>
@endpush
