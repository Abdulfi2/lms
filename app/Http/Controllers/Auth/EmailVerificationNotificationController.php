<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationNotificationController extends Controller
{
    /**
     * Send a new email verification notification.
     */
    public function store(Request $request)
    {
        // Rate limiting berdasarkan email
        $email = $request->input('email') ?? $request->user()?->email;

        if (!$email) {
            return back()->withErrors(['email' => 'Email diperlukan.']);
        }

        // Maksimal 3 request per 60 menit per email
        $key = 'verification-send:' . md5($email);

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);
            $message = 'Terlalu banyak permintaan. Silakan coba lagi setelah ' . ceil($seconds / 60) . ' menit.';

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $message], 429);
            }
            return back()->withErrors(['email' => $message]);
        }

        RateLimiter::hit($key, 3600);

        // Cek apakah user login atau tidak
        if ($request->user()) {
            // User sudah login
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()->intended(route('dashboard'));
            }

            $request->user()->sendEmailVerificationNotification();

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Link verifikasi telah dikirim ke ' . $email
                ]);
            }

            return back()->with('status', 'verification-link-sent');
        }

        // User belum login - cari user berdasarkan email
        $user = User::where('email', $email)->first();

        if (!$user) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email tidak terdaftar.'
                ], 404);
            }
            return back()->withErrors(['email' => 'Email tidak terdaftar.']);
        }

        if ($user->hasVerifiedEmail()) {
            // Hapus session verification
            $request->session()->forget(['verification_email', 'verification_needed']);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email sudah terverifikasi. Silakan login.'
                ], 422);
            }
            return redirect()->route('login')->with('success', 'Email sudah terverifikasi. Silakan login.');
        }

        $user->sendEmailVerificationNotification();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Link verifikasi telah dikirim ke ' . $email
            ]);
        }

        return back()->with('status', 'verification-link-sent');
    }
}