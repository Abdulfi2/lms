<?php
// app/Http/Controllers/Auth/RegisteredUserController.php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Profile;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request)
    {
        // Ambil parameter role dari URL (default: student)
        $role = $request->query('role', 'student');

        // Validasi role yang diizinkan
        if (!in_array($role, ['student', 'instructor'])) {
            $role = 'student';
        }

        return view('auth.register', compact('role'));
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input termasuk role
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:student,instructor'], // Validasi role dari hidden field
        ]);

        try {
            DB::beginTransaction();

            // Parse nama untuk first_name dan last_name
            $nameParts = explode(' ', $validated['name'], 2);
            $firstName = $nameParts[0];
            $lastName = $nameParts[1] ?? '';

            // Create user
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'default_role' => $validated['role'],
                'is_active' => true,
            ]);

            // Assign role sesuai pilihan
            $role = Role::findByName($validated['role'], 'web');
            $user->assignRole($role);

            // Create profile
            $profileData = [
                'profileable_id' => $user->id,
                'profileable_type' => User::class,
                'profile_type' => $validated['role'],
                'first_name' => $firstName,
                'last_name' => $lastName,
                'nickname' => $firstName,
                'is_active' => true,
                'approval_status' => $validated['role'] === 'instructor' ? 'pending' : 'approved',
            ];

            // Jika role instructor, tambahkan data tambahan (opsional)
            if ($validated['role'] === 'instructor') {
                $profileData['professional_info'] = json_encode([
                    'status' => 'pending_approval',
                    'applied_at' => now()->toDateTimeString(),
                ]);
            }

            Profile::create($profileData);

            DB::commit();

            // Log activity
            Log::info('User registered', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $validated['role']
            ]);

            // Set session untuk verifikasi email
            session([
                'verification_email' => $user->email,
                'verification_needed' => true
            ]);

            // Trigger event registered (untuk kirim email verifikasi)
            event(new Registered($user));

            // Login user
            Auth::login($user);

            // Redirect berdasarkan role
            $redirectMessage = $validated['role'] === 'instructor'
                ? 'Pendaftaran berhasil! Akun instruktur Anda akan segera diverifikasi oleh admin.'
                : 'Pendaftaran berhasil! Silakan verifikasi email Anda.';

            return redirect()->route('verification.notice')
                ->with('success', $redirectMessage);

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Registration failed: ' . $e->getMessage(), [
                'email' => $validated['email'] ?? null,
                'role' => $validated['role'] ?? null,
                'trace' => $e->getTraceAsString()
            ]);

            throw ValidationException::withMessages([
                'email' => ['Registrasi gagal. Silakan coba lagi.'],
            ]);
        }
    }
}