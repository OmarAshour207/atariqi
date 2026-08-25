<div class="table-responsive">
    <table class="table table-bordered table-sm mb-0">
        <thead>
        <tr>
            <th>
                {{ __('Page') }}
                <div class="mt-1">
                    <button type="button" class="btn btn-link btn-sm p-0" id="select-all-view">{{ __('Select all View') }}</button>
                </div>
            </th>
            <th class="text-center" style="width: 110px;">{{ __('View') }}</th>
            <th class="text-center" style="width: 110px;">{{ __('Update') }}</th>
            <th class="text-center" style="width: 110px;">{{ __('Delete') }}</th>
        </tr>
        </thead>
        <tbody>
        @foreach($matrix as $row)
            <tr>
                <td>
                    <strong>{{ __($row['name']) }}</strong>
                    <div class="text-muted small">{{ $row['resource'] }}</div>
                </td>
                @foreach(['view', 'update', 'delete'] as $action)
                    @php $perm = $row['permissions'][$action]; @endphp
                    <td class="text-center align-middle">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $perm }}"
                            class="form-check-input m-0 perm-{{ $action }}"
                            {{ in_array($perm, $selected ?? [], true) ? 'checked' : '' }}
                            @if(!empty($readonly)) disabled @endif
                        >
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
<p class="text-muted mt-2 mb-0">
    {{ __('Create, approve, reject, replace and assign use Update. Cancel subscription uses Delete.') }}
</p>
@once
@push('admin_scripts')
<script>
    document.getElementById('select-all-view')?.addEventListener('click', function () {
        document.querySelectorAll('.perm-view').forEach(function (el) {
            if (!el.disabled) el.checked = true;
        });
    });
</script>
@endpush
@endonce
