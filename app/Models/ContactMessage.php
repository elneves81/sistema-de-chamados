<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email', 
        'subject',
        'message',
        'type',
        'status',
        'assigned_to',
        'user_id',
        'responded_at',
        'admin_notes'
    ];

    protected $casts = [
        'responded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Relacionamento com usuário que enviou (se logado)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com admin responsável
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scopes
     */
    public function scopePendente($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeEmergencia($query)
    {
        return $query->where('type', 'emergencia');
    }

    public function scopeRecentes($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Marcar como resolvida
     */
    public function marcarComoResolvida($adminId = null, $notes = null)
    {
        $this->update([
            'status' => 'resolvido',
            'responded_at' => now(),
            'assigned_to' => $adminId,
            'admin_notes' => $notes
        ]);
    }

    /**
     * Atribuir a um admin
     */
    public function atribuirPara($adminId)
    {
        $this->update([
            'assigned_to' => $adminId,
            'status' => 'em_andamento'
        ]);
    }

    /**
     * Badges de prioridade por tipo
     */
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'emergencia' => 'danger',
            'suporte' => 'warning', 
            'duvida' => 'info',
            'sugestao' => 'success'
        ];
        
        return $badges[$this->type] ?? 'secondary';
    }

    /**
     * Labels em português
     */
    public function getTypeLabel()
    {
        $labels = [
            'emergencia' => 'Emergência',
            'suporte' => 'Suporte Técnico',
            'duvida' => 'Dúvida',
            'sugestao' => 'Sugestão'
        ];
        
        return $labels[$this->type] ?? 'Outro';
    }

    public function getStatusLabel()
    {
        $labels = [
            'pendente' => 'Pendente',
            'em_andamento' => 'Em Andamento',
            'resolvido' => 'Resolvido',
            'arquivado' => 'Arquivado'
        ];
        
        return $labels[$this->status] ?? 'Desconhecido';
    }
}
