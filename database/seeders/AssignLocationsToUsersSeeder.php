<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Location;

class AssignLocationsToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Pegar as localizações criadas
        $locations = Location::all();
        
        if ($locations->isEmpty()) {
            $this->command->info('Nenhuma localização encontrada. Execute o LocationSeeder primeiro.');
            return;
        }

        // Pegar todos os usuários
        $users = User::all();
        
        if ($users->isEmpty()) {
            $this->command->info('Nenhum usuário encontrado.');
            return;
        }

        // Distribuir usuários nas localizações de forma aleatória
        foreach ($users as $user) {
            $randomLocation = $locations->random();
            $user->update(['location_id' => $randomLocation->id]);
            
            $this->command->info("Usuário {$user->name} atribuído à localização {$randomLocation->name}");
        }

        $this->command->info('Usuários atribuídos às localizações com sucesso!');
    }
}
