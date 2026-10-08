@extends('layouts.app')

@section('title', 'Tiket')
@section('page-title', 'Tiket')
@section('page-subtitle', 'Tangani permintaan bantuan pengguna')

@section('content')
<div class="space-y-6">
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('support.tickets.index', ['tab' => 'pending']) }}"
            class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === 'pending' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-700 dark:text-gray-300' }}">
            Permintaan Bantuan
            @if ($pendingCount > 0)
                <span class="ml-1 {{ $tab === 'pending' ? 'bg-white/20' : 'bg-red-500 text-white' }} text-xs rounded-full px-1.5 py-0.5">{{ $pendingCount }}</span>
            @endif
        </a>
        <a href="{{ route('support.tickets.index', ['tab' => 'all']) }}"
            class="px-4 py-2 rounded-lg text-sm font-medium transition {{ $tab === 'all' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700 text-gray-700 dark:text-gray-300' }}">
            Semua Tiket
        </a>
    </div>

    <form method="GET" class="flex flex-wrap gap-2">
        <input type="hidden" name="tab" value="{{ $tab }}">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul atau nama pengguna..."
            class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
        @if ($tab !== 'pending')
            <select name="status" onchange="this.form.submit()" class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
                <option value="">Semua Status</option>
                <option value="baru" {{ request('status') == 'baru' ? 'selected' : '' }}>Baru</option>
                <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                <option value="menunggu_user" {{ request('status') == 'menunggu_user' ? 'selected' : '' }}>Menunggu User</option>
                <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
            </select>
        @endif
        <select name="priority" onchange="this.form.submit()" class="px-4 py-2 rounded-lg border dark:border-gray-600 dark:bg-gray-700">
            <option value="">Semua Prioritas</option>
            <option value="tinggi" {{ request('priority') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
            <option value="sedang" {{ request('priority') == 'sedang' ? 'selected' : '' }}>Sedang</option>
            <option value="rendah" {{ request('priority') == 'rendah' ? 'selected' : '' }}>Rendah</option>
        </select>
        <button type="submit" class="px-4 py-2 border rounded-lg dark:border-gray-600 text-sm">Cari</button>
    </form>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Pengguna</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Tanggal</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Prioritas</th>
                        <th class="px-6 py-3 text-right text-xs font-medium uppercase text-gray-500 dark:text-gray-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse ($tickets as $ticket)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">#TK-{{ str_pad($ticket->id, 4, '0', STR_PAD_LEFT) }}</td>
                            <td class="px-6 py-4 font-medium text-gray-800 dark:text-white">{{ Str::limit($ticket->title, 30) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                    <img src="{{ $ticket->user->avatar_url }}" class="w-6 h-6 rounded-full object-cover">
                                    {{ $ticket->user->name }}
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $ticket->category->name ?? 'Umum' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500 dark:text-gray-400">{{ $ticket->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4"><x-ticket-status-badge :status="$ticket->status" /></td>
                            <td class="px-6 py-4"><x-ticket-priority-badge :priority="$ticket->priority" /></td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('support.tickets.show', $ticket) }}" class="px-3 py-1.5 bg-primary text-white text-xs rounded-lg hover:bg-secondary transition">
                                    {{ $ticket->status === 'baru' ? 'Tanggapi' : 'Lihat' }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 text-center text-gray-500 dark:text-gray-400 text-sm">
                                @if ($tab === 'pending')
                                    Tidak ada permintaan bantuan yang menunggu. 🎉
                                @else
                                    Tidak ada tiket ditemukan.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($tickets->hasPages())
            <div class="px-6 py-4 border-t dark:border-gray-700">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
