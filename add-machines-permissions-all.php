<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Criar permissões se não existirem
$permissions = [
    'machines.view' => ['Visualizar Inventário', 'Visualizar inventário de máquinas'],
    'machines.create' => ['Criar Máquinas', 'Criar novas máquinas no inventário'],
    'machines.edit' => ['Editar Máquinas', 'Editar máquinas do inventário'],
    'machines.delete' => ['Excluir Máquinas', 'Excluir máquinas do inventário'],
];

foreach ($permissions as $name => $data) {
    $permission = \App\Models\Permission::firstOrCreate(
        ['name' => $name],
        [
            'display_name' => $data[0],
            'description' => $data[1],
            'guard_name' => 'web',
            'module' => 'inventory'
        ]
    );
    echo "Permissão '{$name}' criada/encontrada: ID {$permission->id}\n";
}

// Atribuir permissões para técnicos
$technicians = \App\Models\User::where('role', 'technician')->get();

echo "\n--- Adicionando permissões para técnicos ---\n";

foreach ($technicians as $tech) {
    foreach (['machines.view', 'machines.create', 'machines.edit'] as $permName) {
        $perm = \App\Models\Permission::where('name', $permName)->first();
        if ($perm && !$tech->permissions()->where('name', $permName)->exists()) {
            $tech->permissions()->attach($perm->id);
            echo "✓ {$permName} adicionada para: {$tech->name}\n";
        }
    }
}

// Atribuir permissões para admins (todas)
$admins = \App\Models\User::where('role', 'admin')->get();

echo "\n--- Adicionando permissões para administradores ---\n";

foreach ($admins as $admin) {
    foreach (['machines.view', 'machines.create', 'machines.edit', 'machines.delete'] as $permName) {
        $perm = \App\Models\Permission::where('name', $permName)->first();
        if ($perm && !$admin->permissions()->where('name', $permName)->exists()) {
            $admin->permissions()->attach($perm->id);
            echo "✓ {$permName} adicionada para: {$admin->name}\n";
        }
    }
}

echo "\nConcluído!\n";
echo "Total de técnicos: {$technicians->count()}\n";
echo "Total de admins: {$admins->count()}\n";
