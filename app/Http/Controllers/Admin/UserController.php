<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Models\Profile;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $query = User::with('profile', 'roles');

        // Search by name or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role')) {
            $query->role($request->role);
        }

        // Filter by status (active/inactive/pending_approval)
        if ($request->filled('status')) {
            if ($request->status === 'pending_approval') {
                $query->whereHas('profile', function ($q) {
                    $q->where('approval_status', 'pending');
                });
            } else {
                $query->where('is_active', $request->status == 'active');
            }
        }


        $users = $query->latest()->paginate(10)->withQueryString();
        $roles = Role::all();

        if ($request->wantsJson()) {
            return response()->json($users);
        }

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            DB::beginTransaction();

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'default_role' => $request->role,
                'is_active' => $request->has('is_active'),
                'email_verified_at' => $request->has('email_verified') ? now() : null,
            ]);

            // Assign role
            $user->assignRole($request->role);

            // Create profile (polymorphic)
            Profile::create([
                'profileable_id' => $user->id,
                'profileable_type' => User::class,
                'profile_type' => $request->role,
                'first_name' => $request->first_name ?? $request->name,
                'last_name' => $request->last_name ?? '',
                'nickname' => $request->nickname,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'addresses' => $request->addresses ? json_encode($request->addresses) : null,
                'is_active' => true,
                'approval_status' => 'approved',
                'approved_at' => now(),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil disimpan.',
                'redirect' => route('admin.users.index')
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create user: ' . $e->getMessage());
            return back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        $user->load('profile', 'roles');
        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $user->load('profile', 'roles');
        $roles = Role::all();
        $userRole = $user->roles->first()->name ?? 'student';

        return view('admin.users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, User $user)
    {
        // Cegah admin mengunci diri sendiri: ganti role sendiri jadi bukan admin,
        // atau menonaktifkan akun sendiri.
        if ($user->id === auth()->id()) {
            if ($request->role !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat mengubah role akun Anda sendiri.'
                ], 422);
            }

            if (!$request->has('is_active')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Update user data
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'is_active' => $request->has('is_active'),
                'default_role' => $request->role,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // Update email verification status
            if ($request->has('email_verified') && !$user->hasVerifiedEmail()) {
                $userData['email_verified_at'] = now();
            } elseif (!$request->has('email_verified') && $user->hasVerifiedEmail()) {
                $userData['email_verified_at'] = null;
            }

            $user->update($userData);

            // Sync role (only one role per user for simplicity)
            $user->syncRoles([$request->role]);

            // Update or create profile
            $profileData = [
                'profile_type' => $request->role,
                'first_name' => $request->first_name ?? $request->name,
                'last_name' => $request->last_name ?? '',
                'nickname' => $request->nickname,
                'phone' => $request->phone,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'addresses' => $request->addresses ? json_encode($request->addresses) : null,
            ];

            if ($user->profile) {
                $user->profile->update($profileData);
            } else {
                $profileData['profileable_id'] = $user->id;
                $profileData['profileable_type'] = User::class;
                $profileData['is_active'] = true;
                $profileData['approval_status'] = 'approved';
                $profileData['approved_at'] = now();
                Profile::create($profileData);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil disimpan.',
                'redirect' => route('admin.users.index')
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update user: ' . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user)
    {
        try {
            // Prevent deleting yourself
            if ($user->id === auth()->id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda tidak dapat menghapus akun sendiri.'
                ], 403);
            }

            DB::beginTransaction();

            // Delete profile first (polymorphic)
            if ($user->profile) {
                $user->profile->delete();
            }

            // Delete user (soft delete if using SoftDeletes)
            $user->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Bulk delete users (optional)
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:users,id'
        ]);

        try {
            DB::beginTransaction();

            $ids = $request->ids;
            // Prevent deleting current user
            $ids = array_diff($ids, [auth()->id()]);

            // Delete profiles first
            Profile::whereIn('profileable_id', $ids)
                ->where('profileable_type', User::class)
                ->delete();

            // Delete users
            User::whereIn('id', $ids)->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => count($ids) . ' user berhasil dihapus.'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user.'
            ], 500);
        }
    }

    /**
     * Approve or reject a pending instructor application (AJAX).
     */
    public function updateApprovalStatus(Request $request, User $user)
    {
        $request->validate([
            'approval_status' => 'required|in:approved,rejected',
        ]);

        if (!$user->profile) {
            return response()->json([
                'success' => false,
                'message' => 'User ini belum memiliki profil.'
            ], 422);
        }

        $user->profile->update([
            'approval_status' => $request->approval_status,
            'approved_at' => $request->approval_status === 'approved' ? now() : null,
        ]);

        return response()->json([
            'success' => true,
            'approval_status' => $request->approval_status,
            'message' => $request->approval_status === 'approved'
                ? 'Instruktur berhasil disetujui.'
                : 'Pengajuan instruktur ditolak.'
        ]);
    }

    /**
     * Toggle user active status (AJAX)
     */
    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menonaktifkan akun Anda sendiri.'
            ], 422);
        }

        try {
            $user->update(['is_active' => !$user->is_active]);

            return response()->json([
                'success' => true,
                'is_active' => $user->is_active,
                'message' => 'Status user berhasil diubah.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengubah status.'
            ], 500);
        }
    }
}