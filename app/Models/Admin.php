<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'employee_perm', 'employee_id', 'permission_id')
            ->withTimestamps();
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(WebPage::class, 'employee_page', 'employee_id', 'page_id')
            ->withTimestamps();
    }

    public function isCompanyAdmin(): bool
    {
        return ($this->role ?? 'agent') === 'admin' || ($this->type ?? '') === 'admin';
    }
}
