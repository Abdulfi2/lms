<?php

namespace App\Models;

use App\Jobs\GenerateCertificateJob;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';

    protected $fillable = [
        'user_id',
        'course_id',
        'amount_paid',
        'payment_status',
        'status',
        'progress',
        'enrolled_at',
        'completed_at',
        'expires_at',
        'certificate_issued_at',
        'rating_given',
        'review_given',
        'last_lesson_id'
    ];

    // =====================
    //  Relations
    // =====================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    protected static function booted()
    {
        static::updated(function ($enrollment) {
            // Hanya generate jika progress >= 100, belum ada certificate_issued_at, dan ada perubahan progress
            if (
                $enrollment->progress >= 100 &&
                !$enrollment->certificate_issued_at &&
                $enrollment->isDirty('progress')
            ) {

                $enrollment->certificate_issued_at = now();
                $enrollment->saveQuietly(); // pakai saveQuietly agar tidak infinite loop

                GenerateCertificateJob::dispatch($enrollment);
            }
        });
    }
}