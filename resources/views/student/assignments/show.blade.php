@extends('layouts.app')

@section('title', $assignment->title)
@section('page-title', $assignment->title)
@section('page-subtitle', $assignment->course->title)

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <!-- Assignment Info -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex justify-between items-start">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800 dark:text-white">{{ $assignment->title }}</h1>
                    <div class="flex items-center space-x-4 mt-2">
                        <span class="text-sm text-gray-500">
                            <i class="far fa-calendar-alt mr-1"></i>
                            @if ($assignment->due_date)
                                Deadline: {{ $assignment->due_date->format('d M Y, H:i') }}
                            @else
                                Tidak ada deadline
                            @endif
                        </span>
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-star mr-1"></i> Maksimal Nilai: {{ $assignment->max_score }}
                        </span>
                    </div>
                </div>

                @if ($isLate && !$submission)
                    <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-sm">Terlambat</span>
                @elseif($submission && $submission->status == 'graded')
                    <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-sm">Nilai:
                        {{ $submission->score }}/{{ $assignment->max_score }}</span>
                @elseif($submission)
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-800 rounded-full text-sm">Menunggu Penilaian</span>
                @else
                    <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm">Belum Dikerjakan</span>
                @endif
            </div>

            <div class="mt-6 prose max-w-none">
                <h3 class="font-semibold text-gray-800 dark:text-white">Deskripsi Tugas</h3>
                {!! nl2br(e($assignment->description)) !!}
            </div>

            @if ($assignment->instructions)
                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg prose max-w-none dark:prose-invert">
                    <h4 class="font-semibold text-gray-800 dark:text-white">Instruksi Pengerjaan</h4>
                    {!! $assignment->instructions !!}
                </div>
            @endif

            @if ($assignment->attachment_url)
                <div class="mt-4">
                    <a href="{{ $assignment->attachment_url }}" target="_blank"
                        class="text-primary hover:underline flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                        </svg>
                        Download Materi Tugas
                    </a>
                </div>
            @endif
        </div>

        <!-- Submission Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <h3 class="text-lg font-semibold mb-4">Pengiriman Tugas</h3>

            @if ($submission && $submission->status === 'returned')
                <div class="bg-orange-50 dark:bg-orange-900/20 rounded-lg p-4 mb-4">
                    <p class="font-semibold text-orange-800 dark:text-orange-400">Tugas Dikembalikan untuk Direvisi</p>
                    @if ($submission->feedback)
                        <p class="text-sm text-orange-700 dark:text-orange-500 mt-2">{{ $submission->feedback }}</p>
                    @endif
                    <p class="text-xs text-orange-600 dark:text-orange-500 mt-2">Silakan revisi dan kirim ulang tugas Anda di bawah ini.</p>
                </div>
            @endif

            @if ($isGraded)
                <!-- Hasil Penilaian -->
                <div class="bg-green-50 dark:bg-green-900/20 rounded-lg p-4 mb-4">
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Nilai Anda</p>
                            <p class="text-3xl font-bold text-green-600">
                                {{ $submission->score }}/{{ $assignment->max_score }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Persentase</p>
                            <p class="text-xl font-semibold">
                                {{ round(($submission->score / $assignment->max_score) * 100) }}%</p>
                        </div>
                    </div>
                    @if ($submission->submission_count > 1)
                        <p class="text-xs text-gray-500 mt-2">Dinilai dari pengiriman ke-{{ $submission->submission_count }}.</p>
                    @endif
                </div>

                @if ($submission->feedback)
                    <div class="mt-4 p-4 border rounded-lg">
                        <h4 class="font-semibold mb-2">Feedback Instruktur</h4>
                        <p class="text-gray-700 dark:text-gray-300">{{ $submission->feedback }}</p>
                        <p class="text-xs text-gray-500 mt-2">Dinilai pada:
                            {{ \Carbon\Carbon::parse($submission->graded_at)->format('d M Y, H:i') }}</p>
                    </div>
                @endif
            @elseif($submission && $submission->status == 'submitted')
                <!-- Menunggu Penilaian -->
                <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <div>
                            <p class="font-semibold text-yellow-800 dark:text-yellow-400">Tugas Telah Dikirim</p>
                            <p class="text-sm text-yellow-700 dark:text-yellow-500">Tugas Anda sedang menunggu penilaian
                                dari instruktur.</p>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <p><strong>Tanggal kirim:</strong>
                        {{ \Carbon\Carbon::parse($submission->submitted_at)->format('d M Y, H:i') }}</p>
                    @if ($submission->submission_count > 1)
                        <p class="text-xs text-gray-500 mt-1">Ini pengiriman ke-{{ $submission->submission_count }} (sudah dikirim ulang {{ $submission->submission_count - 1 }}x).</p>
                    @endif
                    @if ($submission->is_late)
                        <p class="text-red-600">⚠️ Tugas dikirim terlambat</p>
                    @endif
                </div>

                <div class="mb-4">
                    <h4 class="font-semibold mb-2">Jawaban Anda</h4>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                        {!! nl2br(e($submission->content ?? '-')) !!}
                    </div>
                </div>

                @if ($submission->file_url)
                    <div class="mb-4">
                        <h4 class="font-semibold mb-2">Lampiran</h4>
                        <a href="{{ Storage::url($submission->file_url) }}" target="_blank"
                            class="text-primary hover:underline flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            Download Lampiran
                        </a>
                    </div>
                @endif

                <div class="flex space-x-3">
                    <form action="{{ route('student.assignments.cancel', $assignment) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                            Batalkan Kirim
                        </button>
                    </form>
                    <a href="{{ route('student.assignments.submit', $assignment) }}"
                        class="px-4 py-2 border rounded-lg hover:bg-gray-100">
                        Edit Tugas
                    </a>
                </div>
            @elseif($canSubmit)
                <!-- Form Submit Tugas -->
                <form action="{{ route('student.assignments.store', $assignment) }}" method="POST"
                    enctype="multipart/form-data">
                    @csrf

                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">Jawaban (Text)</label>
                        <textarea name="content" rows="6" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700"
                            placeholder="Tulis jawaban Anda di sini...">{{ old('content', $submission->content ?? '') }}</textarea>
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
                        <a href="{{ route('student.assignments.index') }}"
                            class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
                        <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary"
                            {{ $isLate && !$assignment->allow_late_submission ? 'disabled' : '' }}>
                            Kirim Tugas
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
@endsection
