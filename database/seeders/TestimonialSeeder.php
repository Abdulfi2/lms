<?php

namespace Database\Seeders;

use App\Models\Testimonial;
use Illuminate\Database\Seeder;

class TestimonialSeeder extends Seeder
{
    public function run(): void
    {
        $testimonials = [
            [
                'name' => 'Ahmad Rizki',
                'role' => 'Fullstack Developer',
                'content' => 'Platform ini sangat membantu saya dalam mengembangkan skill programming. Materinya lengkap dan mudah dipahami!',
                'rating' => 5,
                'order' => 1,
            ],
            [
                'name' => 'Siti Aminah',
                'role' => 'UI/UX Designer',
                'content' => 'Instruktur profesional dan responsif. Saya sekarang sudah bisa membuat website sendiri setelah mengikuti kursus di sini.',
                'rating' => 5,
                'order' => 2,
            ],
            [
                'name' => 'Budi Santoso',
                'role' => 'Data Analyst',
                'content' => 'Sertifikatnya diakui industri. Setelah menyelesaikan kursus, saya langsung mendapat tawaran pekerjaan!',
                'rating' => 5,
                'order' => 3,
            ],
        ];

        foreach ($testimonials as $testimonial) {
            Testimonial::firstOrCreate(
                ['name' => $testimonial['name'], 'content' => $testimonial['content']],
                $testimonial
            );
        }
    }
}
