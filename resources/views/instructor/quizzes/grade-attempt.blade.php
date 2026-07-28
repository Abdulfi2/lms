@extends('layouts.app')

@section('title', 'Nilai Jawaban - ' . $attempt->user->name)
@section('page-title', 'Nilai Jawaban Essay')
@section('page-subtitle', $attempt->user->name . ' — ' . $quiz->title)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('instructor.courses.quizzes.grading', [$course, $quiz]) }}" class="text-primary hover:underline text-sm">&larr; Kembali ke daftar penilaian</a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
        <p class="text-sm text-gray-500">Siswa</p>
        <p class="font-semibold text-gray-800 dark:text-white">{{ $attempt->user->name }}</p>
        <p class="text-xs text-gray-400 mt-1">Dikumpulkan: {{ $attempt->completed_at->format('d M Y, H:i') }}</p>
    </div>

    <form action="{{ route('instructor.courses.quizzes.attempts.store-grade', [$course, $quiz, $attempt]) }}" method="POST" class="space-y-4">
        @csrf

        @foreach ($essayAnswers as $answer)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 space-y-3">
                <div>
                    <p class="text-xs text-gray-400 mb-1">Soal (maks. {{ $answer->question->points }} poin)</p>
                    <p class="font-medium text-gray-800 dark:text-white">{{ $answer->question->question }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 mb-1">Jawaban Siswa</p>
                    <div class="p-3 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $answer->answer_text ?: '(Tidak dijawab)' }}</div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium mb-1">Poin Diberikan</label>
                        <input type="number" name="grades[{{ $answer->id }}][points_earned]"
                            value="{{ old("grades.$answer->id.points_earned", $answer->points_earned) }}"
                            min="0" max="{{ $answer->question->points }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Catatan untuk Siswa (opsional)</label>
                    <textarea name="grades[{{ $answer->id }}][feedback]" rows="2"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old("grades.$answer->id.feedback", $answer->feedback) }}</textarea>
                </div>
                @if ($answer->graded_at)
                    <p class="text-xs text-green-600">Sudah dinilai sebelumnya pada {{ $answer->graded_at->format('d M Y, H:i') }}.</p>
                @endif
            </div>
        @endforeach

        <div class="flex justify-end">
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary font-medium">
                Simpan Penilaian
            </button>
        </div>
    </form>
</div>
@endsection
