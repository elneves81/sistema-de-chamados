<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'short_name',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'comment',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relacionamentos
    public function assets()
    {
        return $this->hasMany(Asset::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Accessors
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);
        
        return implode(', ', $parts);
    }
}
