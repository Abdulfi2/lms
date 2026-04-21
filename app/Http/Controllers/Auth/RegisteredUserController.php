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

class RegisteredUserController extends Controller
{
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        // Validasi input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        try {
            // Eksekusi dalam transaction
            $user = $this->executeWithTransaction(
                callback: function () use ($validated, $request) {
                    // Parse nama
                    $nameParts = explode(' ', $validated['name'], 2);
                    $firstName = $nameParts[0];
                    $lastName = $nameParts[1] ?? '';

                    // Create user
                    $user = User::create([
                        'name' => $validated['name'],
                        'email' => $validated['email'],
                        'password' => Hash::make($validated['password']),
                        'default_role' => 'student',
                        'is_active' => true,
                    ]);

                    // Assign role student
                    $studentRole = Role::findByName('student', 'web');
                    $user->assignRole($studentRole);

                    // Create profile
                    Profile::create([
                        'profileable_id' => $user->id,
                        'profileable_type' => User::class,
                        'profile_type' => 'student',
                        'first_name' => $firstName,
                        'last_name' => $lastName,
                        'nickname' => $firstName,
                        'is_active' => true,
                        'approval_status' => 'approved',
                    ]);

                    return $user;
                },
                operation: 'register',
                context: [
                    'table' => 'users',
                    'description' => "User registration with email: {$validated['email']}",
                    'new_data' => ['email' => $validated['email'], 'name' => $validated['name']]
                ]
            );

            session([
                'verification_email' => $user->email,
                'verification_needed' => true
            ]);

            event(new Registered($user));

            // Login user
            return redirect()->route('verification.notice')
                ->with('success', 'Pendaftaran berhasil! Silakan verifikasi email Anda.');

        } catch (ValidationException $e) {
            throw $e;
        } catch (\Throwable $e) {
            // Log error sudah dilakukan oleh executeWithTransaction
            // Tampilkan pesan error friendly ke user
            throw ValidationException::withMessages([
                'email' => ['Registration failed. Please try again later.'],
            ]);
        }
    }
}