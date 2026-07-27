@extends('layouts.app')

@section('title', 'Moderasi Forum')
@section('page-title', 'Moderasi Forum')
@section('page-subtitle', 'Tinjau laporan post forum dari siswa')

@section('content')
<div class="space-y-6">
    <div class="flex space-x-2">
        <a href="{{ route('admin.post-reports.index', ['status' => 'pending']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ $status === 'pending' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Menunggu
        </a>
        <a href="{{ route('admin.post-reports.index', ['status' => 'resolved']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ $status === 'resolved' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Diselesaikan
        </a>
        <a href="{{ route('admin.post-reports.index', ['status' => 'dismissed']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ $status === 'dismissed' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Ditolak
        </a>
    </div>

    <div class="space-y-4">
        @forelse ($reports as $report)
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
                <div class="flex justify-between items-start">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 text-xs rounded-full bg-yellow-100 text-yellow-800 uppercase">{{ $report->reason }}</span>
                            <span class="text-xs text-gray-500">dilaporkan oleh {{ $report->user->name ?? '-' }} &middot; {{ $report->created_at->diffForHumans() }}</span>
                        </div>
                        @if ($report->description)
                            <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">"{{ $report->description }}"</p>
                        @endif
                    </div>
                    @if ($report->status !== 'pending')
                        <span class="px-2 py-1 text-xs rounded-full {{ $report->status === 'resolved' ? 'bg-red-100 text-red-800' : 'bg-gray-200 text-gray-700' }}">
                            {{ $report->status === 'resolved' ? 'Post Dihapus' : 'Ditolak' }}
                        </span>
                    @endif
                </div>

                <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                    @if ($report->post)
                        <div class="text-xs text-gray-500 mb-1">
                            Post oleh {{ $report->post->user->name ?? '-' }}
                            @if ($report->post->thread)
                                di thread "{{ $report->post->thread->title }}"
                                @if ($report->post->thread->forum?->course)
                                    ({{ $report->post->thread->forum->course->title }})
                                @endif
                            @endif
                        </div>
                        <p class="text-sm text-gray-800 dark:text-gray-200">{{ \Illuminate\Support\Str::limit($report->post->content, 300) }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">Post sudah dihapus sebelumnya.</p>
                    @endif
                </div>

                @if ($report->status === 'pending' && $report->post)
                    <div class="mt-4 flex justify-end space-x-2">
                        <form method="POST" action="{{ route('admin.post-reports.dismiss', $report) }}">
                            @csrf @method('PATCH')
                            <button type="submit" class="px-3 py-1.5 text-sm border rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                Tolak Laporan
                            </button>
                        </form>
                        <form method="POST" action="{{ route('admin.post-reports.resolve', $report) }}"
                            onsubmit="return confirm('Hapus post ini secara permanen?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Hapus Post
                            </button>
                        </form>
                    </div>
                @elseif ($report->reviewedBy)
                    <p class="text-xs text-gray-400 mt-3">Ditinjau oleh {{ $report->reviewedBy->name }} &middot; {{ $report->reviewed_at?->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        @empty
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-8 text-center text-gray-500">
                Tidak ada laporan.
            </div>
        @endforelse
    </div>

    {{ $reports->links() }}
</div>
@endsection
