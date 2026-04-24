<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quiz extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'description',
        'time_limit',
        'attempts_allowed',
        'passing_score',
        'show_results_immediately',
        'randomize_questions',
        'is_published',
        'published_at'
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'attempts_allowed' => 'integer',
        'passing_score' => 'integer',
        'show_results_immediately' => 'boolean',
        'randomize_questions' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(QuizQuestion::class)->orderBy('order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(QuizAttempt::class);
    }
}