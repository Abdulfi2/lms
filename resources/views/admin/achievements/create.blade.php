@extends('layouts.app')

@section('title', 'Tambah Achievement')
@section('page-title', 'Tambah Achievement')
@section('page-subtitle', 'Buat pencapaian baru untuk siswa')

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.achievements.store') }}" method="POST" class="space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium mb-1">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="description" rows="2" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Ikon</label>
                <input type="text" name="icon" value="{{ old('icon') }}" placeholder="mis. fa-trophy"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Tipe Syarat <span class="text-red-500">*</span></label>
                    <select name="condition_type" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        @foreach ($conditionTypes as $value => $label)
                            <option value="{{ $value }}" {{ old('condition_type') == $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    @error('condition_type')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Nilai Syarat <span class="text-red-500">*</span></label>
                    <input type="number" name="condition_value" value="{{ old('condition_value', 1) }}" min="1" required
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    @error('condition_value')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Poin Hadiah <span class="text-red-500">*</span></label>
                <input type="number" name="points_reward" value="{{ old('points_reward', 0) }}" min="0" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                @error('points_reward')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" checked class="rounded">
                    <span class="ml-2 text-sm">Aktif</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.achievements.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
