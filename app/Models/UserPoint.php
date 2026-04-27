<?php
// app/Models/UserPoint.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPoint extends Model
{
    protected $table = 'user_points';

    protected $fillable = [
        'user_id',
        'total_points',
        'current_level',
        'total_courses_completed',
        'total_quizzes_passed',
        'total_assignments_submitted',
        'total_forum_posts',
        'streak_days',
        'last_activity_at'
    ];

    protected $casts = [
        'last_activity_at' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addPoints($points, $reason)
    {
        $this->total_points += $points;
        $this->save();

        // Check level up
        $this->checkLevelUp();

        // Log point activity
        PointActivity::create([
            'user_id' => $this->user_id,
            'points' => $points,
            'reason' => $reason,
            'total_after' => $this->total_points,
        ]);

        return $this;
    }

    public function checkLevelUp()
    {
        $nextLevel = Level::where('points_required', '>', $this->total_points)
            ->orderBy('points_required')
            ->first();

        $currentLevelData = Level::where('level_number', $this->current_level)->first();

        if (!$nextLevel && $currentLevelData) {
            return; // Sudah level max
        }

        if ($nextLevel && $nextLevel->points_required <= $this->total_points) {
            $this->current_level = $nextLevel->level_number;
            $this->save();

            // Notify user about level up
            // event(new LevelUp($this->user, $nextLevel));
        }
    }
}