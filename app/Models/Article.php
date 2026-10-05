<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Article extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'subtitle',
        'slug',
        'featured_image',
        'excerpt',
        'content',
        'status',
        'is_featured',
        'published_at',
        'views',
        'likes',
        'shares',
        'allow_comments',
        'meta_data',
        'meta_title',
        'meta_description',
        'og_image',
    ];

    protected $casts = [
        'published_at' => 'datetime',
        'allow_comments' => 'boolean',
        'is_featured' => 'boolean',
        'meta_data' => 'array',
        'views' => 'integer',
        'likes' => 'integer',
        'shares' => 'integer',
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'category_id');
    }

    public function tags()
    {
        return $this->morphToMany(Tag::class, 'taggable', 'taggables');
    }

    public function articleLikes()
    {
        return $this->hasMany(ArticleLike::class);
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class);
    }

    public function visits()
    {
        return $this->hasMany(VisitorKhusus::class);
    }

    public function isLikedBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }

        return $this->articleLikes()->where('user_id', $user->id)->exists();
    }

    // ============ BOOT ============
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title) . '-' . uniqid();
            }
        });
    }

    // ============ SCOPES ============
    /**
     * Scope untuk artikel yang sudah dipublikasikan.
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    /**
     * Scope untuk artikel draft.
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope untuk artikel yang sudah diarsipkan.
     */
    public function scopeArchived($query)
    {
        return $query->where('status', 'archived');
    }

    // ============ ACCESSORS ============
    public function getReadingTimeAttribute()
    {
        $words = str_word_count(strip_tags($this->content));
        $minutes = ceil($words / 200);
        return $minutes . ' menit baca';
    }

    public function getFormattedDateAttribute()
    {
        return $this->published_at ? $this->published_at->format('d F Y') : '-';
    }
}