<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\WebPage;
use Illuminate\Http\Request;

class AdminAuthorizationService
{
    public function isCompanyAdmin(?Admin $admin = null): bool
    {
        $admin = $admin ?? auth()->guard('admin')->user();

        return (bool) $admin?->isCompanyAdmin();
    }

    public function canAccessRoute(?string $routeName, ?Admin $admin = null): bool
    {
        $admin = $admin ?? auth()->guard('admin')->user();

        if (! $admin || ! $admin->is_active) {
            return false;
        }

        if ($this->isCompanyAdmin($admin)) {
            return true;
        }

        if (! $routeName) {
            return false;
        }

        $pageRoute = $this->resolvePageRoute($routeName);

        if (! $pageRoute) {
            return false;
        }

        if ($this->adminHasAssignedPage($pageRoute, $admin)) {
            return true;
        }

        $resource = Admin::resourceKeyFromRoute($pageRoute);

        return $resource
            ? $this->adminHasAnyNamedPermission($admin, $this->resourcePermissions($resource))
            : false;
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

        if (! $admin || ! $admin->is_active) {
            return false;
        }

        if ($this->isCompanyAdmin($admin)) {
            return true;
        }

        if (! $routeName) {
            return false;
        }

        $pageRoute = $this->resolvePageRoute($routeName) ?? $routeName;

        if ($this->adminHasAssignedPage($pageRoute, $admin)) {
            return true;
        }

        $resource = Admin::resourceKeyFromRoute($pageRoute);

        return $resource
            ? $this->adminHasAnyNamedPermission($admin, $this->resourcePermissions($resource))
            : false;
    }

    public function resolveRequiredPermission(Request $request, ?string $routeName): string
    {
        $method = strtoupper($request->method());
        $routeName = (string) ($routeName ?? '');

        if (in_array($method, ['GET', 'HEAD'], true)) {
            if (preg_match('/\.(create)$/', $routeName)) {
                return Admin::ACTION_ADD_DELETE;
            }

            // Allow opening edit screens with view; submit remains gated by update.
            return Admin::ACTION_VIEW;
        }

        if (preg_match('/\.(ban)$/', $routeName)) {
            return Admin::ACTION_BAN;
        }

        if (preg_match('/\.(close)$/', $routeName)) {
            return Admin::ACTION_CLOSE;
        }

        if (preg_match('/(assignToAdmin|assign-to-admin|support-tickets\.assign)$/', $routeName)) {
            return Admin::ACTION_ASSIGN;
        }

        if (preg_match('/(approve|reject|updateStatus|edit-info-request\.update)/', $routeName)) {
            return Admin::ACTION_DECIDE;
        }

        if (preg_match('/sendPaymentReminder/', $routeName)) {
            return Admin::ACTION_UPDATE;
        }

        if ($method === 'DELETE' || preg_match('/\.(destroy|delete|cancelPackage|store)$/', $routeName)) {
            return Admin::ACTION_ADD_DELETE;
        }

        if (preg_match('/\.(update|replace)$/', $routeName) || in_array($method, ['PUT', 'PATCH'], true)) {
            return Admin::ACTION_UPDATE;
        }

        if (preg_match('/\.(reply|assignPackage)$/', $routeName)) {
            return Admin::ACTION_UPDATE;
        }

        return Admin::ACTION_UPDATE;
    }

    public function hasPermission(string $code, ?string $routeName = null, ?Admin $admin = null): bool
    {
        $admin = $admin ?? auth()->guard('admin')->user();

        if (! $admin || ! $admin->is_active) {
            return false;
        }

        if ($this->isCompanyAdmin($admin)) {
            return true;
        }

        $routeName = $routeName ?? optional(request()->route())->getName();

        if (str_contains(trim($code), ' ')) {
            return $this->adminHasNamedPermission($admin, strtolower(trim($code)));
        }

        $action = Admin::normalizePermissionAction($code);
        $pageRoute = $this->resolvePageRoute($routeName);
        $resource = Admin::resourceKeyFromRoute($pageRoute);

        if (! $resource) {
            return false;
        }

        if ($routeName && ! $this->canAccessRoute($routeName, $admin)) {
            return false;
        }

        $permissionNames = $this->resourcePermissions($resource);

        if ($action === Admin::ACTION_VIEW) {
            return $this->adminHasAnyNamedPermission($admin, $permissionNames);
        }

        $permission = Admin::permissionName($action, $resource);

        if (! in_array($permission, $permissionNames, true)) {
            return false;
        }

        return $this->adminHasNamedPermission($admin, $permission);
    }

    public function normalizePermission(string $code): string
    {
        return Admin::normalizePermissionAction($code);
    }

    public function resolvePageRoute(?string $routeName): ?string
    {
        if (! $routeName) {
            return null;
        }

        $current = $routeName;
        $visited = [];

        while ($current && ! in_array($current, $visited, true)) {
            $visited[] = $current;

            if ($this->resolvePageForRoute($current)) {
                return $current;
            }

            $current = $this->resolveFallbackRoute($current);
        }

        return null;
    }

    public function resolveFallbackRoute(?string $routeName): ?string
    {
        if (! $routeName) {
            return null;
        }

        $explicit = [
            'homepage-sections.create' => 'homepage-sections.index',
            'homepage-sections.edit' => 'homepage-sections.index',
            'homepage-sections.update' => 'homepage-sections.index',
            'homepage-sections.destroy' => 'homepage-sections.index',
            'homepage-stats.create' => 'homepage-stats.index',
            'homepage-stats.edit' => 'homepage-stats.index',
            'homepage-stats.update' => 'homepage-stats.index',
            'homepage-stats.destroy' => 'homepage-stats.index',
            'testimonials.create' => 'testimonials.index',
            'testimonials.edit' => 'testimonials.index',
            'testimonials.update' => 'testimonials.index',
            'testimonials.destroy' => 'testimonials.index',
            'partner-achievements.create' => 'partner-achievements.index',
            'partner-achievements.edit' => 'partner-achievements.index',
            'partner-achievements.update' => 'partner-achievements.index',
            'partner-achievements.destroy' => 'partner-achievements.index',
            'drivers.packages' => 'drivers.packages',
            'drivers.packagePlans' => 'drivers.packages',
            'drivers.assignPackage' => 'drivers.packages',
            'drivers.cancelPackage' => 'drivers.packages',
            'drivers.rates' => 'drivers.rates',
            'drivers.trips' => 'drivers.trips',
            'drivers.driverTrips' => 'drivers.trips',
            'drivers.earnings' => 'drivers.index',
            'drivers.sendPaymentReminder' => 'drivers.index',
            'drivers.updateStatus' => 'new-drivers.index',
            'drivers.assignToAdmin' => 'new-drivers.index',
            'drivers.ban' => 'drivers.index',
            'drivers.show' => 'drivers.index',
            'drivers.edit' => 'drivers.index',
            'drivers.update' => 'drivers.index',
            'general-dues-percentage.update' => 'general-dues-percentage.show',
            'edit-info-request.show' => 'edit-info-request.index',
            'edit-info-request.update' => 'edit-info-request.index',
            'passengers.all-trips' => 'passengers.all-trips',
            'passengers.profile-update-requests' => 'passengers.profile-update-requests',
            'passengers.show' => 'passengers.index',
            'passengers.trips' => 'passengers.index',
            'passengers.complaints' => 'passengers.index',
            'passengers.approve-profile-update' => 'passengers.profile-update-requests',
            'passengers.reject-profile-update' => 'passengers.profile-update-requests',
            'passengers.assign-to-admin' => 'passengers.profile-update-requests',
            'passengers.ban' => 'passengers.index',
            'passengers.updateApproval' => 'passengers.index',
            'users.unride-rates' => 'users.unride-rates',
            'users.trips' => 'users.trips',
            'users.rates' => 'users.rates',
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
            'packages.create' => 'packages.index',
            'packages.store' => 'packages.index',
            'packages.edit' => 'packages.index',
            'packages.update' => 'packages.index',
            'packages.destroy' => 'packages.index',
            'features.create' => 'features.index',
            'features.store' => 'features.index',
            'features.edit' => 'features.index',
            'features.update' => 'features.index',
            'features.destroy' => 'features.index',
            'employees.create' => 'employees.index',
            'employees.store' => 'employees.index',
            'employees.edit' => 'employees.index',
            'employees.update' => 'employees.index',
            'roles.create' => 'roles.index',
            'roles.store' => 'roles.index',
            'roles.edit' => 'roles.index',
            'roles.update' => 'roles.index',
            'roles.destroy' => 'roles.index',
            'logs.show' => 'logs.index',
        ];

        if (isset($explicit[$routeName]) && $explicit[$routeName] !== $routeName) {
            return $explicit[$routeName];
        }

        if (! str_contains($routeName, '.')) {
            return null;
        }

        [$prefix] = explode('.', $routeName, 2);
        $indexRoute = $prefix . '.index';

        return $indexRoute !== $routeName ? $indexRoute : null;
    }

    private function resourcePermissions(string $resource): array
    {
        $page = collect(Admin::permissionsMatrix())
            ->firstWhere('resource', $resource);

        if (! $page) {
            return [Admin::permissionName(Admin::ACTION_VIEW, $resource)];
        }

        return array_values($page['permissions']);
    }

    private function adminHasNamedPermission(Admin $admin, string $permission): bool
    {
        // Never ask Spatie about a name that is not defined for this guard.
        $owned = $admin->getAllPermissions()->pluck('name');

        return $owned->contains($permission);
    }

    private function adminHasAnyNamedPermission(Admin $admin, array $permissions): bool
    {
        $permissions = array_values(array_filter($permissions));

        if (! $permissions) {
            return false;
        }

        $owned = $admin->getAllPermissions()->pluck('name')->all();

        return (bool) array_intersect($permissions, $owned);
    }

    private function adminHasAssignedPage(string $routeName, Admin $admin): bool
    {
        return $admin->pages()
            ->where('web_pages.route', $routeName)
            ->where('web_pages.is_active', true)
            ->exists();
    }

    public function resolvePageForRoute(?string $routeName): ?WebPage
    {
        if (! $routeName) {
            return null;
        }

        return WebPage::where('route', $routeName)->where('is_active', true)->first();
    }
}
