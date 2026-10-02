@extends('layouts.app')

@section('title', 'Tambah Kategori')
@section('page-title', 'Tambah Kategori Baru')

@section('content')
    <div class="max-w-2xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Kategori <span
                            class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700" required>
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Slug (biarkan kosong untuk
                        auto-generate)</label>
                    <input type="text" name="slug" value="{{ old('slug') }}"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kategori Induk</label>
                    <select name="parent_id"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="">Tidak ada (Root)</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" {{ old('parent_id') == $parent->id ? 'selected' : '' }}>
                                {{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Deskripsi</label>
                    <textarea name="description" rows="3"
                        class="mt-1 w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('description') }}</textarea>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Icon (FontAwesome)</label>
                        <input type="text" name="icon" value="{{ old('icon') }}" placeholder="fas fa-code"
                            class="mt-1 w-full rounded-lg border-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Warna (Hex)</label>
                        <input type="color" name="color" value="{{ old('color', '#769826') }}"
                            class="mt-1 w-full h-10 rounded border">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Urutan</label>
                    <input type="number" name="order" value="{{ old('order', 0) }}"
                        class="mt-1 w-32 rounded-lg border-gray-300">
                </div>

                <div class="flex items-center">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                        class="rounded border-gray-300">
                    <label class="ml-2 text-sm text-gray-700 dark:text-gray-300">Aktif</label>
                </div>
            </div>

            <div class="mt-6 flex justify-end space-x-3">
                <a href="{{ route('admin.categories.index') }}"
                    class="px-4 py-2 border rounded-lg hover:bg-gray-100">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan</button>
            </div>
        </form>
    </div>
@endsection
