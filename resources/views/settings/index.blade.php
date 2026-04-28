@extends('layouts.app')

@section('title', 'Pengaturan')
@section('page-title', 'Pengaturan')
@section('page-subtitle', 'Atur preferensi akun Anda')

@section('content')
    <div x-data="{ activeTab: 'general' }" class="max-w-5xl mx-auto">
        <!-- Tabs -->
        <div class="border-b dark:border-gray-700 mb-6">
            <nav class="flex flex-wrap gap-4">
                <button @click="activeTab = 'general'" :class="{ 'border-primary text-primary': activeTab === 'general' }"
                    class="py-2 px-4 border-b-2 font-medium text-sm transition">
                    Umum
                </button>
                <button @click="activeTab = 'notifications'"
                    :class="{ 'border-primary text-primary': activeTab === 'notifications' }"
                    class="py-2 px-4 border-b-2 font-medium text-sm transition">
                    Notifikasi
                </button>
                <button @click="activeTab = 'learning'" :class="{ 'border-primary text-primary': activeTab === 'learning' }"
                    class="py-2 px-4 border-b-2 font-medium text-sm transition">
                    Pembelajaran
                </button>
                <button @click="activeTab = 'privacy'" :class="{ 'border-primary text-primary': activeTab === 'privacy' }"
                    class="py-2 px-4 border-b-2 font-medium text-sm transition">
                    Privasi
                </button>
                <button @click="activeTab = 'accessibility'"
                    :class="{ 'border-primary text-primary': activeTab === 'accessibility' }"
                    class="py-2 px-4 border-b-2 font-medium text-sm transition">
                    Aksesibilitas
                </button>
            </nav>
        </div>

        <!-- Tab: General Settings -->
        <div x-show="activeTab === 'general'" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('settings.general') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bahasa</label>
                            <select name="language"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <option value="id" {{ $settings->language == 'id' ? 'selected' : '' }}>Indonesia
                                </option>
                                <option value="en" {{ $settings->language == 'en' ? 'selected' : '' }}>English</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Zona
                                Waktu</label>
                            <select name="timezone"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <option value="Asia/Jakarta" {{ $settings->timezone == 'Asia/Jakarta' ? 'selected' : '' }}>
                                    Asia/Jakarta (WIB)</option>
                                <option value="Asia/Makassar"
                                    {{ $settings->timezone == 'Asia/Makassar' ? 'selected' : '' }}>Asia/Makassar (WITA)
                                </option>
                                <option value="Asia/Jayapura"
                                    {{ $settings->timezone == 'Asia/Jayapura' ? 'selected' : '' }}>Asia/Jayapura (WIT)
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Format
                                Tanggal</label>
                            <select name="date_format"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                                <option value="d/m/Y" {{ $settings->date_format == 'd/m/Y' ? 'selected' : '' }}>DD/MM/YYYY
                                </option>
                                <option value="Y-m-d" {{ $settings->date_format == 'Y-m-d' ? 'selected' : '' }}>YYYY-MM-DD
                                </option>
                                <option value="m/d/Y" {{ $settings->date_format == 'm/d/Y' ? 'selected' : '' }}>MM/DD/YYYY
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="dark_mode" value="1"
                                {{ $settings->dark_mode ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Mode Gelap</span>
                        </label>
                        <label class="flex items-center">
                            <input type="checkbox" name="compact_view" value="1"
                                {{ $settings->compact_view ? 'checked' : '' }} class="rounded border-gray-300">
                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Tampilan Kompak</span>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>

        <!-- Tab: Notification Settings -->
        <div x-show="activeTab === 'notifications'" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('settings.notifications') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Email Notifikasi</p>
                            <p class="text-sm text-gray-500">Terima notifikasi melalui email</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="email_notifications" value="1"
                                {{ $settings->email_notifications ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Push Notifikasi</p>
                            <p class="text-sm text-gray-500">Terima notifikasi di browser</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="push_notifications" value="1"
                                {{ $settings->push_notifications ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Pengingat Tugas</p>
                            <p class="text-sm text-gray-500">Notifikasi ketika ada tugas baru atau deadline mendekat</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="assignment_reminder" value="1"
                                {{ $settings->assignment_reminder ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Pengingat Quiz</p>
                            <p class="text-sm text-gray-500">Notifikasi ketika ada quiz baru</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="quiz_reminder" value="1"
                                {{ $settings->quiz_reminder ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Update Kursus</p>
                            <p class="text-sm text-gray-500">Notifikasi ketika ada update materi kursus</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="course_update_notification" value="1"
                                {{ $settings->course_update_notification ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Balasan Forum</p>
                            <p class="text-sm text-gray-500">Notifikasi ketika ada balasan di thread Anda</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="forum_reply_notification" value="1"
                                {{ $settings->forum_reply_notification ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Sertifikat</p>
                            <p class="text-sm text-gray-500">Notifikasi ketika sertifikat tersedia</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="certificate_notification" value="1"
                                {{ $settings->certificate_notification ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Pengingat Event</p>
                            <p class="text-sm text-gray-500">Notifikasi ketika ada event baru atau mendekati jadwal</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="event_reminder" value="1"
                                {{ $settings->event_reminder ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>

        <!-- Tab: Learning Settings -->
        <div x-show="activeTab === 'learning'" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('settings.learning') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Putar Video Otomatis</p>
                            <p class="text-sm text-gray-500">Video akan otomatis diputar saat halaman lesson dimuat</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="auto_play_video" value="1"
                                {{ $settings->auto_play_video ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Tampilkan Subtitle</p>
                            <p class="text-sm text-gray-500">Tampilkan subtitle pada video (jika tersedia)</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="show_subtitles" value="1"
                                {{ $settings->show_subtitles ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kualitas
                            Video</label>
                        <select name="video_quality"
                            class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700">
                            <option value="auto" {{ $settings->video_quality == 'auto' ? 'selected' : '' }}>Auto
                                (Sesuaikan koneksi)</option>
                            <option value="1080p" {{ $settings->video_quality == '1080p' ? 'selected' : '' }}>1080p (Full
                                HD)</option>
                            <option value="720p" {{ $settings->video_quality == '720p' ? 'selected' : '' }}>720p (HD)
                            </option>
                            <option value="480p" {{ $settings->video_quality == '480p' ? 'selected' : '' }}>480p (SD)
                            </option>
                            <option value="360p" {{ $settings->video_quality == '360p' ? 'selected' : '' }}>360p (Low)
                            </option>
                        </select>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Tandai Selesai Otomatis</p>
                            <p class="text-sm text-gray-500">Lesson otomatis ditandai selesai jika sudah dilihat 90%</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="auto_mark_complete" value="1"
                                {{ $settings->auto_mark_complete ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Target Belajar
                            Harian (menit)</label>
                        <input type="number" name="daily_goal_minutes" value="{{ $settings->daily_goal_minutes }}"
                            min="0" max="480" step="15" class="w-32 rounded-lg border-gray-300">
                        <p class="text-xs text-gray-500 mt-1">Atur target waktu belajar setiap hari (0 = tidak ada target)
                        </p>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>

        <!-- Tab: Privacy Settings -->
        <div x-show="activeTab === 'privacy'" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('settings.privacy') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Profil Publik</p>
                            <p class="text-sm text-gray-500">Profil Anda dapat dilihat oleh pengguna lain</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="profile_public" value="1"
                                {{ $settings->profile_public ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Tampilkan Progress</p>
                            <p class="text-sm text-gray-500">Progress belajar Anda dapat dilihat publik</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="show_progress" value="1"
                                {{ $settings->show_progress ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Tampilkan Sertifikat</p>
                            <p class="text-sm text-gray-500">Sertifikat Anda dapat dilihat dan diverifikasi publik</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="show_certificates" value="1"
                                {{ $settings->show_certificates ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Izinkan Pesan</p>
                            <p class="text-sm text-gray-500">Pengguna lain dapat mengirim pesan pribadi kepada Anda</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="allow_messages" value="1"
                                {{ $settings->allow_messages ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>

        <!-- Tab: Accessibility Settings -->
        <div x-show="activeTab === 'accessibility'" class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6">
            <form action="{{ route('settings.accessibility') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Kontras Tinggi</p>
                            <p class="text-sm text-gray-500">Meningkatkan kontras warna untuk keterbacaan</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="high_contrast" value="1"
                                {{ $settings->high_contrast ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b dark:border-gray-700">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Teks Besar</p>
                            <p class="text-sm text-gray-500">Memperbesar ukuran font untuk keterbacaan</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="large_text" value="1"
                                {{ $settings->large_text ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p class="font-medium text-gray-800 dark:text-white">Screen Reader</p>
                            <p class="text-sm text-gray-500">Optimasi untuk pembaca layar (screen reader)</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="screen_reader" value="1"
                                {{ $settings->screen_reader ? 'checked' : '' }} class="sr-only peer">
                            <div
                                class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-primary/30 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                            </div>
                        </label>
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-secondary">Simpan
                        Perubahan</button>
                </div>
            </form>
        </div>

        <!-- Reset to Default -->
        <div class="mt-6 p-4 bg-red-50 dark:bg-red-900/20 rounded-xl">
            <div class="flex justify-between items-center">
                <div>
                    <h3 class="font-semibold text-red-800 dark:text-red-400">Reset Pengaturan</h3>
                    <p class="text-sm text-red-600 dark:text-red-300">Kembalikan semua pengaturan ke nilai default</p>
                </div>
                <form action="{{ route('settings.reset') }}" method="POST"
                    onsubmit="return confirm('Yakin ingin mereset semua pengaturan ke default?')">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">Reset</button>
                </form>
            </div>
        </div>
    </div>
@endsection
