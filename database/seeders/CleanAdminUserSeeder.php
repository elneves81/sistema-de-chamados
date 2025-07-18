<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class CleanAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Limpando usuários admin...');
        
        // Primeiro, vamos ver quantos usuários existem com esse email
        $count = DB::table('users')->where('email', 'admin@admin.com')->count();
        $this->command->info('Usuários encontrados com email admin@admin.com: ' . $count);
        
        // Deletar TODOS os registros com esse email (incluindo soft deletes)
        DB::table('users')->where('email', 'admin@admin.com')->delete();
        $this->command->info('Registros removidos do banco.');
        
        // Agora criar um novo usuário
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->command->info('✅ Usuário admin criado com sucesso!');
        $this->command->info('ID: ' . $admin->id);
        $this->command->info('Email: ' . $admin->email);
        $this->command->info('Nome: ' . $admin->name);
        $this->command->info('Role: ' . $admin->role);
        
        // Testar senha
        if (Hash::check('admin123', $admin->password)) {
            $this->command->info('✅ Senha verificada com sucesso!');
        } else {
            $this->command->error('❌ Erro na verificação da senha!');
        }

        $this->command->info('');
        $this->command->info('🔑 CREDENCIAIS FINAIS:');
        $this->command->info('👤 Email: admin@admin.com');
        $this->command->info('🔒 Senha: admin123');
        $this->command->info('🌐 URL: http://localhost:8080/login');
    }
}
