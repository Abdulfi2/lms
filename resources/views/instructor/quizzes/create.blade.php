{{-- resources/views/instructor/quizzes/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Buat Quiz Baru')
@section('page-title', 'Buat Quiz')
@section('page-subtitle', 'Untuk kursus: ' . $course->title)

@section('content')
    <div class="max-w-3xl mx-auto">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('instructor.courses.quizzes.store', $course) }}" method="POST">
                @csrf

                <div class="space-y-6">
                    <!-- Judul Quiz -->
                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Judul Quiz <span class="text-red-500">*</span>
                        </label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" required
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-primary focus:ring-primary">
                        @error('title')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Deskripsi -->
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Deskripsi (opsional)
                        </label>
                        <textarea name="description" id="description" rows="3"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-primary focus:ring-primary">{{ old('description') }}</textarea>
                        @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Waktu Pengerjaan (menit) -->
                        <div>
                            <label for="time_limit" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Waktu (menit) <span class="text-gray-400 text-xs">(0 = tidak terbatas)</span>
                            </label>
                            <input type="number" name="time_limit" id="time_limit" value="{{ old('time_limit', 0) }}"
                                min="0"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-primary focus:ring-primary">
                            @error('time_limit')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Jumlah Attempt yang Diizinkan -->
                        <div>
                            <label for="attempts_allowed"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Maksimal Attempt <span class="text-gray-400 text-xs">(1 = sekali)</span>
                            </label>
                            <input type="number" name="attempts_allowed" id="attempts_allowed"
                                value="{{ old('attempts_allowed', 1) }}" min="1"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-primary focus:ring-primary">
                            @error('attempts_allowed')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Passing Score (%) -->
                        <div>
                            <label for="passing_score"
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Passing Score (%) <span class="text-gray-400 text-xs">(default: 70)</span>
                            </label>
                            <input type="number" name="passing_score" id="passing_score"
                                value="{{ old('passing_score', 70) }}" min="0" max="100"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-primary focus:ring-primary">
                            @error('passing_score')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Opsi Lanjutan -->
                    <div class="space-y-2">
                        <label class="flex items-center">
                            <input type="checkbox" name="randomize_questions" value="1"
                                {{ old('randomize_questions') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Acak urutan soal</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="is_published" value="1"
                                {{ old('is_published', true) ? 'checked' : '' }}
                                class="rounded border-gray-300 text-primary focus:ring-primary">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publikasikan (siswa dapat
                                mengerjakan)</span>
                        </label>
                    </div>

                    @if ($errors->any())
                        <div class="bg-red-50 dark:bg-red-900/20 border-l-4 border-red-500 p-3 rounded">
                            <p class="text-red-600 dark:text-red-400 text-sm">Ada kesalahan pada data yang dimasukkan.</p>
                        </div>
                    @endif
                </div>

                <div class="mt-8 flex justify-end space-x-3">
                    <a href="{{ route('instructor.courses.quizzes.index', $course) }}"
                        class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition shadow-sm">
                        Simpan dan Lanjut ke Soal
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
