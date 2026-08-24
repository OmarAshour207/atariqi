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

        $current = $routeName;
        $visited = [];

        while ($current && ! in_array($current, $visited, true)) {
            $visited[] = $current;

            if ($this->adminHasAssignedPage($current, $admin)) {
                return true;
            }

            $current = $this->resolveFallbackRoute($current);
        }

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

        if (! $admin || ! $admin->is_active) {
            return false;
        }

        if ($this->isCompanyAdmin($admin)) {
            return true;
        }

        if (! $routeName) {
            return false;
        }

        return $this->adminHasAssignedPage($routeName, $admin);
    }

    public function resolveRequiredPermission(Request $request, ?string $routeName): string
    {
        $method = strtoupper($request->method());
        $routeName = (string) ($routeName ?? '');

        if (in_array($method, ['GET', 'HEAD'], true)) {
            if (preg_match('/\.(create)$/', $routeName)) {
                return Admin::ACTION_UPDATE;
            }

            return Admin::ACTION_VIEW;
        }

        if ($method === 'DELETE' || preg_match('/\.(destroy|delete|cancelPackage)$/', $routeName)) {
            return Admin::ACTION_DELETE;
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

        if ($routeName && ! $this->canAccessRoute($routeName, $admin)) {
            return false;
        }

        $code = $this->normalizePermission($code);

        if ($code === Admin::ACTION_VIEW) {
            return $admin->hasAnyPermission(Admin::availableActions());
        }

        return $admin->hasPermissionTo($code, 'admin');
    }

    public function normalizePermission(string $code): string
    {
        $code = strtolower(trim($code));

        return match ($code) {
            'view', 'show' => Admin::ACTION_VIEW,
            'delete', 'destroy', 'cancel' => Admin::ACTION_DELETE,
            default => Admin::ACTION_UPDATE,
        };
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
            'drivers.assignToAdmin' => 'drivers.index',
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
            'passengers.assign-to-admin' => 'passengers.index',
            'passengers.ban' => 'passengers.index',
            'passengers.updateApproval' => 'passengers.index',
            'users.unride-rates' => 'users.unride-rates',
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
