<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): JsonResponse|RedirectResponse
    {
        try {
            $request->authenticate();

            $user = $request->user();

            $request->session()->regenerate();

            // Catatan: user yang belum verifikasi TETAP dibiarkan login (tidak
            // di-logout paksa di sini) supaya mereka bisa menuju link verifikasi
            // email yang sedang coba diakses (route itu butuh auth). Middleware
            // 'verified' pada route lain yang tetap akan mengarahkan mereka ke
            // halaman verifikasi kalau mencoba mengakses halaman terproteksi.
            if (is_null($user->email_verified_at)) {
                if ($request->wantsJson()) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Login berhasil, namun email Anda belum diverifikasi.',
                        'redirect' => route('verification.notice'),
                        'need_verification' => true
                    ]);
                }

                return redirect()->intended(route('verification.notice'))
                    ->with('warning', 'Email Anda belum diverifikasi. Silakan cek email Anda untuk melakukan verifikasi.');
            }

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil',
                    'redirect' => $this->redirectTo($user),
                    'user' => $user
                ]);
            }

            return redirect()->intended($this->redirectTo($user));

        } catch (ValidationException $e) {
            if ($request->wantsJson()) {
                $firstError = $e->errors();
                $firstErrorMessage = is_array($firstError) ? reset($firstError)[0] : 'Email atau password salah';

                return response()->json([
                    'success' => false,
                    'message' => $firstErrorMessage,
                    'errors' => $firstError
                ], 422);
            }
            throw $e;
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

    private function redirectTo($user): string
    {
        if ($user->hasRole('admin')) {
            return route('admin.dashboard');
        }
        if ($user->hasRole('instructor')) {
            return route('instructor.dashboard');
        }
        if ($user->hasRole('event_manager')) {
            return route('admin.events.index');
        }
        return route('student.dashboard');
    }
}