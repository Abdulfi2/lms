<?php
// database/seeders/GamificationSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Level;
use App\Models\Badge;
use App\Models\Achievement;

class GamificationSeeder extends Seeder
{
    public function run(): void
    {
        // Levels
        $levels = [
            ['name' => 'Pemula', 'level_number' => 1, 'points_required' => 0, 'icon' => '🌱'],
            ['name' => 'Pelajar', 'level_number' => 2, 'points_required' => 500, 'icon' => '📖'],
            ['name' => 'Mahir', 'level_number' => 3, 'points_required' => 1500, 'icon' => '⚡'],
            ['name' => 'Ahli', 'level_number' => 4, 'points_required' => 3500, 'icon' => '🏆'],
            ['name' => 'Master', 'level_number' => 5, 'points_required' => 7000, 'icon' => '👑'],
        ];

        foreach ($levels as $level) {
            Level::create($level);
        }

        // Badges
        $badges = [
            ['name' => 'First Course', 'slug' => 'first-course', 'icon' => '🎓', 'color' => '#10B981', 'description' => 'Menyelesaikan kursus pertama', 'type' => 'course', 'required_value' => 1],
            ['name' => 'Course Master', 'slug' => 'course-master', 'icon' => '🏅', 'color' => '#3B82F6', 'description' => 'Menyelesaikan 5 kursus', 'type' => 'course', 'required_value' => 5],
            ['name' => 'Quiz Pro', 'slug' => 'quiz-pro', 'icon' => '📝', 'color' => '#8B5CF6', 'description' => 'Lulus 10 quiz', 'type' => 'quiz', 'required_value' => 10],
            ['name' => 'Assignment Hero', 'slug' => 'assignment-hero', 'icon' => '📂', 'color' => '#F59E0B', 'description' => 'Mengirim 10 tugas', 'type' => 'assignment', 'required_value' => 10],
            ['name' => '7 Day Streak', 'slug' => '7-day-streak', 'icon' => '🔥', 'color' => '#EF4444', 'description' => 'Belajar 7 hari berturut-turut', 'type' => 'streak', 'required_value' => 7],
            ['name' => '30 Day Streak', 'slug' => '30-day-streak', 'icon' => '⚡', 'color' => '#F97316', 'description' => 'Belajar 30 hari berturut-turut', 'type' => 'streak', 'required_value' => 30],
            ['name' => 'Forum Lover', 'slug' => 'forum-lover', 'icon' => '💬', 'color' => '#06B6D4', 'description' => 'Membuat 25 posting di forum', 'type' => 'forum', 'required_value' => 25],
            ['name' => 'Perfect Score', 'slug' => 'perfect-score', 'icon' => '⭐', 'color' => '#FBBF24', 'description' => 'Mendapatkan nilai sempurna di quiz', 'type' => 'special', 'required_value' => 0],
        ];

        foreach ($badges as $badge) {
            Badge::create($badge);
        }

        // Achievements
        $achievements = [
            ['name' => 'First Step', 'slug' => 'first-step', 'icon' => '👣', 'description' => 'Menyelesaikan 1 kursus', 'points_reward' => 50, 'condition_type' => 'course_completed', 'condition_value' => 1],
            ['name' => 'Dedicated Learner', 'slug' => 'dedicated-learner', 'icon' => '📚', 'description' => 'Menyelesaikan 5 kursus', 'points_reward' => 200, 'condition_type' => 'course_completed', 'condition_value' => 5],
            ['name' => 'Quiz Enthusiast', 'slug' => 'quiz-enthusiast', 'icon' => '🎯', 'description' => 'Lulus 5 quiz', 'points_reward' => 100, 'condition_type' => 'quiz_passed', 'condition_value' => 5],
            ['name' => 'Assignment Doer', 'slug' => 'assignment-doer', 'icon' => '✅', 'description' => 'Mengirim 5 tugas', 'points_reward' => 100, 'condition_type' => 'assignment_submitted', 'condition_value' => 5],
            ['name' => 'Consistency King', 'slug' => 'consistency-king', 'icon' => '👑', 'description' => 'Streak 14 hari', 'points_reward' => 150, 'condition_type' => 'streak_days', 'condition_value' => 14],
            ['name' => 'Forum Star', 'slug' => 'forum-star', 'icon' => '🌟', 'description' => 'Membuat 10 posting forum', 'points_reward' => 100, 'condition_type' => 'forum_posts', 'condition_value' => 10],
            ['name' => 'Point Collector', 'slug' => 'point-collector', 'icon' => '💰', 'description' => 'Mengumpulkan 1000 poin', 'points_reward' => 200, 'condition_type' => 'total_points', 'condition_value' => 1000],
        ];

        foreach ($achievements as $achievement) {
            Achievement::create($achievement);
        }
    }
}