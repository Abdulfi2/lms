<?php
// app/Models/CoursePricing.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CoursePricing extends Model
{
    protected $table = 'course_pricing';

    protected $fillable = [
        'course_id',
        'country_code',
        'price',
        'discount',
        'currency'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}