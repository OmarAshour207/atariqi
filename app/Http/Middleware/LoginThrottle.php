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
            // Send email to admin ONLY once per lockout period
            if (!Cache::has($key . '_notified')) {
                Mail::to(config('mail.admin_email'))->send(new UnauthorizedLoginAttempt($request));
                Cache::put($key . '_notified', true, now()->addHour());
            }

            return response()->json(['message' => 'عدد محاولات تسجيل الدخول كبير جدًا. يرجى المحاولة مرة أخرى بعد ساعة واحدة.'], 429);
        }

        $response = $next($request);

        // We only want to increment on failed login POST (not GET)
        if (
            $request->isMethod('post') &&
            ($response->getStatusCode() === 401 || $response->getStatusCode() === 422)
        ) {
            // Increment attempts on failed login
            Cache::put($key, $attempts + 1, now()->addHour());
        } elseif (
            $request->isMethod('post') &&
            $response->getStatusCode() === 200
        ) {
            // Only reset on successful POST login
            Cache::forget($key);
            Cache::forget($key . '_notified');
        }

        return $response;
    }
}
