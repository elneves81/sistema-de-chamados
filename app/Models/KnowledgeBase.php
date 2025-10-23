<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KnowledgeBase extends Model
{
    use HasFactory;

    protected $table = 'knowledge_base';

    protected $fillable = [
        'title',
        'content',
        'excerpt',
        'category_id',
        'author_id',
        'status',
        'is_public',
        'is_featured',
        'tags',
        'views',
        'published_at'
    ];

    protected $casts = [
        'tags' => 'array',
        'is_public' => 'boolean',
        'is_featured' => 'boolean',
        'published_at' => 'datetime',
        'views' => 'integer'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function incrementViews()
    {
        $this->increment('views');
    }

    public function getRouteKeyName()
    {
        return 'id';
    }
}
