@extends('layouts.app')

@section('title', 'Edit Tugas')
@section('page-title', 'Edit Tugas')
@section('page-subtitle', 'Perbarui detail tugas untuk siswa Anda.')

@push('styles')
    <link href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css" rel="stylesheet">
@endpush

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('instructor.assignments.update', $assignment->id) }}" method="POST" class="space-y-6" id="assignment-form">
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
                    <div id="quill-instructions" class="bg-white dark:bg-gray-900 rounded-b-lg" style="min-height: 150px;"></div>
                    <textarea name="instructions" id="instructions-hidden" class="hidden">{{ old('instructions', $assignment->instructions) }}</textarea>
                    <p class="mt-1 text-xs text-gray-400">Berikan langkah-langkah detail pengerjaan, bisa pakai daftar bernomor/bullet.</p>
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

        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6">
            <div class="flex justify-between items-center mb-4">
                <div>
                    <h3 class="font-semibold text-gray-800 dark:text-white">Rubrik Penilaian (Opsional)</h3>
                    <p class="text-xs text-gray-400 mt-1">Jika diisi, penilaian tugas akan dihitung berdasarkan kriteria di bawah ini alih-alih skor tunggal.</p>
                </div>
                <button type="button" onclick="addRubricRow()" class="text-sm text-primary">+ Tambah Kriteria</button>
            </div>
            <div id="rubric-rows" class="space-y-3">
                @foreach ($assignment->rubricItems as $item)
                    <div class="rubric-row flex gap-2 items-start">
                        <input type="text" class="rubric-criteria flex-1 rounded-lg border-gray-300 text-sm" placeholder="Kriteria" value="{{ $item->criteria }}">
                        <input type="text" class="rubric-description flex-1 rounded-lg border-gray-300 text-sm" placeholder="Deskripsi (opsional)" value="{{ $item->description }}">
                        <input type="number" class="rubric-max-points w-24 rounded-lg border-gray-300 text-sm" placeholder="Poin" min="1" value="{{ $item->max_points }}">
                        <button type="button" onclick="this.closest('.rubric-row').remove()" class="text-red-500 px-2">Hapus</button>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('instructor.assignments.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition">Simpan Perubahan</button>
        </div>
    </form>
</div>

<template id="rubric-row-template">
    <div class="rubric-row flex gap-2 items-start">
        <input type="text" class="rubric-criteria flex-1 rounded-lg border-gray-300 text-sm" placeholder="Kriteria (mis. Kerapian Kode)">
        <input type="text" class="rubric-description flex-1 rounded-lg border-gray-300 text-sm" placeholder="Deskripsi (opsional)">
        <input type="number" class="rubric-max-points w-24 rounded-lg border-gray-300 text-sm" placeholder="Poin" min="1">
        <button type="button" onclick="this.closest('.rubric-row').remove()" class="text-red-500 px-2">Hapus</button>
    </div>
</template>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
<script>
    const instructionsQuill = new Quill('#quill-instructions', {
        theme: 'snow',
        placeholder: 'Berikan langkah-langkah detail pengerjaan...',
        modules: {
            toolbar: [
                ['bold', 'italic', 'underline'],
                [{ list: 'ordered' }, { list: 'bullet' }],
                ['link'],
                ['clean']
            ]
        }
    });
    const instructionsHidden = document.getElementById('instructions-hidden');
    if (instructionsHidden.value) {
        instructionsQuill.root.innerHTML = instructionsHidden.value;
    }
    document.getElementById('assignment-form').addEventListener('submit', function () {
        instructionsHidden.value = instructionsQuill.root.innerHTML;
    });

    function addRubricRow() {
        const template = document.getElementById('rubric-row-template');
        const clone = template.content.cloneNode(true);
        document.getElementById('rubric-rows').appendChild(clone);
    }

    document.querySelector('form').addEventListener('submit', function () {
        document.querySelectorAll('#rubric-rows .rubric-row').forEach((row, idx) => {
            const criteria = row.querySelector('.rubric-criteria').value;
            const description = row.querySelector('.rubric-description').value;
            const maxPoints = row.querySelector('.rubric-max-points').value;
            if (!criteria || !maxPoints) return;

            ['criteria', 'description', 'max_points'].forEach((field) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `rubric[${idx}][${field}]`;
                input.value = field === 'criteria' ? criteria : (field === 'description' ? description : maxPoints);
                this.appendChild(input);
            });
        });
    });

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
