<?php
// app/Models/Submission.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'submissions';

    protected $fillable = [
        'assignment_id',
        'student_id',
        'content',
        'attachments',
        'submission_count',
        'is_late',
        'status',
        'submitted_at',
        'graded_at',
        'feedback',
        'score',
        'ai_score',
        'plagiarism_percentage',
        'graded_by',
        'rubric_scores'
    ];

    protected $casts = [
        'attachments' => 'array',
        'rubric_scores' => 'array',
        'is_late' => 'boolean',
        'submitted_at' => 'datetime',
        'graded_at' => 'datetime',
        'score' => 'integer',
        'ai_score' => 'float',
        'plagiarism_percentage' => 'float',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    public function grader()
    {
        return $this->belongsTo(User::class, 'graded_by');
    }
}