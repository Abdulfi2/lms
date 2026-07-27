@extends('layouts.app')

@section('title', 'Submisi Assignment: ' . $assignment->title)
@section('page-title', 'Daftar Submisi')
@section('page-subtitle', 'Assignment: ' . $assignment->title)

@section('content')
<div class="mb-6">
    <a href="{{ route('instructor.submissions.index') }}" class="text-primary hover:underline flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar Assignment
    </a>
</div>

<div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
    <div class="px-6 py-4 border-b dark:border-gray-700">
        <h3 class="font-semibold text-gray-800 dark:text-white">Submisi Siswa</h3>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Siswa</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tgl Kirim</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Skor</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($submissions as $submission)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                                <img class="h-10 w-10 rounded-full" src="{{ $submission->student->avatar_url }}" alt="">
                            </div>
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $submission->student->name }}</div>
                                <div class="text-sm text-gray-500">{{ $submission->student->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900 dark:text-white">{{ $submission->submitted_at ? $submission->submitted_at->format('d M Y, H:i') : '-' }}</div>
                        @if($submission->is_late)
                            <span class="text-xs text-red-600 font-semibold">Terlambat</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $statusClasses = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'submitted' => 'bg-blue-100 text-blue-800',
                                'graded' => 'bg-green-100 text-green-800',
                                'returned' => 'bg-yellow-100 text-yellow-800',
                                'late' => 'bg-red-100 text-red-800',
                            ];
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClasses[$submission->status] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ ucfirst($submission->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white font-bold">
                        {{ $submission->score ?? '-' }} / {{ $assignment->max_score }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <a href="{{ route('instructor.submissions.edit', $submission->id) }}" class="text-primary hover:text-blue-900 bg-blue-50 px-3 py-1 rounded">
                            {{ $submission->status === 'graded' ? 'Edit Nilai' : 'Beri Nilai' }}
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                        Belum ada siswa yang mengirimkan tugas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4">
        {{ $submissions->links() }}
    </div>
</div>
@endsection
