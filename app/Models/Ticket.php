<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'description',
        'status',
        'priority',
        'closed_at',
    ];

    protected $casts = [
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(TicketCategory::class, 'category_id');
    }

    public function replies()
    {
        return $this->hasMany(TicketReply::class);
    }

    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['baru', 'diproses', 'menunggu_user']);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'baru' => 'Baru',
            'diproses' => 'Diproses',
            'menunggu_user' => 'Menunggu User',
            'selesai' => 'Selesai',
            default => ucfirst($this->status),
        };
    }

    public function priorityLabel(): string
    {
        return match ($this->priority) {
            'tinggi' => 'Tinggi',
            'sedang' => 'Sedang',
            'rendah' => 'Rendah',
            default => ucfirst($this->priority),
        };
    }
}
