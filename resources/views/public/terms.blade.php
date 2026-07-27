@extends('layouts.client')

@section('title', 'Syarat & Ketentuan')
@section('page-title', 'Syarat & Ketentuan')
@section('page-subtitle', 'Terakhir diperbarui: ' . now()->translatedFormat('d F Y'))

@section('content')
<div class="max-w-3xl mx-auto bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-5 text-sm text-gray-700 dark:text-gray-300">
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">1. Penerimaan Ketentuan</h3>
        <p>Dengan mendaftar dan menggunakan {{ config('app.name', 'LMS') }}, Anda menyetujui untuk terikat pada syarat dan ketentuan ini.</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">2. Akun Pengguna</h3>
        <p>Anda bertanggung jawab menjaga kerahasiaan kredensial akun Anda. Segala aktivitas yang terjadi melalui akun Anda menjadi tanggung jawab Anda sepenuhnya.</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">3. Konten Kursus</h3>
        <p>Seluruh materi kursus dilindungi hak cipta. Anda tidak diperkenankan menyalin, mendistribusikan, atau menjual ulang materi kursus tanpa izin tertulis dari instruktur atau pengelola platform.</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">4. Pembayaran & Pengembalian Dana</h3>
        <p>Pembayaran kursus berbayar diproses sesuai metode yang tersedia pada platform. Kebijakan pengembalian dana (refund) mengikuti ketentuan yang berlaku dan dievaluasi oleh admin secara kasus per kasus.</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">5. Perilaku Pengguna</h3>
        <p>Pengguna dilarang mengunggah konten yang melanggar hukum, melecehkan pengguna lain, atau berupa spam. Pelanggaran dapat mengakibatkan penangguhan atau penghapusan akun.</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">6. Perubahan Layanan</h3>
        <p>Kami berhak mengubah, menangguhkan, atau menghentikan sebagian maupun seluruh layanan sewaktu-waktu dengan atau tanpa pemberitahuan sebelumnya.</p>
    </section>
    <section>
        <h3 class="font-semibold text-gray-900 dark:text-white mb-2">7. Kontak</h3>
        <p>Pertanyaan mengenai syarat dan ketentuan ini dapat disampaikan melalui halaman <a href="{{ route('contact.create') }}" class="text-primary hover:underline">Kontak</a>.</p>
    </section>
</div>
@endsection
