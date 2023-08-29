<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocaleCheck
{
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->get('locale');

        if($locale == 'en')
            session()->put('locale', 'en');

        if(session()->has('locale')) {
            app()->setLocale(session()->get('locale'));
        } else {
            session()->put('locale', 'ar');
            app()->setLocale('ar');
        }
        return $next($request);
    }
}
