<?php
// app/Models/Category.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'icon',
        'banner',
        'color',
        'meta_title',
        'meta_description',
        'order',
        'is_active',
        'course_count'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order' => 'integer',
        'course_count' => 'integer',
    ];

    // Relasi parent category (self-referential)
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    // Relasi child categories
    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('order');
    }

    // Relasi many-to-many dengan Course
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_category');
    }

    // Scope untuk kategori aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope untuk kategori parent (root)
    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    // Accessor untuk breadcrumb path
    public function getPathAttribute(): string
    {
        $path = [$this->name];
        $parent = $this->parent;
        while ($parent) {
            array_unshift($path, $parent->name);
            $parent = $parent->parent;
        }
        return implode(' / ', $path);
    }

    // Auto-generate slug saat creating/updating
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
        static::updating(function ($category) {
            if ($category->isDirty('name')) {
                $category->slug = \Illuminate\Support\Str::slug($category->name);
            }
        });
    }
}