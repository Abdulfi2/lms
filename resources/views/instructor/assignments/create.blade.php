@extends('layouts.app')

@section('title', 'Tambah Tugas Baru')
@section('page-title', 'Buat Tugas')
@section('page-subtitle', 'Tambahkan tugas baru untuk kursus atau materi tertentu.')

@section('content')
<div class="max-w-4xl mx-auto">
    <form action="{{ route('instructor.assignments.store') }}" method="POST" class="space-y-6">
        @csrf
        
        <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Kursus Selection -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Kursus <span class="text-red-500">*</span></label>
                    <select name="course_id" id="course_id" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                        <option value="">-- Pilih Kursus --</option>
                        @foreach($courses as $course)
                            <option value="{{ $course->id }}">{{ $course->title }}</option>
                        @endforeach
                    </select>
                    @error('course_id') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <!-- Lesson Selection -->
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Pilih Materi (Opsional)</label>
                    <select name="lesson_id" id="lesson_id" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition" disabled>
                        <option value="">-- Pilih Kursus Terlebih Dahulu --</option>
                    </select>
                    <p class="mt-1 text-[10px] text-gray-400 italic">Pilih materi jika tugas ini spesifik untuk satu pertemuan.</p>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Judul Tugas <span class="text-red-500">*</span></label>
                    <input type="text" name="title" required value="{{ old('title') }}" placeholder="Contoh: Implementasi CRUD Dasar" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Deskripsi Singkat <span class="text-red-500">*</span></label>
                    <textarea name="description" rows="3" required class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition" placeholder="Jelaskan tujuan dari tugas ini...">{{ old('description') }}</textarea>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Instruksi Detail</label>
                    <textarea name="instructions" rows="6" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition" placeholder="Berikan langkah-langkah detail pengerjaan...">{{ old('instructions') }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Skor Maksimal <span class="text-red-500">*</span></label>
                    <input type="number" name="max_score" required value="{{ old('max_score', 100) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Skor Kelulusan <span class="text-red-500">*</span></label>
                    <input type="number" name="passing_score" required value="{{ old('passing_score', 70) }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Batas Waktu (Deadline)</label>
                    <input type="datetime-local" name="due_date" value="{{ old('due_date') }}" class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 focus:ring-primary focus:border-primary transition">
                </div>

                <div class="flex items-center space-x-6">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published') ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Publikasikan Langsung</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="allow_late_submission" value="1" {{ old('allow_late_submission') ? 'checked' : '' }} class="rounded border-gray-300 text-primary focus:ring-primary">
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
            <div id="rubric-rows" class="space-y-3"></div>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('instructor.assignments.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition">Batal</a>
            <button type="submit" class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-700 transition">Simpan Tugas</button>
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
<script>
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
