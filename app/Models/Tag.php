<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active',
        'usage_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

    // Polymorphic relation to all taggable models
    public function taggables()
    {
        return $this->hasMany(Taggable::class);
    }

    // Helper to get all courses using this tag
    public function courses()
    {
        return $this->morphedByMany(Course::class, 'taggable', 'taggables');
    }

    // Helper to get all articles using this tag
    public function articles()
    {
        return $this->morphedByMany(Article::class, 'taggable', 'taggables');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($tag) {
            if (empty($tag->slug)) {
                $tag->slug = \Illuminate\Support\Str::slug($tag->name);
            }
        });
        static::updating(function ($tag) {
            if ($tag->isDirty('name')) {
                $tag->slug = \Illuminate\Support\Str::slug($tag->name);
            }
        });
    }
}