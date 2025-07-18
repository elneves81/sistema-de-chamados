<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class License extends Model
{
    use HasFactory;

    protected $fillable = [
        'software_id',
        'license_key',
        'purchase_date',
        'expiration_date',
        'seats',
        'price',
        'notes',
        'vendor',
        'order_number',
        'is_active',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'expiration_date' => 'date',
        'seats' => 'integer',
        'price' => 'float',
        'is_active' => 'boolean',
    ];

    public function software()
    {
        return $this->belongsTo(Software::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'license_id');
    }

    public function getUsedSeatsAttribute()
    {
        return $this->assets()->count();
    }

    public function getAvailableSeatsAttribute()
    {
        return max(0, $this->seats - $this->used_seats);
    }

    public function getIsExpiredAttribute()
    {
        if (!$this->expiration_date) {
            return false;
        }

        return $this->expiration_date->isPast();
    }

    public function getStatusAttribute()
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->is_expired) {
            return 'expired';
        }

        if ($this->available_seats <= 0) {
            return 'depleted';
        }

        return 'valid';
    }
}
