<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocaleCheck
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->get('locale');

        if (in_array($locale, ['en', 'ar'], true)) {
            session(['locale' => $locale]);
        }

        if (session()->has('locale')) {
            app()->setLocale(session('locale'));
        } else {
            session(['locale' => config('app.locale', 'ar')]);
            app()->setLocale(config('app.locale', 'ar'));
        }

        return $next($request);
    }
}
