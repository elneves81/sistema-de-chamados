<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FixAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Usar updateOrCreate para evitar duplicatas
        $admin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrador',
                'email_verified_at' => now(),
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->command->info('Usuário admin configurado com sucesso!');
        $this->command->info('Credenciais do administrador:');
        $this->command->info('Email: ' . $admin->email);
        $this->command->info('Nome: ' . $admin->name);
        $this->command->info('Role: ' . $admin->role);
        $this->command->info('ID: ' . $admin->id);
        $this->command->info('Ativo: ' . ($admin->is_active ? 'Sim' : 'Não'));
        
        // Testar se a senha está correta
        if (Hash::check('admin123', $admin->password)) {
            $this->command->info('✅ Senha verificada com sucesso!');
        } else {
            $this->command->error('❌ Erro na verificação da senha!');
        }

        // Criar usuário de backup também
        $backupAdmin = User::updateOrCreate(
            ['email' => 'admin@sistema.com'],
            [
                'name' => 'Admin Sistema',
                'email_verified_at' => now(),
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        $this->command->info('');
        $this->command->info('🔑 CREDENCIAIS DE LOGIN:');
        $this->command->info('👤 Email: admin@admin.com');
        $this->command->info('🔒 Senha: admin123');
        $this->command->info('');
        $this->command->info('🔑 CREDENCIAIS DE BACKUP:');
        $this->command->info('👤 Email: admin@sistema.com');
        $this->command->info('🔒 Senha: 123456');
        
        // Verificar email_verified_at
        if ($admin->email_verified_at) {
            $this->command->info('✅ Email verificado: ' . $admin->email_verified_at);
        } else {
            $this->command->error('❌ Email não verificado!');
        }
    }
}
