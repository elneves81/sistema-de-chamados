<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiInteraction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_input', 
        'ai_response',
        'interaction_type',
        'context_data',
        'response_time',
        'was_helpful'
    ];

    protected $casts = [
        'ai_response' => 'array',
        'context_data' => 'array',
        'was_helpful' => 'boolean',
        'response_time' => 'integer'
    ];

    /**
     * Relacionamento com usuário
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para interações de chatbot
     */
    public function scopeChatbot($query)
    {
        return $query->where('interaction_type', 'chatbot');
    }

    /**
     * Scope para classificações
     */
    public function scopeClassification($query)
    {
        return $query->where('interaction_type', 'classification');
    }

    /**
     * Scope para hoje
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Accessor para tempo de resposta formatado
     */
    public function getFormattedResponseTimeAttribute()
    {
        if (!$this->response_time) return 'N/A';
        
        return $this->response_time < 1000 
            ? $this->response_time . 'ms'
            : round($this->response_time / 1000, 2) . 's';
    }
}
