<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Get the authenticated user
        $user = auth()->user();

        // Check if the user is admin
        if ($user && $user->is_admin) {
            return $next($request);
        }

        // User is not an admin
        return response()->json([
            'message' => 'Unauthorized'
        ], 403);
    }
}
