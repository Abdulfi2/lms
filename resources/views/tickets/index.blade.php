@extends('layouts.app')

@section('title', 'Tiket Bantuan Saya')
@section('page-title', 'Tiket Bantuan Saya')
@section('page-subtitle', 'Riwayat permintaan bantuan yang pernah Anda kirim')

@section('content')
<div class="space-y-6">
    <div class="flex justify-end">
        <a href="{{ route('tickets.create') }}" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition">
            + Buat Tiket Baru
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="divide-y dark:divide-gray-700">
            @forelse ($tickets as $ticket)
                <a href="{{ route('tickets.show', $ticket) }}" class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition gap-3">
                    <div class="min-w-0">
                        <p class="font-medium text-gray-800 dark:text-white truncate">{{ $ticket->title }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $ticket->category->name ?? 'Umum' }} &middot; {{ $ticket->created_at->diffForHumans() }} &middot; {{ $ticket->replies_count }} balasan
                        </p>
                    </div>
                    <x-ticket-status-badge :status="$ticket->status" />
                </a>
            @empty
                <div class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                    Anda belum pernah membuat tiket bantuan.
                    <a href="{{ route('tickets.create') }}" class="text-primary hover:underline">Buat sekarang</a>.
                </div>
            @endforelse
        </div>
        @if ($tickets->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
