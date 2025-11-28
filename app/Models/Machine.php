<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Machine extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'patrimonio',
        'numero_serie',
        'modelo',
        'marca',
        'tipo',
        'descricao',
        'user_id',
        'processador',
        'memoria_ram',
        'armazenamento',
        'sistema_operacional',
        'data_aquisicao',
        'valor_aquisicao',
        'status',
        'observacoes',
        // Campos de contrato/licitação
        'contrato_licitacao',
        'numero_licitacao',
        // Campos de troca
        'is_troca',
        'patrimonio_substituido',
        'motivo_troca',
        // Campos de entrega/recebimento
        'recebedor_id',
        'data_entrega',
        'assinatura_digital',
        'nome_legivel_assinatura',
        'assinatura_status',
        'assinatura_validada_em',
        'assinatura_validada_por',
        'assinatura_usuario_validador',
        'assinatura_validada_por_terceiro',
        'ip_entrega',
        'entregue_por_id',
        'observacoes_entrega',
    ];

    protected $casts = [
        'data_aquisicao' => 'date',
        'data_entrega' => 'datetime',
        'assinatura_validada_em' => 'datetime',
        'valor_aquisicao' => 'decimal:2',
        'is_troca' => 'boolean',
        'assinatura_validada_por_terceiro' => 'boolean',
    ];

    /**
     * Relacionamento com usuário vinculado
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relacionamento com quem recebeu a máquina
     */
    public function recebedor()
    {
        return $this->belongsTo(User::class, 'recebedor_id');
    }

    /**
     * Relacionamento com técnico que entregou
     */
    public function entregador()
    {
        return $this->belongsTo(User::class, 'entregue_por_id');
    }

    /**
     * Relacionamento com quem validou a assinatura
     */
    public function validadorAssinatura()
    {
        return $this->belongsTo(User::class, 'assinatura_validada_por');
    }

    /**
     * Scopes para filtros
     */
    public function scopeAtivas($query)
    {
        return $query->where('status', 'ativo');
    }

    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Accessors
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'ativo' => '<span class="badge bg-success">Ativo</span>',
            'inativo' => '<span class="badge bg-secondary">Inativo</span>',
            'manutencao' => '<span class="badge bg-warning">Manutenção</span>',
            'descartado' => '<span class="badge bg-danger">Descartado</span>',
        ];

        return $badges[$this->status] ?? '';
    }

    public function getTipoBadgeAttribute()
    {
        $badges = [
            'desktop' => '<span class="badge bg-primary"><i class="bi bi-pc-display"></i> Desktop</span>',
            'notebook' => '<span class="badge bg-info"><i class="bi bi-laptop"></i> Notebook</span>',
            'servidor' => '<span class="badge bg-dark"><i class="bi bi-server"></i> Servidor</span>',
            'monitor' => '<span class="badge bg-success"><i class="bi bi-display"></i> Monitor</span>',
            'impressora' => '<span class="badge bg-secondary"><i class="bi bi-printer"></i> Impressora</span>',
        ];

        return $badges[$this->tipo] ?? '';
    }

    public function getAssinaturaStatusBadgeAttribute()
    {
        $badges = [
            'nao_requerida' => '<span class="badge bg-secondary"><i class="bi bi-slash-circle"></i> Não Requerida</span>',
            'pendente' => '<span class="badge bg-warning"><i class="bi bi-clock-history"></i> Assinatura Pendente</span>',
            'validada' => '<span class="badge bg-success"><i class="bi bi-check-circle-fill"></i> Assinatura Validada</span>',
        ];

        return $badges[$this->assinatura_status] ?? '';
    }
}
