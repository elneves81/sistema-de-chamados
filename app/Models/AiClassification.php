<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiClassification extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id', 
        'original_category_id',
        'suggested_category_id',
        'confidence_score',
        'classification_data',
        'was_accepted',
        'admin_feedback'
    ];

    protected $casts = [
        'classification_data' => 'array',
        'confidence_score' => 'decimal:4',
        'was_accepted' => 'boolean'
    ];

    /**
     * Relacionamento com ticket
     */
    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com categoria original
     */
    public function originalCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'original_category_id');
    }

    /**
     * Relacionamento com categoria sugerida
     */
    public function suggestedCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'suggested_category_id');
    }

    /**
     * Scope para classificações aceitas
     */
    public function scopeAccepted($query)
    {
        return $query->where('was_accepted', true);
    }

    /**
     * Scope para classificações rejeitadas
     */
    public function scopeRejected($query)
    {
        return $query->where('was_accepted', false);
    }

    /**
     * Scope para hoje
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
