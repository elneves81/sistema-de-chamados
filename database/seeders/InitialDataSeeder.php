<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetStatus;
use App\Models\Location;
use App\Models\Department;
use App\Models\Category;

class InitialDataSeeder extends Seeder
{
    public function run()
    {
        // Status de Assets
        $statuses = [
            ['name' => 'Em Uso', 'color' => '#28a745'],
            ['name' => 'Em Manutenção', 'color' => '#ffc107'],
            ['name' => 'Em Estoque', 'color' => '#17a2b8'],
            ['name' => 'Aposentado', 'color' => '#dc3545'],
            ['name' => 'Emprestado', 'color' => '#6610f2'],
        ];

        foreach ($statuses as $status) {
            AssetStatus::create($status);
        }

        // Localizações
        $locations = [
            ['name' => 'Matriz', 'city' => 'São Paulo', 'state' => 'SP'],
            ['name' => 'Filial Rio', 'city' => 'Rio de Janeiro', 'state' => 'RJ'],
            ['name' => 'Filial BH', 'city' => 'Belo Horizonte', 'state' => 'MG'],
        ];

        foreach ($locations as $location) {
            Location::create($location);
        }

        // Departamentos
        $departments = [
            ['name' => 'TI'],
            ['name' => 'RH'],
            ['name' => 'Financeiro'],
            ['name' => 'Comercial'],
            ['name' => 'Operações'],
        ];

        foreach ($departments as $department) {
            Department::create($department);
        }

        // Categorias de Chamados
        $categories = [
            ['name' => 'Hardware', 'slug' => 'hardware', 'color' => '#dc3545', 'sla_hours' => 4],
            ['name' => 'Software', 'slug' => 'software', 'color' => '#28a745', 'sla_hours' => 8],
            ['name' => 'Rede', 'slug' => 'network', 'color' => '#17a2b8', 'sla_hours' => 2],
            ['name' => 'Acesso', 'slug' => 'access', 'color' => '#ffc107', 'sla_hours' => 1],
            ['name' => 'E-mail', 'slug' => 'email', 'color' => '#6610f2', 'sla_hours' => 4],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
