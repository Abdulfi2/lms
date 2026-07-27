@extends('layouts.app')

@section('title', 'Kirim Notifikasi')
@section('page-title', 'Kirim Notifikasi')
@section('page-subtitle', 'Broadcast pengumuman ke user tertentu')

@section('content')
<div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
    <form method="POST" action="{{ route('admin.notifications.store') }}" x-data="{ audience: 'all' }" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">Judul <span class="text-red-500">*</span></label>
            <input type="text" name="title" value="{{ old('title') }}" required maxlength="255" class="w-full rounded-lg border-gray-300">
            @error('title') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Pesan <span class="text-red-500">*</span></label>
            <textarea name="message" rows="4" required maxlength="1000" class="w-full rounded-lg border-gray-300">{{ old('message') }}</textarea>
            @error('message') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Link Tujuan (opsional)</label>
            <input type="text" name="action_url" value="{{ old('action_url') }}" placeholder="/courses" class="w-full rounded-lg border-gray-300">
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Audiens <span class="text-red-500">*</span></label>
            <select name="audience" x-model="audience" required class="w-full rounded-lg border-gray-300">
                <option value="all" {{ old('audience') === 'all' ? 'selected' : '' }}>Semua User</option>
                <option value="students" {{ old('audience') === 'students' ? 'selected' : '' }}>Semua Siswa</option>
                <option value="instructors" {{ old('audience') === 'instructors' ? 'selected' : '' }}>Semua Instruktur</option>
                <option value="course" {{ old('audience') === 'course' ? 'selected' : '' }}>Siswa di Kursus Tertentu</option>
            </select>
            @error('audience') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div x-show="audience === 'course'" x-cloak>
            <label class="block text-sm font-medium mb-1">Pilih Kursus</label>
            <select name="course_id" class="w-full rounded-lg border-gray-300">
                <option value="">-- Pilih Kursus --</option>
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" {{ (string) old('course_id') === (string) $course->id ? 'selected' : '' }}>{{ $course->title }}</option>
                @endforeach
            </select>
            @error('course_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('admin.notifications.index') }}" class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
            <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Kirim</button>
        </div>
    </form>
</div>
@endsection
