<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'platform',
        'token',
        'device_info',
        'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
        'device_info' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
