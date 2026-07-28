<?php
// app/Http/Controllers/Student/AchievementController.php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\UserPoint;
use App\Models\Achievement;
use App\Models\UserAchievement;
use App\Models\UserBadge;
use App\Services\GamificationService;
use Illuminate\Http\Request;

class AchievementController extends Controller
{
    /**
     * Menampilkan halaman prestasi student.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Data gamifikasi dari service
        $gamification = GamificationService::getUserProgress($user);

        // Achievement yang sudah didapat
        $earnedAchievementIds = UserAchievement::where('user_id', $user->id)
            ->pluck('achievement_id')
            ->toArray();

        // Achievement yang belum didapat
        $pendingAchievements = Achievement::where('is_active', true)
            ->whereNotIn('id', $earnedAchievementIds)
            ->get();

        // Hitung progress untuk setiap achievement yang belum didapat
        foreach ($pendingAchievements as $achievement) {
            $progress = $this->calculateAchievementProgress($user, $achievement);
            $achievement->current_progress = $progress['current'];
            $achievement->required_value = $progress['required'];
            $achievement->progress_percentage = $progress['percentage'];
        }

        // Achievement yang sudah didapat
        $earnedAchievements = Achievement::whereIn('id', $earnedAchievementIds)
            ->get();

        // Stats ringkasan
        $stats = [
            'total_achievements' => Achievement::where('is_active', true)->count(),
            'earned_achievements' => count($earnedAchievementIds),
            'total_points_earned' => UserAchievement::where('user_id', $user->id)
                ->join('achievements', 'user_achievements.achievement_id', '=', 'achievements.id')
                ->sum('achievements.points_reward'),
            'completion_percentage' => Achievement::where('is_active', true)->count() > 0
                ? round((count($earnedAchievementIds) / Achievement::where('is_active', true)->count()) * 100)
                : 0,
        ];

        // Badges yang sudah didapat
        $badges = UserBadge::where('user_id', $user->id)
            ->with('badge')
            ->orderBy('earned_at', 'desc')
            ->get();

        // Achievement terbaru (5 terakhir)
        $recentAchievements = UserAchievement::where('user_id', $user->id)
            ->with('achievement')
            ->orderBy('earned_at', 'desc')
            ->limit(5)
            ->get();

        return view('student.achievements.index', compact(
            'gamification',
            'earnedAchievements',
            'pendingAchievements',
            'stats',
            'badges',
            'recentAchievements'
        ));
    }

    /**
     * Menampilkan detail achievement tertentu.
     */
    public function show(Achievement $achievement)
    {
        $user = auth()->user();

        // Cek apakah achievement sudah didapat
        $userAchievement = UserAchievement::where('user_id', $user->id)
            ->where('achievement_id', $achievement->id)
            ->first();
        $isEarned = (bool) $userAchievement;

        // Data user yang sudah mendapatkan achievement ini
        $recentEarners = UserAchievement::where('achievement_id', $achievement->id)
            ->with('user')
            ->orderBy('earned_at', 'desc')
            ->limit(10)
            ->get();

        $totalEarners = UserAchievement::where('achievement_id', $achievement->id)->count();

        // Progress user untuk achievement ini
        $progress = $this->calculateAchievementProgress($user, $achievement);

        return view('student.achievements.show', compact(
            'achievement',
            'isEarned',
            'userAchievement',
            'recentEarners',
            'totalEarners',
            'progress'
        ));
    }

    /**
     * Menampilkan papan peringkat (leaderboard).
     */
    public function leaderboard(Request $request)
    {
        $limit = $request->input('limit', 50);
        $period = $request->input('period', 'all') === 'weekly' ? 'weekly' : 'all';
        $leaderboard = GamificationService::getLeaderboard($limit, $period);

        $userRank = $leaderboard->search(function ($item) {
            return $item['user_id'] === auth()->id();
        });

        $userRank = $userRank !== false ? $userRank + 1 : null;

        // Kursus yang diikuti siswa, untuk pintasan ke peringkat kelas per-kursus.
        $enrolledCourses = auth()->user()->enrollments()
            ->whereIn('status', ['active', 'completed'])
            ->with('course')
            ->get()
            ->pluck('course')
            ->filter();

        return view('student.achievements.leaderboard', compact('leaderboard', 'userRank', 'period', 'enrolledCourses'));
    }

    /**
     * Peringkat kelas untuk satu kursus tertentu (progress + rata-rata nilai quiz).
     */
    public function courseLeaderboard(\App\Models\Course $course)
    {
        $isEnrolled = auth()->user()->enrollments()
            ->where('course_id', $course->id)
            ->whereIn('status', ['active', 'completed'])
            ->exists();

        if (!$isEnrolled) {
            abort(403, 'Anda tidak terdaftar di kursus ini.');
        }

        $leaderboard = GamificationService::getCourseLeaderboard($course->id);

        $userRank = $leaderboard->search(function ($item) {
            return $item['user_id'] === auth()->id();
        });
        $userRank = $userRank !== false ? $userRank + 1 : null;

        return view('student.achievements.course-leaderboard', compact('course', 'leaderboard', 'userRank'));
    }

    /**
     * Menampilkan semua badge yang tersedia dan yang sudah didapat.
     */
    public function badges()
    {
        $user = auth()->user();

        $allBadges = \App\Models\Badge::where('is_active', true)->get();
        $earnedBadgeIds = UserBadge::where('user_id', $user->id)->pluck('badge_id')->toArray();

        foreach ($allBadges as $badge) {
            $badge->is_earned = in_array($badge->id, $earnedBadgeIds);
            if ($badge->is_earned) {
                $badge->earned_at = UserBadge::where('user_id', $user->id)
                    ->where('badge_id', $badge->id)
                    ->first()->earned_at;
            }
        }

        return view('student.achievements.badges', compact('allBadges'));
    }

    /**
     * Helper untuk menghitung progress achievement.
     */
    private function calculateAchievementProgress($user, $achievement)
    {
        $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $current = 0;
        $required = $achievement->condition_value;

        switch ($achievement->condition_type) {
            case 'course_completed':
                $current = $userPoint->total_courses_completed;
                break;
            case 'quiz_passed':
                $current = $userPoint->total_quizzes_passed;
                break;
            case 'assignment_submitted':
                $current = $userPoint->total_assignments_submitted;
                break;
            case 'streak_days':
                $current = $userPoint->streak_days;
                break;
            case 'forum_posts':
                $current = $userPoint->total_forum_posts;
                break;
            case 'total_points':
                $current = $userPoint->total_points;
                break;
        }

        $percentage = $required > 0 ? min(100, round(($current / $required) * 100)) : 0;

        return [
            'current' => $current,
            'required' => $required,
            'percentage' => $percentage,
        ];
    }
}