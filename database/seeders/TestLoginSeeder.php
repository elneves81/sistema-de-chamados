<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class TestLoginSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar o usuário admin
        $admin = User::where('email', 'admin@admin.com')->first();
        
        if (!$admin) {
            $this->command->error('❌ Usuário admin não encontrado!');
            return;
        }

        $this->command->info('📧 Testando credenciais...');
        $this->command->info('Email: ' . $admin->email);
        $this->command->info('Nome: ' . $admin->name);
        $this->command->info('Role: ' . $admin->role);
        $this->command->info('Ativo: ' . ($admin->is_active ? 'Sim' : 'Não'));
        $this->command->info('Email verificado: ' . ($admin->email_verified_at ? 'Sim' : 'Não'));
        
        // Testar senha
        $senha = 'admin123';
        if (Hash::check($senha, $admin->password)) {
            $this->command->info('✅ Hash da senha está correto!');
        } else {
            $this->command->error('❌ Hash da senha está incorreto!');
            
            // Vamos atualizar a senha novamente
            $admin->password = Hash::make($senha);
            $admin->save();
            
            if (Hash::check($senha, $admin->password)) {
                $this->command->info('✅ Senha atualizada e verificada!');
            } else {
                $this->command->error('❌ Ainda há problema com a senha!');
            }
        }

        // Testar attempt de login
        $credentials = [
            'email' => 'admin@admin.com',
            'password' => 'admin123'
        ];

        if (Auth::attempt($credentials)) {
            $this->command->info('✅ Teste de login bem-sucedido!');
            Auth::logout(); // Logout após teste
        } else {
            $this->command->error('❌ Teste de login falhou!');
            
            // Verificar se o usuário está ativo
            if (!$admin->is_active) {
                $this->command->error('Usuário não está ativo!');
            }
        }

        $this->command->info('');
        $this->command->info('🔑 CREDENCIAIS CONFIRMADAS:');
        $this->command->info('👤 Email: admin@admin.com');
        $this->command->info('🔒 Senha: admin123');
        $this->command->info('🌐 URL: http://localhost:8080/login');
    }
}
