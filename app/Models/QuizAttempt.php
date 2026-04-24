<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'attempt_id',
        'question_id',
        'selected_option_id',
        'answer_text',
        'is_correct',
        'points_earned'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'points_earned' => 'integer',
    ];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(QuizAttempt::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(QuizQuestion::class);
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(QuizOption::class, 'selected_option_id');
    }
}