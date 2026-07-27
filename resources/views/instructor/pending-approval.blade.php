@extends('layouts.client')

@section('content')
    <div class="max-w-md mx-auto bg-white rounded-2xl shadow-xl p-8 md:p-12 text-center">
        @if ($status === 'rejected')
            <div class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Pengajuan Instruktur Ditolak</h2>
            <p class="text-gray-600 mb-4">
                Mohon maaf, pengajuan akun instruktur Anda tidak disetujui oleh admin.
            </p>
            <p class="text-gray-500 text-sm mb-6">
                Jika Anda merasa ini keliru, silakan hubungi kami di
                <strong class="text-primary">support@lms.com</strong> untuk informasi lebih lanjut.
            </p>
        @else
            <div class="mx-auto w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-10 h-10 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 mb-4">Menunggu Persetujuan Admin</h2>
            <p class="text-gray-600 mb-4">
                Akun instruktur Anda sedang ditinjau oleh admin. Anda akan bisa mengelola kursus setelah
                pengajuan disetujui.
            </p>
            <p class="text-gray-500 text-sm mb-6">
                Proses ini biasanya memakan waktu 1-2 hari kerja. Ada pertanyaan? Hubungi
                <strong class="text-primary">support@lms.com</strong>.
            </p>
        @endif

        @if (session('error'))
            <div class="mb-4 p-3 bg-yellow-100 text-yellow-700 rounded-lg text-sm">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-3">
            <a href="{{ route('dashboard') }}"
                class="block w-full py-3 px-4 border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold rounded-lg transition duration-200 text-center">
                Muat Ulang Status
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                    class="w-full py-3 px-4 text-gray-500 hover:text-gray-700 font-semibold rounded-lg transition duration-200 text-center">
                    Keluar
                </button>
            </form>
        </div>
    </div>
@endsection
