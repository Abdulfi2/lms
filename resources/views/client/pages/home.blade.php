<x-client-layout>
    <x-slot name="hero">
        <div class="grid md:grid-cols-2 gap-12 items-center">
            <div>
                <h1 class="text-5xl md:text-6xl font-bold mb-6 leading-tight">
                    Belajar Lebih Mudah,<br>
                    <span class="text-yellow-300">Terarah, dan Fleksibel</span>
                </h1>
                <p class="text-xl mb-8 text-blue-100 leading-relaxed">
                    Platform pembelajaran modern untuk mengakses materi, mengikuti kelas online,
                    dan memantau progres belajar Anda dengan mudah.
                </p>
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="#"
                        class="bg-white text-primary px-8 py-4 rounded-xl font-semibold text-lg shadow-2xl hover:shadow-xl transform hover:-translate-y-1 transition-all duration-300">Mulai
                        Belajar</a>
                    <a href="#"
                        class="border-2 border-white text-white px-8 py-4 rounded-xl font-semibold text-lg hover:bg-white hover:text-primary transition-all duration-300">Lihat
                        Kursus</a>
                </div>
            </div>
            <div class="relative">
                <!-- Placeholder untuk image -->
                <div
                    class="bg-gradient-to-br from-purple-400 to-pink-500 rounded-3xl p-12 shadow-2xl transform rotate-6 hover:rotate-0 transition-transform duration-500">
                    <div class="text-6xl animate-bounce">📚</div>
                    <p class="text-center text-white font-bold mt-4">Dashboard LMS</p>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Fitur Utama -->
    <section class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Fitur Unggulan Kami</h2>
                <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                    Semua fitur dirancang untuk memudahkan proses belajar mengajar Anda.
                </p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div
                    class="group text-center p-8 rounded-2xl hover:shadow-2xl hover:-translate-y-4 transition-all duration-300 border border-gray-100">
                    <div
                        class="w-20 h-20 bg-gradient-to-r from-blue-500 to-blue-600 rounded-2xl mx-auto mb-6 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                        📱
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Belajar Kapan Saja</h3>
                    <p class="text-gray-600">Akses materi pelajaran dari mana saja, kapan saja melalui perangkat Anda.
                    </p>
                </div>
                <div
                    class="group text-center p-8 rounded-2xl hover:shadow-2xl hover:-translate-y-4 transition-all duration-300 border border-gray-100">
                    <div
                        class="w-20 h-20 bg-gradient-to-r from-green-500 to-green-600 rounded-2xl mx-auto mb-6 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                        📊
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Progress Otomatis</h3>
                    <p class="text-gray-600">Pantau kemajuan belajar Anda secara real-time dengan dashboard interaktif.
                    </p>
                </div>
                <div
                    class="group text-center p-8 rounded-2xl hover:shadow-2xl hover:-translate-y-4 transition-all duration-300 border border-gray-100">
                    <div
                        class="w-20 h-20 bg-gradient-to-r from-purple-500 to-purple-600 rounded-2xl mx-auto mb-6 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                        💬
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Forum Diskusi</h3>
                    <p class="text-gray-600">Diskusikan materi dengan teman dan pengajar melalui forum terintegrasi.</p>
                </div>
                <div
                    class="group text-center p-8 rounded-2xl hover:shadow-2xl hover:-translate-y-4 transition-all duration-300 border border-gray-100">
                    <div
                        class="w-20 h-20 bg-gradient-to-r from-orange-500 to-orange-600 rounded-2xl mx-auto mb-6 flex items-center justify-center text-3xl group-hover:scale-110 transition-transform">
                        🏆
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-4">Sertifikat</h3>
                    <p class="text-gray-600">Dapatkan sertifikat resmi setelah menyelesaikan setiap kursus.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Kategori Kursus -->
    <section class="py-24 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-20">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Pilih Kategori Kursus</h2>
                <p class="text-xl text-gray-600">Temukan kursus yang sesuai dengan minat dan kebutuhan belajar Anda.</p>
            </div>
            <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="#"
                    class="group bg-white rounded-2xl p-8 text-center hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-gray-100">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-xl mx-auto mb-6 flex items-center justify-center text-2xl text-white">
                        💻
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Teknologi</h3>
                    <p class="text-gray-600">Programming, AI, Data Science</p>
                </a>
                <a href="#"
                    class="group bg-white rounded-2xl p-8 text-center hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-gray-100">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-emerald-500 to-teal-600 rounded-xl mx-auto mb-6 flex items-center justify-center text-2xl text-white">
                        🗣️
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Bahasa</h3>
                    <p class="text-gray-600">Inggris, Arab, Mandarin</p>
                </a>
                <a href="#"
                    class="group bg-white rounded-2xl p-8 text-center hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-gray-100">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-amber-500 to-orange-600 rounded-xl mx-auto mb-6 flex items-center justify-center text-2xl text-white">
                        💼
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Bisnis</h3>
                    <p class="text-gray-600">Marketing, Manajemen</p>
                </a>
                <a href="#"
                    class="group bg-white rounded-2xl p-8 text-center hover:shadow-xl hover:-translate-y-2 transition-all duration-300 border border-gray-100">
                    <div
                        class="w-16 h-16 bg-gradient-to-r from-rose-500 to-pink-600 rounded-xl mx-auto mb-6 flex items-center justify-center text-2xl text-white">
                        🕌
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Agama</h3>
                    <p class="text-gray-600">Studi Islam, Fiqih</p>
                </a>
            </div>
        </div>
    </section>

    <!-- Statistik -->
    <section class="py-24 bg-gradient-to-r from-primary to-secondary text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid md:grid-cols-3 gap-12 text-center">
                <div>
                    <div class="text-5xl font-bold mb-4">10K+</div>
                    <div class="text-xl opacity-90">Peserta Aktif</div>
                </div>
                <div>
                    <div class="text-5xl font-bold mb-4">300+</div>
                    <div class="text-xl opacity-90">Materi Kursus</div>
                </div>
                <div>
                    <div class="text-5xl font-bold mb-4">95%</div>
                    <div class="text-xl opacity-90">Kepuasan Pengguna</div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Penutup -->
    <section class="py-24 bg-white">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl font-bold text-gray-900 mb-6">Siap Mulai Belajar Hari Ini?</h2>
            <p class="text-xl text-gray-600 mb-12 max-w-2xl mx-auto">
                Bergabunglah dengan ribuan siswa yang sudah sukses meningkatkan skill mereka bersama kami.
            </p>
            <a href="#"
                class="bg-primary text-white px-12 py-6 rounded-2xl font-bold text-xl shadow-2xl hover:shadow-3xl hover:-translate-y-1 transition-all duration-300">Daftar
                Sekarang Gratis!</a>
        </div>
    </section>
</x-client-layout>
