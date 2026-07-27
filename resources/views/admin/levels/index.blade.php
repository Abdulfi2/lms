@extends('layouts.app')

@section('title', 'Manajemen Level')
@section('page-title', 'Level')
@section('page-subtitle', 'Kelola tingkatan level berdasarkan poin siswa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('admin.levels.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
            + Tambah Level
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Level</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Poin Dibutuhkan</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($levels as $level)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm font-bold">{{ $level->level_number }}</td>
                            <td class="px-6 py-4 text-sm">{{ $level->name }}</td>
                            <td class="px-6 py-4 text-sm">{{ number_format($level->points_required) }} poin</td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <a href="{{ route('admin.levels.edit', $level) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                                <button onclick="deleteLevel({{ $level->id }}, '{{ addslashes($level->name) }}')" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">Belum ada level.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $levels->links() }}
        </div>
    </div>
</div>

@push('scripts')
<script>
    function deleteLevel(id, name) {
        if (!confirm(`Hapus level "${name}"?`)) return;
        fetch(`/admin/levels/${id}`, {
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
