@extends('layouts.app')

@section('title', 'Manajemen Badge')
@section('page-title', 'Badge')
@section('page-subtitle', 'Kelola lencana yang bisa diraih siswa')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari badge..."
                class="w-64 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Cari</button>
        </form>
        <a href="{{ route('admin.badges.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
            + Tambah Badge
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse ($badges as $badge)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold" style="background-color: {{ $badge->color }}">
                            {{ strtoupper(substr($badge->name, 0, 1)) }}
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800 dark:text-white">{{ $badge->name }}</h3>
                            <p class="text-xs text-gray-500">{{ $types[$badge->type] ?? $badge->type }} &ge; {{ $badge->required_value }}</p>
                        </div>
                    </div>
                    <button onclick="toggleStatus({{ $badge->id }})"
                        class="px-2 py-1 text-xs rounded-full {{ $badge->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $badge->is_active ? 'Aktif' : 'Nonaktif' }}
                    </button>
                </div>
                <p class="text-sm text-gray-500 mt-3">{{ Str::limit($badge->description, 80) }}</p>
                <div class="flex justify-between items-center mt-4 pt-3 border-t dark:border-gray-700">
                    <span class="text-xs text-gray-400">{{ $badge->users()->count() }} siswa meraih</span>
                    <div class="space-x-2">
                        <a href="{{ route('admin.badges.edit', $badge) }}" class="text-blue-600 hover:text-blue-800 text-sm">Edit</a>
                        <button onclick="deleteBadge({{ $badge->id }}, '{{ addslashes($badge->name) }}')" class="text-red-600 hover:text-red-800 text-sm">Hapus</button>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-span-full bg-white dark:bg-gray-800 rounded-xl shadow-sm p-12 text-center text-gray-500">
                Belum ada badge.
            </div>
        @endforelse
    </div>

    {{ $badges->links() }}
</div>

@push('scripts')
<script>
    function toggleStatus(id) {
        fetch(`/admin/badges/${id}/toggle-status`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
        })
        .then(res => res.json())
        .then(data => { if (data.success) location.reload(); });
    }

    function deleteBadge(id, name) {
        if (!confirm(`Hapus badge "${name}"?`)) return;
        fetch(`/admin/badges/${id}`, {
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
