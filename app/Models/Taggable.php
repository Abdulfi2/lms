<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Taggable extends Model
{
    protected $table = 'taggables';
    protected $fillable = ['tag_id', 'taggable_id', 'taggable_type'];

    public function tag()
    {
        return $this->belongsTo(Tag::class);
    }

    public function taggable()
    {
        return $this->morphTo();
    }
}