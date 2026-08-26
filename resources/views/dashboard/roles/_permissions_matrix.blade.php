<div class="table-responsive">
    <table class="table table-bordered table-sm mb-0">
        <thead>
        <tr>
            <th style="min-width: 180px;">
                {{ __('Page') }}
                <div class="mt-1">
                    <button type="button" class="btn btn-link btn-sm p-0" id="select-all-view">{{ __('Select all View') }}</button>
                </div>
            </th>
            @foreach(\App\Models\Admin::actionLabels() as $action => $label)
                <th class="text-center" style="min-width: 90px;">{{ __($label) }}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
        @foreach($matrix as $row)
            <tr>
                <td>
                    <strong>{{ __($row['name']) }}</strong>
                    <div class="text-muted small">{{ $row['resource'] }}</div>
                </td>
                @foreach(\App\Models\Admin::availableActions() as $action)
                    @php $perm = $row['permissions'][$action]; @endphp
                    <td class="text-center align-middle">
                        <input
                            type="checkbox"
                            name="permissions[]"
                            value="{{ $perm }}"
                            class="form-check-input m-0 perm-{{ $action }}"
                            title="{{ __(\App\Models\Admin::actionLabels()[$action]) }}"
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
    {{ __('View = read only. Approve/Reject = requests. Add/Delete = create & delete. Update/Remind = edit & reminders. Assign / Close / Ban = matching actions.') }}
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
