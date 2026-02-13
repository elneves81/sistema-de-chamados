<?php

namespace App\Observers;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Support\Facades\Log;

class UserObserver
{
    /**
     * Handle the User "created" event.
     * Sincroniza permissões de inventário quando um usuário é criado
     */
    public function created(User $user): void
    {
        $this->syncInventoryPermissions($user);
    }

    /**
     * Handle the User "updated" event.
     * Sincroniza permissões de inventário quando o role do usuário muda
     */
    public function updated(User $user): void
    {
        // Verificar se o role mudou
        if ($user->wasChanged('role')) {
            $this->syncInventoryPermissions($user);
        }
    }

    /**
     * Sincroniza as permissões de inventário e almoxarifado baseado no role do usuário
     * 
     * - Técnicos e Admins recebem automaticamente acesso ao inventário (machines.*, inventory.*) e almoxarifado (stock.*)
     * - Isso inclui: criar/editar máquinas, tablets, assinaturas, gerenciar almoxarifado
     * - Outros roles têm essas permissões removidas (exceto super admins)
     */
    private function syncInventoryPermissions(User $user): void
    {
        try {
            // Buscar todas as permissões de inventário (machines, inventory) e almoxarifado (stock)
            $inventoryPermissions = Permission::whereIn('module', ['machines', 'inventory', 'stock'])->get();
            
            if ($inventoryPermissions->isEmpty()) {
                Log::warning('Nenhuma permissão de inventário encontrada para sincronizar', [
                    'user_id' => $user->id,
                    'user_role' => $user->role,
                    'message' => 'Verifique se as permissões foram criadas: php artisan db:seed --class=PermissionSeeder'
                ]);
                return;
            }
            
            if ($user->role === 'technician' || $user->role === 'admin') {
                // Usuário é técnico ou admin: garantir que tem todas as permissões de inventário
                foreach ($inventoryPermissions as $permission) {
                    $user->grantPermission($permission->name);
                }
                
                Log::info('Permissões de inventário concedidas automaticamente', [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_role' => $user->role,
                    'permissions_granted' => $inventoryPermissions->pluck('name')->toArray()
                ]);
            } else {
                // Usuário não é mais técnico: remover permissões de inventário
                // (apenas se não for super admin)
                if (!$user->is_super_admin) {
                    foreach ($inventoryPermissions as $permission) {
                        $user->revokePermission($permission->name);
                    }
                    
                    Log::info('Permissões de inventário revogadas automaticamente', [
                        'user_id' => $user->id,
                        'user_name' => $user->name,
                        'user_role' => $user->role,
                        'permissions_revoked' => $inventoryPermissions->pluck('name')->toArray()
                    ]);
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao sincronizar permissões de inventário', [
                'user_id' => $user->id,
                'user_role' => $user->role,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
