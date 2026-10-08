@extends('layouts.app')

@section('title', 'Artikel')
@section('page-title', 'Artikel')
@section('page-subtitle', 'Tinjau, tulis, dan kelola artikel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('editor.articles.index', ['tab' => 'pending']) }}"
            class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === 'pending' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-700 dark:text-gray-300' }}">
            Menunggu Review
            @if ($pendingCount > 0)
                <span class="ml-1 {{ $tab === 'pending' ? 'bg-white/20' : 'bg-red-500 text-white' }} text-xs rounded-full px-1.5 py-0.5">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('editor.articles.index', ['tab' => 'mine']) }}"
            class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === 'mine' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-700 dark:text-gray-300' }}">
            Artikel Saya
        </a>
        <a href="{{ route('editor.articles.index', ['tab' => 'all']) }}"
            class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === 'all' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-700 dark:text-gray-300' }}">
            Semua Artikel
        </a>
    </div>

    <div class="flex justify-between items-center flex-wrap gap-3">
        <form method="GET" class="flex gap-2">
            <input type="hidden" name="tab" value="{{ $tab }}">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau penulis..."
                class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
            @if ($tab !== 'pending')
                <select name="status" onchange="this.form.submit()" class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Menunggu Review</option>
                    <option value="revision" {{ request('status') == 'revision' ? 'selected' : '' }}>Revisi</option>
                    <option value="ready_to_publish" {{ request('status') == 'ready_to_publish' ? 'selected' : '' }}>Siap Terbit</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            @endif
            <button type="submit" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-sm">Cari</button>
        </form>
        <a href="{{ route('editor.articles.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
            + Tulis Artikel
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Penulis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($articles as $article)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($article->featured_image)
                                        <img src="{{ Storage::url($article->featured_image) }}" class="w-10 h-10 rounded object-cover">
                                    @endif
                                    <span class="font-medium text-gray-800 dark:text-white">{{ Str::limit($article->title, 40) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $article->author->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $article->category->name ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <x-article-status-badge :status="$article->status" />
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $article->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    @if ($article->user_id === Auth::id())
                                        <a href="{{ route('editor.articles.edit', $article) }}" class="text-primary hover:underline text-sm">Edit</a>
                                    @elseif ($article->status === 'draft')
                                        <a href="{{ route('editor.articles.show', $article) }}" class="px-3 py-1.5 bg-primary text-white text-xs rounded-lg hover:bg-secondary transition">Review Sekarang</a>
                                    @elseif ($article->status === 'ready_to_publish')
                                        <form method="POST" action="{{ route('editor.articles.publish', $article) }}">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="px-3 py-1.5 bg-primary text-white text-xs rounded-lg hover:bg-secondary transition">Terbitkan</button>
                                        </form>
                                        <a href="{{ route('editor.articles.show', $article) }}" class="text-gray-500 hover:underline text-sm">Lihat</a>
                                    @else
                                        <a href="{{ route('editor.articles.show', $article) }}" class="text-gray-500 hover:underline text-sm">Lihat</a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                @if ($tab === 'pending')
                                    Tidak ada draft yang menunggu review. 🎉
                                @else
                                    Tidak ada artikel ditemukan.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($articles->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $articles->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
