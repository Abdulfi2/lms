@extends('layouts.app')

@section('title', 'Pengguna')
@section('page-title', 'Pengguna')
@section('page-subtitle', 'Lihat data pengguna untuk membantu troubleshooting (lihat-saja)')

@section('content')
<div class="space-y-6">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..."
            class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
        <select name="role" onchange="this.form.submit()" class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
            <option value="">Semua Role</option>
            @foreach ($roles as $roleOption)
                <option value="{{ $roleOption->name }}" {{ request('role') == $roleOption->name ? 'selected' : '' }}>
                    {{ ucfirst(str_replace('_', ' ', $roleOption->name)) }}
                </option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-sm">Cari</button>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Bergabung</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->avatar_url }}" class="w-9 h-9 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white">{{ $user->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400 capitalize">
                                {{ $user->roles->pluck('name')->map(fn ($r) => str_replace('_', ' ', $r))->implode(', ') ?: '-' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $user->is_active ? 'Aktif' : 'Tidak Aktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $user->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('support.users.show', $user) }}" class="text-primary hover:underline text-sm">Lihat</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                Tidak ada user ditemukan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($users->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
