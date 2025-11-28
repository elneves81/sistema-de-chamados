<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Ramal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ramais';

    protected $fillable = [
        'departamento',
        'descricao',
        'ramal',
    ];

    /**
     * Scope para buscar por departamento
     */
    public function scopeByDepartamento($query, $departamento)
    {
        return $query->where('departamento', 'like', "%{$departamento}%");
    }

    /**
     * Scope para buscar por ramal
     */
    public function scopeByRamal($query, $ramal)
    {
        return $query->where('ramal', 'like', "%{$ramal}%");
    }
}
