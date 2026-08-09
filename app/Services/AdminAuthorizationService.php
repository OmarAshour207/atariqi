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

    public function resolvePageForRoute(?string $routeName): ?WebPage
    {
        if (!$routeName) {
            return null;
        }

        return WebPage::where('route', $routeName)->where('is_active', true)->first();
    }
}
