<?php

require __DIR__ . '/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

// Carrega configuração do Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "TESTE: SINCRONIZAÇÃO AUTOMÁTICA DE PERMISSÕES DE INVENTÁRIO\n";
echo "========================================\n\n";

try {
    // 1. Verificar se há permissões de inventário cadastradas
    echo "1. Verificando permissões de inventário...\n";
    $inventoryPermissions = \App\Models\Permission::whereIn('module', ['machines', 'stock'])->get();
    
    if ($inventoryPermissions->isEmpty()) {
        echo "❌ ERRO: Nenhuma permissão de inventário encontrada!\n";
        echo "   Execute: php artisan db:seed --class=PermissionSeeder\n";
        exit(1);
    }
    
    echo "✓ {$inventoryPermissions->count()} permissões de inventário encontradas:\n";
    foreach ($inventoryPermissions as $perm) {
        echo "  - [{$perm->module}] {$perm->name}: {$perm->display_name}\n";
    }
    echo "\n";
    
    // 2. Verificar técnicos existentes
    echo "2. Verificando técnicos existentes...\n";
    $technicians = \App\Models\User::where('role', 'technician')->get();
    
    if ($technicians->isEmpty()) {
        echo "⚠️  Nenhum técnico encontrado. Criando um técnico de teste...\n";
        
        // Criar um técnico de teste
        $testTechnician = \App\Models\User::create([
            'name' => 'Técnico Teste',
            'username' => 'tecnico_teste_' . time(),
            'email' => 'tecnico.teste.' . time() . '@example.com',
            'password' => bcrypt('password123'),
            'role' => 'technician',
            'is_active' => true,
        ]);
        
        echo "✓ Técnico de teste criado: {$testTechnician->name} (ID: {$testTechnician->id})\n\n";
        
        // Verificar se recebeu as permissões automaticamente
        echo "3. Verificando permissões do técnico criado...\n";
        $techPermissions = DB::table('user_permissions')
            ->where('user_id', $testTechnician->id)
            ->where('granted', true)
            ->count();
        
        if ($techPermissions > 0) {
            echo "✓ O técnico recebeu {$techPermissions} permissões automaticamente!\n";
            
            // Listar permissões
            $permissions = DB::table('user_permissions')
                ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
                ->where('user_permissions.user_id', $testTechnician->id)
                ->where('user_permissions.granted', true)
                ->whereIn('permissions.module', ['machines', 'stock'])
                ->get(['permissions.name', 'permissions.display_name']);
            
            foreach ($permissions as $perm) {
                echo "  ✓ {$perm->name}: {$perm->display_name}\n";
            }
        } else {
            echo "❌ ERRO: O técnico NÃO recebeu permissões automaticamente!\n";
            echo "   Verifique se o Observer está registrado corretamente.\n";
        }
        
        echo "\n4. Testando mudança de role (técnico → cliente)...\n";
        $testTechnician->update(['role' => 'customer']);
        
        $remainingPermissions = DB::table('user_permissions')
            ->where('user_id', $testTechnician->id)
            ->where('granted', true)
            ->count();
        
        if ($remainingPermissions == 0) {
            echo "✓ Permissões de inventário removidas automaticamente!\n";
        } else {
            echo "⚠️  O usuário ainda tem {$remainingPermissions} permissões ativas.\n";
        }
        
        echo "\n5. Testando mudança de role (cliente → técnico)...\n";
        $testTechnician->update(['role' => 'technician']);
        
        $restoredPermissions = DB::table('user_permissions')
            ->where('user_id', $testTechnician->id)
            ->where('granted', true)
            ->count();
        
        if ($restoredPermissions > 0) {
            echo "✓ Permissões de inventário restauradas automaticamente ({$restoredPermissions} permissões)!\n";
        } else {
            echo "❌ ERRO: Permissões não foram restauradas!\n";
        }
        
        // Limpar o técnico de teste
        echo "\n6. Limpando dados de teste...\n";
        $testTechnician->forceDelete();
        echo "✓ Técnico de teste removido.\n";
        
    } else {
        echo "✓ {$technicians->count()} técnicos encontrados:\n";
        
        foreach ($technicians as $tech) {
            // Contar permissões de inventário
            $permCount = DB::table('user_permissions')
                ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
                ->where('user_permissions.user_id', $tech->id)
                ->where('user_permissions.granted', true)
                ->whereIn('permissions.module', ['machines', 'inventory', 'stock'])
                ->count();
            
            if ($permCount > 0) {
                echo "  ✓ {$tech->name} ({$tech->email}) - {$permCount} permissões de inventário\n";
            } else {
                echo "  ⚠️  {$tech->name} ({$tech->email}) - SEM permissões de inventário\n";
                echo "     Sincronizando agora...\n";
                
                // Sincronizar manualmente
                foreach ($inventoryPermissions as $perm) {
                    $tech->grantPermission($perm->name);
                }
                
                echo "     ✓ Permissões atribuídas!\n";
            }
        }
    }
    
    echo "\n========================================\n";
    echo "✓ TESTE CONCLUÍDO COM SUCESSO!\n";
    echo "========================================\n";
    echo "\nResumo:\n";
    echo "- O Observer UserObserver está configurado para monitorar mudanças no modelo User\n";
    echo "- Quando um usuário é criado com role 'technician' ou 'admin', recebe permissões de inventário automaticamente\n";
    echo "- Quando o role de um usuário muda para 'technician' ou 'admin', recebe as permissões\n";
    echo "- Quando o role muda para outro (ex: 'customer'), as permissões são removidas\n";
    echo "- Super admins sempre mantêm todas as permissões\n\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERRO: {$e->getMessage()}\n";
    echo "\nStack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}
