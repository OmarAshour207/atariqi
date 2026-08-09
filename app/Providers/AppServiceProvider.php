<?php

namespace App\Providers;

use App\Services\AdminAuthorizationService;
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
    }
}
