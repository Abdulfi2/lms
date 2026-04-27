<?php
// app/Models/Assignment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Assignment extends Model
{
    use SoftDeletes;

    protected $table = 'assignments';

    protected $fillable = [
        'course_id',
        'lesson_id',
        'title',
        'description',
        'instructions',
        'max_score',
        'passing_score',
        'time_limit',
        'due_date',
        'is_published',
        'slug',
        'allow_late_submission',
        'late_penalty',
        'attachment',
        'is_group_assignment',
        'max_group_size',
        'enable_ai_check',
        'enable_plagiarism_check',
        'enable_peer_review',
        'rubric',
        'total_submissions',
        'graded_count',
        'average_score'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'is_published' => 'boolean',
        'allow_late_submission' => 'boolean',
        'is_group_assignment' => 'boolean',
        'enable_ai_check' => 'boolean',
        'enable_plagiarism_check' => 'boolean',
        'enable_peer_review' => 'boolean',
        'rubric' => 'array',
        'max_score' => 'integer',
        'passing_score' => 'integer',
        'time_limit' => 'integer',
        'late_penalty' => 'integer',
        'total_submissions' => 'integer',
        'graded_count' => 'integer',
        'average_score' => 'float',
        'deleted_at' => 'datetime',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function lesson()
    {
        return $this->belongsTo(Lesson::class);
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class, 'assignment_id');
    }

    public function rubricItems()
    {
        return $this->hasMany(AssignmentRubric::class, 'assignment_id');
    }

    /**
     * Boot function untuk auto-generate slug.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($assignment) {
            if (empty($assignment->slug)) {
                $assignment->slug = Str::slug($assignment->title) . '-' . uniqid();
            }
        });

        static::updating(function ($assignment) {
            if ($assignment->isDirty('title')) {
                $assignment->slug = Str::slug($assignment->title) . '-' . $assignment->id;
            }
        });
    }

    /**
     * Scope untuk assignment yang sudah dipublikasikan.
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Scope untuk assignment yang belum deadline.
     */
    public function scopeNotOverdue($query)
    {
        return $query->where('due_date', '>', now());
    }

    /**
     * Scope untuk assignment yang sudah deadline.
     */
    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now());
    }

    /**
     * Cek apakah assignment sudah melewati deadline.
     */
    public function isOverdue()
    {
        return $this->due_date < now();
    }

    /**
     * Hitung penalti keterlambatan (dalam persen).
     */
    public function calculateLatePenalty($daysLate)
    {
        if (!$this->allow_late_submission || $this->late_penalty <= 0) {
            return 0;
        }
        return min($this->late_penalty * $daysLate, 100);
    }

    /**
     * Update statistik submission.
     */
    public function updateStats()
    {
        $this->total_submissions = $this->submissions()->count();
        $this->graded_count = $this->submissions()->where('status', 'graded')->count();
        $this->average_score = $this->submissions()->whereNotNull('score')->avg('score') ?? 0;
        $this->saveQuietly();
    }
}