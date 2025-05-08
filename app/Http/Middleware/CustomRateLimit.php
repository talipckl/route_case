<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class CustomRateLimit
{
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();
        $key = 'rate_limit:' . $ip;
        $maxAttempts = 5;
        $decaySeconds = 60;

        $attempts = Cache::get($key, 0);

        if ($attempts >= $maxAttempts) {
            return response()->json([
                'message' => 'Too many requests. Please wait.'
            ], Response::HTTP_TOO_MANY_REQUESTS);
        }

        Cache::put($key, $attempts + 1, $decaySeconds);

        return $next($request);
    }
}
