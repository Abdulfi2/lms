@extends('layouts.app')

@section('title', 'Manajemen Tugas')
@section('page-title', 'Daftar Tugas')
@section('page-subtitle', 'Kelola semua tugas untuk siswa Anda di sini.')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Semua Tugas</h2>
        <a href="{{ route('instructor.assignments.create') }}" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-secondary transition flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Tambah Tugas Baru
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Judul Tugas</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Kursus / Materi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Deadline</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-center">Statistik</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($assignments as $assignment)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $assignment->title }}</div>
                        <div class="text-xs text-gray-500 truncate max-w-[200px]">{{ Str::limit($assignment->description, 50) }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400 font-medium">{{ $assignment->course->title }}</div>
                        @if($assignment->lesson)
                            <div class="text-xs text-gray-400">Lesson: {{ $assignment->lesson->title }}</div>
                        @else
                            <div class="text-xs text-gray-400">General Course Assignment</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($assignment->due_date)
                            <div class="text-sm {{ $assignment->isOverdue() ? 'text-red-500 font-semibold' : 'text-gray-600 dark:text-gray-400' }}">
                                {{ $assignment->due_date->format('d M Y') }}
                            </div>
                            <div class="text-[10px] text-gray-400">{{ $assignment->due_date->format('H:i') }}</div>
                        @else
                            <span class="text-sm text-gray-400">No Deadline</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-center">
                        <x-status-badge :status="$assignment->is_published" class="text-[10px]" />
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="flex flex-col items-center">
                            <span class="text-sm font-bold text-gray-700 dark:text-gray-300">{{ $assignment->total_submissions }}</span>
                            <span class="text-[10px] text-gray-400 uppercase">Submisi</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end space-x-2">
                            <a href="{{ route('instructor.submissions.show', $assignment->id) }}" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Lihat Submisi">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                            </a>
                            <a href="{{ route('instructor.assignments.edit', $assignment->id) }}" class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition" title="Edit Tugas">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            <form action="{{ route('instructor.assignments.destroy', $assignment->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tugas ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus Tugas">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        Belum ada tugas yang dibuat.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-6">
        {{ $assignments->links() }}
    </div>
</div>
@endsection
