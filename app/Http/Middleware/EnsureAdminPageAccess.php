<?php

namespace App\Http\Middleware;

use App\Services\AdminAuthorizationService;
use Closure;
use Illuminate\Http\Request;

class EnsureAdminPageAccess
{
    public function __construct(private AdminAuthorizationService $authz)
    {
    }

    public function handle(Request $request, Closure $next, ?string $permission = null)
    {
        $admin = auth()->guard('admin')->user();

        if (!$admin) {
            return redirect()->route('dashboard.login');
        }

        if (!$admin->is_active) {
            auth()->guard('admin')->logout();

            return redirect()->route('dashboard.login')->with('error', __('Your account is inactive.'));
        }

        $routeName = $request->route()?->getName();

        $exemptRoutes = [
            'dashboard.index',
            'dashboard.logout',
            'profile.edit',
            'profile.update',
            'language',
        ];

        if (in_array($routeName, $exemptRoutes, true)) {
            return $next($request);
        }

        if (!$this->authz->canAccessRoute($routeName, $admin)) {
            abort(403, __('You do not have permission to access this page.'));
        }

        $permission = $permission ?? $this->authz->resolveRequiredPermission($request, $routeName);

        if (!$this->authz->hasPermission($permission, $routeName, $admin)) {
            abort(403, __('You do not have permission to perform this action.'));
        }

        return $next($request);
    }
}
