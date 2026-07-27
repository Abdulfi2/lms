<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enrollment extends Model
{
    use HasFactory;

    protected $table = 'enrollments';

    protected $fillable = [
        'user_id',
        'course_id',
        'coupon_id',
        'amount_paid',
        'discount_amount',
        'payment_status',
        'refund_reason',
        'refunded_at',
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

    protected $casts = [
        'enrolled_at' => 'datetime',
        'completed_at' => 'datetime',
        'expires_at' => 'datetime',
        'certificate_issued_at' => 'datetime',
        'refunded_at' => 'datetime',
        'rating_given' => 'boolean',
        'review_given' => 'boolean',
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

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}