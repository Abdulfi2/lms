<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     * Hanya bisa diakses jika:
     * 1. User baru saja register (session 'verification_needed' = true)
     * 2. User login gagal karena email belum terverifikasi (session 'verification_email' ada)
     * 3. User mencoba akses langsung → redirect ke login
     */
    public function __invoke(Request $request)
    {
        // Jika user sudah login dan email sudah terverifikasi
        if (Auth::check()) {
            if ($request->user()->hasVerifiedEmail()) {
                return redirect()->intended(route('dashboard'));
            }
            // User login tapi email belum verifikasi - lanjutkan ke halaman verifikasi
            $email = $request->user()->email;
            return view('auth.verify-email', compact('email'));
        }

        // Jika user belum login, cek session atau redirect ke login
        $email = $request->session()->get('verification_email');

        if (!$email) {
            // Tidak ada session, redirect ke login dengan pesan
            return redirect()->route('login')
                ->with('error', 'Silakan login terlebih dahulu untuk verifikasi email.');
        }

        // Cek rate limiting untuk akses halaman verifikasi (5 kali per jam)
        $key = 'verify-page:' . md5($email);
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return redirect()->route('login')
                ->with('error', 'Terlalu banyak percobaan. Silakan coba lagi setelah ' . ceil($seconds / 60) . ' menit.');
        }
        RateLimiter::hit($key, 3600);

        return view('auth.verify-email', compact('email'));
    }
}