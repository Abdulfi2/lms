@extends('layouts.app')

@section('title', 'Laporan Pendapatan')
@section('page-title', 'Pendapatan Saya')
@section('page-subtitle', 'Kelola penghasilan Anda dari kursus yang terjual.')

@section('content')
<div class="space-y-6">
    <!-- Earnings Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-gradient-to-br from-blue-600 to-blue-700 p-6 rounded-2xl shadow-lg text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Transaksi Selesai</p>
                    <h3 class="text-3xl font-bold mt-1">Rp {{ number_format($totalEarnings, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 bg-white/20 rounded-xl">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-blue-100">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/></svg>
                <span>Nilai transaksi kotor, belum dipotong komisi platform</span>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Dalam Proses</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($pendingEarnings, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl text-yellow-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-4">Transaksi tertunda atau sedang diproses</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Sudah Dicairkan</p>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1">Rp {{ number_format($totalPaidOut, 0, ',', '.') }}</h3>
                </div>
                <div class="p-3 bg-green-50 dark:bg-green-900/20 rounded-xl text-green-600">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                </div>
            </div>
            @if ($lastPayout)
                <p class="text-xs text-gray-400 mt-4">Terakhir dicairkan {{ $lastPayout->paid_at->format('d M Y') }}</p>
            @else
                <p class="text-xs text-gray-400 mt-4">Belum pernah dicairkan oleh admin</p>
            @endif
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <div class="flex items-center justify-between">
            <p class="text-sm text-gray-600 dark:text-gray-300">Saldo belum dicairkan</p>
            <p class="text-lg font-bold {{ $outstandingBalance > 0 ? 'text-yellow-600' : 'text-gray-900 dark:text-white' }}">Rp {{ number_format($outstandingBalance, 0, ',', '.') }}</p>
        </div>

        @if ($hasPendingRequest)
            <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/20 rounded-lg text-sm text-yellow-700 dark:text-yellow-400">
                Anda memiliki permintaan payout sebesar Rp {{ number_format($totalRequestPending, 0, ',', '.') }} yang sedang menunggu review admin.
            </div>
        @elseif ($outstandingBalance > 0)
            <form method="POST" action="{{ route('instructor.payout-requests.store') }}" class="mt-3 flex flex-wrap gap-2 items-start">
                @csrf
                <input type="number" name="amount" step="1" max="{{ $outstandingBalance }}" value="{{ old('amount', $outstandingBalance) }}" required
                    class="w-40 rounded-lg border-gray-300 text-sm">
                <input type="text" name="method" placeholder="Metode (mis. Transfer BCA 1234567890)" maxlength="255"
                    class="flex-1 min-w-[200px] rounded-lg border-gray-300 text-sm">
                <button type="submit" class="px-4 py-2 bg-primary text-white text-sm rounded-lg hover:bg-secondary">Ajukan Pencairan</button>
            </form>
        @endif
    </div>

    <!-- Monthly Chart -->
    <div class="bg-white dark:bg-gray-800 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700">
        <h3 class="font-bold text-gray-900 dark:text-white mb-6">Pertumbuhan Pendapatan</h3>
        <div class="h-64 w-full">
            <canvas id="earningsChart"></canvas>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="p-6 border-b dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white">Riwayat Transaksi</h3>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Detail Kursus</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Siswa</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-center">Tanggal</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-center">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($transactions as $transaction)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <td class="px-6 py-4">
                        <div class="text-sm font-bold text-gray-900 dark:text-white">{{ $transaction->enrollment->course->title }}</div>
                        <div class="text-[10px] text-gray-400">Order ID: {{ $transaction->order_id }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex items-center">
                            <div class="w-7 h-7 bg-gray-100 rounded-full flex items-center justify-center text-[10px] font-bold text-gray-500 mr-2">
                                {{ substr($transaction->user->name, 0, 1) }}
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $transaction->user->name }}</div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        <div class="text-sm text-gray-600 dark:text-gray-400">{{ $transaction->created_at->format('d M Y') }}</div>
                        <div class="text-[10px] text-gray-400">{{ $transaction->created_at->format('H:i') }}</div>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($transaction->status == 'completed')
                            <span class="px-2 py-1 text-[10px] font-bold bg-green-100 text-green-700 rounded-full uppercase">Selesai</span>
                        @elseif($transaction->status == 'pending' || $transaction->status == 'processing')
                            <span class="px-2 py-1 text-[10px] font-bold bg-yellow-100 text-yellow-700 rounded-full uppercase">Proses</span>
                        @else
                            <span class="px-2 py-1 text-[10px] font-bold bg-red-100 text-red-700 rounded-full uppercase">{{ $transaction->status }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <span class="text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($transaction->amount, 0, ',', '.') }}</span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        Belum ada transaksi pendapatan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="p-4">
            {{ $transactions->links() }}
        </div>
    </div>

    <!-- Payout History -->
    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-2xl overflow-hidden border border-gray-100 dark:border-gray-700">
        <div class="p-6 border-b dark:border-gray-700">
            <h3 class="font-bold text-gray-900 dark:text-white">Riwayat Payout</h3>
        </div>
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50 dark:bg-gray-700/50">
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Tanggal</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Metode / Referensi</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($payouts as $payout)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ $payout->paid_at?->format('d M Y') ?? $payout->created_at->format('d M Y') }}</td>
                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                        {{ $payout->method ?? '-' }}
                        @if ($payout->reference)
                            <div class="text-[10px] text-gray-400">Ref: {{ $payout->reference }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4">
                        @if ($payout->status === 'paid')
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Cair</span>
                        @elseif ($payout->status === 'rejected')
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800" title="{{ $payout->note }}">Ditolak</span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Menunggu</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right text-sm font-bold text-gray-900 dark:text-white">Rp {{ number_format($payout->amount, 0, ',', '.') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-gray-500 dark:text-gray-400">
                        Belum ada payout yang dicairkan.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@push('scripts')
<script>
    const ctx = document.getElementById('earningsChart').getContext('2d');
    const chartData = @json($monthlyEarnings);
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: chartData.map(item => item.month),
            datasets: [{
                label: 'Pendapatan Bulanan',
                data: chartData.map(item => item.total),
                backgroundColor: '#3B82F6',
                borderRadius: 8,
                barThickness: 30,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
</script>
@endpush
@endsection
