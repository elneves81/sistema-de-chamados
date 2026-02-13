<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Carrega configuração do Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "SINCRONIZAR PERMISSÕES DE INVENTÁRIO PARA TODOS OS TÉCNICOS\n";
echo "========================================\n\n";

try {
    // Buscar permissões de inventário
    $inventoryPermissions = \App\Models\Permission::whereIn('module', ['machines', 'inventory', 'stock'])->get();
    
    if ($inventoryPermissions->isEmpty()) {
        echo "❌ ERRO: Nenhuma permissão de inventário encontrada!\n";
        echo "   Execute: php artisan db:seed --class=PermissionSeeder\n";
        exit(1);
    }
    
    echo "✓ Permissões de inventário encontradas: {$inventoryPermissions->count()}\n";
    foreach ($inventoryPermissions as $perm) {
        echo "  - [{$perm->module}] {$perm->name}\n";
    }
    echo "\n";
    
    // Buscar todos os técnicos e admins
    $users = \App\Models\User::whereIn('role', ['technician', 'admin'])->get();
    
    if ($users->isEmpty()) {
        echo "⚠️  Nenhum técnico ou admin encontrado!\n";
        exit(0);
    }
    
    echo "Processando {$users->count()} usuários (técnicos e admins)...\n\n";
    
    $granted = 0;
    $alreadyHad = 0;
    $errors = 0;
    
    foreach ($users as $user) {
        echo "Usuário: {$user->name} ({$user->email}) - Role: {$user->role}\n";
        
        foreach ($inventoryPermissions as $permission) {
            try {
                // Verificar se já tem a permissão
                $hasPermission = DB::table('user_permissions')
                    ->where('user_id', $user->id)
                    ->where('permission_id', $permission->id)
                    ->exists();
                
                if (!$hasPermission) {
                    // Atribuir permissão usando o método do model
                    $user->grantPermission($permission->name);
                    $granted++;
                    echo "  ✓ Concedida: {$permission->name}\n";
                } else {
                    // Garantir que está habilitada (granted = true)
                    DB::table('user_permissions')
                        ->where('user_id', $user->id)
                        ->where('permission_id', $permission->id)
                        ->update(['granted' => true]);
                    $alreadyHad++;
                }
            } catch (\Exception $e) {
                echo "  ❌ Erro ao processar {$permission->name}: {$e->getMessage()}\n";
                $errors++;
            }
        }
        
        echo "\n";
    }
    
    echo "========================================\n";
    echo "RESULTADO DA SINCRONIZAÇÃO\n";
    echo "========================================\n";
    echo "Usuários processados: {$users->count()}\n";
    echo "Permissões concedidas: {$granted}\n";
    echo "Permissões já existentes: {$alreadyHad}\n";
    echo "Erros: {$errors}\n";
    echo "========================================\n\n";
    
    if ($errors > 0) {
        echo "⚠️  Houve alguns erros durante a sincronização.\n";
        exit(1);
    } else {
        echo "✓ Sincronização concluída com sucesso!\n";
        echo "\nTodos os técnicos e admins agora têm acesso ao inventário e almoxarifado.\n";
    }
    
} catch (\Exception $e) {
    echo "\n❌ ERRO: {$e->getMessage()}\n";
    echo "\nStack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}
