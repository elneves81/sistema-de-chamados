<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            [
                'name' => 'Matriz - São Paulo',
                'short_name' => 'SP-MTZ',
                'address' => 'Av. Paulista, 1000',
                'city' => 'São Paulo',
                'state' => 'SP',
                'country' => 'Brasil',
                'postal_code' => '01310-100',
                'phone' => '(11) 3000-0000',
                'email' => 'sp.matriz@empresa.com',
                'comment' => 'Sede principal da empresa',
                'is_active' => true,
            ],
            [
                'name' => 'Filial Rio de Janeiro',
                'short_name' => 'RJ-FIL',
                'address' => 'Rua das Laranjeiras, 500',
                'city' => 'Rio de Janeiro',
                'state' => 'RJ',
                'country' => 'Brasil',
                'postal_code' => '22240-000',
                'phone' => '(21) 2000-0000',
                'email' => 'rj.filial@empresa.com',
                'comment' => 'Filial Rio de Janeiro',
                'is_active' => true,
            ],
            [
                'name' => 'Centro de Distribuição - Campinas',
                'short_name' => 'CP-CD',
                'address' => 'Rod. Anhanguera, Km 100',
                'city' => 'Campinas',
                'state' => 'SP',
                'country' => 'Brasil',
                'postal_code' => '13100-000',
                'phone' => '(19) 3500-0000',
                'email' => 'cd.campinas@empresa.com',
                'comment' => 'Centro de distribuição',
                'is_active' => true,
            ],
            [
                'name' => 'Home Office',
                'short_name' => 'HOME',
                'address' => 'Trabalho remoto',
                'city' => 'Diversos',
                'state' => 'Diversos',
                'country' => 'Brasil',
                'postal_code' => null,
                'phone' => null,
                'email' => null,
                'comment' => 'Localização para funcionários em home office',
                'is_active' => true,
            ],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }
    }
}
