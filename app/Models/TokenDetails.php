<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TokenDetails extends Model
{
    protected $fillable = [
        'model_token',
        'email',
        'token',
        'token_type',
        'expires_in',
        'refresh_token',
        'scope',
    ];

    protected $casts = [
        'expires_in' => 'datetime',
    ];
}
