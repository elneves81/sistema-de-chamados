<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketTag extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color', 
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relacionamentos
    public function tickets()
    {
        return $this->belongsToMany(Ticket::class, 'ticket_tag_pivot');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
