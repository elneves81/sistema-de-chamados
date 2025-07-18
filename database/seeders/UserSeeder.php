<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Admin
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@sistema.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'phone' => '(11) 99999-9999',
            'department' => 'TI',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Técnico
        User::create([
            'name' => 'João Técnico',
            'email' => 'tecnico@sistema.com',
            'password' => Hash::make('tecnico123'),
            'role' => 'technician',
            'phone' => '(11) 88888-8888',
            'department' => 'Suporte',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Cliente
        User::create([
            'name' => 'Maria Cliente',
            'email' => 'cliente@sistema.com',
            'password' => Hash::make('cliente123'),
            'role' => 'customer',
            'phone' => '(11) 77777-7777',
            'department' => 'Vendas',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Mais alguns usuários de teste
        User::create([
            'name' => 'Pedro Silva',
            'email' => 'pedro@sistema.com',
            'password' => Hash::make('123456'),
            'role' => 'customer',
            'phone' => '(11) 66666-6666',
            'department' => 'Marketing',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Ana Suporte',
            'email' => 'ana@sistema.com',
            'password' => Hash::make('123456'),
            'role' => 'technician',
            'phone' => '(11) 55555-5555',
            'department' => 'Suporte',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
