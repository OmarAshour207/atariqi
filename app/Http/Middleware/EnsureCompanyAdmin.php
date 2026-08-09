<?php

namespace App\Http\Middleware;

use App\Services\AdminAuthorizationService;
use Closure;
use Illuminate\Http\Request;

class EnsureCompanyAdmin
{
    public function __construct(private AdminAuthorizationService $authz)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        if (!$this->authz->isCompanyAdmin()) {
            abort(403, __('Only company administrators can access this section.'));
        }

        return $next($request);
    }
}
