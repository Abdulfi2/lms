@extends('layouts.client')

@section('content')
    <section class="relative bg-gradient-to-br from-primary to-secondary overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width="60" height="60"
                viewBox="0 0 60 60" xmlns="http://www.w3.org/2000/svg"%3E%3Cg fill="none" fill-rule="evenodd"%3E%3Cg
                fill="%23ffffff" fill-opacity="0.4"%3E%3Cpath
                d="M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z"
                /%3E%3C/g%3E%3C/g%3E%3C/svg%3E'); background-repeat: repeat;"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 md:py-28">
            <div class="text-center">
                <h1 class="text-4xl md:text-6xl font-extrabold text-white mb-6 leading-tight">
                    {{ \App\Models\Setting::get('hero_title', 'Belajar Tanpa Batas') }}
                    <span class="block text-yellow-300">{{ \App\Models\Setting::get('hero_subtitle', 'Tingkatkan Skillmu Sekarang!') }}</span>
                </h1>
                <p class="text-xl text-white/90 max-w-2xl mx-auto mb-8">
                    {{ \App\Models\Setting::get('hero_description', 'Platform pembelajaran online terbaik dengan ribuan kursus berkualitas dari instruktur berpengalaman. Mulai perjalanan belajarmu hari ini!') }}
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('register', ['role' => 'student']) }}"
                        class="px-8 py-3 bg-white text-primary rounded-lg font-semibold hover:bg-gray-100 transition transform hover:scale-105 shadow-lg">
                        🎓 Daftar sebagai Student
                    </a>
                    <a href="{{ route('register', ['role' => 'instructor']) }}"
                        class="px-8 py-3 bg-transparent border-2 border-white text-white rounded-lg font-semibold hover:bg-white/10 transition transform hover:scale-105">
                        👨‍🏫 Jadi Instruktur
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistik Section -->
    <section class="py-12 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                <div>
                    <div class="text-4xl font-bold text-primary">{{ \App\Models\Setting::get('stat_students', '50K+') }}</div>
                    <div class="text-gray-600 dark:text-gray-400 mt-1">Siswa Aktif</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-primary">{{ \App\Models\Setting::get('stat_courses', '500+') }}</div>
                    <div class="text-gray-600 dark:text-gray-400 mt-1">Kursus Premium</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-primary">{{ \App\Models\Setting::get('stat_instructors', '200+') }}</div>
                    <div class="text-gray-600 dark:text-gray-400 mt-1">Instruktur Ahli</div>
                </div>
                <div>
                    <div class="text-4xl font-bold text-primary">{{ \App\Models\Setting::get('stat_certificates', '100K+') }}</div>
                    <div class="text-gray-600 dark:text-gray-400 mt-1">Sertifikat Diterbitkan</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Fitur Unggulan -->
    <section class="py-16 bg-gray-50 dark:bg-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Kenapa Harus Belajar
                    di Sini?</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Pengalaman belajar terbaik
                    dengan fitur-fitur modern yang memudahkan proses belajarmu</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Kursus Berkualitas</h3>
                    <p class="text-gray-600 dark:text-gray-400">Materi yang disusun oleh para ahli di bidangnya
                        dengan kurikulum terkini.</p>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Sertifikat Resmi</h3>
                    <p class="text-gray-600 dark:text-gray-400">Dapatkan sertifikat yang diakui industri setelah
                        menyelesaikan kursus.</p>
                </div>

                <div class="bg-white dark:bg-gray-900 rounded-xl shadow-lg p-6 hover:shadow-xl transition">
                    <div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-4">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Belajar Fleksibel</h3>
                    <p class="text-gray-600 dark:text-gray-400">Akses materi kapan saja, di mana saja, sesuai dengan
                        jadwalmu.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Kursus Populer -->
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Kursus Populer</h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Ribuan siswa telah memilih
                    kursus ini untuk mengembangkan karir mereka</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($popularCourses as $course)
                    <div
                        class="bg-gray-50 dark:bg-gray-800 rounded-xl overflow-hidden shadow-lg hover:shadow-xl transition">
                        <img src="{{ $course->thumbnail ? Storage::url($course->thumbnail) : 'https://placehold.co/400x200/769826/white?text=Course' }}"
                            class="w-full h-48 object-cover">
                        <div class="p-5">
                            <div class="flex items-center justify-between mb-2">
                                <span
                                    class="text-xs bg-primary/10 text-primary px-2 py-1 rounded-full">{{ ucfirst($course->level) }}</span>
                                <div class="flex text-yellow-400 text-sm">
                                    @for ($i = 1; $i <= 5; $i++)
                                        {{ $i <= round($course->average_rating) ? '★' : '☆' }}
                                    @endfor
                                    <span class="text-gray-500 ml-1">({{ $course->rating_count }})</span>
                                </div>
                            </div>
                            <h3 class="font-bold text-lg text-gray-900 dark:text-white mb-2 line-clamp-1">
                                {{ $course->title }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 line-clamp-2">
                                {{ $course->short_description }}</p>
                            <div class="flex justify-between items-center">
                                <div>
                                    @if ($course->price > 0)
                                        <span class="text-lg font-bold text-primary">Rp
                                            {{ number_format($course->price, 0, ',', '.') }}</span>
                                    @else
                                        <span class="text-lg font-bold text-green-600">Gratis</span>
                                    @endif
                                </div>
                                <a href="{{ route('courses.show', $course->slug) }}"
                                    class="text-primary hover:underline text-sm">Detail →</a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <p class="text-gray-500">Kursus sedang dipersiapkan. Silakan cek kembali nanti.</p>
                    </div>
                @endforelse
            </div>

            <div class="text-center mt-10">
                <a href="{{ route('courses.index') }}"
                    class="inline-block px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-secondary transition">
                    Lihat Semua Kursus
                </a>
            </div>
        </div>
    </section>

    <!-- Untuk Instruktur -->
    <section class="py-16 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-12">
                <div class="flex-1 text-center md:text-left">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Ingin Menjadi
                        Instruktur?</h2>
                    <p class="text-lg text-gray-600 dark:text-gray-300 mb-6">Bagikan pengetahuan dan keahlianmu
                        kepada ribuan siswa. Kami akan membantu Anda membangun kursus berkualitas dan mendapatkan
                        penghasilan tambahan.</p>
                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Dukungan penuh dari tim kami</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Bagi hasil yang menguntungkan</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7">
                                </path>
                            </svg>
                            <span class="text-gray-700 dark:text-gray-300">Jangkau siswa dari seluruh
                                Indonesia</span>
                        </div>
                    </div>
                    <a href="{{ route('register', ['role' => 'instructor']) }}"
                        class="inline-block px-6 py-3 bg-primary text-white rounded-lg font-semibold hover:bg-secondary transition">
                        Daftar Jadi Instruktur
                    </a>
                </div>
                <div class="flex-1">
                    <img src="https://placehold.co/500x400/769826/white?text=Instructor" alt="Instructor"
                        class="rounded-lg shadow-xl">
                </div>
            </div>
        </div>
    </section>

    <!-- Testimoni -->
    <section class="py-16 bg-white dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white mb-4">Apa Kata Siswa Kami?
                </h2>
                <p class="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">Ribuan siswa telah merasakan
                    manfaat belajar di platform kami</p>
            </div>

            <div class="grid md:grid-cols-3 gap-6">
                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 shadow">
                    <div class="flex text-yellow-400 mb-3">★★★★★</div>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">"Platform ini sangat membantu saya dalam
                        mengembangkan skill programming. Materinya lengkap dan mudah dipahami!"</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary font-bold">
                            A</div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Ahmad Rizki</h4>
                            <p class="text-xs text-gray-500">Fullstack Developer</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 shadow">
                    <div class="flex text-yellow-400 mb-3">★★★★★</div>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">"Instruktur profesional dan responsif. Saya
                        sekarang sudah bisa membuat website sendiri setelah mengikuti kursus di sini."</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary font-bold">
                            S</div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Siti Aminah</h4>
                            <p class="text-xs text-gray-500">UI/UX Designer</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gray-50 dark:bg-gray-800 rounded-xl p-6 shadow">
                    <div class="flex text-yellow-400 mb-3">★★★★★</div>
                    <p class="text-gray-700 dark:text-gray-300 mb-4">"Sertifikatnya diakui industri. Setelah
                        menyelesaikan kursus, saya langsung mendapat tawaran pekerjaan!"</p>
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary font-bold">
                            B</div>
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white">Budi Santoso</h4>
                            <p class="text-xs text-gray-500">Data Analyst</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-primary">
        <div class="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Siap Memulai Perjalanan Belajarmu?</h2>
            <p class="text-xl text-white/90 mb-8">Bergabunglah dengan ribuan siswa lainnya dan raih kesuksesan
                bersama kami.</p>
            <a href="{{ route('register', ['role' => 'student']) }}"
                class="inline-block px-8 py-3 bg-white text-primary rounded-lg font-semibold hover:bg-gray-100 transition transform hover:scale-105 shadow-lg">
                Daftar Gratis Sekarang
            </a>
        </div>
    </section>
@endsection
