<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lesson extends Model
{
    use SoftDeletes;

    /**
     * Ubah berbagai bentuk link YouTube (watch, share pendek, shorts, atau embed) jadi
     * format embed baku, supaya instruktur tidak perlu tahu istilah teknis "link embed"
     * saat menempel URL video — cukup salin link apa saja dari YouTube.
     */
    public static function normalizeVideoUrl(string $url): string
    {
        $patterns = [
            '/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/',
            '/youtu\.be\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/shorts\/([a-zA-Z0-9_-]+)/',
            '/youtube\.com\/embed\/([a-zA-Z0-9_-]+)/',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return 'https://www.youtube.com/embed/' . $matches[1];
            }
        }

        // Bukan link YouTube yang dikenali (mis. Vimeo) — simpan apa adanya.
        return $url;
    }

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

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function course()
    {
        return $this->hasOneThrough(Course::class, Section::class, 'id', 'id', 'section_id', 'course_id');
    }

    public function pretest()
    {
        return $this->hasOne(Quiz::class)->where('quiz_type', 'pretest');
    }

    public function posttest()
    {
        return $this->hasOne(Quiz::class)->where('quiz_type', 'posttest');
    }

    public function resources()
    {
        return $this->hasMany(LessonResource::class)->orderBy('order');
    }
}