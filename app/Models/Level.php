<?php
// app/Models/Level.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Level extends Model
{
    protected $fillable = ['name', 'level_number', 'points_required', 'icon', 'description'];
}