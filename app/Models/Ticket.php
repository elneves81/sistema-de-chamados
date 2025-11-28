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
        'support_technician_id',
        'category_id',
        'location_id',
        'local',
        'due_date',
        'resolved_at',
        'resolved_by',
        'closed_at',
        'closed_by',
        'resolution_time',
        'attachments'
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'resolved_at' => 'datetime',
        'closed_at' => 'datetime',
        'resolution_time' => 'decimal:2',
        'attachments' => 'array'
    ];

    /**
     * Boot do modelo para calcular automaticamente o resolution_time
     */
    protected static function booted()
    {
        static::saving(function ($ticket) {
            // Calcula o tempo de resolução quando o ticket for resolvido
            if ($ticket->resolved_at && $ticket->isDirty('resolved_at')) {
                $ticket->resolution_time = round($ticket->created_at->diffInHours($ticket->resolved_at, true), 2);
            }
        });
    }

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

    public function supportTechnician()
    {
        return $this->belongsTo(User::class, 'support_technician_id');
    }

    // Múltiplos técnicos de suporte (novo relacionamento)
    public function supportTechnicians()
    {
        return $this->belongsToMany(User::class, 'ticket_support_technicians')
                    ->withTimestamps()
                    ->withPivot('assigned_at', 'assigned_by')
                    ->orderBy('ticket_support_technicians.created_at');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function resolvedBy()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class)->orderBy('created_at');
    }

    public function activityLogs()
    {
        return $this->hasMany(TicketActivityLog::class)->orderBy('created_at', 'desc');
    }

    public function tags()
    {
        return $this->belongsToMany(TicketTag::class, 'ticket_tag_pivot');
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
