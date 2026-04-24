<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\Section;

class SectionSeeder extends Seeder
{
    public function run(): void
    {
        $courses = Course::all();

        if ($courses->isEmpty()) {
            $this->command->warn('Tidak ada course. Jalankan CourseSeeder terlebih dahulu.');
            return;
        }

        $sectionsData = [
            // Laravel Course (ID 1)
            [
                'title' => 'Pengenalan Laravel',
                'description' => 'Mengenal apa itu Laravel, sejarah, dan keunggulannya.',
                'order' => 1,
            ],
            [
                'title' => 'Instalasi dan Konfigurasi',
                'description' => 'Instalasi Laravel, Homestead, Valet, dan konfigurasi environment.',
                'order' => 2,
            ],
            [
                'title' => 'Routing dan Controller',
                'description' => 'Membahas routing dasar, routing parameter, dan controller.',
                'order' => 3,
            ],
            [
                'title' => 'Blade Template Engine',
                'description' => 'Membahas template engine Blade, layout, dan komponen.',
                'order' => 4,
            ],
            // Vue.js Course (ID 2)
            [
                'title' => 'Dasar Vue.js',
                'description' => 'Pengenalan Vue.js, reaktivitas, dan instance Vue.',
                'order' => 1,
            ],
            [
                'title' => 'Directives dan Data Binding',
                'description' => 'Membahas v-bind, v-model, v-for, v-if, dan event handling.',
                'order' => 2,
            ],
            [
                'title' => 'Komponen Vue',
                'description' => 'Membuat komponen, props, emit, dan slot.',
                'order' => 3,
            ],
            // Tailwind CSS Course (ID 3)
            [
                'title' => 'Instalasi Tailwind CSS',
                'description' => 'Cara menginstal dan mengkonfigurasi Tailwind CSS.',
                'order' => 1,
            ],
            [
                'title' => 'Utility Classes Dasar',
                'description' => 'Membahas utility classes untuk layout, spacing, typography, dan warna.',
                'order' => 2,
            ],
            // Alpine.js Course (ID 4)
            [
                'title' => 'Pengenalan Alpine.js',
                'description' => 'Apa itu Alpine.js, perbandingan dengan framework lain.',
                'order' => 1,
            ],
            [
                'title' => 'x-data, x-init, x-show',
                'description' => 'Membahas directive dasar Alpine.js.',
                'order' => 2,
            ],
        ];

        foreach ($courses as $courseIndex => $course) {
            // Ambil data sections untuk course ini berdasarkan indeks
            $startIdx = $courseIndex * 4;
            $courseSections = array_slice($sectionsData, $startIdx, 4);

            foreach ($courseSections as $sectionData) {
                Section::create([
                    'course_id' => $course->id,
                    'title' => $sectionData['title'],
                    'description' => $sectionData['description'],
                    'order' => $sectionData['order'],
                    'is_published' => true,
                ]);
            }
        }

        // Update total_sections dan total_lessons di course
        foreach ($courses as $course) {
            $course->total_sections = $course->sections()->count();
            $course->total_lessons = $course->lessons()->count();
            $course->saveQuietly();
        }

        $this->command->info('SectionSeeder: ' . Section::count() . ' sections berhasil dibuat.');
    }
}