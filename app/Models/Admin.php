<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory, HasRoles, Notifiable;

    public const ROLE_ADMIN = 'admin';
    public const ROLE_SUPPORT = 'support';
    public const ROLE_AGENT = 'agent';

    public const ACTION_VIEW = 'view';
    public const ACTION_UPDATE = 'update';
    public const ACTION_DELETE = 'delete';

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

    protected $guard_name = 'admin';

    protected static function booted(): void
    {
        static::saving(function (self $admin) {
            $admin->role = self::normalizeRole($admin->role, $admin->type);
            $admin->type = $admin->role;
        });
    }

    public function pages(): BelongsToMany
    {
        return $this->belongsToMany(WebPage::class, 'employee_page', 'employee_id', 'page_id')
            ->withTimestamps();
    }

    public function isCompanyAdmin(): bool
    {
        return $this->hasRole(self::ROLE_ADMIN) || $this->role === self::ROLE_ADMIN;
    }

    public static function availableRoles(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_SUPPORT,
            self::ROLE_AGENT,
        ];
    }

    public static function availableActions(): array
    {
        return [
            self::ACTION_VIEW,
            self::ACTION_UPDATE,
            self::ACTION_DELETE,
        ];
    }

    public static function normalizeRole(?string $role, ?string $type = null): string
    {
        $values = [
            strtolower(trim((string) $role)),
            strtolower(trim((string) $type)),
        ];

        if (in_array('admin', $values, true)) {
            return self::ROLE_ADMIN;
        }

        if (in_array('support', $values, true) || in_array('supervisor', $values, true)) {
            return self::ROLE_SUPPORT;
        }

        return self::ROLE_AGENT;
    }
}
