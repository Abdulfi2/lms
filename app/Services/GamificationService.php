<?php
// app/Services/GamificationService.php

namespace App\Services;

use App\Models\User;
use App\Models\UserPoint;
use App\Models\Level;
use App\Models\Badge;
use App\Models\Achievement;
use App\Models\UserBadge;
use App\Models\UserAchievement;
use App\Models\PointActivity;
use Illuminate\Support\Facades\DB;

class GamificationService
{
    /**
     * Tambah poin untuk user.
     */
    public static function addPoints(User $user, int $points, string $reason)
    {
        $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $userPoint->addPoints($points, $reason);

        // Check achievements after adding points
        self::checkAchievements($user);
        self::checkBadges($user);

        return $userPoint;
    }

    /**
     * Update streak harian.
     */
    public static function updateStreak(User $user)
    {
        $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $today = now()->toDateString();

        if ($userPoint->last_activity_at == $today) {
            return; // Already updated today
        }

        $yesterday = now()->subDay()->toDateString();

        if ($userPoint->last_activity_at == $yesterday) {
            $userPoint->streak_days += 1;
        } else {
            $userPoint->streak_days = 1;
        }

        $userPoint->last_activity_at = $today;
        $userPoint->save();

        // Bonus point for streak
        if ($userPoint->streak_days >= 7 && $userPoint->streak_days % 7 == 0) {
            $bonus = 50;
            self::addPoints($user, $bonus, "Streak {$userPoint->streak_days} hari! +{$bonus} poin");
        }

        self::checkBadges($user);
    }

    /**
     * Track course completed.
     */
    public static function courseCompleted(User $user, $courseId)
    {
        $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $userPoint->total_courses_completed += 1;
        $userPoint->save();

        // Points for completing course
        self::addPoints($user, 100, "Menyelesaikan kursus +100 poin");

        self::checkAchievements($user);
        self::checkBadges($user);
    }

    /**
     * Track quiz passed.
     */
    public static function quizPassed(User $user, $quizId, $score)
    {
        $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $userPoint->total_quizzes_passed += 1;
        $userPoint->save();

        // Points based on score
        $points = round($score / 10);
        self::addPoints($user, $points, "Mengerjakan quiz +{$points} poin");

        self::checkAchievements($user);
        self::checkBadges($user);
    }

    /**
     * Track assignment submitted.
     */
    public static function assignmentSubmitted(User $user)
    {
        $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $userPoint->total_assignments_submitted += 1;
        $userPoint->save();

        self::addPoints($user, 50, "Mengirim tugas +50 poin");

        self::checkAchievements($user);
        self::checkBadges($user);
    }

    /**
     * Track forum post.
     */
    public static function forumPostCreated(User $user)
    {
        $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $userPoint->total_forum_posts += 1;
        $userPoint->save();

        self::addPoints($user, 10, "Membuat posting forum +10 poin");

        self::checkAchievements($user);
        self::checkBadges($user);
    }

    /**
     * Check and award achievements.
     */
    public static function checkAchievements(User $user)
    {
        $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $earnedIds = UserAchievement::where('user_id', $user->id)->pluck('achievement_id')->toArray();

        $achievements = Achievement::where('is_active', true)
            ->whereNotIn('id', $earnedIds)
            ->get();

        foreach ($achievements as $achievement) {
            $conditionMet = false;

            switch ($achievement->condition_type) {
                case 'course_completed':
                    $conditionMet = $userPoint->total_courses_completed >= $achievement->condition_value;
                    break;
                case 'quiz_passed':
                    $conditionMet = $userPoint->total_quizzes_passed >= $achievement->condition_value;
                    break;
                case 'assignment_submitted':
                    $conditionMet = $userPoint->total_assignments_submitted >= $achievement->condition_value;
                    break;
                case 'streak_days':
                    $conditionMet = $userPoint->streak_days >= $achievement->condition_value;
                    break;
                case 'forum_posts':
                    $conditionMet = $userPoint->total_forum_posts >= $achievement->condition_value;
                    break;
                case 'total_points':
                    $conditionMet = $userPoint->total_points >= $achievement->condition_value;
                    break;
            }

            if ($conditionMet) {
                // Award achievement
                UserAchievement::create([
                    'user_id' => $user->id,
                    'achievement_id' => $achievement->id,
                    'earned_at' => now(),
                ]);

                // Add points reward
                if ($achievement->points_reward > 0) {
                    self::addPoints($user, $achievement->points_reward, "Achievement: {$achievement->name} +{$achievement->points_reward} poin");
                }
            }
        }
    }

    /**
     * Check and award badges.
     */
    public static function checkBadges(User $user)
    {
        $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);
        $earnedIds = UserBadge::where('user_id', $user->id)->pluck('badge_id')->toArray();

        $badges = Badge::where('is_active', true)
            ->whereNotIn('id', $earnedIds)
            ->get();

        foreach ($badges as $badge) {
            $conditionMet = false;

            switch ($badge->type) {
                case 'course':
                    $conditionMet = $userPoint->total_courses_completed >= $badge->required_value;
                    break;
                case 'quiz':
                    $conditionMet = $userPoint->total_quizzes_passed >= $badge->required_value;
                    break;
                case 'assignment':
                    $conditionMet = $userPoint->total_assignments_submitted >= $badge->required_value;
                    break;
                case 'streak':
                    $conditionMet = $userPoint->streak_days >= $badge->required_value;
                    break;
                case 'forum':
                    $conditionMet = $userPoint->total_forum_posts >= $badge->required_value;
                    break;
                case 'special':
                    // Manual assignment by admin
                    $conditionMet = false;
                    break;
            }

            if ($conditionMet) {
                UserBadge::create([
                    'user_id' => $user->id,
                    'badge_id' => $badge->id,
                    'earned_at' => now(),
                ]);
            }
        }
    }

    /**
     * Get leaderboard.
     */
    public static function getLeaderboard($limit = 50, $period = 'all')
    {
        if ($period === 'weekly') {
            return PointActivity::where('created_at', '>=', now()->subDays(7))
                ->selectRaw('user_id, SUM(points) as total_points')
                ->groupBy('user_id')
                ->orderByDesc('total_points')
                ->limit($limit)
                ->get()
                ->map(function ($item, $index) {
                    $user = User::find($item->user_id);
                    if (!$user) {
                        return null;
                    }
                    return [
                        'rank' => $index + 1,
                        'user_id' => $item->user_id,
                        'user_name' => $user->name,
                        'avatar' => $user->avatar_url,
                        'total_points' => (int) $item->total_points,
                        'current_level' => optional(UserPoint::where('user_id', $item->user_id)->first())->current_level ?? 1,
                        'badges_count' => UserBadge::where('user_id', $item->user_id)->count(),
                    ];
                })
                ->filter()
                ->values();
        }

        return UserPoint::with('user')
            ->orderBy('total_points', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($item, $index) {
                return [
                    'rank' => $index + 1,
                    'user_id' => $item->user_id,
                    'user_name' => $item->user->name,
                    'avatar' => $item->user->avatar_url,
                    'total_points' => $item->total_points,
                    'current_level' => $item->current_level,
                    'badges_count' => UserBadge::where('user_id', $item->user_id)->count(),
                ];
            });
    }

    /**
     * Peringkat siswa dalam SATU kursus tertentu, berdasarkan progress penyelesaian
     * dan rata-rata nilai quiz di kursus itu — bukan poin gamifikasi global (poin
     * tidak dilacak per-kursus), supaya tetap benar-benar mencerminkan kompetisi
     * di kelas tersebut.
     */
    public static function getCourseLeaderboard($courseId, $limit = 50)
    {
        $quizIds = \App\Models\Quiz::where('course_id', $courseId)->pluck('id');

        return \App\Models\Enrollment::with('user')
            ->where('course_id', $courseId)
            ->whereIn('status', ['active', 'completed'])
            ->get()
            ->map(function ($enrollment) use ($quizIds) {
                $avgQuizScore = $quizIds->isEmpty() ? null : \App\Models\QuizAttempt::where('user_id', $enrollment->user_id)
                    ->whereIn('quiz_id', $quizIds)
                    ->where('status', 'completed')
                    ->avg('percentage');

                return [
                    'user_id' => $enrollment->user_id,
                    'user_name' => $enrollment->user->name,
                    'avatar' => $enrollment->user->avatar_url,
                    'progress' => round($enrollment->progress ?? 0),
                    'avg_quiz_score' => $avgQuizScore !== null ? round($avgQuizScore) : null,
                ];
            })
            ->sortByDesc(fn ($item) => $item['progress'] * 1000 + ($item['avg_quiz_score'] ?? 0))
            ->take($limit)
            ->values()
            ->map(function ($item, $index) {
                $item['rank'] = $index + 1;
                return $item;
            });
    }

    /**
     * Get user progress.
     */
    public static function getUserProgress(User $user)
    {
        $userPoint = UserPoint::firstOrCreate(['user_id' => $user->id]);

        // Pastikan total_points tidak null
        $totalPoints = (int) ($userPoint->total_points ?? 0);

        // Cari level berikutnya dengan aman
        $nextLevel = Level::where('points_required', '>', $totalPoints)
            ->orderBy('points_required', 'asc')
            ->first();

        // Cari level saat ini
        $currentLevel = Level::where('points_required', '<=', $totalPoints)
            ->orderBy('points_required', 'desc')
            ->first();

        // Jika belum punya level (total_points = 0), ambil level pertama
        if (!$currentLevel) {
            $currentLevel = Level::orderBy('points_required', 'asc')->first();
        }

        // Hitung persentase progress ke level berikutnya
        $progressPercentage = 0;
        $pointsToNextLevel = 0;

        if ($nextLevel) {
            $pointsToNextLevel = $nextLevel->points_required - $totalPoints;
            $pointsNeededForCurrentLevel = $currentLevel ? $currentLevel->points_required : 0;
            $pointsRange = $nextLevel->points_required - $pointsNeededForCurrentLevel;

            if ($pointsRange > 0) {
                $progressPercentage = round((($totalPoints - $pointsNeededForCurrentLevel) / $pointsRange) * 100);
            }
        } else {
            $progressPercentage = 100; // Sudah di level tertinggi
        }

        return [
            'current_level_icon' => $currentLevel->icon ?? '🌱',
            'current_level' => $currentLevel->level_number ?? 1,
            'current_level_name' => $currentLevel->name ?? 'Pemula',
            'total_points' => $totalPoints,
            'next_level_name' => $nextLevel->name ?? null,
            'points_to_next_level' => max(0, $pointsToNextLevel),
            'progress_percentage' => min(100, max(0, $progressPercentage)),
            'streak_days' => $userPoint->streak_days ?? 0,
            'badges' => UserBadge::where('user_id', $user->id)->with('badge')->get(),
        ];
    }
}