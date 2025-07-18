<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KnowledgeArticleRating extends Model
{
    use HasFactory;

    protected $fillable = [
        'article_id',
        'user_id',
        'rating',
        'comment'
    ];

    protected $casts = [
        'rating' => 'integer'
    ];

    public function article(): BelongsTo
    {
        return $this->belongsTo(KnowledgeArticle::class, 'article_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function booted()
    {
        static::created(function ($rating) {
            $rating->article->updateRating();
        });

        static::updated(function ($rating) {
            $rating->article->updateRating();
        });

        static::deleted(function ($rating) {
            $rating->article->updateRating();
        });
    }
}
