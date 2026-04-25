<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'user_id',
        'course_id',
        'certificate_number',
        'url',
        'file_path',
        'issued_at',
        'expires_at',
        'is_verified',
        'verification_code',
        'metadata'
    ];

    protected $casts = [
        'issued_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_verified' => 'boolean',
        'metadata' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    // Auto generate certificate number & verification code
    protected static function booted()
    {
        static::creating(function ($certificate) {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = 'CERT-' . strtoupper(uniqid());
            }
            if (empty($certificate->verification_code)) {
                $certificate->verification_code = md5(uniqid() . $certificate->user_id . $certificate->course_id);
            }
        });
    }
}