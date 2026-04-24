<?php

namespace Database\Seeders;

use App\Models\Course;
use Illuminate\Database\Seeder;

class QuizSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $course = Course::first();
        $quiz = $course->quizzes()->create([
            'title' => 'Laravel Basics Quiz',
            'description' => 'Test your understanding of Laravel basics',
            'time_limit' => 30,
            'attempts_allowed' => 2,
            'passing_score' => 70,
            'is_published' => true,
        ]);

        $q1 = $quiz->questions()->create(['question' => 'What is Laravel?', 'type' => 'multiple_choice', 'points' => 10]);
        $q1->options()->createMany([
            ['option_text' => 'PHP Framework', 'is_correct' => true],
            ['option_text' => 'JavaScript Library', 'is_correct' => false],
            ['option_text' => 'Database', 'is_correct' => false],
        ]);

        $q2 = $quiz->questions()->create(['question' => 'Laravel uses MVC pattern?', 'type' => 'true_false', 'points' => 5]);
        $q2->options()->createMany([
            ['option_text' => 'Benar', 'is_correct' => true],
            ['option_text' => 'Salah', 'is_correct' => false],
        ]);
    }
}