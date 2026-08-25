<?php

namespace App\Models;

use Database\Seeders\WebPagesSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
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
            $admin->type = $admin->role === self::ROLE_ADMIN ? self::ROLE_ADMIN : $admin->role;
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

    public static function availableActions(): array
    {
        return [
            self::ACTION_VIEW,
            self::ACTION_UPDATE,
            self::ACTION_DELETE,
        ];
    }

    public static function systemRoleNames(): array
    {
        return [
            self::ROLE_ADMIN,
            self::ROLE_SUPPORT,
            self::ROLE_AGENT,
        ];
    }

    public static function availableRoles(): array
    {
        return Role::where('guard_name', 'admin')
            ->orderBy('name')
            ->pluck('name')
            ->all();
    }

    public static function resourceKeyFromRoute(?string $route): ?string
    {
        if (! $route) {
            return null;
        }

        $resource = preg_replace('/\.(index|show|create|edit|store|update|destroy)$/', '', $route);

        return $resource !== '' ? $resource : $route;
    }

    public static function permissionName(string $action, string $resource): string
    {
        return self::normalizePermissionAction($action) . ' ' . $resource;
    }

    public static function normalizePermissionAction(string $action): string
    {
        $action = strtolower(trim($action));

        return match ($action) {
            'view', 'show' => self::ACTION_VIEW,
            'delete', 'destroy', 'cancel' => self::ACTION_DELETE,
            default => self::ACTION_UPDATE,
        };
    }

    public static function resourceKeys(): array
    {
        return collect(WebPagesSeeder::definitions())
            ->map(fn (array $page) => self::resourceKeyFromRoute($page['route']))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public static function allPermissionNames(): array
    {
        $names = [];

        foreach (self::resourceKeys() as $resource) {
            foreach (self::availableActions() as $action) {
                $names[] = self::permissionName($action, $resource);
            }
        }

        return $names;
    }

    public static function permissionsMatrix(): array
    {
        return collect(WebPagesSeeder::definitions())
            ->map(function (array $page) {
                $resource = self::resourceKeyFromRoute($page['route']);

                return [
                    'name' => $page['name'],
                    'route' => $page['route'],
                    'resource' => $resource,
                    'permissions' => [
                        self::ACTION_VIEW => self::permissionName(self::ACTION_VIEW, $resource),
                        self::ACTION_UPDATE => self::permissionName(self::ACTION_UPDATE, $resource),
                        self::ACTION_DELETE => self::permissionName(self::ACTION_DELETE, $resource),
                    ],
                ];
            })
            ->values()
            ->all();
    }

    public static function pageIdsFromPermissionNames(array $permissions): array
    {
        $resources = [];

        foreach ($permissions as $permission) {
            if (! str_contains($permission, ' ')) {
                continue;
            }

            [, $resource] = explode(' ', $permission, 2);
            $resources[$resource] = true;
        }

        return WebPage::where('is_active', true)
            ->get()
            ->filter(function (WebPage $page) use ($resources) {
                $resource = self::resourceKeyFromRoute($page->route);

                return $resource && isset($resources[$resource]);
            })
            ->pluck('id')
            ->all();
    }

    public static function ensureViewWithMutations(array $permissions): array
    {
        $byResource = [];

        foreach ($permissions as $permission) {
            if (! str_contains($permission, ' ')) {
                continue;
            }

            [$action, $resource] = explode(' ', $permission, 2);
            $byResource[$resource][] = $action;
        }

        foreach ($byResource as $resource => $actions) {
            if (array_intersect($actions, [self::ACTION_UPDATE, self::ACTION_DELETE])
                && ! in_array(self::ACTION_VIEW, $actions, true)
            ) {
                $permissions[] = self::permissionName(self::ACTION_VIEW, $resource);
            }
        }

        return array_values(array_unique($permissions));
    }

    public static function slugifyRoleName(string $name): string
    {
        return Str::slug(strtolower(trim($name)), '-');
    }

    public static function normalizeRole(?string $role, ?string $type = null): string
    {
        $role = strtolower(trim((string) $role));
        $type = strtolower(trim((string) $type));

        if ($role === '' && $type !== '') {
            $role = $type;
        }

        if ($role === 'supervisor') {
            $role = self::ROLE_SUPPORT;
        }

        if ($type === 'admin' || $role === self::ROLE_ADMIN) {
            return self::ROLE_ADMIN;
        }

        if ($role === '') {
            return self::ROLE_AGENT;
        }

        return self::slugifyRoleName($role) ?: self::ROLE_AGENT;
    }
}
