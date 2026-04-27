<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PointActivity extends Model
{
    protected $table = 'point_activities';

    protected $fillable = [
        'user_id',
        'points',
        'reason',
        'total_after',
        'reference_type',
        'reference_id'
    ];

    protected $casts = [
        'points' => 'integer',
        'total_after' => 'integer',
        'reference_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}