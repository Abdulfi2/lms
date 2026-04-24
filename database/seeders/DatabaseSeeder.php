<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            TagSeeder::class,
            CourseSeeder::class,
            SectionSeeder::class,
            LessonSeeder::class,
            QuizSeeder::class,
        ]);
    }
}