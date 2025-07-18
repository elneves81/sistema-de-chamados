<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'status',
        'priority',
        'user_id',
        'assigned_to',
        'category_id',
        'due_date',
        'resolved_at',
        'closed_at',
        'attachments'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'attachments' => 'array'
    ];

    // Relacionamentos
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Alias para compatibilidade com assignedTo
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at');
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeInProgress($query)
    {
        return $query->where('status', 'in_progress');
    }

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
                    ->whereNotIn('status', ['resolved', 'closed']);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    // Accessors
    public function getStatusLabelAttribute()
    {
        $labels = [
            'open' => 'Aberto',
            'in_progress' => 'Em Andamento',
            'waiting' => 'Aguardando',
            'resolved' => 'Resolvido',
            'closed' => 'Fechado'
        ];

        return $labels[$this->status] ?? $this->status;
    }

    public function getPriorityLabelAttribute()
    {
        $labels = [
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta',
            'urgent' => 'Urgente'
        ];

        return $labels[$this->priority] ?? $this->priority;
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'open' => 'bg-info',
            'in_progress' => 'bg-warning',
            'waiting' => 'bg-secondary',
            'resolved' => 'bg-success',
            'closed' => 'bg-dark'
        ];

        return $colors[$this->status] ?? 'bg-secondary';
    }

    public function getPriorityColorAttribute()
    {
        $colors = [
            'low' => 'text-success',
            'medium' => 'text-info',
            'high' => 'text-warning',
            'urgent' => 'text-danger'
        ];

        return $colors[$this->priority] ?? 'text-secondary';
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date && 
               $this->due_date->isPast() && 
               !in_array($this->status, ['resolved', 'closed']);
    }

    // Métodos auxiliares
    public function canBeClosedBy(User $user)
    {
        return $user->role === 'admin' || 
               $user->role === 'technician' || 
               $this->user_id === $user->id;
    }

    public function generateTicketNumber()
    {
        return 'TK-' . str_pad($this->id, 6, '0', STR_PAD_LEFT);
    }
}
