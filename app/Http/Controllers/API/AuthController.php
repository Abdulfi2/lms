<?php
// app/Http/Controllers/API/AuthController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Login via API - returns access token
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string|max:255',
            'abilities' => 'nullable|array',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah.'
            ], 401);
        }

        // Check if email is verified
        if (!$user->hasVerifiedEmail()) {
            return response()->json([
                'success' => false,
                'message' => 'Email belum diverifikasi. Silakan verifikasi email Anda.'
            ], 403);
        }

        // Check if user is active
        if (!$user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Anda tidak aktif. Silakan hubungi administrator.'
            ], 403);
        }

        // Revoke old tokens (optional - limit to 5 active tokens)
        if ($user->tokens()->count() >= 5) {
            $oldestToken = $user->tokens()->oldest()->first();
            $oldestToken->delete();
        }

        $deviceName = $request->device_name ?? $request->header('User-Agent', 'Unknown Device');
        $abilities = $request->abilities ?? $this->getDefaultAbilities($user);

        $token = $user->createToken($deviceName, $abilities);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'token' => $token->plainTextToken,
            'token_type' => 'Bearer',
            'expires_in' => config('sanctum.expiration') * 60,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? 'student',
                'avatar' => $user->avatar_url,
            ]
        ]);
    }

    /**
     * Get current authenticated user
     */
    public function user(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('profile', 'roles');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? 'student',
                'profile' => $user->profile,
                'email_verified_at' => $user->email_verified_at,
                'created_at' => $user->created_at,
            ]
        ]);
    }

    /**
     * Logout - revoke current token
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    /**
     * Register new user via API
     */
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'default_role' => 'student',
            'is_active' => true,
        ]);

        $user->assignRole('student');

        // Create profile
        Profile::create([
            'profileable_id' => $user->id,
            'profileable_type' => User::class,
            'profile_type' => 'student',
            'first_name' => $request->name,
            'is_active' => true,
            'approval_status' => 'approved',
        ]);

        // Send verification email
        $user->sendEmailVerificationNotification();

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil. Silakan verifikasi email Anda.',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ], 201);
    }

    /**
     * Get default abilities based on user role
     */
    private function getDefaultAbilities($user): array
    {
        $abilities = ['basic-access'];

        if ($user->hasRole('admin')) {
            $abilities = ['*'];
        } elseif ($user->hasRole('instructor')) {
            $abilities = array_merge($abilities, [
                'courses:create',
                'courses:update',
                'courses:delete',
                'lessons:manage',
                'assignments:grade',
                'students:view',
            ]);
        } else {
            $abilities = array_merge($abilities, [
                'courses:view',
                'courses:enroll',
                'lessons:view',
                'assignments:submit',
                'quizzes:attempt',
                'certificates:view',
            ]);
        }

        return $abilities;
    }
}