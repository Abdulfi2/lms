@extends('layouts.app')

@section('title', 'Manajemen Permission')
@section('page-title', 'Permission')
@section('page-subtitle', 'Kelola daftar permission sistem')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari permission..."
                class="w-64 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            <select name="module" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                <option value="">Semua Modul</option>
                @foreach ($modules as $module)
                    <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>{{ ucfirst($module) }}</option>
                @endforeach
            </select>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Filter</button>
            @if (request()->filled('search') || request()->filled('module'))
                <a href="{{ route('admin.permissions.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Reset</a>
            @endif
        </form>

        <a href="{{ route('admin.permissions.create') }}"
            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition flex items-center">
            + Tambah Permission
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nama Permission</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Modul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Dipakai Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Dibuat</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($permissions as $permission)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-medium text-gray-800 dark:text-white">{{ $permission->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">{{ ucfirst($permission->module ?? 'General') }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $permission->roles_count }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $permission->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.permissions.edit', $permission) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                                <form action="{{ route('admin.permissions.destroy', $permission) }}" method="POST" class="inline-block"
                                    data-confirm="Hapus permission {{ $permission->name }}? Ini akan mencabutnya dari {{ $permission->roles_count }} role yang memakainya.">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada permission.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $permissions->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    // destroyPermission() di controller mengembalikan JSON, jadi submit form hapus
    // ditangani lewat fetch supaya pesan sukses/gagal tampil dengan benar (bukan
    // browser menampilkan mentah-mentah isi JSON sebagai halaman).
    document.querySelectorAll('form[action*="/admin/permissions/"]').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();

            if (!confirm(this.dataset.confirm)) {
                return;
            }

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    window.toast.success(data.message);
                    setTimeout(() => location.reload(), 800);
                } else {
                    window.toast.error(data.message || 'Gagal menghapus permission');
                }
            })
            .catch(() => window.toast.error('Gagal menghapus permission'));
        });
    });
</script>
@endpush
@endsection
