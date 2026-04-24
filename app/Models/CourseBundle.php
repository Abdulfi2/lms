<?php
// app/Models/CourseBundle.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseBundle extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'thumbnail',
        'discount_percentage',
        'bundle_price',
        'original_price',
        'is_active',
        'total_courses',
        'total_students',
        'valid_until'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'discount_percentage' => 'decimal:2',
        'bundle_price' => 'decimal:2',
        'original_price' => 'decimal:2',
        'valid_until' => 'datetime',
    ];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'bundle_course')
            ->withPivot('order')
            ->orderBy('pivot_order');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($bundle) {
            if (empty($bundle->slug)) {
                $bundle->slug = \Illuminate\Support\Str::slug($bundle->title);
            }
        });
    }
}