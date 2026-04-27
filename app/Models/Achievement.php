<?php
// app/Models/Achievement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Achievement extends Model
{
    protected $table = 'achievements';

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'points_reward',
        'condition_type',
        'condition_value',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'points_reward' => 'integer',
        'condition_value' => 'integer',
    ];

    /**
     * Relasi many-to-many dengan user melalui tabel user_achievements.
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_achievements')
            ->withTimestamps()
            ->withPivot('achievement_id', 'user_id', 'earned_at');
    }

    /**
     * Boot function untuk auto-generate slug.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($achievement) {
            if (empty($achievement->slug)) {
                $achievement->slug = Str::slug($achievement->name);
            }
        });

        static::updating(function ($achievement) {
            if ($achievement->isDirty('name')) {
                $achievement->slug = Str::slug($achievement->name);
            }
        });
    }

    /**
     * Scope untuk achievement yang aktif.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope berdasarkan tipe kondisi.
     */
    public function scopeByConditionType($query, $type)
    {
        return $query->where('condition_type', $type);
    }

    /**
     * Cek apakah achievement bisa diberikan ke user berdasarkan progress.
     */
    public function canAward($user, $progressValue = null)
    {
        $userPoint = UserPoint::where('user_id', $user->id)->first();
        if (!$userPoint) {
            return false;
        }

        switch ($this->condition_type) {
            case 'course_completed':
                $condition = $userPoint->total_courses_completed >= $this->condition_value;
                break;
            case 'quiz_passed':
                $condition = $userPoint->total_quizzes_passed >= $this->condition_value;
                break;
            case 'assignment_submitted':
                $condition = $userPoint->total_assignments_submitted >= $this->condition_value;
                break;
            case 'streak_days':
                $condition = $userPoint->streak_days >= $this->condition_value;
                break;
            case 'forum_posts':
                $condition = $userPoint->total_forum_posts >= $this->condition_value;
                break;
            case 'total_points':
                $condition = $userPoint->total_points >= $this->condition_value;
                break;
            default:
                $condition = false;
        }

        return $condition;
    }

    /**
     * Award achievement to a user.
     */
    public function awardTo(User $user)
    {
        if ($this->canAward($user) && !$user->achievements()->where('achievement_id', $this->id)->exists()) {
            $user->achievements()->attach($this->id, ['earned_at' => now()]);
            return true;
        }
        return false;
    }

    /**
     * Get formatted points reward.
     */
    public function getFormattedPointsRewardAttribute()
    {
        return number_format($this->points_reward, 0, ',', '.');
    }
}