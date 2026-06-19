<?php

declare(strict_types=1);

namespace Modules\Auth\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

/**
 * Rate limiter for login attempts.
 *
 * Limits to 5 failed attempts within 60 seconds, with a temporary lockout.
 * Uses a composite key of email + IP address to prevent distributed attacks.
 */
class LoginRateLimiter
{
    /**
     * Max login attempts before lockout.
     */
    private const MAX_ATTEMPTS = 5;

    /**
     * Lockout duration in seconds.
     */
    private const DECAY_SECONDS = 60;

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $this->throttleKey($request);

        // Check if locked out
        if (RateLimiter::tooManyAttempts($key, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($key);
            throw new TooManyRequestsHttpException(
                $seconds,
                "Too many login attempts. Please try again in {$seconds} seconds."
            );
        }

        $response = $next($request);

        // If login failed (401), increment the counter
        if ($response->getStatusCode() === 401) {
            RateLimiter::hit($key, self::DECAY_SECONDS);
        }

        // If login succeeded (200), clear the counter
        if ($response->getStatusCode() === 200) {
            RateLimiter::clear($key);
        }

        return $response;
    }

    /**
     * Generate a unique throttle key for the request.
     */
    private function throttleKey(Request $request): string
    {
        $email = Str::lower($request->input('email', ''));
        $ip = $request->ip();

        return "login|{$email}|{$ip}";
    }
}
