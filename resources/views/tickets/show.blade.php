@extends('layouts.app')

@section('title', 'Tiket #' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT))
@section('page-title', $ticket->title)
@section('page-subtitle', 'Tiket #' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT))

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <a href="{{ route('tickets.index') }}" class="text-sm text-gray-500 hover:text-primary transition">&larr; Kembali ke daftar tiket</a>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
        <div class="flex items-center justify-between flex-wrap gap-2 mb-3">
            <div class="flex items-center gap-2">
                <x-ticket-status-badge :status="$ticket->status" />
                <x-ticket-priority-badge :priority="$ticket->priority" />
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->category->name ?? 'Umum' }}</span>
            </div>
            <span class="text-xs text-gray-400">{{ $ticket->created_at->format('d M Y H:i') }}</span>
        </div>
        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $ticket->description }}</p>
    </div>

    <div class="space-y-4">
        @foreach ($ticket->replies as $reply)
            <div class="flex gap-3 {{ $reply->is_staff_reply ? '' : 'flex-row-reverse' }}">
                <img src="{{ $reply->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                <div class="max-w-[80%] {{ $reply->is_staff_reply ? 'bg-white dark:bg-gray-800' : 'bg-primary/10' }} rounded-xl p-4">
                    <p class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">
                        {{ $reply->user->name }} @if ($reply->is_staff_reply) <span class="text-primary">(Support)</span> @endif
                    </p>
                    <p class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $reply->message }}</p>
                    <p class="text-xs text-gray-400 mt-1">{{ $reply->created_at->diffForHumans() }}</p>
                </div>
            </div>
        @endforeach
    </div>

    @if ($ticket->status === 'selesai')
        <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-4 text-sm text-green-800 dark:text-green-400 text-center">
            Tiket ini sudah ditutup. Mengirim balasan akan membuka kembali tiket ini.
        </div>
    @endif

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
        <form method="POST" action="{{ route('tickets.reply', $ticket) }}">
            @csrf
            <textarea name="message" rows="3" required placeholder="Tulis balasan Anda..."
                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('message') }}</textarea>
            @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            <div class="flex justify-end mt-3">
                <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition text-sm">Kirim Balasan</button>
            </div>
        </form>
    </div>
</div>
@endsection
