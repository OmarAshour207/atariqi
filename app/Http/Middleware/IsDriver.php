<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class IsDriver
{
    public function handle(Request $request, Closure $next)
    {
        if(auth()->user()->{"user-type"} != 'driver') {
            return response()->json([
                'success'   => false,
                'message'   => __('User not authorized')
            ], 401);
        }
        return $next($request);
    }
}
