<?php
// app/Models/Thread.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Thread extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'forum_id',
        'title',
        'slug',
        'content',
        'user_id',
        'is_pinned',
        'is_locked',
        'is_solved',
        'view_count',
        'reply_count',
        'last_post_at',
        'last_post_user_id'
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'is_locked' => 'boolean',
        'is_solved' => 'boolean',
    ];

    public function forum()
    {
        return $this->belongsTo(Forum::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lastPostUser()
    {
        return $this->belongsTo(User::class, 'last_post_user_id');
    }

    public function posts()
    {
        return $this->hasMany(Post::class);
    }

    public function latestPost()
    {
        return $this->hasOne(Post::class)->latest();
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'thread_tags');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($thread) {
            $thread->slug = \Illuminate\Support\Str::slug($thread->title) . '-' . uniqid();
        });
    }
}