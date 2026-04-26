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
        'quiz_type',
        'time_limit',
        'attempts_allowed',
        'passing_score',
        'randomize_questions',
        'show_results_immediately',
        'show_correct_answers',
        'show_explanation',
        'is_published',
        'published_at',
        'total_questions',
        'total_attempts',
        'average_score',
        'is_mandatory',
        'min_score_to_pass'
    ];

    protected $casts = [
        'time_limit' => 'integer',
        'attempts_allowed' => 'integer',
        'passing_score' => 'integer',
        'show_results_immediately' => 'boolean',
        'randomize_questions' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'quiz_type' => 'string',
        'is_mandatory' => 'boolean',
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