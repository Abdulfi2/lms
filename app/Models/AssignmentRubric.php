<?php
// app/Models/AssignmentRubric.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentRubric extends Model
{
    protected $table = 'assignment_rubrics';

    protected $fillable = [
        'assignment_id',
        'criteria',
        'description',
        'max_points',
        'weight',
        'order'
    ];

    protected $casts = [
        'max_points' => 'integer',
        'weight' => 'float',
        'order' => 'integer',
    ];

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }
}