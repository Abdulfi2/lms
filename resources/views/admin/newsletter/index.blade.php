@extends('layouts.app')

@section('title', 'Newsletter')
@section('page-title', 'Newsletter')
@section('page-subtitle', 'Daftar email yang berlangganan newsletter dari halaman Blog & Artikel')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap items-center justify-between gap-3">
        <form method="GET" class="flex gap-2">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari email..."
                class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
            <button type="submit" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-sm">Cari</button>
        </form>
        <p class="text-sm text-gray-500 dark:text-gray-400">Total: {{ $subscribers->total() }} subscriber</p>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Berlangganan Sejak</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($subscribers as $subscriber)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $subscriber->email }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $subscriber->created_at->format('d M Y H:i') }}</td>
                        <td class="px-4 py-3 text-right">
                            <form method="POST" action="{{ route('admin.newsletter.destroy', $subscriber) }}" onsubmit="return confirm('Hapus subscriber ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:underline text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">Belum ada subscriber.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $subscribers->links() }}
</div>
@endsection
