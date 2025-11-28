<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_id',
        'user_id',
        'target_user_id',
        'action',
        'description',
        'changes',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'changes' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relacionamento com o ticket
     */
    public function ticket()
    {
        return $this->belongsTo(Ticket::class);
    }

    /**
     * Relacionamento com o usuário que fez a ação
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relacionamento com o usuário afetado (ex: atribuído a)
     */
    public function targetUser()
    {
        return $this->belongsTo(User::class, 'target_user_id');
    }

    /**
     * Registrar uma atividade no ticket
     */
    public static function log($ticketId, $action, $description, $changes = null, $targetUserId = null)
    {
        return self::create([
            'ticket_id' => $ticketId,
            'user_id' => auth()->id(),
            'target_user_id' => $targetUserId,
            'action' => $action,
            'description' => $description,
            'changes' => $changes,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Ícone baseado no tipo de ação
     */
    public function getIconAttribute()
    {
        return match($this->action) {
            'created' => 'bi-plus-circle-fill text-success',
            'updated' => 'bi-pencil-fill text-primary',
            'assigned' => 'bi-person-check-fill text-info',
            'transferred' => 'bi-arrow-left-right text-warning',
            'commented' => 'bi-chat-left-text-fill text-secondary',
            'status_changed' => 'bi-arrow-repeat text-primary',
            'priority_changed' => 'bi-exclamation-triangle-fill text-warning',
            'closed' => 'bi-check-circle-fill text-success',
            'reopened' => 'bi-arrow-counterclockwise text-info',
            default => 'bi-circle-fill text-muted',
        };
    }
}
