<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class CheckUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('=== USUÁRIOS NO SISTEMA ===');
        
        foreach (User::all() as $user) {
            $this->command->info($user->name . ' (' . $user->email . ') - Role: ' . $user->role . ' - Ativo: ' . ($user->is_active ? 'Sim' : 'Não'));
        }
        
        $this->command->info('=== TOTAL: ' . User::count() . ' usuários ===');
    }
}
