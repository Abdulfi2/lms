<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\RateLimiter;

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     * Cek token validity BEFORE showing the form
     */
    public function create(Request $request)
    {
        $email = $request->query('email');
        $token = $request->route('token');

        // Validasi token sebelum menampilkan form
        $broker = Password::broker();
        $user = $broker->getUser(['email' => $email]);

        if (!$user) {
            // Token tidak valid atau user tidak ditemukan
            return redirect()->route('password.request')
                ->with('error', 'Link reset password tidak valid. Silakan request ulang.');
        }

        // Cek apakah token valid dan belum expired
        $tokenExists = $broker->tokenExists($user, $token);

        if (!$tokenExists) {
            return redirect()->route('password.request')
                ->with('error', 'Link reset password sudah kadaluarsa atau tidak valid. Silakan request ulang.');
        }

        return view('auth.reset-password', [
            'request' => $request,
            'email' => $email,
            'token' => $token
        ]);
    }

    /**
     * Handle an incoming new password request.
     */
    public function store(Request $request): JsonResponse|\Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Rate limiting
        $key = 'password-reset-attempt:' . $request->email;

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terlalu banyak percobaan. Silakan coba lagi setelah ' . ceil($seconds / 60) . ' menit.'
                ], 429);
            }

            throw ValidationException::withMessages([
                'email' => ['Terlalu banyak percobaan. Silakan coba lagi nanti.'],
            ]);
        }

        // Attempt to reset the user's password
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        RateLimiter::clear($key);

        // Handle response based on status
        if ($status === Password::PASSWORD_RESET) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Password berhasil direset. Silakan login.',
                    'redirect' => route('login')
                ]);
            }

            return redirect()->route('login')->with('status', __($status));
        }

        RateLimiter::hit($key, 3600);

        // Specific error message for expired token
        $errorMessage = $status === Password::INVALID_TOKEN
            ? 'Link reset password sudah kadaluarsa atau tidak valid. Silakan request ulang.'
            : trans($status);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'is_expired' => $status === Password::INVALID_TOKEN
            ], 422);
        }

        throw ValidationException::withMessages([
            'email' => [$errorMessage],
        ]);
    }
}