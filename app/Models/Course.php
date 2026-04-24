<?php
// app/Models/Course.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'requirements',
        'learning_objectives',
        'target_audience',
        'thumbnail',
        'video_promo',
        'price',
        'sale_price',
        'sale_starts_at',
        'sale_ends_at',
        'level',
        'language',
        'duration_total',
        'total_lessons',
        'total_sections',
        'total_students',
        'average_rating',
        'rating_count',
        'reviews_count',
        'enrolled_count',
        'wishlist_count',
        'status',
        'is_featured',
        'has_certificate',
        'certificate_template',
        'max_students',
        'enrollment_end_date',
        'start_date',
        'end_date',
        'access_days',
        'prerequisites',
        'meta_keywords',
        'meta_description',
        'instructor_id',
        'published_at'
    ];

    protected $casts = [
        'requirements' => 'array',
        'learning_objectives' => 'array',
        'target_audience' => 'array',
        'prerequisites' => 'array',
        'sale_starts_at' => 'datetime',
        'sale_ends_at' => 'datetime',
        'enrollment_end_date' => 'datetime',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'published_at' => 'datetime',
        'is_featured' => 'boolean',
        'has_certificate' => 'boolean',
        'price' => 'decimal:2',
        'sale_price' => 'decimal:2',
    ];

    protected $appends = ['formatted_price', 'final_price'];

    // Relationships
    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'instructor_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'course_category');
    }

    public function tags(): BelongsToMany
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables');
    }

    public function pricing(): HasMany
    {
        return $this->hasMany(CoursePricing::class);
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function lessons()
    {
        return $this->hasManyThrough(Lesson::class, Section::class, 'course_id', 'section_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    // Accessors
    /**
     * Get final price after discount (jika ada diskon aktif)
     */
    public function getFinalPriceAttribute(): float
    {
        // Cek apakah ada diskon dan masih dalam periode
        if ($this->sale_price && $this->sale_starts_at <= now() && $this->sale_ends_at >= now()) {
            return (float) $this->sale_price;
        }
        return (float) $this->price;
    }

    /**
     * Get formatted price (Gratis jika harga 0)
     */
    public function getFormattedPriceAttribute(): string
    {
        $finalPrice = $this->final_price;

        if ($finalPrice <= 0) {
            return 'Gratis';
        }

        return 'Rp ' . number_format($finalPrice, 0, ',', '.');
    }

    /**
     * Get original formatted price (untuk perbandingan jika ada diskon)
     */
    public function getOriginalFormattedPriceAttribute(): string
    {
        $price = (float) $this->price; // Cast ke float

        if ($price <= 0) {
            return 'Gratis';
        }

        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    public function updateCounts(): void
    {
        $this->total_lessons = $this->lessons()->count();
        $this->total_sections = $this->sections()->count();
        $this->saveQuietly();
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

    // Auto-generate slug
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($course) {
            if (empty($course->slug)) {
                $course->slug = \Illuminate\Support\Str::slug($course->title) . '-' . uniqid();
            }
        });
        static::updating(function ($course) {
            if ($course->isDirty('title')) {
                $course->slug = \Illuminate\Support\Str::slug($course->title) . '-' . $course->id;
            }
        });
    }
}