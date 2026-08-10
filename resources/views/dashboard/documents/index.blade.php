@extends('dashboard.layouts.app')
@section('content')
<div class="mdk-drawer-layout__content page">
    <div class="container-fluid page__container">
        @include('dashboard.partials.session')
        <h1>{{ __('Documents Management') }}</h1>
        <div class="card table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                <tr>
                    <th>{{ __('Document Type') }}</th>
                    <th>{{ __('File Name') }}</th>
                    <th>{{ __('File Path') }}</th>
                    <th>{{ __('Last Updated') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($documents as $document)
                    <tr>
                        <td>{{ $document->{'title-ar'} ?? $document->{'title-eng'} }}</td>
                        <td>{{ basename($document->getRawOriginal('file-link')) }}</td>
                        <td>{{ $document->getRawOriginal('file-link') }}</td>
                        <td>{{ $document->{'date-of-edit'} ?? $document->{'date-of-add'} }}</td>
                        <td>
                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-info">{{ __('Download') }}</a>
                            <button class="btn btn-sm btn-warning" data-toggle="modal" data-target="#replaceDoc{{ $document->id }}">{{ __('Replace') }}</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center">{{ __('No documents found.') }}</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @foreach($documents as $document)
            <div class="modal fade" id="replaceDoc{{ $document->id }}" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <form method="POST" action="{{ route('documents.replace', $document) }}" enctype="multipart/form-data" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title">{{ __('Replace Document') }}</h5>
                            <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <input type="file" name="file" accept="application/pdf" class="form-control" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
