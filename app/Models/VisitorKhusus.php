<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorKhusus extends Model
{
    protected $table = 'visitor_khusus';

    protected $fillable = ['article_id', 'user_id', 'ip_address', 'user_agent', 'visited_at'];

    protected $casts = [
        'visited_at' => 'datetime',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
