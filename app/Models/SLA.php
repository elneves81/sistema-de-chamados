<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SLA extends Model
{
    use HasFactory;

    protected $table = 'slas'; // Especifica o nome da tabela

    protected $fillable = [
        'name',
        'description',
        'response_time',
        'resolution_time',
        'is_active',
        'priority_low_modifier',
        'priority_normal_modifier',
        'priority_high_modifier',
        'priority_critical_modifier',
        'business_hours_only'
    ];

    protected $casts = [
        'response_time' => 'integer',
        'resolution_time' => 'integer',
        'is_active' => 'boolean',
        'priority_low_modifier' => 'float',
        'priority_normal_modifier' => 'float',
        'priority_high_modifier' => 'float',
        'priority_critical_modifier' => 'float',
        'business_hours_only' => 'boolean'
    ];

    // Relacionamentos
    public function categories()
    {
        return $this->hasMany(Category::class, 'sla_id');
    }

    public function tickets()
    {
        return $this->hasManyThrough(Ticket::class, Category::class, 'sla_id', 'category_id');
    }

    // Métodos
    public function getResponseTimeFormatted()
    {
        return $this->formatTime($this->response_time);
    }

    public function getResolutionTimeFormatted()
    {
        return $this->formatTime($this->resolution_time);
    }

    public function getResolutionTimeForPriority($priority)
    {
        $modifier = 1; // Padrão sem modificação
        
        switch (strtolower($priority)) {
            case 'low':
                $modifier = $this->priority_low_modifier ?: 1.5;
                break;
            case 'normal':
                $modifier = $this->priority_normal_modifier ?: 1.0;
                break;
            case 'high':
                $modifier = $this->priority_high_modifier ?: 0.75;
                break;
            case 'critical':
                $modifier = $this->priority_critical_modifier ?: 0.5;
                break;
        }

        return $this->resolution_time * $modifier;
    }

    private function formatTime($minutes)
    {
        if ($minutes < 60) {
            return $minutes . ' minutos';
        } elseif ($minutes < 1440) { // menos de 24 horas
            $hours = floor($minutes / 60);
            $min = $minutes % 60;
            return $hours . ' hora' . ($hours > 1 ? 's' : '') . ($min > 0 ? ' e ' . $min . ' minuto' . ($min > 1 ? 's' : '') : '');
        } else {
            $days = floor($minutes / 1440);
            $hours = floor(($minutes % 1440) / 60);
            return $days . ' dia' . ($days > 1 ? 's' : '') . ($hours > 0 ? ' e ' . $hours . ' hora' . ($hours > 1 ? 's' : '') : '');
        }
    }
}
