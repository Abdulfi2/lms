<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckTokenAbility
{
    public function handle(Request $request, Closure $next, $ability)
    {
        if (!$request->user() || !$request->user()->tokenCan($ability)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Missing required permission: ' . $ability
            ], 403);
        }

        return $next($request);
    }
}