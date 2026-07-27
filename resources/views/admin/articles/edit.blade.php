@extends('layouts.app')

@section('title', 'Edit Artikel')
@section('page-title', 'Edit Artikel')
@section('page-subtitle', $article->title)

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('admin.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            @if ($article->featured_image)
                <div>
                    <img src="{{ Storage::url($article->featured_image) }}" class="w-full h-40 object-cover rounded-lg mb-2">
                </div>
            @endif

            <div>
                <label class="block text-sm font-medium mb-1">Judul <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $article->title) }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Kategori</label>
                <select name="category_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Tanpa Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $article->category_id) == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Ringkasan</label>
                <textarea name="excerpt" rows="2"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('excerpt', $article->excerpt) }}</textarea>
                @error('excerpt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Konten <span class="text-red-500">*</span></label>
                <textarea name="content" rows="10" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">{{ old('content', $article->content) }}</textarea>
                @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">Ganti Gambar Unggulan</label>
                <input type="file" name="featured_image" accept="image/*" class="w-full">
                <p class="text-xs text-gray-500 mt-1">Maksimal 2MB. Kosongkan jika tidak ingin mengganti.</p>
                @error('featured_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">Status <span class="text-red-500">*</span></label>
                    <select name="status" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                        <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $article->status) == 'published' ? 'selected' : '' }}>Published</option>
                        <option value="archived" {{ old('status', $article->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium mb-1">Tanggal Publikasi</label>
                    <input type="datetime-local" name="published_at"
                        value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}"
                        class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                </div>
            </div>

            <div class="flex justify-end space-x-3 pt-4">
                <a href="{{ route('admin.articles.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
