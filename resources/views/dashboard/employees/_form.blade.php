<div class="form-group">
    <label>{{ __('Name') }}</label>
    <input name="name" class="form-control" value="{{ old('name', $employee->name ?? '') }}" required>
</div>
<div class="form-group">
    <label>{{ __('Email') }}</label>
    <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email ?? '') }}" required>
</div>
<div class="form-group">
    <label>{{ __('Password') }}</label>
    <input type="password" name="password" class="form-control" {{ $employee ? '' : 'required' }}>
    @if($employee)
        <small class="form-text text-muted">{{ __('Leave blank to keep the current password.') }}</small>
    @endif
</div>
<div class="form-group">
    <label>{{ __('Password Confirmation') }}</label>
    <input type="password" name="password_confirmation" class="form-control" {{ $employee ? '' : 'required' }}>
</div>
<div class="form-group">
    <label>{{ __('Role') }}</label>
    <select name="role" class="form-control" required>
        @foreach($roles as $role)
            <option value="{{ $role }}" {{ old('role', $employee->role ?? '') === $role ? 'selected' : '' }}>
                {{ \App\Models\Admin::roleLabel($role) }}
            </option>
        @endforeach
    </select>
    <small class="form-text text-muted">{{ __('Permissions come from the selected role. Manage them in Roles Management.') }}</small>
</div>
<div class="form-check mb-3">
    <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $employee->is_active ?? true) ? 'checked' : '' }}>
    <label for="is_active" class="form-check-label">{{ __('Active') }}</label>
</div>
