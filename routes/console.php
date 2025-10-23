<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('user:create-admin', function () {
    try {
        // Verificar se já existe
        $existing = User::where('email', 'admin@ditis.com')->first();
        if ($existing) {
            $existing->role = 'superadmin';
            $existing->password = bcrypt('123456');
            $existing->save();
            $this->info("Usuário admin@ditis.com atualizado para superadmin!");
            $this->info("ID: {$existing->id}");
            return;
        }
        
        $user = User::create([
            'name' => 'Super Admin DITIS',
            'email' => 'admin@ditis.com',
            'password' => bcrypt('123456'),
            'role' => 'superadmin',
            'location_id' => 1,
            'status' => 'active'
        ]);
        
        $this->info("✅ Superadmin criado com sucesso!");
        $this->info("📧 Email: admin@ditis.com");
        $this->info("🔑 Senha: 123456");
        $this->info("🆔 ID: {$user->id}");
        $this->info("🎭 Role: {$user->role}");
    } catch (Exception $e) {
        $this->error("❌ Erro: " . $e->getMessage());
    }
})->purpose('Criar um usuário superadmin');

Artisan::command('user:list-admins', function () {
    $admins = User::whereIn('role', ['admin', 'superadmin'])->get();
    
    if ($admins->isEmpty()) {
        $this->error("❌ Nenhum admin encontrado!");
        return;
    }
    
    $this->info("👑 Administradores cadastrados:");
    $this->table(
        ['ID', 'Nome', 'Email', 'Role', 'Status'],
        $admins->map(function ($user) {
            return [
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->status
            ];
        })
    );
})->purpose('Listar todos os administradores');

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
