@extends('layouts.app')

@section('title', 'Edit Level')
@section('page-title', 'Edit Level')
@section('page-subtitle', $level->name)

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.levels.update', $level) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Nomor Level <span class="text-red-500">*</span></label>
                    <input type="number" name="level_number" value="{{ old('level_number', $level->level_number) }}" min="1" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    @error('level_number')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Poin Dibutuhkan <span class="text-red-500">*</span></label>
                    <input type="number" name="points_required" value="{{ old('points_required', $level->points_required) }}" min="0" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    @error('points_required')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Nama Level <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $level->name) }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Ikon</label>
                <input type="text" name="icon" value="{{ old('icon', $level->icon) }}"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Deskripsi</label>
                <textarea name="description" rows="2"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('description', $level->description) }}</textarea>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.levels.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
