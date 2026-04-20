<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'title', 'slug', 'short_description', 'description', 'requirements',
        'learning_objectives', 'target_audience', 'thumbnail', 'video_promo',
        'price', 'sale_price', 'sale_starts_at', 'sale_ends_at', 'level',
        'language', 'duration_total', 'total_lessons', 'total_sections',
        'total_students', 'average_rating', 'rating_count', 'reviews_count',
        'status', 'is_featured', 'has_certificate', 'certificate_template',
        'max_students', 'enrollment_end_date', 'start_date', 'end_date',
        'access_days', 'prerequisites', 'instructor_id', 'published_at'
    ];
    
    protected $casts = [
        'requirements' => 'array',
        'learning_objectives' => 'array',
        'target_audience' => 'array',
        'prerequisites' => 'array',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'sale_starts_at' => 'datetime',
        'sale_ends_at' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'enrollment_end_date' => 'datetime',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'has_certificate' => 'boolean',
        'deleted_at' => 'datetime',
    ];
    
    // Relationships
    public function instructor()
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }
    
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'course_category');
    }
    
    public function sections()
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }
    
    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Section::class);
    }
    
    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }
    
    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }
    
    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }
    
    public function students()
    {
        return $this->belongsToMany(User::class, 'enrollments')
                    ->withPivot('progress', 'status', 'enrolled_at', 'completed_at');
    }
    
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
    
    // Accessors
    public function getFinalPriceAttribute()
    {
        if ($this->sale_price && $this->sale_starts_at <= now() && $this->sale_ends_at >= now()) {
            return $this->sale_price;
        }
        return $this->price;
    }
    
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->final_price, 0, ',', '.');
    }
    
    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
    
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}