<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class SuccessRateLimit
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $userId = Auth::id();
        Log::info("Checking rate limits for user ID: {$userId}");


        $key = 'schedule-post:' . $userId;

        // Check if the limit has been reached
        if (RateLimiter::tooManyAttempts($key, 10)) {
            return response()->json([
                'message' => 'Too many attempts for today.'],
                429);
        }

        // Proceed with the request
        $response = $next($request);

        // Check if the response was successful
        if ($response->status() === 200 && strpos($request->path(), 'schedule') !== false) {
            // Increment the attempt
            RateLimiter::hit($key, 1440);
        }

        return $response;
    }
}
