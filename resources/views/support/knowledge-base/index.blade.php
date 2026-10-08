@extends('layouts.app')

@section('title', 'Basis Pengetahuan')
@section('page-title', 'Basis Pengetahuan')
@section('page-subtitle', 'Kelola artikel swalayan untuk pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center flex-wrap gap-3">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari artikel..."
                class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
            <button type="submit" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-sm">Cari</button>
        </form>
        <a href="{{ route('support.knowledge-base.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
            + Tulis Artikel
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Dilihat</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($articles as $article)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ Str::limit($article->title, 40) }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $article->category ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs rounded-full {{ $article->is_published ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                    {{ $article->is_published ? 'Terbit' : 'Draft' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $article->views }}</td>
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-3">
                                    <a href="{{ route('support.knowledge-base.edit', $article) }}" class="text-primary hover:underline text-sm">Edit</a>
                                    <form method="POST" action="{{ route('support.knowledge-base.destroy', $article) }}" onsubmit="return confirm('Hapus artikel ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:underline text-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                Belum ada artikel basis pengetahuan.
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
