<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['name', 'role', 'content', 'rating', 'order', 'is_active'];

    protected $casts = [
        'rating' => 'integer',
        'order' => 'integer',
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('id');
    }

    public function initial(): string
    {
        return strtoupper(mb_substr($this->name, 0, 1));
    }
}
