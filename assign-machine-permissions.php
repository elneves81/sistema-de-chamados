<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Permission;
use App\Models\User;

$viewPerm = Permission::where('name', 'machines.view')->first();
$managePerm = Permission::where('name', 'machines.manage')->first();

if(!$viewPerm || !$managePerm) {
    echo "Permissões não encontradas. Execute: php artisan db:seed --class=PermissionSeeder\n";
    exit;
}

echo "Permissões encontradas:\n";
echo "- machines.view (ID: {$viewPerm->id})\n";
echo "- machines.manage (ID: {$managePerm->id})\n\n";

// Atribuir a todos os admins
$admins = User::where('role', 'admin')->get();
foreach($admins as $admin) {
    $admin->permissions()->syncWithoutDetaching([$viewPerm->id, $managePerm->id]);
    echo "✓ Admin: {$admin->name} ({$admin->email})\n";
}

// Atribuir a todos os técnicos
$tecnicos = User::where('role', 'technician')->get();
foreach($tecnicos as $tecnico) {
    $tecnico->permissions()->syncWithoutDetaching([$viewPerm->id, $managePerm->id]);
    echo "✓ Técnico: {$tecnico->name} ({$tecnico->email})\n";
}

echo "\n✅ Total: " . ($admins->count() + $tecnicos->count()) . " usuários atualizados\n";
