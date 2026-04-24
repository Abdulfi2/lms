<?php
// app/Http/Controllers/Admin/RolePermissionController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolePermissionController extends Controller
{
    /**
     * Display a listing of roles.
     */
    /**
     * Display a listing of roles.
     */
    public function roles(Request $request)
    {
        $query = Role::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        $roles = $query->orderBy('name')->paginate(10);

        // Count users per role and permissions per role
        foreach ($roles as $role) {
            $role->users_count = User::role($role->name)->count();
            $role->permissions_count = $role->permissions->count(); // Tambahkan ini
        }

        if ($request->wantsJson()) {
            // Transform data untuk response JSON
            $rolesData = $roles->through(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                    'guard_name' => $role->guard_name,
                    'users_count' => $role->users_count,
                    'permissions_count' => $role->permissions_count,
                    'created_at' => $role->created_at,
                    'updated_at' => $role->updated_at,
                ];
            });

            return response()->json($rolesData);
        }

        return view('admin.roles.index', compact('roles'));
    }

    /**
     * Show form to create new role.
     */
    public function createRole()
    {
        $permissions = Permission::orderBy('name')->get();
        $permissionsByModule = $this->groupPermissionsByModule($permissions);

        return view('admin.roles.create', compact('permissionsByModule'));
    }

    /**
     * Store a newly created role.
     */
    public function storeRole(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $request->name,
                'guard_name' => 'web',
            ]);

            if ($request->has('permissions')) {
                $role->givePermissionTo($request->permissions);
            }

            DB::commit();

            return redirect()->route('admin.roles.index')
                ->with('success', "Role '{$role->name}' berhasil dibuat.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal membuat role: ' . $e->getMessage());
        }
    }

    /**
     * Show form to edit role.
     */
    public function editRole(Role $role)
    {
        $permissions = Permission::orderBy('name')->get();
        $permissionsByModule = $this->groupPermissionsByModule($permissions);
        $rolePermissions = $role->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('role', 'permissionsByModule', 'rolePermissions'));
    }

    /**
     * Update the specified role.
     */
    public function updateRole(Request $request, Role $role)
    {
        // Prevent editing super admin role
        if ($role->name === 'admin' && !$request->has('force')) {
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Role Admin tidak dapat diubah untuk keamanan.'
                ], 403);
            }
            return back()->with('error', 'Role Admin tidak dapat diubah untuk keamanan.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        try {
            DB::beginTransaction();

            $role->update(['name' => $request->name]);
            $role->syncPermissions($request->permissions ?? []);

            DB::commit();

            // Selalu return JSON untuk AJAX request
            return response()->json([
                'success' => true,
                'message' => "Role '{$role->name}' berhasil diperbarui.",
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'permissions_count' => $role->permissions->count(),
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete the specified role.
     */
    public function destroyRole(Role $role)
    {
        // Prevent deleting important roles
        if (in_array($role->name, ['admin', 'instructor', 'student'])) {
            return response()->json([
                'success' => false,
                'message' => "Role '{$role->name}' tidak dapat dihapus karena merupakan role sistem."
            ], 403);
        }

        try {
            $roleName = $role->name;
            $role->delete();

            return response()->json([
                'success' => true,
                'message' => "Role '{$roleName}' berhasil dihapus."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus role: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display a listing of permissions.
     */
    public function permissions(Request $request)
    {
        $query = Permission::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', "%{$request->search}%");
        }

        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        $permissions = $query->orderBy('module')->orderBy('name')->paginate(20);

        // Count roles using this permission
        foreach ($permissions as $permission) {
            $permission->roles_count = $permission->roles->count();
        }

        // Get all unique modules for filter
        $modules = Permission::select('module')->distinct()->whereNotNull('module')->pluck('module');

        if ($request->wantsJson()) {
            $permissionsData = $permissions->through(function ($permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'module' => $permission->module,
                    'guard_name' => $permission->guard_name,
                    'roles_count' => $permission->roles_count,
                    'created_at' => $permission->created_at,
                ];
            });

            return response()->json($permissionsData);
        }

        return view('admin.permissions.index', compact('permissions', 'modules'));
    }

    /**
     * Show form to create new permission.
     */
    public function createPermission()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created permission.
     */
    public function storePermission(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'module' => 'nullable|string|max:100',
        ]);

        try {
            Permission::create([
                'name' => $request->name,
                'guard_name' => 'web',
                'module' => $request->module,
            ]);

            // Clear permission cache
            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            return redirect()->route('admin.permissions.index')
                ->with('success', "Permission '{$request->name}' berhasil dibuat.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal membuat permission: ' . $e->getMessage());
        }
    }

    /**
     * Edit permission.
     */
    public function editPermission(Permission $permission)
    {
        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update permission.
     */
    public function updatePermission(Request $request, Permission $permission)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name,' . $permission->id,
            'module' => 'nullable|string|max:100',
        ]);

        try {
            $permission->update([
                'name' => $request->name,
                'module' => $request->module,
            ]);

            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            return redirect()->route('admin.permissions.index')
                ->with('success', "Permission '{$permission->name}' berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui permission: ' . $e->getMessage());
        }
    }

    /**
     * Delete permission.
     */
    public function destroyPermission(Permission $permission)
    {
        try {
            $permissionName = $permission->name;
            $permission->delete();

            app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

            return response()->json([
                'success' => true,
                'message' => "Permission '{$permissionName}' berhasil dihapus."
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus permission: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign role to user.
     */
    public function assignRoleToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncRoles([$request->role]);

        return response()->json([
            'success' => true,
            'message' => "Role '{$request->role}' berhasil diberikan ke user {$user->name}."
        ]);
    }

    /**
     * Assign permission to user directly.
     */
    public function assignPermissionToUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|exists:permissions,name',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->givePermissionTo($request->permission);

        return response()->json([
            'success' => true,
            'message' => "Permission '{$request->permission}' berhasil diberikan ke user {$user->name}."
        ]);
    }

    /**
     * Revoke permission from user.
     */
    public function revokePermissionFromUser(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'permission' => 'required|exists:permissions,name',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->revokePermissionTo($request->permission);

        return response()->json([
            'success' => true,
            'message' => "Permission '{$request->permission}' berhasil dicabut dari user {$user->name}."
        ]);
    }

    /**
     * Group permissions by module for display.
     */
    private function groupPermissionsByModule($permissions)
    {
        $grouped = [];

        foreach ($permissions as $permission) {
            $module = $permission->module ?? 'General';
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $permission;
        }

        return $grouped;
    }
}