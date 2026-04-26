<?php
// app/Models/Forum.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'is_active',
        'order',
        'thread_count',
        'post_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function latestThread()
    {
        return $this->hasOne(Thread::class)->latest();
    }
}