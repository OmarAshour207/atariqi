<?php

namespace App\Providers;

use App\Services\AdminAuthorizationService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        View::composer('dashboard.layouts.menu', function ($view) {
            $view->with('adminAuthz', app(AdminAuthorizationService::class));
        });

        Blade::if('adminRoute', function (?string $route, ?string $fallback = null) {
            return app(AdminAuthorizationService::class)->isPageAssigned($route);
        });

        Blade::if('adminAnyRoute', function (...$routes) {
            return app(AdminAuthorizationService::class)->canAccessAnyRoute($routes);
        });

        Blade::if('adminCan', function (string $permission, ?string $route = null) {
            return app(AdminAuthorizationService::class)->hasPermission($permission, $route);
        });
    }
}
