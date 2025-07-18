<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:users.permissions');
    }

    /**
     * Lista todos os usuários para gerenciar suas permissões
     */
    public function index(Request $request)
    {
        $query = User::with('permissions')->where('is_super_admin', false);
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        $users = $query->paginate(15);
        $roles = ['customer', 'technician', 'admin'];

        return view('admin.permissions.index', compact('users', 'roles'));
    }

    /**
     * Mostra e edita as permissões de um usuário específico
     */
    public function edit(User $user)
    {
        if ($user->is_super_admin) {
            return redirect()->route('admin.permissions.index')
                           ->with('error', 'Não é possível editar permissões de super administradores.');
        }

        $permissions = Permission::all()->groupBy('module');
        $userPermissions = $user->permissions->pluck('pivot.granted', 'name')->toArray();

        return view('admin.permissions.edit', compact('user', 'permissions', 'userPermissions'));
    }

    /**
     * Atualiza as permissões de um usuário
     */
    public function update(Request $request, User $user)
    {
        if ($user->is_super_admin) {
            return redirect()->route('admin.permissions.index')
                           ->with('error', 'Não é possível editar permissões de super administradores.');
        }

        $permissions = $request->input('permissions', []);
        
        // Remove todas as permissões existentes
        $user->permissions()->detach();

        // Adiciona as novas permissões
        foreach ($permissions as $permissionName => $granted) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $user->permissions()->attach($permission->id, [
                    'granted' => $granted === '1'
                ]);
            }
        }

        return redirect()->route('admin.permissions.index')
                        ->with('success', 'Permissões atualizadas com sucesso!');
    }

    /**
     * Aplica permissões padrão baseadas no role do usuário
     */
    public function applyDefaultPermissions(User $user)
    {
        if ($user->is_super_admin) {
            return redirect()->route('admin.permissions.index')
                           ->with('error', 'Super administradores já possuem todas as permissões.');
        }

        $defaultPermissions = $this->getDefaultPermissionsByRole($user->role);
        
        // Remove todas as permissões existentes
        $user->permissions()->detach();

        // Aplica permissões padrão
        foreach ($defaultPermissions as $permissionName) {
            $permission = Permission::where('name', $permissionName)->first();
            if ($permission) {
                $user->permissions()->attach($permission->id, ['granted' => true]);
            }
        }

        return redirect()->route('admin.permissions.edit', $user)
                        ->with('success', 'Permissões padrão aplicadas com sucesso!');
    }

    private function getDefaultPermissionsByRole($role)
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

        return $rolePermissions[$role] ?? [];
    }
}
