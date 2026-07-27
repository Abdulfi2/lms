<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    protected $fillable = [
        'course_id',
        'title',
        'description',
        'order',
        'is_published',
        'total_lessons',
        'total_duration'
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'order' => 'integer',
        'total_lessons' => 'integer',
        'total_duration' => 'integer',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }

    // Auto update counts
    protected static function booted()
    {
        static::saved(function ($section) {
            $section->updateCounts();
        });
        static::deleted(function ($section) {
            // JANGAN panggil $section->updateCounts() di sini — method itu memanggil
            // saveQuietly() pada model ini, tapi setelah delete() Eloquent sudah
            // menandai $section->exists = false, sehingga save() akan meng-INSERT ULANG
            // baris yang baru saja dihapus (dengan id yang sama). Section jadi tidak
            // pernah benar-benar terhapus. Cukup perbarui hitungan di course induknya.
            $section->course?->updateCounts();
        });
    }

    public function updateCounts(): void
    {
        $this->total_lessons = $this->lessons()->count();
        $this->total_duration = $this->lessons()->sum('duration');
        $this->saveQuietly();

        // Update juga total lessons & duration di course
        $this->course->updateCounts();
    }
}