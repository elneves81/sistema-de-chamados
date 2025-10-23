<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Criar usuário admin de teste
        $admin = User::updateOrCreate(
            ['email' => 'admin@test.com'],
            [
                'name' => 'Administrador',
                'email' => 'admin@test.com',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );

        // Criar usuário comum de teste
        $user = User::updateOrCreate(
            ['email' => 'user@test.com'],
            [
                'name' => 'Usuário Teste',
                'email' => 'user@test.com',
                'password' => Hash::make('123456'),
                'role' => 'customer',
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Usuários de teste criados:');
        $this->command->info('Admin: admin@test.com / 123456');
        $this->command->info('User: user@test.com / 123456');
    }
}
