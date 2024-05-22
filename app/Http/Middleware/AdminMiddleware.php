<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        return $next($request);
        if (Auth::guard('sanctum')->check()) {
            $user = Auth::guard('sanctum')->user();
            if ($user && $user instanceof \App\Models\Admin) {
                return $next($request);
            }
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
}
