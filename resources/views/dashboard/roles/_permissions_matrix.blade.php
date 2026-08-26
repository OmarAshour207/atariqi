@php
    $groups = \App\Models\Admin::permissionsMatrixGrouped();
    $labels = \App\Models\Admin::actionLabels();
    $selected = $selected ?? [];
    $readonly = !empty($readonly);
@endphp

@foreach($groups as $group)
    <div class="card mb-3">
        <div class="card-header d-flex align-items-center justify-content-between">
            <strong>{{ __($group['label']) }}</strong>
            <span class="text-muted small">{{ count($group['pages']) }} {{ __('pages') }}</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th style="min-width: 220px;">{{ __('Page') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($group['pages'] as $row)
                        <tr>
                            <td>
                                <strong>{{ __($row['name']) }}</strong>
                                <div class="text-muted small">{{ $row['resource'] }}</div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap" style="gap: 10px 18px;">
                                    @foreach($row['actions'] as $action)
                                        @php $perm = $row['permissions'][$action]; @endphp
                                        <label class="mb-0 d-inline-flex align-items-center" style="gap: 6px;">
                                            <input
                                                type="checkbox"
                                                name="permissions[]"
                                                value="{{ $perm }}"
                                                class="form-check-input m-0 perm-{{ $action }}"
                                                {{ in_array($perm, $selected, true) ? 'checked' : '' }}
                                                @if($readonly) disabled @endif
                                            >
                                            <span>{{ __($labels[$action] ?? $action) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endforeach

<p class="text-muted mb-0">
    {{ __('Only actions that exist for each page are shown. View = read only. Approve/Reject, Add/Delete, Update/Remind, Assign, Close and Ban appear when the page supports them.') }}
</p>
