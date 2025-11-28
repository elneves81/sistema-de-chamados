<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Tickets
            [
                'name' => 'tickets.view.own',
                'display_name' => 'Ver próprios chamados',
                'description' => 'Permite visualizar apenas os chamados criados pelo próprio usuário',
                'module' => 'tickets'
            ],
            [
                'name' => 'tickets.view.all',
                'display_name' => 'Ver todos os chamados',
                'description' => 'Permite visualizar todos os chamados do sistema',
                'module' => 'tickets'
            ],
            [
                'name' => 'tickets.create',
                'display_name' => 'Criar chamados',
                'description' => 'Permite criar novos chamados',
                'module' => 'tickets'
            ],
            [
                'name' => 'tickets.edit.own',
                'display_name' => 'Editar próprios chamados',
                'description' => 'Permite editar chamados criados pelo próprio usuário',
                'module' => 'tickets'
            ],
            [
                'name' => 'tickets.edit.all',
                'display_name' => 'Editar todos os chamados',
                'description' => 'Permite editar qualquer chamado',
                'module' => 'tickets'
            ],
            [
                'name' => 'tickets.assign',
                'display_name' => 'Atribuir chamados',
                'description' => 'Permite atribuir chamados a técnicos',
                'module' => 'tickets'
            ],
            [
                'name' => 'tickets.close',
                'display_name' => 'Fechar chamados',
                'description' => 'Permite fechar chamados',
                'module' => 'tickets'
            ],
            
            // Dashboard e relatórios
            [
                'name' => 'dashboard.view',
                'display_name' => 'Ver dashboard',
                'description' => 'Permite acessar o dashboard principal',
                'module' => 'dashboard'
            ],
            [
                'name' => 'dashboard.metrics',
                'display_name' => 'Ver métricas',
                'description' => 'Permite visualizar métricas e relatórios',
                'module' => 'dashboard'
            ],
            [
                'name' => 'dashboard.export',
                'display_name' => 'Exportar relatórios',
                'description' => 'Permite exportar relatórios em PDF/Excel',
                'module' => 'dashboard'
            ],
            
            // Painel TV
            [
                'name' => 'board.view',
                'display_name' => 'Ver painel TV',
                'description' => 'Permite acessar o painel de monitoramento TV',
                'module' => 'board'
            ],
            
            // Usuários
            [
                'name' => 'users.view',
                'display_name' => 'Ver usuários',
                'description' => 'Permite visualizar lista de usuários',
                'module' => 'users'
            ],
            [
                'name' => 'users.create',
                'display_name' => 'Criar usuários',
                'description' => 'Permite criar novos usuários',
                'module' => 'users'
            ],
            [
                'name' => 'users.edit',
                'display_name' => 'Editar usuários',
                'description' => 'Permite editar informações de usuários',
                'module' => 'users'
            ],
            [
                'name' => 'users.delete',
                'display_name' => 'Excluir usuários',
                'description' => 'Permite excluir usuários',
                'module' => 'users'
            ],
            [
                'name' => 'users.permissions',
                'display_name' => 'Gerenciar permissões',
                'description' => 'Permite definir permissões de usuários (apenas super admin)',
                'module' => 'users'
            ],
            
            // Categorias
            [
                'name' => 'categories.view',
                'display_name' => 'Ver categorias',
                'description' => 'Permite visualizar categorias',
                'module' => 'categories'
            ],
            [
                'name' => 'categories.manage',
                'display_name' => 'Gerenciar categorias',
                'description' => 'Permite criar, editar e excluir categorias',
                'module' => 'categories'
            ],
            
            // Máquinas / Inventário
            [
                'name' => 'machines.view',
                'display_name' => 'Ver inventário',
                'description' => 'Permite visualizar o inventário de máquinas',
                'module' => 'machines'
            ],
            [
                'name' => 'machines.manage',
                'display_name' => 'Gerenciar inventário',
                'description' => 'Permite criar, editar e excluir máquinas do inventário',
                'module' => 'machines'
            ],
            
            // Sistema
            [
                'name' => 'system.ldap',
                'display_name' => 'LDAP/Active Directory',
                'description' => 'Permite gerenciar integração com LDAP/AD',
                'module' => 'system'
            ],
            [
                'name' => 'system.monitoring',
                'display_name' => 'Monitoramento do sistema',
                'description' => 'Permite acessar ferramentas de monitoramento',
                'module' => 'system'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }
    }
}
