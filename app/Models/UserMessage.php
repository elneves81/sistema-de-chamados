<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class UserMessage extends Model
{
    use HasFactory;

    protected $table = 'user_messages';

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'subject',
        'message',
        'priority',
        'is_read',
        'email_sent',
        'read_at',
        'email_sent_at',
        'attachments'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'email_sent' => 'boolean',
        'read_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'attachments' => 'array'
    ];

    // Relacionamentos
    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    // Scopes
    public function scopeUnread(Builder $query)
    {
        return $query->where('is_read', false);
    }

    public function scopeForUser(Builder $query, $userId)
    {
        return $query->where('to_user_id', $userId);
    }

    public function scopeFromUser(Builder $query, $userId)
    {
        return $query->where('from_user_id', $userId);
    }

    public function scopeByPriority(Builder $query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Métodos auxiliares
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    public function markEmailSent()
    {
        $this->update([
            'email_sent' => true,
            'email_sent_at' => now()
        ]);
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'success',
            'medium' => 'warning',
            'high' => 'danger',
            'urgent' => 'dark'
        ];

        return $colors[$this->priority] ?? 'secondary';
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            'low' => 'Baixa',
            'medium' => 'Média', 
            'high' => 'Alta',
            'urgent' => 'Urgente'
        ];

        return $labels[$this->priority] ?? 'Média';
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
