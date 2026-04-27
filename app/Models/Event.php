<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organizer_id',
        'title',
        'slug',
        'description',
        'type',
        'image',
        'category',
        'speaker',
        'speaker_bio',
        'speaker_photo',
        'location',
        'zoom_link',
        'meeting_id',
        'passcode',
        'start_time',
        'end_time',
        'max_participants',
        'price_type',
        'price',
        'status',
        'is_featured',
        'total_registrations'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'is_featured' => 'boolean',
        'price' => 'decimal:2',
    ];

    public function organizer()
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function getRemainingSlotsAttribute()
    {
        if (!$this->max_participants)
            return null;
        return $this->max_participants - $this->total_registrations;
    }

    public function getFormattedPriceAttribute()
    {
        if ($this->price_type === 'free' || $this->price <= 0) {
            return 'Gratis';
        }
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function getZoomMeetingUrlAttribute()
    {
        if ($this->zoom_link)
            return $this->zoom_link;
        if ($this->meeting_id) {
            return "https://zoom.us/j/{$this->meeting_id}" . ($this->passcode ? "?pwd={$this->passcode}" : "");
        }
        return null;
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($event) {
            $event->slug = Str::slug($event->title) . '-' . uniqid();
        });
    }
}