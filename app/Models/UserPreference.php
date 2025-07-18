<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'theme',
        'sidebar_collapsed',
        'notifications_enabled',
        'email_notifications',
        'language',
        'items_per_page',
        'dashboard_widgets'
    ];

    protected $casts = [
        'sidebar_collapsed' => 'boolean',
        'notifications_enabled' => 'boolean',
        'email_notifications' => 'boolean',
        'items_per_page' => 'integer',
        'dashboard_widgets' => 'json'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getWidgetEnabledAttribute($widget)
    {
        if (!$this->dashboard_widgets) {
            return true; // Por padrão, todos os widgets estão ativos
        }

        $widgets = json_decode($this->dashboard_widgets, true);
        return isset($widgets[$widget]) ? (bool)$widgets[$widget] : true;
    }
}
