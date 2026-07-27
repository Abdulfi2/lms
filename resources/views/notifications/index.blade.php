@extends('layouts.app')

@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')

@section('content')
<div class="max-w-2xl mx-auto space-y-4">
    @if ($notifications->where('read_at', null)->count() > 0)
        <form method="POST" action="{{ route('notifications.mark-all-read') }}" class="text-right">
            @csrf
            <button type="submit" class="text-sm text-primary hover:underline">Tandai semua terbaca</button>
        </form>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm divide-y divide-gray-100 dark:divide-gray-700 overflow-hidden">
        @forelse ($notifications as $notification)
            <a href="{{ route('notifications.read', $notification) }}"
                class="block px-5 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition {{ !$notification->read_at ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-white">{{ $notification->title }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $notification->message }}</p>
                        <p class="text-xs text-gray-400 mt-2">{{ $notification->sent_at->diffForHumans() }}</p>
                    </div>
                    @if (!$notification->read_at)
                        <span class="w-2 h-2 mt-1 rounded-full bg-blue-500 shrink-0"></span>
                    @endif
                </div>
            </a>
        @empty
            <div class="px-5 py-12 text-center text-gray-500">Belum ada notifikasi.</div>
        @endforelse
    </div>

    {{ $notifications->links() }}
</div>
@endsection
