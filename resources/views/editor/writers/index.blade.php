@extends('layouts.app')

@section('title', 'Penulis')
@section('page-title', 'Penulis')
@section('page-subtitle', 'Direktori penulis dan ringkasan performa artikel mereka')

@section('content')
<div class="space-y-6">
    <form method="GET" class="flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama penulis..."
            class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
        <button type="submit" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-sm">Cari</button>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Penulis</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Total Artikel</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Published</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Menunggu Review</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Revisi</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Aktivitas Terakhir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($writers as $writer)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $writer->avatar_url }}" class="w-8 h-8 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium text-gray-800 dark:text-white">{{ $writer->name }}</p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $writer->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $writer->total_articles }}</td>
                            <td class="px-6 py-4 text-sm text-green-600">{{ $writer->published_articles }}</td>
                            <td class="px-6 py-4 text-sm text-yellow-600">{{ $writer->pending_articles }}</td>
                            <td class="px-6 py-4 text-sm text-red-500">{{ $writer->revision_articles }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">
                                {{ $writer->articles_max_created_at ? \Carbon\Carbon::parse($writer->articles_max_created_at)->diffForHumans() : '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                Belum ada penulis.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($writers->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $writers->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
