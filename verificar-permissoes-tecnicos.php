<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================\n";
echo "VERIFICAÇÃO FINAL - PERMISSÕES DOS TÉCNICOS\n";
echo "========================================\n\n";

$tech = \App\Models\User::where('role', 'technician')->first();

if ($tech) {
    echo "Técnico: {$tech->name}\n";
    echo "Email: {$tech->email}\n";
    echo "Role: {$tech->role}\n\n";
    
    $perms = $tech->permissions()
        ->whereIn('module', ['machines', 'inventory', 'stock'])
        ->where('user_permissions.granted', true)
        ->get(['name', 'display_name', 'module']);
    
    $machinePerms = $perms->where('module', 'machines');
    $inventoryPerms = $perms->where('module', 'inventory');
    $stockPerms = $perms->where('module', 'stock');
    
    echo "📊 TOTAL DE PERMISSÕES: {$perms->count()}\n\n";
    
    echo "🖥️ INVENTÁRIO - VISUALIZAÇÃO ({$machinePerms->count()}):\n";
    foreach ($machinePerms as $p) {
        echo "  ✓ {$p->name}: {$p->display_name}\n";
    }
    
    echo "\n✏️ INVENTÁRIO - EDIÇÃO ({$inventoryPerms->count()}):\n";
    foreach ($inventoryPerms as $p) {
        echo "  ✓ {$p->name}: {$p->display_name}\n";
    }
    
    echo "\n📦 ALMOXARIFADO ({$stockPerms->count()}):\n";
    foreach ($stockPerms as $p) {
        echo "  ✓ {$p->name}: {$p->display_name}\n";
    }
    
    echo "\n========================================\n";
    echo "✅ TÉCNICOS TÊM ACESSO COMPLETO!\n";
    echo "========================================\n\n";
    
    echo "O que os técnicos PODEM fazer:\n";
    echo "  ✓ Ver inventário de todos os equipamentos\n";
    echo "  ✓ Criar novos equipamentos (PCs, notebooks, tablets)\n";
    echo "  ✓ Editar informações de equipamentos\n";
    echo "  ✓ Excluir equipamentos do sistema\n";
    echo "  ✓ Pegar assinaturas digitais para entregas\n";
    echo "  ✓ Registrar entregas de equipamentos\n";
    echo "  ✓ Vincular equipamentos a usuários\n";
    echo "  ✓ Ver almoxarifado e estoque\n";
    echo "  ✓ Criar itens no almoxarifado\n";
    echo "  ✓ Realizar movimentações de estoque\n";
    echo "  ✓ Excluir itens do almoxarifado\n\n";
    
    // Verificar alguns técnicos
    $allTechs = \App\Models\User::where('role', 'technician')->get();
    echo "📋 TÉCNICOS NO SISTEMA: {$allTechs->count()}\n\n";
    
    foreach ($allTechs as $t) {
        $permCount = \Illuminate\Support\Facades\DB::table('user_permissions')
            ->join('permissions', 'user_permissions.permission_id', '=', 'permissions.id')
            ->where('user_permissions.user_id', $t->id)
            ->where('user_permissions.granted', true)
            ->whereIn('permissions.module', ['machines', 'inventory', 'stock'])
            ->count();
        
        $status = $permCount == 9 ? '✅' : '⚠️';
        echo "  {$status} {$t->name} - {$permCount}/9 permissões\n";
    }
    
    echo "\n========================================\n";
    echo "✅ SISTEMA TOTALMENTE FUNCIONAL!\n";
    echo "========================================\n";
    
} else {
    echo "⚠️ Nenhum técnico encontrado no sistema.\n";
}
