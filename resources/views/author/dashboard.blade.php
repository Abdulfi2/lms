@extends('layouts.app')

@section('title', 'Dashboard Author')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang kembali, ' . Auth::user()->name)

@section('content')
<div class="space-y-6">
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Artikel</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ $stats['total'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Draft</p>
            <p class="text-2xl font-bold text-yellow-600 mt-1">{{ $stats['draft'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Published</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['published'] }}</p>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Dilihat</p>
            <p class="text-2xl font-bold text-gray-800 dark:text-white mt-1">{{ number_format($stats['views']) }}</p>
        </div>
    </div>

    <div class="p-4 bg-primary/5 border border-primary/20 rounded-xl text-sm text-gray-700 dark:text-gray-300">
        Artikel yang Anda tulis akan tersimpan sebagai <strong>draft</strong>. Admin atau editor akan meninjau dan
        mempublikasikannya — Anda tidak bisa mempublikasikan artikel sendiri.
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="px-6 py-4 border-b dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-800 dark:text-white">Artikel Terbaru</h3>
            <a href="{{ route('author.articles.create') }}" class="px-3 py-1.5 bg-primary text-white text-sm rounded-lg hover:bg-secondary transition">
                + Tulis Artikel
            </a>
        </div>
        <div class="divide-y dark:divide-gray-700">
            @forelse ($recentArticles as $article)
                <a href="{{ route('author.articles.edit', $article) }}" class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                    <div>
                        <p class="font-medium text-gray-800 dark:text-white">{{ $article->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $article->category->name ?? 'Tanpa kategori' }} &middot; {{ $article->created_at->format('d M Y') }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $article->status == 'published' ? 'bg-green-100 text-green-800' : ($article->status == 'draft' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                        {{ ucfirst($article->status) }}
                    </span>
                </a>
            @empty
                <div class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Anda belum menulis artikel apa pun.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
