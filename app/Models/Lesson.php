<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'section_id',
        'title',
        'content',
        'content_html',
        'type',
        'duration',
        'order',
        'is_free_preview',
        'is_required',
        'points',
        'video_url',
        'video_id',
        'video_embed',
        'attachment',
        'is_downloadable',
        'has_preview',
        'status',
        'view_count',
        'like_count',
        'comment_count'
    ];

    protected $casts = [
        'is_free_preview' => 'boolean',
        'is_required' => 'boolean',
        'is_downloadable' => 'boolean',
        'has_preview' => 'boolean',
        'duration' => 'integer',
        'order' => 'integer',
        'points' => 'integer',
        'view_count' => 'integer',
        'like_count' => 'integer',
        'comment_count' => 'integer',
    ];

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, Section::class, 'id', 'id', 'section_id', 'course_id');
    }
}