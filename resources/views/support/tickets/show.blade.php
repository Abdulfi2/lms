@extends('layouts.app')

@section('title', 'Tiket #' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT))
@section('page-title', $ticket->title)
@section('page-subtitle', 'Tiket #TK-' . str_pad($ticket->id, 4, '0', STR_PAD_LEFT) . ' dari ' . $ticket->user->name)

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <!-- Kolom Utama -->
    <div class="lg:col-span-2 space-y-6">
        <a href="{{ route('support.tickets.index', ['tab' => 'pending']) }}" class="text-sm text-gray-500 hover:text-primary transition">&larr; Kembali ke daftar</a>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <div class="flex items-center gap-2 mb-3">
                <x-ticket-status-badge :status="$ticket->status" />
                <x-ticket-priority-badge :priority="$ticket->priority" />
                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->category->name ?? 'Umum' }}</span>
            </div>
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $ticket->description }}</p>
        </div>

        <div class="space-y-4">
            @foreach ($ticket->replies as $reply)
                <div class="flex gap-3 {{ $reply->is_staff_reply ? 'flex-row-reverse' : '' }}">
                    <img src="{{ $reply->user->avatar_url }}" class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                    <div class="max-w-[80%] {{ $reply->is_staff_reply ? 'bg-primary/10' : 'bg-white dark:bg-gray-800' }} rounded-xl p-4">
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
            <p class="text-xs text-gray-400 text-center">Tiket ini sudah selesai. Mengirim balasan akan membuka kembali tiket ini.</p>
        @endif
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <form method="POST" action="{{ route('support.tickets.reply', $ticket) }}">
                @csrf
                <textarea name="message" rows="3" required placeholder="Tulis balasan untuk pengguna..."
                    class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">{{ old('message') }}</textarea>
                @error('message')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                <div class="flex justify-end mt-3">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary transition text-sm">Kirim Balasan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5">
            <h3 class="font-semibold text-gray-800 dark:text-white mb-3">Pengguna</h3>
            <div class="flex items-center gap-3">
                <img src="{{ $ticket->user->avatar_url }}" class="w-10 h-10 rounded-full object-cover">
                <div>
                    <p class="text-sm font-medium text-gray-800 dark:text-white">{{ $ticket->user->name }}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ $ticket->user->email }}</p>
                </div>
            </div>
            <a href="{{ route('support.users.show', $ticket->user) }}" class="block mt-3 text-xs text-primary hover:underline">Lihat profil lengkap &rarr;</a>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 space-y-4">
            <h3 class="font-semibold text-gray-800 dark:text-white">Status Tiket</h3>
            <form method="POST" action="{{ route('support.tickets.status', $ticket) }}">
                @csrf
                @method('PATCH')
                <select name="status" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                    <option value="baru" {{ $ticket->status == 'baru' ? 'selected' : '' }}>Baru</option>
                    <option value="diproses" {{ $ticket->status == 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="menunggu_user" {{ $ticket->status == 'menunggu_user' ? 'selected' : '' }}>Menunggu User</option>
                    <option value="selesai" {{ $ticket->status == 'selesai' ? 'selected' : '' }}>Selesai</option>
                </select>
            </form>

            <h3 class="font-semibold text-gray-800 dark:text-white">Prioritas</h3>
            <form method="POST" action="{{ route('support.tickets.priority', $ticket) }}">
                @csrf
                @method('PATCH')
                <select name="priority" onchange="this.form.submit()" class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:ring-primary focus:border-primary">
                    <option value="rendah" {{ $ticket->priority == 'rendah' ? 'selected' : '' }}>Rendah</option>
                    <option value="sedang" {{ $ticket->priority == 'sedang' ? 'selected' : '' }}>Sedang</option>
                    <option value="tinggi" {{ $ticket->priority == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                </select>
            </form>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 text-sm text-gray-500 dark:text-gray-400 space-y-1">
            <p>Dibuat: {{ $ticket->created_at->format('d M Y H:i') }}</p>
            @if ($ticket->closed_at)
                <p>Ditutup: {{ $ticket->closed_at->format('d M Y H:i') }}</p>
            @endif
        </div>
    </div>
</div>
@endsection
