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
        $isTrashed = $request->status === 'trashed';

        // Profile user yang di-soft-delete juga ikut ter-soft-delete (lihat destroy()),
        // jadi harus ikut di-withTrashed supaya tetap muncul di tab "User Dihapus".
        $query = User::with([
            'profile' => fn ($q) => $isTrashed ? $q->withTrashed() : $q,
            'roles',
        ]);

        if ($isTrashed) {
            $query->onlyTrashed();
        }

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

        // Filter by tipe user: internal (karyawan Zakat Sukses) vs external - independen
        // dari role, jadi filter terpisah dari filter status/role di atas.
        if ($request->filled('employee_type')) {
            $query->where('is_internal', $request->employee_type === 'internal');
        }

        // Filter by status (active/inactive/pending_approval/trashed)
        if ($request->filled('status') && !$isTrashed) {
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
                'is_active' => $request->boolean('is_active'),
                'is_internal' => $request->boolean('is_internal'),
                'email_verified_at' => $request->boolean('email_verified') ? now() : null,
            ]);

            // Assign role
            $user->assignRole($request->role);

            // Create profile (polymorphic)
            Profile::create([
                'profileable_id' => $user->id,
                'profileable_type' => User::class,
                'profile_type' => $this->profileTypeForRole($request->role),
                'first_name' => $request->first_name ?? $request->name,
                'last_name' => $request->last_name ?? '',
                'nickname' => $request->nickname,
                'phone' => $request->phone,
                // Form JS selalu mengirim string kosong untuk field yang tidak diisi (bukan
                // absen) — '' bukan nilai ENUM/DATE yang valid di MySQL, jadi normalisasi ke null.
                'gender' => $request->gender ?: null,
                'birth_date' => $request->birth_date ?: null,
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
            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user. Silakan coba lagi atau hubungi administrator.'
            ], 500);
        }
    }

    /**
     * Petakan role ke nilai ENUM profiles.profile_type. Role staf tambahan
     * (event_manager, editor, author) tidak ada di ENUM, jadi dicatat sebagai 'employee'.
     */
    private function profileTypeForRole(string $role): string
    {
        $allowed = ['student', 'instructor', 'admin', 'employee', 'alumni', 'support'];

        return in_array($role, $allowed, true) ? $role : 'employee';
    }

    /**
     * Email unik untuk dipasang ke baris yang akan di-soft-delete, supaya email aslinya
     * bebas dipakai ulang (users.email punya unique index di level DB yang tidak tahu
     * soal deleted_at). Diprefix id user yang sudah pasti unik, jadi aman dipanggil
     * berkali-kali untuk email yang sama tanpa saling bertabrakan.
     */
    private function freedEmail(User $user): string
    {
        return mb_substr("deleted-{$user->id}-{$user->email}", 0, 255);
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

            if (!$request->boolean('is_active')) {
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
                'is_active' => $request->boolean('is_active'),
                'is_internal' => $request->boolean('is_internal'),
                'default_role' => $request->role,
            ];

            // Update password if provided
            if ($request->filled('password')) {
                $userData['password'] = Hash::make($request->password);
            }

            // Update email verification status
            if ($request->boolean('email_verified') && !$user->hasVerifiedEmail()) {
                $userData['email_verified_at'] = now();
            } elseif (!$request->boolean('email_verified') && $user->hasVerifiedEmail()) {
                $userData['email_verified_at'] = null;
            }

            $user->update($userData);

            // Sync role (only one role per user for simplicity)
            $user->syncRoles([$request->role]);

            // Update or create profile
            $profileData = [
                'profile_type' => $this->profileTypeForRole($request->role),
                'first_name' => $request->first_name ?? $request->name,
                'last_name' => $request->last_name ?? '',
                'nickname' => $request->nickname,
                'phone' => $request->phone,
                // Form JS selalu mengirim string kosong untuk field yang tidak diisi (bukan
                // absen) — '' bukan nilai ENUM/DATE yang valid di MySQL, jadi normalisasi ke null.
                'gender' => $request->gender ?: null,
                'birth_date' => $request->birth_date ?: null,
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
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui user. Silakan coba lagi atau hubungi administrator.'
            ], 500);
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

            // users.email punya unique index di level DB yang tidak tahu soal deleted_at,
            // jadi baris yang sudah di-soft-delete tetap mengunci emailnya selamanya kalau
            // tidak dibebaskan - akibatnya registrasi/pembuatan user baru dengan email yang
            // sama akan selalu gagal. Lepaskan email sebelum soft delete supaya bisa dipakai
            // ulang, tapi baris lama tetap tersimpan untuk audit.
            $user->update(['email' => $this->freedEmail($user)]);

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
     * Restore a soft-deleted user.
     */
    public function restore(string $id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);

            DB::beginTransaction();

            // Kembalikan email asli (lihat freedEmail()) kalau belum direbut user lain
            // sejak dihapus - kalau sudah dipakai, biarkan email "deleted-..."-nya supaya
            // tidak bentrok; admin bisa edit manual setelah dipulihkan.
            if (preg_match('/^deleted-' . $user->id . '-(.+)$/', $user->email, $matches)) {
                $originalEmail = $matches[1];
                $emailTaken = User::where('email', $originalEmail)->exists();
                if (!$emailTaken) {
                    $user->email = $originalEmail;
                    $user->save();
                }
            }

            $user->restore();

            $trashedProfile = $user->profile()->onlyTrashed()->first();
            if ($trashedProfile) {
                $trashedProfile->restore();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dipulihkan.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to restore user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memulihkan user.'
            ], 500);
        }
    }

    /**
     * Hapus permanen (hard delete) user yang SUDAH di-soft-delete.
     *
     * PERINGATAN: banyak foreign key ke users.id di-set onDelete('cascade')
     * (enrollments, courses.instructor_id, submissions, dll) - menghapus baris
     * users secara permanen akan memicu MySQL ikut menghapus permanen semua data
     * terkait. Kalau user ini instruktur, SEMUA kursus yang dia buat (beserta
     * section/lesson/enrollment siswa lain di kursus itu) ikut terhapus permanen.
     * Tidak bisa dibatalkan - makanya hanya bisa dipanggil untuk user yang sudah
     * di soft delete (onlyTrashed), bukan user aktif, sebagai pengaman dua langkah.
     */
    public function forceDestroy(string $id)
    {
        try {
            $user = User::onlyTrashed()->findOrFail($id);

            DB::beginTransaction();

            $trashedProfile = $user->profile()->onlyTrashed()->first();
            if ($trashedProfile) {
                $trashedProfile->forceDelete();
            }

            $user->forceDelete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'User dihapus permanen.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to permanently delete user: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user secara permanen: ' . $e->getMessage()
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

            // Lepaskan email masing-masing dulu (lihat destroy()) sebelum soft delete massal,
            // supaya email yang sudah dihapus tetap bisa dipakai ulang untuk user baru.
            $usersToDelete = User::whereIn('id', $ids)->get();
            foreach ($usersToDelete as $userToDelete) {
                $userToDelete->update(['email' => $this->freedEmail($userToDelete)]);
            }

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