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

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function checkAndGenerateCertificate()
    {
        if ($this->progress >= 100 && !$this->certificate_issued_at) {
            $this->certificate_issued_at = now();
            $this->save();

            // Dispatch job untuk generate PDF
            GenerateCertificateJob::dispatch($this);
        }
    }

    protected static function booted()
    {
        static::updated(function ($enrollment) {
            if ($enrollment->progress >= 100 && !$enrollment->certificate_issued_at) {
                $enrollment->certificate_issued_at = now();
                $enrollment->save();
                GenerateCertificateJob::dispatch($enrollment);
            }
        });
    }
}