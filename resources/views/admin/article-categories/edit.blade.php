@extends('layouts.app')

@section('title', 'Edit Kategori Artikel')
@section('page-title', 'Edit Kategori Artikel')
@section('page-subtitle', $articleCategory->name)

@section('content')
<div class="max-w-xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.article-categories.update', $articleCategory) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $articleCategory->name) }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                @error('name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Deskripsi</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('description', $articleCategory->description) }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Ikon</label>
                    <input type="text" name="icon" value="{{ old('icon', $articleCategory->icon) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    @error('icon')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Urutan</label>
                    <input type="number" name="order" value="{{ old('order', $articleCategory->order) }}" min="0"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    @error('order')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="inline-flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', $articleCategory->is_active) ? 'checked' : '' }} class="rounded">
                    <span class="ml-2 text-sm">Aktif</span>
                </label>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.article-categories.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
