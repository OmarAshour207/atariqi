<?php

namespace App\Models;

use Database\Seeders\WebPagesSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Schema;
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
    public const ACTION_DECIDE = 'decide';
    public const ACTION_ADD_DELETE = 'add-delete';
    public const ACTION_UPDATE = 'update';
    public const ACTION_ASSIGN = 'assign';
    public const ACTION_CLOSE = 'close';
    public const ACTION_BAN = 'ban';

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
            $resolved = self::resolveStoredRole($admin->role, $admin->type);
            $admin->role = $resolved['role'];
            $admin->type = $resolved['type'];
        });
    }

    public function scopeRole($query, string $roleName)
    {
        return $query->where('role', $roleName);
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
            self::ACTION_DECIDE,
            self::ACTION_ADD_DELETE,
            self::ACTION_UPDATE,
            self::ACTION_ASSIGN,
            self::ACTION_CLOSE,
            self::ACTION_BAN,
        ];
    }

    public static function actionLabels(): array
    {
        return [
            self::ACTION_VIEW => 'View',
            self::ACTION_DECIDE => 'Approve / Reject',
            self::ACTION_ADD_DELETE => 'Add / Delete',
            self::ACTION_UPDATE => 'Update / Remind',
            self::ACTION_ASSIGN => 'Assign',
            self::ACTION_CLOSE => 'Close',
            self::ACTION_BAN => 'Ban',
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
            'decide', 'approve', 'reject', 'accept' => self::ACTION_DECIDE,
            'add-delete', 'add', 'delete', 'destroy', 'cancel', 'manage' => self::ACTION_ADD_DELETE,
            'assign' => self::ACTION_ASSIGN,
            'close' => self::ACTION_CLOSE,
            'ban' => self::ACTION_BAN,
            'update', 'edit', 'remind', 'replace' => self::ACTION_UPDATE,
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

        foreach (self::permissionsMatrix() as $page) {
            foreach ($page['permissions'] as $permission) {
                $names[] = $permission;
            }
        }

        return array_values(array_unique($names));
    }

    public static function permissionsMatrix(): array
    {
        return collect(WebPagesSeeder::definitions())
            ->map(function (array $page) {
                $resource = self::resourceKeyFromRoute($page['route']);
                $actions = array_values(array_intersect(
                    $page['actions'] ?? [self::ACTION_VIEW],
                    self::availableActions()
                ));

                if (! in_array(self::ACTION_VIEW, $actions, true)) {
                    array_unshift($actions, self::ACTION_VIEW);
                }

                $permissions = [];
                foreach ($actions as $action) {
                    $permissions[$action] = self::permissionName($action, $resource);
                }

                return [
                    'name' => $page['name'],
                    'route' => $page['route'],
                    'resource' => $resource,
                    'group' => $page['group'] ?? 'general',
                    'actions' => $actions,
                    'permissions' => $permissions,
                ];
            })
            ->values()
            ->all();
    }

    public static function permissionsMatrixGrouped(): array
    {
        $labels = WebPagesSeeder::groupLabels();

        return collect(self::permissionsMatrix())
            ->groupBy('group')
            ->map(function ($pages, $group) use ($labels) {
                return [
                    'key' => $group,
                    'label' => $labels[$group] ?? ucfirst((string) $group),
                    'pages' => $pages->values()->all(),
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
            $nonView = array_diff($actions, [self::ACTION_VIEW]);
            if ($nonView && ! in_array(self::ACTION_VIEW, $actions, true)) {
                $permissions[] = self::permissionName(self::ACTION_VIEW, $resource);
            }
        }

        return array_values(array_unique($permissions));
    }

    public static function roleLabel(string $role): string
    {
        $key = 'role.' . $role;
        $translated = __($key);

        if ($translated !== $key) {
            return $translated;
        }

        return ucfirst(str_replace('-', ' ', $role));
    }

    public static function isValidRoleSlug(string $name): bool
    {
        return (bool) preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $name);
    }

    public static function slugifyRoleName(string $name): string
    {
        $name = trim($name);

        if ($name === '') {
            return '';
        }

        if (self::isValidRoleSlug($name)) {
            return $name;
        }

        if (preg_match('/[|\/]/', $name)) {
            foreach (array_reverse(preg_split('/[|\/]/', $name) ?: []) as $part) {
                $slug = Str::slug(strtolower(trim($part)), '-');
                if ($slug !== '') {
                    return $slug;
                }
            }
        }

        $slug = Str::slug(strtolower($name), '-');

        if ($slug !== '') {
            return $slug;
        }

        $ascii = preg_replace('/[^a-zA-Z0-9\s-]/', '', $name) ?? '';

        return Str::slug(strtolower(trim($ascii)), '-');
    }

    public static function resolveStoredRole(?string $role, ?string $type = null): array
    {
        foreach (array_values(array_unique(array_filter([trim((string) $role), trim((string) $type)]))) as $candidate) {
            $record = Role::where('guard_name', 'admin')->where('name', $candidate)->first();

            if ($record) {
                $canonical = self::isValidRoleSlug($record->name)
                    ? $record->name
                    : self::slugifyRoleName($record->name);

                if ($canonical !== '' && self::roleExists($canonical)) {
                    return self::storedRolePayload($canonical);
                }

                return self::storedRolePayload($record->name);
            }

            $slugged = self::slugifyRoleName($candidate);

            if ($slugged !== '' && self::roleExists($slugged)) {
                return self::storedRolePayload($slugged);
            }
        }

        $normalized = self::normalizeRole($role, $type);

        return self::storedRolePayload($normalized);
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

        if ($type === self::ROLE_ADMIN || $role === self::ROLE_ADMIN) {
            return self::ROLE_ADMIN;
        }

        if ($role === '') {
            return self::ROLE_AGENT;
        }

        if (self::roleExists($role) && self::isValidRoleSlug($role)) {
            return $role;
        }

        $slugged = self::slugifyRoleName($role);

        if ($slugged !== '' && self::roleExists($slugged)) {
            return $slugged;
        }

        return $slugged !== '' ? $slugged : self::ROLE_AGENT;
    }

    private static function storedRolePayload(string $role): array
    {
        return [
            'role' => $role,
            'type' => $role === self::ROLE_ADMIN ? self::ROLE_ADMIN : $role,
        ];
    }

    public static function roleExists(string $roleName): bool
    {
        if ($roleName === '' || ! Schema::hasTable('roles')) {
            return false;
        }

        return Role::where('guard_name', 'admin')->where('name', $roleName)->exists();
    }
}
