@extends('layouts.client')

@section('title', 'Tentang Kami')
@section('page-title', 'Tentang Kami')
@section('page-subtitle', 'Mengenal lebih dekat ' . config('app.name', 'LMS'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 space-y-4">
        <p class="text-gray-700 dark:text-gray-300">
            {{ config('app.name', 'LMS') }} adalah platform pembelajaran online yang menghubungkan siswa dengan instruktur
            berkualitas di berbagai bidang. Kami percaya bahwa pendidikan yang baik harus bisa diakses oleh siapa saja,
            kapan saja, dan di mana saja.
        </p>
        <p class="text-gray-700 dark:text-gray-300">
            Melalui kursus video, tugas interaktif, kuis, forum diskusi, dan sertifikat kelulusan, kami membantu
            siswa mengembangkan keterampilan baru sekaligus membantu instruktur membagikan keahlian mereka kepada
            audiens yang lebih luas.
        </p>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-4">
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-2xl font-bold text-primary">{{ number_format(\App\Models\Course::where('status', 'published')->count()) }}</p>
                <p class="text-sm text-gray-500">Kursus Aktif</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-2xl font-bold text-primary">{{ number_format(\App\Models\User::role('student')->count()) }}</p>
                <p class="text-sm text-gray-500">Siswa Terdaftar</p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-lg">
                <p class="text-2xl font-bold text-primary">{{ number_format(\App\Models\User::role('instructor')->count()) }}</p>
                <p class="text-sm text-gray-500">Instruktur</p>
            </div>
        </div>
    </div>
</div>
@endsection
