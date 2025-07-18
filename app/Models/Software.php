<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Software extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'version',
        'manufacturer_id',
        'type',
        'description',
        'is_paid',
        'purchase_date',
        'expiration_date',
        'license_key',
        'license_count',
    ];

    protected $casts = [
        'is_paid' => 'boolean',
        'purchase_date' => 'date',
        'expiration_date' => 'date',
        'license_count' => 'integer',
    ];

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function assets()
    {
        return $this->belongsToMany(Asset::class, 'asset_software');
    }

    public function licenses()
    {
        return $this->hasMany(License::class);
    }

    public function getTotalLicensesAttribute()
    {
        return $this->license_count ?: 0;
    }

    public function getUsedLicensesAttribute()
    {
        return $this->assets()->count();
    }

    public function getAvailableLicensesAttribute()
    {
        if (!$this->license_count) {
            return null; // Licenças ilimitadas
        }
        return max(0, $this->license_count - $this->used_licenses);
    }

    public function getLicenseStatusAttribute()
    {
        if (!$this->is_paid) {
            return 'free';
        }
        
        if (!$this->license_count) {
            return 'unlimited';
        }
        
        if ($this->available_licenses <= 0) {
            return 'depleted';
        }
        
        if ($this->expiration_date && $this->expiration_date->isPast()) {
            return 'expired';
        }
        
        return 'valid';
    }
}
