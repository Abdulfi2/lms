<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportSchedule extends Model
{
    protected $fillable = ['user_id', 'title', 'type', 'date', 'notes'];

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'jadwal_support' => 'Jadwal Support',
            'maintenance' => 'Maintenance',
            'meeting' => 'Meeting',
            'kegiatan' => 'Kegiatan',
            default => ucfirst($this->type),
        };
    }

    public function typeColor(): string
    {
        return match ($this->type) {
            'jadwal_support' => '#22C55E',
            'maintenance' => '#F97316',
            'meeting' => '#3B82F6',
            'kegiatan' => '#EF4444',
            default => '#9CA3AF',
        };
    }
}
