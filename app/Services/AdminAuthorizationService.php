<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\WebPage;

class AdminAuthorizationService
{
    public function isCompanyAdmin(?Admin $admin = null): bool
    {
        $admin = $admin ?? auth()->guard('admin')->user();

        if (!$admin) {
            return false;
        }

        return ($admin->role ?? 'agent') === 'admin' || ($admin->type ?? '') === 'admin';
    }

    public function canAccessRoute(?string $routeName, ?Admin $admin = null): bool
    {
        $admin = $admin ?? auth()->guard('admin')->user();

        if (!$admin || !$admin->is_active) {
            return false;
        }

        if ($this->isCompanyAdmin($admin)) {
            return true;
        }

        if (!$routeName) {
            return false;
        }

        $current = $routeName;
        $visited = [];

        while ($current && !in_array($current, $visited, true)) {
            $visited[] = $current;

            if ($this->adminHasAssignedPage($current, $admin)) {
                return true;
            }

            $current = $this->resolveFallbackRoute($current);
        }

        return false;
    }

    public function canAccessMenuRoute(?string $routeName, ?string $fallbackRoute = null, ?Admin $admin = null): bool
    {
        if ($this->isPageAssigned($routeName, $admin)) {
            return true;
        }

        // Menu shows only explicitly assigned pages — no fallback inheritance.
        return false;
    }

    public function canAccessAnyRoute(array $routes, ?Admin $admin = null): bool
    {
        foreach ($routes as $route) {
            $routeName = is_array($route) ? ($route[0] ?? null) : $route;

            if ($routeName && $this->isPageAssigned($routeName, $admin)) {
                return true;
            }
        }

        return false;
    }

    public function isPageAssigned(?string $routeName, ?Admin $admin = null): bool
    {
        $admin = $admin ?? auth()->guard('admin')->user();

        if (!$admin || !$admin->is_active) {
            return false;
        }

        if ($this->isCompanyAdmin($admin)) {
            return true;
        }

        if (!$routeName) {
            return false;
        }

        return $this->adminHasAssignedPage($routeName, $admin);
    }

    public function resolveRequiredPermission(\Illuminate\Http\Request $request, ?string $routeName): string
    {
        $method = strtoupper($request->method());
        $routeName = (string) ($routeName ?? '');

        if ($method === 'DELETE') {
            return 'delete';
        }

        if (in_array($method, ['PUT', 'PATCH'], true)) {
            return 'edit';
        }

        if ($method === 'POST') {
            if (preg_match('/\.(store|create)$/', $routeName)) {
                return 'add';
            }

            if (preg_match('/\.(destroy|delete)$/', $routeName)) {
                return 'delete';
            }

            if (preg_match('/\.(approve|updateStatus|updateApproval|approve-profile-update)/', $routeName)) {
                return 'approve';
            }

            if (preg_match('/\.(reject|reject-profile-update|ban|close)/', $routeName)) {
                return 'reject';
            }

            return 'edit';
        }

        return 'view';
    }

    public function resolveFallbackRoute(?string $routeName): ?string
    {
        if (!$routeName) {
            return null;
        }

        $explicit = [
            'homepage-sections.create' => 'homepage-sections.index',
            'homepage-sections.edit' => 'homepage-sections.index',
            'homepage-sections.update' => 'homepage-sections.index',
            'homepage-stats.index' => 'homepage-sections.index',
            'homepage-stats.create' => 'homepage-sections.index',
            'homepage-stats.edit' => 'homepage-sections.index',
            'testimonials.index' => 'homepage-sections.index',
            'testimonials.create' => 'homepage-sections.index',
            'testimonials.edit' => 'homepage-sections.index',
            'partner-achievements.index' => 'homepage-sections.index',
            'partner-achievements.create' => 'homepage-sections.index',
            'partner-achievements.edit' => 'homepage-sections.index',
            'drivers.packages' => 'drivers.index',
            'drivers.rates' => 'drivers.index',
            'drivers.trips' => 'drivers.index',
            'drivers.packagePlans' => 'drivers.index',
            'drivers.assignPackage' => 'drivers.index',
            'drivers.cancelPackage' => 'drivers.index',
            'drivers.driverTrips' => 'drivers.index',
            'drivers.earnings' => 'drivers.index',
            'drivers.sendPaymentReminder' => 'drivers.index',
            'drivers.updateStatus' => 'drivers.index',
            'drivers.assignToAdmin' => 'drivers.index',
            'drivers.ban' => 'drivers.index',
            'general-dues-percentage.show' => 'drivers.index',
            'general-dues-percentage.update' => 'general-dues-percentage.show',
            'edit-info-request.show' => 'edit-info-request.index',
            'edit-info-request.update' => 'edit-info-request.index',
            'passengers.all-trips' => 'passengers.index',
            'passengers.profile-update-requests' => 'passengers.index',
            'passengers.show' => 'passengers.index',
            'passengers.trips' => 'passengers.index',
            'passengers.complaints' => 'passengers.index',
            'passengers.approve-profile-update' => 'passengers.index',
            'passengers.reject-profile-update' => 'passengers.index',
            'passengers.assign-to-admin' => 'passengers.index',
            'passengers.ban' => 'passengers.index',
            'passengers.updateApproval' => 'passengers.index',
            'users.unride-rates' => 'passengers.index',
            'support-tickets.show' => 'support-tickets.index',
            'support-tickets.reply' => 'support-tickets.index',
            'support-tickets.assign' => 'support-tickets.index',
            'support-tickets.close' => 'support-tickets.index',
            'announcements.create' => 'announcements.index',
            'announcements.store' => 'announcements.index',
            'announcements.destroy' => 'announcements.index',
            'universities.create' => 'universities.index',
            'universities.store' => 'universities.index',
            'universities.services' => 'universities.index',
            'universities.services.store' => 'universities.index',
            'universities.destroy' => 'universities.index',
            'cities.create' => 'cities.index',
            'cities.store' => 'cities.index',
            'cities.neighborhoods.store' => 'cities.index',
            'neighborhoods.update' => 'cities.index',
            'neighborhoods.destroy' => 'cities.index',
            'delivery-services.edit' => 'delivery-services.index',
            'delivery-services.update' => 'delivery-services.index',
            'documents.download' => 'documents.index',
            'documents.replace' => 'documents.index',
            'settings.store' => 'settings.index',
        ];

        if (isset($explicit[$routeName])) {
            return $explicit[$routeName];
        }

        if (!str_contains($routeName, '.')) {
            return null;
        }

        [$prefix, $action] = explode('.', $routeName, 2);

        $candidates = match ($action) {
            'update', 'edit' => [$prefix . '.show', $prefix . '.index'],
            'store', 'create', 'destroy', 'download', 'replace', 'reply', 'assign', 'close', 'ban',
            'cancel', 'assignPackage', 'assignToAdmin', 'sendPaymentReminder', 'updateStatus',
            'approve-profile-update', 'reject-profile-update', 'assign-to-admin', 'updateApproval',
            'services', 'storeServices', 'storeNeighborhood', 'updateNeighborhood', 'destroyNeighborhood',
            'packagePlans', 'driverTrips', 'earnings', 'complaints', 'trips', 'show' => [$prefix . '.index'],
            default => [$prefix . '.index'],
        };

        foreach ($candidates as $candidate) {
            if ($candidate !== $routeName) {
                return $candidate;
            }
        }

        return null;
    }

    private function adminHasAssignedPage(string $routeName, Admin $admin): bool
    {
        return $admin->pages()
            ->where('web_pages.route', $routeName)
            ->where('web_pages.is_active', true)
            ->exists();
    }

    public function hasPermission(string $code, ?string $routeName = null, ?Admin $admin = null): bool
    {
        $admin = $admin ?? auth()->guard('admin')->user();

        if (!$admin || !$admin->is_active) {
            return false;
        }

        if ($this->isCompanyAdmin($admin)) {
            return true;
        }

        $routeName = $routeName ?? optional(request()->route())->getName();

        if (!$this->canAccessRoute($routeName, $admin)) {
            return false;
        }

        if ($code === 'view') {
            return true;
        }

        return $admin->permissions()->where('permissions.code', $code)->exists();
    }

    public function can(string $permission, ?string $routeName = null, ?Admin $admin = null): bool
    {
        return $this->hasPermission($permission, $routeName, $admin);
    }

    public function resolvePageForRoute(?string $routeName): ?WebPage
    {
        if (!$routeName) {
            return null;
        }

        return WebPage::where('route', $routeName)->where('is_active', true)->first();
    }
}
