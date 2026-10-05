<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArticleComment extends Model
{
    protected $fillable = ['article_id', 'user_id', 'content'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
