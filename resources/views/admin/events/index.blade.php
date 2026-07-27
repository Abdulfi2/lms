@extends('layouts.app')

@section('title', 'Manajemen Event')
@section('page-title', 'Manajemen Event')
@section('page-subtitle', 'Kelola webinar, workshop, dan event lainnya')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div></div>
        <a href="{{ route('admin.events.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">
            + Buat Event Baru
        </a>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Total Event</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Published</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['published'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Akan Datang</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['upcoming'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Draft</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4">
            <p class="text-xs text-gray-500">Total Peserta</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ $stats['total_participants'] }}</p>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-4 flex flex-wrap gap-3">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari event..."
            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
        <select name="status" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <option value="">Semua Status</option>
            <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
        </select>
        <select name="date_range" class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 text-sm">
            <option value="">Semua Waktu</option>
            <option value="upcoming" {{ request('date_range') == 'upcoming' ? 'selected' : '' }}>Akan Datang</option>
            <option value="past" {{ request('date_range') == 'past' ? 'selected' : '' }}>Sudah Lewat</option>
        </select>
        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg text-sm">Filter</button>
        <a href="{{ route('admin.events.index') }}" class="px-4 py-2 border rounded-lg text-sm dark:border-gray-600">Reset</a>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Event</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Tipe</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Jadwal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Peserta</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($events as $event)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.events.show', $event) }}" class="font-medium text-gray-800 dark:text-white hover:text-primary">
                                    {{ $event->title }}
                                </a>
                                <div class="text-xs text-gray-500">{{ $event->organizer->name ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 text-xs">{{ ucfirst(str_replace('_', ' ', $event->type)) }}</td>
                            <td class="px-6 py-4 text-xs">{{ optional($event->start_time)->format('d/m/Y H:i') ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs">
                                {{ $event->total_registrations }}{{ $event->max_participants ? '/' . $event->max_participants : '' }}
                            </td>
                            <td class="px-6 py-4">
                                <x-status-badge :status="$event->status" />
                            </td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex justify-end space-x-2">
                                    <a href="{{ route('admin.events.registrations', $event) }}" class="text-purple-600 hover:text-purple-800" title="Peserta">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.events.edit', $event) }}" class="text-blue-600 hover:text-blue-800" title="Edit">
                                        <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.events.destroy', $event) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin hapus event ini?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800" title="Hapus">
                                            <svg class="w-5 h-5 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">Belum ada event. Buat event pertama Anda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t dark:border-gray-700">
            {{ $events->links() }}
        </div>
    </div>
</div>
@endsection
