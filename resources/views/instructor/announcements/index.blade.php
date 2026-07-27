@extends('layouts.app')

@section('title', 'Pengumuman')
@section('page-title', 'Pengumuman')
@section('page-subtitle', 'Riwayat pengumuman yang Anda kirim ke siswa')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('instructor.announcements.create') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">+ Kirim Pengumuman</a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Judul</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pesan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kursus</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Penerima</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Dikirim</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($broadcasts as $broadcast)
                    <tr>
                        <td class="px-4 py-3 text-sm font-medium text-gray-900 dark:text-white">{{ $broadcast->title }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ \Illuminate\Support\Str::limit($broadcast->message, 60) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ \App\Models\Course::find($broadcast->course_id)?->title ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $broadcast->recipients }} siswa</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ $broadcast->sent_at->format('d/m/Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">Belum ada pengumuman yang dikirim.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $broadcasts->links() }}
</div>
@endsection
