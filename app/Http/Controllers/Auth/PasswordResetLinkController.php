<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create()
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     */
    public function store(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Rate limiting: maksimal 3 request per jam per email
        $key = 'password-reset:' . $request->email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak permintaan. Silakan coba lagi setelah ' . ceil($seconds / 60) . ' menit.'
                ], 429);
            }

            throw ValidationException::withMessages([
                'email' => ['Terlalu banyak permintaan. Silakan coba lagi setelah ' . ceil($seconds / 60) . ' menit.'],
            ]);
        }

        RateLimiter::hit($key, 3600);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status === Password::RESET_LINK_SENT) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Link reset password telah dikirim ke email Anda.'
                ]);
            }

            return back()->with('status', __($status));
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => 'Email tidak ditemukan dalam sistem kami.'
            ], 422);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}