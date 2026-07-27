@extends('layouts.app')

@section('title', 'Manajemen Achievement')
@section('page-title', 'Achievement')
@section('page-subtitle', 'Kelola pencapaian yang bisa diraih siswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari achievement..."
                class="w-64 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Cari</button>
        </form>
        <a href="{{ route('admin.achievements.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
            + Tambah Achievement
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Syarat</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Poin</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Diraih</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($achievements as $achievement)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="font-medium text-gray-800 dark:text-white">{{ $achievement->name }}</div>
                                <div class="text-xs text-gray-500">{{ Str::limit($achievement->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $conditionTypes[$achievement->condition_type] ?? $achievement->condition_type }} &ge; {{ $achievement->condition_value }}</td>
                            <td class="px-6 py-4 text-sm">+{{ $achievement->points_reward }}</td>
                            <td class="px-6 py-4 text-sm">{{ $achievement->users()->count() }} siswa</td>
                            <td class="px-6 py-4">
                                <button onclick="toggleStatus({{ $achievement->id }})"
                                    class="px-2 py-1 text-xs rounded-full {{ $achievement->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $achievement->is_active ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.achievements.edit', $achievement) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                                <button onclick="deleteAchievement({{ $achievement->id }}, '{{ addslashes($achievement->name) }}')" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada achievement.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $achievements->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    function toggleStatus(id) {
        fetch(`/admin/achievements/${id}/toggle-status`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    }

    function deleteAchievement(id, name) {
        if (!confirm(`Hapus achievement "${name}"?`)) return;
        fetch(`/admin/achievements/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) { window.toast.success(data.message); setTimeout(() => location.reload(), 600); }
            else { window.toast.error(data.message); }
        });
    }
</script>
@endpush
@endsection
