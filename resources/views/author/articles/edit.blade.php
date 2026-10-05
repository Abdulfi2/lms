@extends('layouts.app')

@section('title', 'Edit Artikel')
@section('page-title', 'Edit Artikel')
@section('page-subtitle', 'Perbarui artikel Anda')

@section('content')
<div class="max-w-3xl mx-auto">
    @if ($article->status === 'published')
        <div class="mb-4 p-4 bg-green-50 dark:bg-green-900/20 rounded-lg text-sm text-green-800 dark:text-green-400">
            Artikel ini sudah dipublikasikan. Anda masih bisa mengubah isinya, tapi status publikasi hanya bisa diubah oleh admin/editor.
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <form action="{{ route('author.articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Judul <span class="text-red-500">*</span></label>
                <input type="text" name="title" value="{{ old('title', $article->title) }}" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Kategori</label>
                <select name="category_id" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
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
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Ringkasan</label>
                <textarea name="excerpt" rows="2"
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('excerpt', $article->excerpt) }}</textarea>
                @error('excerpt')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Konten <span class="text-red-500">*</span></label>
                <textarea name="content" rows="10" required
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('content', $article->content) }}</textarea>
                @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Gambar Unggulan</label>
                @if ($article->featured_image)
                    <img src="{{ Storage::url($article->featured_image) }}" class="w-32 h-20 object-cover rounded-lg mb-2">
                @endif
                <input type="file" name="featured_image" accept="image/*" class="w-full">
                <p class="text-xs text-gray-500 mt-1">Maksimal 2MB. Kosongkan jika tidak ingin mengganti gambar.</p>
                @error('featured_image')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-1 text-gray-700 dark:text-gray-300">Status</label>
                @if ($article->status === 'published')
                    <div class="px-3 py-2 rounded-lg bg-green-100 text-green-800 text-sm inline-block">Published</div>
                @else
                    <select name="status" required class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                        <option value="draft" {{ old('status', $article->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="archived" {{ old('status', $article->status) == 'archived' ? 'selected' : '' }}>Archived</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-1">Publish hanya bisa dilakukan oleh admin/editor setelah ditinjau.</p>
                @endif
                @error('status')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end space-x-3 pt-4 border-t dark:border-gray-700">
                <a href="{{ route('author.articles.index') }}" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 transition">Batal</a>
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>
@endsection
