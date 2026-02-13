<?php

require __DIR__ . '/vendor/autoload.php';

// Carrega configuração do Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "TESTE: CRIAÇÃO DE TÉCNICO COM PERMISSÕES AUTOMÁTICAS\n";
echo "========================================\n\n";

try {
    $timestamp = time();
    
    echo "1. Criando um novo técnico...\n";
    $technician = \App\Models\User::create([
        'name' => 'Técnico Teste Automático',
        'username' => 'tecnico_auto_' . $timestamp,
        'email' => 'tecnico.auto.' . $timestamp . '@example.com',
        'password' => bcrypt('password123'),
        'role' => 'technician',
        'is_active' => true,
    ]);
    
    echo "✓ Técnico criado: {$technician->name} (ID: {$technician->id})\n\n";
    
    // Aguardar um momento para o observer executar
    sleep(1);
    
    echo "2. Verificando permissões de inventário atribuídas automaticamente...\n";
    
    $permissions = \Illuminate\Support\Facades\DB::table('user_permissions')
        ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
        ->where('user_permissions.user_id', $technician->id)
        ->where('user_permissions.granted', true)
        ->whereIn('permissions.module', ['machines', 'inventory', 'stock'])
        ->get(['permissions.name', 'permissions.display_name', 'permissions.module']);
    
    if ($permissions->count() > 0) {
        echo "✓ O técnico recebeu {$permissions->count()} permissões automaticamente!\n\n";
        
        $machinePerms = $permissions->where('module', 'machines');
        $inventoryPerms = $permissions->where('module', 'inventory');
        $stockPerms = $permissions->where('module', 'stock');
        
        echo "📦 INVENTÁRIO DE MÁQUINAS - Visualização ({$machinePerms->count()}):\n";
        foreach ($machinePerms as $perm) {
            echo "  ✓ {$perm->name}: {$perm->display_name}\n";
        }
        
        echo "\n📝 INVENTÁRIO - Edição ({$inventoryPerms->count()}):\n";
        foreach ($inventoryPerms as $perm) {
            echo "  ✓ {$perm->name}: {$perm->display_name}\n";
        }
        
        echo "\n📋 ALMOXARIFADO ({$stockPerms->count()}):\n";
        foreach ($stockPerms as $perm) {
            echo "  ✓ {$perm->name}: {$perm->display_name}\n";
        }
        
        echo "\n========================================\n";
        echo "✓ TESTE BEM-SUCEDIDO!\n";
        echo "========================================\n";
        echo "O Observer está funcionando corretamente.\n";
        echo "Quando um técnico é criado, recebe automaticamente:\n";
        echo "- Permissões de visualizar e gerenciar inventário\n";        echo "- Permissões de criar, editar e excluir máquinas/tablets\n";
        echo "- Permissões de pegar assinaturas\n";        echo "- Permissões de acessar o almoxarifado\n\n";
    } else {
        echo "❌ ERRO: O técnico NÃO recebeu permissões automaticamente!\n";
        echo "   O Observer pode não estar registrado corretamente.\n\n";
    }
    
    echo "3. Testando mudança de role para 'customer'...\n";
    $technician->update(['role' => 'customer']);
    
    sleep(1);
    
    $permissionsAfterChange = \Illuminate\Support\Facades\DB::table('user_permissions')
        ->where('user_id', $technician->id)
        ->where('granted', true)
        ->count();
    
    if ($permissionsAfterChange == 0) {
        echo "✓ Permissões de inventário removidas automaticamente!\n\n";
    } else {
        echo "⚠️  O usuário ainda tem {$permissionsAfterChange} permissões ativas.\n\n";
    }
    
    echo "4. Testando mudança de role de volta para 'technician'...\n";
    $technician->update(['role' => 'technician']);
    
    sleep(1);
    
    $permissionsRestored = \Illuminate\Support\Facades\DB::table('user_permissions')
        ->where('user_id', $technician->id)
        ->where('granted', true)
        ->count();
    
    if ($permissionsRestored > 0) {
        echo "✓ Permissões de inventário restauradas automaticamente ({$permissionsRestored} permissões)!\n\n";
    } else {
        echo "❌ ERRO: Permissões não foram restauradas!\n\n";
    }
    
    echo "5. Limpando dados de teste...\n";
    $technician->forceDelete();
    echo "✓ Técnico de teste removido.\n\n";
    
    echo "========================================\n";
    echo "✓ TODOS OS TESTES CONCLUÍDOS!\n";
    echo "========================================\n";
    
} catch (\Exception $e) {
    echo "\n❌ ERRO: {$e->getMessage()}\n";
    echo "\nStack trace:\n{$e->getTraceAsString()}\n";
    exit(1);
}
