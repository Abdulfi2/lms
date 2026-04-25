<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonCompletion extends Model
{
    protected $table = 'lesson_completions';

    protected $fillable = [
        'user_id',
        'lesson_id',
        'is_completed',
        'completed_at',
        'time_spent',
        'watch_percentage',
        'last_position',
        'notes',
        'rating',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'time_spent' => 'integer',
        'watch_percentage' => 'integer',
        'last_position' => 'integer',
        'rating' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }
}