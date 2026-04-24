<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Course;
use App\Models\CoursePricing;
use App\Models\User;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Support\Str;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Ambil instructor (user dengan role instructor)
        $instructors = User::role('instructor')->get();
        if ($instructors->isEmpty()) {
            $this->command->warn('Tidak ada instructor ditemukan. Jalankan UserSeeder terlebih dahulu.');
            return;
        }

        // Ambil kategori dan tag yang sudah ada
        $categories = Category::all();
        $tags = Tag::all();

        if ($categories->isEmpty()) {
            $this->command->warn('Tidak ada kategori. Jalankan CategorySeeder terlebih dahulu.');
            return;
        }

        // Data kursus contoh
        $coursesData = [
            [
                'title' => 'Laravel 11 dari Dasar hingga Mahir',
                'short_description' => 'Pelajari Laravel 11 dari nol sampai siap deploy aplikasi profesional.',
                'description' => 'Kursus ini mencakup semua aspek Laravel: routing, controller, blade, eloquent, authentication, API, dan deployment. Cocok untuk pemula hingga menengah.',
                'price' => 499000,
                'sale_price' => 299000,
                'sale_starts_at' => now(),
                'sale_ends_at' => now()->addDays(30),
                'level' => 'beginner',
                'language' => 'id',
                'duration_total' => 40,
                'status' => 'published',
                'is_featured' => true,
                'has_certificate' => true,
                'requirements' => ['Dasar PHP', 'Mengenal konsep OOP', 'Text editor'],
                'learning_objectives' => ['Membuat aplikasi CRUD', 'Memahami MVC', 'Menggunakan Eloquent ORM', 'Membuat REST API'],
                'target_audience' => ['Mahasiswa IT', 'Web Developer Pemula', 'Freelancer'],
                'prerequisites' => ['Dasar HTML/CSS', 'Pengetahuan dasar PHP'],
                'meta_keywords' => 'laravel, php, framework, web development',
                'meta_description' => 'Kursus Laravel terlengkap dengan project nyata',
            ],
            [
                'title' => 'Vue.js 3 Composition API',
                'short_description' => 'Kuasi Vue.js 3 dengan Composition API dan build aplikasi modern.',
                'description' => 'Kursus ini membahas Vue.js 3 dari dasar hingga lanjutan, termasuk Composition API, Pinia state management, Vue Router, dan integrasi dengan backend.',
                'price' => 399000,
                'sale_price' => 249000,
                'sale_starts_at' => now(),
                'sale_ends_at' => now()->addDays(20),
                'level' => 'intermediate',
                'language' => 'id',
                'duration_total' => 35,
                'status' => 'published',
                'is_featured' => true,
                'has_certificate' => true,
                'requirements' => ['Dasar JavaScript', 'ES6', 'HTML/CSS'],
                'learning_objectives' => ['Menguasai Composition API', 'Membuat SPA', 'Mengelola state dengan Pinia'],
                'target_audience' => ['Frontend Developer', 'Fullstack Developer'],
                'prerequisites' => ['JavaScript modern', 'Pengalaman dengan Vue 2 (opsional)'],
                'meta_keywords' => 'vuejs, vue3, composition api, frontend',
                'meta_description' => 'Kursus Vue.js 3 terbaru dengan Composition API',
            ],
            [
                'title' => 'Tailwind CSS Mastery',
                'short_description' => 'Bangun UI modern dengan cepat menggunakan Tailwind CSS.',
                'description' => 'Pelajari Tailwind CSS dari dasar hingga teknik lanjutan seperti custom configuration, plugin, dan integrasi dengan framework JS.',
                'price' => 199000,
                'sale_price' => 99000,
                'sale_starts_at' => now(),
                'sale_ends_at' => now()->addDays(15),
                'level' => 'beginner',
                'language' => 'id',
                'duration_total' => 15,
                'status' => 'published',
                'is_featured' => false,
                'has_certificate' => false,
                'requirements' => ['Dasar CSS', 'HTML'],
                'learning_objectives' => ['Mendesain responsive', 'Membuat komponen reusable', 'Optimasi build'],
                'target_audience' => ['Web Designer', 'Frontend Developer', 'Backend yang ingin mempercantik UI'],
                'prerequisites' => ['Pengetahuan dasar CSS'],
                'meta_keywords' => 'tailwind, css, utility-first, frontend',
                'meta_description' => 'Kursus Tailwind CSS untuk mempercepat styling',
            ],
            [
                'title' => 'Alpine.js untuk Interaktivitas Ringan',
                'short_description' => 'Tambahkan interaktivitas ke website Anda tanpa framework berat.',
                'description' => 'Alpine.js adalah framework JavaScript ringan untuk interaktivitas frontend. Kursus ini mencakup directives, components, dan integrasi dengan Laravel.',
                'price' => 149000,
                'sale_price' => 79000,
                'sale_starts_at' => now(),
                'sale_ends_at' => now()->addDays(10),
                'level' => 'beginner',
                'language' => 'id',
                'duration_total' => 8,
                'status' => 'published',
                'is_featured' => false,
                'has_certificate' => false,
                'requirements' => ['Dasar JavaScript', 'HTML/CSS'],
                'learning_objectives' => ['Membuat modal, dropdown', 'Validasi form real-time', 'Integrasi dengan backend'],
                'target_audience' => ['Laravel Developer', 'Frontend Pemula'],
                'prerequisites' => ['Pengetahuan dasar JavaScript'],
                'meta_keywords' => 'alpinejs, javascript, frontend, interaktif',
                'meta_description' => 'Kursus Alpine.js untuk interaktivitas cepat',
            ],
        ];

        foreach ($coursesData as $index => $data) {
            // Pilih instructor secara bergantian
            $instructor = $instructors[$index % $instructors->count()];

            $course = Course::create([
                'title' => $data['title'],
                'slug' => Str::slug($data['title']) . '-' . uniqid(),
                'short_description' => $data['short_description'],
                'description' => $data['description'],
                'requirements' => $data['requirements'],
                'learning_objectives' => $data['learning_objectives'],
                'target_audience' => $data['target_audience'],
                'price' => $data['price'],
                'sale_price' => $data['sale_price'],
                'sale_starts_at' => $data['sale_starts_at'],
                'sale_ends_at' => $data['sale_ends_at'],
                'level' => $data['level'],
                'language' => $data['language'],
                'duration_total' => $data['duration_total'],
                'status' => $data['status'],
                'is_featured' => $data['is_featured'],
                'has_certificate' => $data['has_certificate'],
                'prerequisites' => $data['prerequisites'],
                'meta_keywords' => $data['meta_keywords'],
                'meta_description' => $data['meta_description'],
                'instructor_id' => $instructor->id,
                'published_at' => now(),
                'total_lessons' => rand(20, 50),
                'total_sections' => rand(5, 10),
            ]);

            // Attach categories (pilih 1-2 kategori acak)
            $randomCategories = $categories->random(min(2, $categories->count()));
            $course->categories()->attach($randomCategories->pluck('id')->toArray());

            // Attach tags (pilih 2-3 tag acak)
            if ($tags->isNotEmpty()) {
                $randomTags = $tags->random(min(3, $tags->count()));
                $course->tags()->attach($randomTags->pluck('id')->toArray());
                // Update usage_count (opsional, bisa dilakukan di observer)
                foreach ($randomTags as $tag) {
                    $tag->increment('usage_count');
                }
            }

            // (Opsional) Buat pricing regional
            CoursePricing::create([
                'course_id' => $course->id,
                'country_code' => 'ID',
                'price' => $data['price'],
                'discount' => $data['sale_price'] ?? null,
                'currency' => 'IDR',
            ]);
        }

        $this->command->info('CourseSeeder: ' . count($coursesData) . ' kursus berhasil dibuat.');
    }
}