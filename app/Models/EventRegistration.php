<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    protected $fillable = [
        'event_id',
        'user_id',
        'name',
        'email',
        'phone',
        'notes',
        'status',
        'amount_paid',
        'payment_proof',
        'checked_in_at'
    ];

    protected $casts = [
        'amount_paid' => 'decimal:2',
        'checked_in_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}