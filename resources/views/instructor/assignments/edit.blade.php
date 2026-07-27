@extends('layouts.app')

@section('title', 'Edit Tugas')
@section('page-title', 'Edit Tugas')
@section('page-subtitle', 'Perbarui detail tugas untuk siswa Anda.')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('instructor.assignments.update', $assignment->id) }}" method="POST" class="space-y-6">
        @csrf
        @method('PUT')
        
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kursus Selection -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Kursus <span class="text-red-500">*</span></label>
                    <select name="course_id" id="course_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}" {{ $assignment->course_id == $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Lesson Selection -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Materi (Opsional)</label>
                    <select name="lesson_id" id="lesson_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                        <option value="">-- Pilih Materi (Opsional) --</option>
                        @foreach($lessons as $lesson)
                            <option value="{{ $lesson->id }}" {{ $assignment->lesson_id == $lesson->id ? 'selected' : '' }}>{{ $lesson->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Judul Tugas <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title', $assignment->title) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi Singkat <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">{{ old('description', $assignment->description) }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Instruksi Detail</label>
                    <textarea name="instructions" rows="6" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">{{ old('instructions', $assignment->instructions) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Skor Maksimal <span class="text-red-500">*</span></label>
                    <input type="number" name="max_score" required value="{{ old('max_score', $assignment->max_score) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Skor Kelulusan <span class="text-red-500">*</span></label>
                    <input type="number" name="passing_score" required value="{{ old('passing_score', $assignment->passing_score) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Batas Waktu (Deadline)</label>
                    <input type="datetime-local" name="due_date" value="{{ old('due_date', $assignment->due_date ? $assignment->due_date->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                </div>

                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $assignment->is_published) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publikasikan Langsung</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allow_late_submission" value="1" {{ old('allow_late_submission', $assignment->allow_late_submission) ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Izinkan Terlambat</span>
                    </label>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('instructor.assignments.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition">Simpan Perubahan</button>
        </div>
    </form>
</div>

@push('scripts')
<script>
    document.getElementById('course_id').addEventListener('change', function() {
        const courseId = this.value;
        const lessonSelect = document.getElementById('lesson_id');
        
        if (!courseId) {
            lessonSelect.innerHTML = '<option value="">-- Pilih Kursus Terlebih Dahulu --</option>';
            lessonSelect.disabled = true;
            return;
        }

        lessonSelect.disabled = false;
        lessonSelect.innerHTML = '<option value="">Memuat...</option>';

        fetch(`/instructor/api/courses/${courseId}/lessons`)
            .then(response => response.json())
            .then(data => {
                lessonSelect.innerHTML = '<option value="">-- Pilih Materi (Opsional) --</option>';
                data.forEach(lesson => {
                    const option = document.createElement('option');
                    option.value = lesson.id;
                    option.textContent = lesson.title;
                    lessonSelect.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error fetching lessons:', error);
                lessonSelect.innerHTML = '<option value="">Gagal memuat materi</option>';
            });
    });
</script>
@endpush
@endsection
