<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cria ou atualiza o usuário super admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@sistema.com'],
            [
                'name' => 'Super Administrador',
                'password' => Hash::make('SuperAdmin@123'),
                'role' => 'admin',
                'is_super_admin' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('Super administrador criado/atualizado:');
        $this->command->info('Email: superadmin@sistema.com');
        $this->command->info('Senha: SuperAdmin@123');
        $this->command->warn('IMPORTANTE: Altere a senha após o primeiro login!');
    }
}
