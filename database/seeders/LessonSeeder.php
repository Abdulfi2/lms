<?php
// database/seeders/LessonSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Section;
use App\Models\Lesson;

class LessonSeeder extends Seeder
{
    public function run(): void
    {
        $sections = Section::all();

        if ($sections->isEmpty()) {
            $this->command->warn('Tidak ada section. Jalankan SectionSeeder terlebih dahulu.');
            return;
        }

        $lessonTitles = [
            'Apa itu Laravel?',
            'Sejarah Laravel',
            'Keunggulan Laravel',
            'Instalasi Composer',
            'Instalasi Laravel via Composer',
            'Konfigurasi Environment (.env)',
            'Membuat Route Dasar',
            'Route Parameter',
            'Membuat Controller',
            'Mengirim Data ke View',
            'Membuat Layout Blade',
            'Komponen Blade',
            'Mengenal Reaktivitas Vue',
            'Data dan Methods',
            'v-bind dan v-model',
            'v-for untuk Looping',
            'Conditional Rendering',
            'Membuat Komponen Sederhana',
            'Props dan Emit',
            'Instalasi Tailwind via NPM',
            'Menggunakan Utility Classes',
            'Responsive Design dengan Tailwind',
            'Apa itu Alpine?',
            'x-data dan x-init',
            'x-show dan x-transition',
        ];

        foreach ($sections as $section) {
            // Ambil 3-5 lesson per section
            $lessonCount = rand(3, 5);
            for ($i = 0; $i < $lessonCount; $i++) {
                $titleIndex = ($section->id + $i) % count($lessonTitles);
                $lessonType = ['video', 'article', 'quiz'][array_rand(['video', 'article', 'quiz'])];
                $duration = $lessonType === 'video' ? rand(5, 30) : rand(10, 60);

                Lesson::create([
                    'section_id' => $section->id,
                    'title' => $lessonTitles[$titleIndex] . ' ' . ($i + 1),
                    'content' => 'Ini adalah konten lesson. Silakan pelajari materi ini dengan baik.',
                    'type' => $lessonType,
                    'duration' => $duration,
                    'order' => $i + 1,
                    'is_free_preview' => $i === 0,
                    'is_required' => true,
                    'points' => $lessonType === 'quiz' ? 10 : 5,
                    'video_url' => $lessonType === 'video' ? 'https://www.youtube.com/embed/dQw4w9WgXcQ' : null,
                    'status' => 'published',
                ]);
            }
        }

        // Update total_lessons dan total_duration di sections dan courses
        foreach ($sections as $section) {
            $section->total_lessons = $section->lessons()->count();
            $section->total_duration = $section->lessons()->sum('duration');
            $section->saveQuietly();

            // Update course totals
            if ($section->course) {
                $section->course->total_lessons = $section->course->lessons()->count();
                $section->course->total_sections = $section->course->sections()->count();
                $section->course->saveQuietly();
            }
        }

        $this->command->info('LessonSeeder: ' . Lesson::count() . ' lessons berhasil dibuat.');
    }
}