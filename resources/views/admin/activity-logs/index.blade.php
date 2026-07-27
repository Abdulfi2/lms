@extends('layouts.app')

@section('title', 'Activity Log')
@section('page-title', 'Activity Log')
@section('page-subtitle', 'Riwayat aktivitas sistem (read-only)')

@section('content')
<div class="space-y-6">
    <form method="GET" class="flex flex-wrap gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari user, aksi, atau deskripsi..."
            class="w-72 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
        <select name="action" class="px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            <option value="">Semua Aksi</option>
            @foreach ($actions as $action)
                <option value="{{ $action }}" {{ request('action') === $action ? 'selected' : '' }}>{{ $action }}</option>
            @endforeach
        </select>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Filter</button>
        @if (request()->filled('search') || request()->filled('action'))
            <a href="{{ route('admin.activity-logs.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Reset</a>
        @endif
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Waktu</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Aksi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Deskripsi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">IP</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($logs as $log)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td class="px-6 py-4 text-sm">{{ $log->user->name ?? 'Sistem' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full bg-gray-100 dark:bg-gray-700">{{ $log->action }}</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-300">{{ Str::limit($log->description ?? '-', 60) }}</td>
                            <td class="px-6 py-4 text-xs text-gray-400">{{ $log->ip_address ?? '-' }}</td>
                            <td class="px-6 py-4 text-right">
                                @if ($log->old_data || $log->new_data)
                                    <a href="{{ route('admin.activity-logs.show', $log) }}" class="text-blue-600 hover:text-blue-800 text-sm">Detail</a>
                                @else
                                    <span class="text-xs text-gray-300">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada activity log.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $logs->links() }}
        </div>
    </div>
</div>
@endsection
