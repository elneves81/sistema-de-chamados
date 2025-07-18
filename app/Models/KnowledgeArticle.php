<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class KnowledgeArticle extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'content',
        'excerpt',
        'status',
        'category_id',
        'author_id',
        'tags',
        'views',
        'rating',
        'rating_count',
        'is_featured',
        'is_public',
        'published_at'
    ];

    protected $casts = [
        'tags' => 'json',
        'views' => 'integer',
        'rating' => 'decimal:2',
        'rating_count' => 'integer',
        'is_featured' => 'boolean',
        'is_public' => 'boolean',
        'published_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($article) {
            if (empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });

        static::updating(function ($article) {
            if ($article->isDirty('title') && empty($article->slug)) {
                $article->slug = Str::slug($article->title);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(KnowledgeCategory::class, 'category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(KnowledgeArticleRating::class, 'article_id');
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopePopular($query)
    {
        return $query->orderBy('views', 'desc');
    }

    public function scopeHighRated($query)
    {
        return $query->orderBy('rating', 'desc');
    }

    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    // Accessors
    public function getFormattedTagsAttribute(): array
    {
        return $this->tags ?? [];
    }

    public function getShortExcerptAttribute(): string
    {
        if (!empty($this->excerpt)) {
            return $this->excerpt;
        }
        
        $excerpt = strip_tags($this->content);
        return strlen($excerpt) > 200 ? substr($excerpt, 0, 200) . '...' : $excerpt;
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // Helper methods
    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published' && $this->is_public;
    }

    public function updateRating(): void
    {
        $ratings = $this->ratings();
        $this->update([
            'rating' => $ratings->avg('rating') ?? 0,
            'rating_count' => $ratings->count()
        ]);
    }
}
