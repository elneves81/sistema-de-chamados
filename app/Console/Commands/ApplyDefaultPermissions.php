<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Permission;

class ApplyDefaultPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:apply-default';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Aplica permissões padrão baseadas nos roles dos usuários existentes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Aplicando permissões padrão para usuários existentes...');

        $defaultPermissions = [
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

        $users = User::where('is_super_admin', false)->get();
        $progressBar = $this->output->createProgressBar($users->count());

        foreach ($users as $user) {
            $rolePermissions = $defaultPermissions[$user->role] ?? [];
            
            // Remove permissões existentes
            $user->permissions()->detach();

            // Aplica permissões padrão
            foreach ($rolePermissions as $permissionName) {
                $permission = Permission::where('name', $permissionName)->first();
                if ($permission) {
                    $user->permissions()->attach($permission->id, ['granted' => true]);
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();
        $this->info("Permissões padrão aplicadas para {$users->count()} usuários.");
        $this->warn('Super administradores não foram afetados - eles têm acesso total ao sistema.');
    }
}
