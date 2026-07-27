<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    /**
     * Cek apakah kupon bisa dipakai untuk course & jumlah pembelian tertentu.
     * Mengembalikan pesan error, atau null jika valid.
     */
    public function validationError(Course $course, float $amount): ?string
    {
        if (!$this->is_active) {
            return 'Kupon tidak aktif.';
        }

        if ($this->starts_at && now()->lt($this->starts_at)) {
            return 'Kupon belum berlaku.';
        }

        if ($this->expires_at && now()->gt($this->expires_at)) {
            return 'Kupon sudah kedaluwarsa.';
        }

        if ($this->max_uses !== null && $this->used_count >= $this->max_uses) {
            return 'Kupon sudah mencapai batas penggunaan.';
        }

        if ($this->course_id !== null && $this->course_id !== $course->id) {
            return 'Kupon tidak berlaku untuk kursus ini.';
        }

        if ($this->min_purchase !== null && $amount < $this->min_purchase) {
            return 'Minimal pembelian Rp ' . number_format($this->min_purchase, 0, ',', '.') . ' untuk memakai kupon ini.';
        }

        return null;
    }

    public function calculateDiscount(float $amount): float
    {
        $discount = $this->type === 'percentage'
            ? $amount * ($this->value / 100)
            : $this->value;

        return min($discount, $amount);
    }
}
