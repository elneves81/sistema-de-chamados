<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'is_super_admin',
        'phone',
        'department',
        'location_id', // Localização do usuário
        'employee_id', // ID do funcionário (para integração com AD)
        'ldap_dn', // Distinguished Name do LDAP/AD
        'ldap_upn', // User Principal Name do LDAP/AD
        'auth_via_ldap', // Autenticação via LDAP habilitada
        'is_active',
        'last_login_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
        'is_super_admin' => 'boolean',
        'auth_via_ldap' => 'boolean',
        'last_login_at' => 'datetime'
    ];

    // Relacionamentos
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function assignedTickets()
    {
        return $this->hasMany(Ticket::class, 'assigned_to');
    }

    public function comments()
    {
        return $this->hasMany(TicketComment::class);
    }

    // Relacionamentos adicionais
    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'assigned_to');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    // Relacionamentos para permissões
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'user_permissions')
                    ->withPivot('granted')
                    ->withTimestamps();
    }

    // Métodos para verificar permissões
    public function hasPermission($permission)
    {
        // Super admin tem todas as permissões
        if ($this->is_super_admin) {
            return true;
        }

        $userPermission = $this->permissions()->where('name', $permission)->first();
        
        if ($userPermission) {
            return $userPermission->pivot->granted;
        }

        // Permissões padrão baseadas no role (backwards compatibility)
        return $this->hasDefaultPermission($permission);
    }

    private function hasDefaultPermission($permission)
    {
        $rolePermissions = [
            'customer' => [
                'tickets.view.own',
                'tickets.create',
                'tickets.edit.own'
            ],
            'technician' => [
                'tickets.view.all',
                'tickets.create',
                'tickets.edit.all',
                'tickets.assign',
                'tickets.close',
                'dashboard.view',
                'dashboard.metrics',
                'categories.view'
            ],
            'admin' => [
                'tickets.view.all',
                'tickets.create',
                'tickets.edit.all',
                'tickets.assign',
                'tickets.close',
                'dashboard.view',
                'dashboard.metrics',
                'dashboard.export',
                'board.view',
                'users.view',
                'users.create',
                'users.edit',
                'users.delete',
                'categories.view',
                'categories.manage',
                'system.ldap',
                'system.monitoring'
            ]
        ];

        return in_array($permission, $rolePermissions[$this->role] ?? []);
    }

    public function grantPermission($permission)
    {
        $permissionModel = Permission::where('name', $permission)->first();
        if ($permissionModel) {
            $this->permissions()->syncWithoutDetaching([
                $permissionModel->id => ['granted' => true]
            ]);
        }
    }

    public function revokePermission($permission)
    {
        $permissionModel = Permission::where('name', $permission)->first();
        if ($permissionModel) {
            $this->permissions()->syncWithoutDetaching([
                $permissionModel->id => ['granted' => false]
            ]);
        }
    }

    public function isSuperAdmin()
    {
        return $this->is_super_admin;
    }
}
