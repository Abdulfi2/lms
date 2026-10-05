@php
    $dashboardNotifications = auth()->user()->appNotifications()->unread()->latest('sent_at')->limit(3)->get();
    $dashboardUnreadCount = auth()->user()->appNotifications()->unread()->count();
@endphp

@if ($dashboardUnreadCount > 0)
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl p-4 md:p-5">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center flex-shrink-0">
                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                    </path>
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center justify-between gap-2">
                    <p class="font-semibold text-gray-800 dark:text-white">
                        Anda punya {{ $dashboardUnreadCount }} notifikasi baru
                    </p>
                    <a href="{{ route('notifications.index') }}" class="text-xs text-primary hover:underline flex-shrink-0">Lihat semua</a>
                </div>
                <div class="mt-2 space-y-2">
                    @foreach ($dashboardNotifications as $notification)
                        <a href="{{ route('notifications.read', $notification) }}"
                            class="block bg-white dark:bg-gray-800 rounded-lg px-3 py-2 hover:shadow-sm transition">
                            <p class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $notification->title }}</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($notification->message, 100) }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $notification->sent_at->diffForHumans() }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endif
