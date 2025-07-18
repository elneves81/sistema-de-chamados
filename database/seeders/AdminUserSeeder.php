<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar se o usuário admin já existe
        $admin = User::where('email', 'admin@admin.com')->first();

        if ($admin) {
            // Atualizar o usuário existente para ser admin
            $admin->update([
                'name' => 'Administrador',
                'role' => 'admin',
                'is_active' => true
            ]);
            
            $this->command->info('Usuário admin atualizado com sucesso!');
        } else {
            // Criar novo usuário admin
            User::create([
                'name' => 'Administrador',
                'email' => 'admin@admin.com',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]);
            
            $this->command->info('Usuário admin criado com sucesso!');
        }

        // Verificar se existem outros usuários para teste
        $userCount = User::count();
        
        if ($userCount == 1) {
            // Criar alguns usuários de teste
            User::create([
                'name' => 'João Silva',
                'email' => 'joao@empresa.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'technician',
                'is_active' => true,
            ]);

            User::create([
                'name' => 'Maria Santos',
                'email' => 'maria@empresa.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'customer',
                'is_active' => true,
            ]);

            User::create([
                'name' => 'Pedro Oliveira',
                'email' => 'pedro@empresa.com',
                'email_verified_at' => now(),
                'password' => Hash::make('password'),
                'role' => 'technician',
                'is_active' => true,
            ]);

            $this->command->info('Usuários de teste criados com sucesso!');
        }

        $this->command->info('Total de usuários no sistema: ' . User::count());
    }
}
