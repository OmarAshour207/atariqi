<div class="modal fade" id="{{ $id }}" tabindex="-1" role="dialog" aria-labelledby="{{ $id }}Label" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="POST" action="{{ $action }}" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title" id="{{ $id }}Label">{{ $title ?? __('Confirm Delete') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p>{{ __('Are you sure you want to delete this item?') }}</p>
                <div class="form-group">
                    <label>{{ __('Delete Reason') }} <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __('Cancel') }}</button>
                <button type="submit" class="btn btn-danger">{{ __('Delete') }}</button>
            </div>
        </form>
    </div>
</div>
