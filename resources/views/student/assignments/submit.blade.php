@extends('layouts.app')

@section('title', 'Edit Pengiriman - ' . $assignment->title)
@section('page-title', 'Edit Pengiriman Tugas')
@section('page-subtitle', $assignment->title)

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-xl font-bold text-gray-800 dark:text-white">{{ $assignment->title }}</h1>
                <span class="text-sm text-gray-500">
                    @if ($assignment->due_date)
                        Deadline: {{ $assignment->due_date->format('d M Y, H:i') }}
                    @else
                        Tidak ada deadline
                    @endif
                </span>
            </div>

            @php $isLate = $assignment->due_date !== null && $assignment->due_date < now(); @endphp

            <form action="{{ route('student.assignments.store', $assignment) }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Jawaban (Text)</label>
                    <textarea name="content" rows="6" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                        placeholder="Tulis jawaban Anda di sini...">{{ old('content', $submission->content ?? '') }}</textarea>
                    @error('content') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1">Lampiran (File)</label>
                    <input type="file" name="attachment" class="w-full">
                    <p class="text-xs text-gray-500 mt-1">Format: PDF, DOC, DOCX, ZIP, JPG, PNG. Maksimal 10MB.</p>
                    @if ($submission && $submission->file_url)
                        <p class="text-sm text-gray-500 mt-1">File saat ini: <a
                                href="{{ Storage::url($submission->file_url) }}" target="_blank"
                                class="text-primary">Lihat</a></p>
                    @endif
                    @error('attachment') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                @if ($isLate && $assignment->allow_late_submission)
                    <div class="mb-4 p-3 bg-yellow-50 rounded-lg">
                        <p class="text-sm text-yellow-800">⚠️ Tugas ini sudah melewati deadline. Pengiriman terlambat
                            akan dikenakan penalti nilai.</p>
                    </div>
                @elseif($isLate && !$assignment->allow_late_submission)
                    <div class="mb-4 p-3 bg-red-50 rounded-lg">
                        <p class="text-sm text-red-800">❌ Maaf, deadline tugas sudah lewat dan pengiriman terlambat
                            tidak diizinkan.</p>
                    </div>
                @endif

                <div class="flex justify-end space-x-3">
                    <a href="{{ route('student.assignments.show', $assignment) }}"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary"
                        {{ $isLate && !$assignment->allow_late_submission ? 'disabled' : '' }}>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
