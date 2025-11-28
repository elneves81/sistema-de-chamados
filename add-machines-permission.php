<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

// Criar permissão se não existir
$permission = \App\Models\Permission::firstOrCreate(
    ['name' => 'machines.view'],
    [
        'description' => 'Visualizar inventário de máquinas',
        'guard_name' => 'web'
    ]
);

echo "Permissão 'machines.view' criada/encontrada: ID {$permission->id}\n";

// Atribuir para todos os admins
$admins = \App\Models\User::where('role', 'admin')->get();

foreach ($admins as $admin) {
    if (!$admin->permissions()->where('name', 'machines.view')->exists()) {
        $admin->permissions()->attach($permission->id);
        echo "✓ Permissão adicionada para: {$admin->name}\n";
    } else {
        echo "- {$admin->name} já possui a permissão\n";
    }
}

echo "\nConcluído! Total de admins: {$admins->count()}\n";
