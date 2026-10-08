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

    public const TYPE_LABELS = [
        'webinar' => 'Webinar',
        'workshop' => 'Workshop',
        'parenting' => 'Parenting',
        'live_class' => 'Live Class',
        'zoom_meeting' => 'Zoom Meeting',
        'seminar' => 'Seminar',
    ];

    public const CATEGORY_LABELS = [
        'education' => 'Pendidikan',
        'parenting' => 'Parenting',
        'technology' => 'Teknologi',
        'business' => 'Bisnis',
        'health' => 'Kesehatan',
        'other' => 'Lainnya',
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

    /**
     * Status siklus hidup untuk tampilan: draft, cancelled, upcoming, ongoing, finished.
     * Event yang statusnya 'published' dihitung dari jadwal, bukan hanya kolom status.
     */
    public function getLifecycleStatusAttribute(): string
    {
        if (in_array($this->status, ['draft', 'cancelled'], true)) {
            return $this->status;
        }

        if ($this->status === 'completed') {
            return 'finished';
        }

        if ($this->start_time->isFuture()) {
            return 'upcoming';
        }

        // Tanpa end_time (sesi tanpa jadwal selesai pasti), jangan anggap selesai
        // hanya karena start_time sudah lewat — tetap 'ongoing' sampai ada info lain.
        if (!$this->end_time) {
            return 'ongoing';
        }

        return $this->end_time->isPast() ? 'finished' : 'ongoing';
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