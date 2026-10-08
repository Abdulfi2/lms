@extends('layouts.app')

@section('title', 'Basis Pengetahuan')
@section('page-title', 'Basis Pengetahuan')
@section('page-subtitle', 'Cari jawaban sebelum membuat tiket bantuan')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel bantuan..."
                class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700 w-64">
            @if ($categories->isNotEmpty())
                <select name="category" onchange="this.form.submit()" class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Semua Kategori</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>{{ $category }}</option>
                    @endforeach
                </select>
            @endif
            <button type="submit" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-sm">Cari</button>
        </form>
        <a href="{{ route('tickets.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition text-sm">
            Tidak menemukan jawaban? Buat Tiket
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @forelse ($articles as $article)
            <a href="{{ route('knowledge-base.show', $article) }}" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 hover:shadow-md transition">
                @if ($article->category)
                    <span class="px-2 py-0.5 text-xs rounded-full bg-primary/10 text-primary">{{ $article->category }}</span>
                @endif
                <h3 class="font-semibold text-gray-800 dark:text-white mt-2">{{ $article->title }}</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit(strip_tags($article->content), 100) }}</p>
            </a>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500 dark:text-gray-400">
                Belum ada artikel basis pengetahuan.
            </div>
        @endforelse
    </div>

    <div>{{ $articles->links() }}</div>
</div>
@endsection
