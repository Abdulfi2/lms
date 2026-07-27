@extends('layouts.app')

@section('title', 'Penilaian Tugas: ' . $submission->student->name)
@section('page-title', 'Form Penilaian')
@section('page-subtitle', 'Siswa: ' . $submission->student->name)

@section('content')
<div class="mb-6">
    <a href="{{ route('instructor.submissions.show', $submission->assignment_id) }}" class="text-primary hover:underline flex items-center">
        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        Kembali ke Daftar Submisi
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Konten Submisi -->
    <div class="lg:col-span-2 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Isi Jawaban</h3>
            <div class="prose dark:prose-invert max-w-none border dark:border-gray-700 p-4 rounded-lg bg-gray-50 dark:bg-gray-900">
                {!! nl2br(e($submission->content)) !!}
            </div>
            
            @if($submission->attachments)
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Lampiran:</h4>
                    <div class="space-y-2">
                        @foreach(json_decode($submission->attachments, true) as $attachment)
                            <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="flex items-center p-2 border dark:border-gray-700 rounded hover:bg-gray-50 dark:hover:bg-gray-700 text-blue-600">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/></svg>
                                {{ $attachment['name'] ?? 'Unduh File' }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
        
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Instruksi Tugas</h3>
            <div class="text-sm text-gray-600 dark:text-gray-400">
                {!! $submission->assignment->instructions !!}
            </div>
        </div>
    </div>

    <!-- Form Penilaian -->
    <div class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 sticky top-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-4">Input Nilai</h3>
            
            <form action="{{ route('instructor.submissions.update', $submission->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Skor (Maks: {{ $submission->assignment->max_score }})</label>
                    <input type="number" name="score" value="{{ old('score', $submission->score) }}" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary"
                        max="{{ $submission->assignment->max_score }}" min="0" required>
                    @error('score') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Umpan Balik (Feedback)</label>
                    <textarea name="feedback" rows="5" 
                        class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary"
                        placeholder="Tulis saran untuk siswa...">{{ old('feedback', $submission->feedback) }}</textarea>
                    @error('feedback') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary">
                        <option value="graded" {{ $submission->status === 'graded' ? 'selected' : '' }}>Selesai Dinilai</option>
                        <option value="returned" {{ $submission->status === 'returned' ? 'selected' : '' }}>Kembalikan ke Siswa</option>
                    </select>
                </div>
                
                <button type="submit" class="w-full bg-primary hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-lg transition duration-200">
                    Simpan Penilaian
                </button>
            </form>
            
            <div class="mt-6 pt-6 border-t dark:border-gray-700 text-xs text-gray-500">
                <div class="flex justify-between mb-1">
                    <span>Passing Score:</span>
                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ $submission->assignment->passing_score }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Due Date:</span>
                    <span>{{ $submission->assignment->due_date ? $submission->assignment->due_date->format('d M Y') : '-' }}</span>
                </div>
                @if($submission->is_late)
                    <div class="mt-2 p-2 bg-red-50 text-red-700 rounded border border-red-100">
                        Siswa terlambat mengumpulkan tugas.
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
