<?php
// app/Http/Middleware/UpdateUserLastSeen.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpdateUserLastSeen
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $user->last_seen_at = now();
            $user->saveQuietly();
        }

        return $next($request);
    }
}