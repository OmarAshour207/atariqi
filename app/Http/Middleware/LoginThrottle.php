<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\UnauthorizedLoginAttempt;

class LoginThrottle
{
    public function handle(Request $request, Closure $next)
    {
        $key = 'login_attempts_' . $request->ip();
        $attempts = Cache::get($key, 0);

        if ($attempts >= 5) {
            // Send email to admin
            Mail::to(config('mail.admin_email'))->send(new UnauthorizedLoginAttempt($request));

            return response()->json(['message' => 'عدد محاولات تسجيل الدخول كبير جدًا. يرجى المحاولة مرة أخرى بعد ساعة واحدة.'], 429);
        }

        $response = $next($request);

        if ($response->getStatusCode() === 401 || $response->getStatusCode() === 422) {
            // Increment attempts on failed login
            Cache::put($key, $attempts + 1, now()->addHour());
        } else {
            // Reset on successful login
            Cache::forget($key);
        }

        return $response;
    }
}
