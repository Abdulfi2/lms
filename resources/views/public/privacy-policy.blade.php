@extends('layouts.client')

@section('title', 'Kebijakan Privasi')
@section('page-title', 'Kebijakan Privasi')
@section('page-subtitle', 'Terakhir diperbarui: ' . now()->translatedFormat('d F Y'))

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-5 text-sm text-gray-700 dark:text-gray-300">
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">1. Data yang Kami Kumpulkan</h3>
        <p>Kami mengumpulkan data yang Anda berikan saat mendaftar (nama, email, foto profil) serta data aktivitas belajar seperti progres kursus, nilai tugas, dan riwayat pendaftaran kursus.</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">2. Penggunaan Data</h3>
        <p>Data Anda digunakan untuk menyediakan layanan pembelajaran, memproses pendaftaran kursus dan pembayaran, mengirimkan notifikasi terkait akun/kursus, serta meningkatkan kualitas platform.</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">3. Keamanan Data</h3>
        <p>Kami menerapkan praktik keamanan standar industri untuk melindungi data Anda, termasuk enkripsi kata sandi dan pembatasan akses berbasis peran.</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">4. Berbagi Data</h3>
        <p>Kami tidak menjual data pribadi Anda kepada pihak ketiga. Data dapat dibagikan kepada instruktur kursus yang Anda ikuti sebatas untuk keperluan pengajaran (misalnya nama dan progres belajar).</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">5. Hak Anda</h3>
        <p>Anda dapat mengakses, memperbarui, atau meminta penghapusan data akun Anda melalui halaman Profil, atau dengan menghubungi kami melalui halaman <a href="{{ route('contact.create') }}" class="text-primary hover:underline">Kontak</a>.</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">6. Perubahan Kebijakan</h3>
        <p>Kebijakan privasi ini dapat diperbarui dari waktu ke waktu. Perubahan signifikan akan diinformasikan melalui platform.</p>
    </section>
</div>
@endsection
