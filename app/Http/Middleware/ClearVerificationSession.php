<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ClearVerificationSession
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Jika user sudah login dan email terverifikasi, hapus session
        if ($request->user() && $request->user()->hasVerifiedEmail()) {
            $request->session()->forget(['verification_email', 'verification_needed']);
        }

        return $response;
    }
}