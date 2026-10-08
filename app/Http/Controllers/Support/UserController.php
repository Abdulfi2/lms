<?php

namespace App\Http\Controllers\Support;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Lihat-saja — tidak ada create/edit/delete di sini, sesuai permission
     * 'view users' yang di-seed untuk role support (lihat RolesAndPermissionsSeeder).
     */
    public function index(Request $request)
    {
        $query = User::with('profile', 'roles');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->role($request->role);
        }

        $users = $query->latest()->paginate(15)->withQueryString();
        $roles = Role::all();

        return view('support.users.index', compact('users', 'roles'));
    }

    public function show(User $user)
    {
        $user->load(['profile', 'roles', 'enrollments.course']);

        return view('support.users.show', compact('user'));
    }
}
