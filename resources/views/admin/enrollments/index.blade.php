@extends('layouts.app')

@section('title', 'Konfirmasi Pembayaran')
@section('page-title', 'Konfirmasi Pembayaran')
@section('page-subtitle', 'Tinjau dan konfirmasi pembayaran kursus berbayar secara manual')

@section('content')
<div class="space-y-6">
    <div class="flex space-x-2">
        <a href="{{ route('admin.enrollments.index', ['status' => 'pending']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ request('status', 'pending') === 'pending' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Menunggu
        </a>
        <a href="{{ route('admin.enrollments.index', ['status' => 'paid']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ request('status') === 'paid' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Lunas
        </a>
        <a href="{{ route('admin.enrollments.index', ['status' => 'failed']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ request('status') === 'failed' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Gagal
        </a>
        <a href="{{ route('admin.enrollments.index', ['status' => 'refunded']) }}"
            class="px-3 py-1.5 rounded-lg text-sm {{ request('status') === 'refunded' ? 'bg-primary text-white' : 'bg-white dark:bg-gray-800 border dark:border-gray-700' }}">
            Refund
        </a>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Siswa</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kursus</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tagihan</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tanggal Daftar</th>
                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse ($enrollments as $enrollment)
                    <tr>
                        <td class="px-4 py-3 text-sm text-gray-900 dark:text-white">{{ $enrollment->user->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">{{ $enrollment->course->title ?? '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-300">Rp {{ number_format($enrollment->amount_paid, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-sm text-gray-500">{{ optional($enrollment->enrolled_at)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded-full
                                @if ($enrollment->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif ($enrollment->payment_status === 'failed') bg-red-100 text-red-800
                                @elseif ($enrollment->payment_status === 'refunded') bg-gray-200 text-gray-700
                                @else bg-yellow-100 text-yellow-800 @endif">
                                {{ ucfirst($enrollment->payment_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right">
                            @if ($enrollment->payment_status === 'pending')
                                <form method="POST" action="{{ route('admin.enrollments.mark-paid', $enrollment) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="px-3 py-1.5 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700">
                                        Tandai Lunas
                                    </button>
                                </form>
                            @elseif ($enrollment->payment_status === 'paid')
                                <button type="button" onclick="document.getElementById('refund-form-{{ $enrollment->id }}').classList.toggle('hidden')"
                                    class="px-3 py-1.5 border border-red-300 text-red-600 text-sm rounded-lg hover:bg-red-50">
                                    Refund
                                </button>
                            @elseif ($enrollment->payment_status === 'refunded')
                                <span class="text-xs text-gray-400" title="{{ $enrollment->refund_reason }}">
                                    Direfund {{ optional($enrollment->refunded_at)->format('d/m/Y') }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400" title="Status {{ $enrollment->payment_status }} perlu ditinjau manual sebelum ditandai lunas kembali.">
                                    Perlu Peninjauan
                                </span>
                            @endif
                        </td>
                    </tr>
                    @if ($enrollment->payment_status === 'paid')
                        <tr id="refund-form-{{ $enrollment->id }}" class="hidden bg-red-50/50 dark:bg-red-900/10">
                            <td colspan="6" class="px-4 py-3">
                                <form method="POST" action="{{ route('admin.enrollments.refund', $enrollment) }}"
                                    onsubmit="return confirm('Refund akan mencabut akses siswa ke kursus ini. Lanjutkan?');"
                                    class="flex items-center gap-2">
                                    @csrf
                                    <input type="text" name="refund_reason" required maxlength="255" placeholder="Alasan refund..."
                                        class="flex-1 rounded-lg border-gray-300 text-sm">
                                    <button type="submit" class="px-3 py-1.5 bg-red-600 text-white text-sm rounded-lg hover:bg-red-700 shrink-0">
                                        Konfirmasi Refund
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">Tidak ada data.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{ $enrollments->links() }}
</div>
@endsection
