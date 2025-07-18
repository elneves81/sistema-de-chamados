<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Suporte Técnico',
                'description' => 'Problemas relacionados a hardware, software e sistemas',
                'color' => '#3B82F6',
                'active' => true,
            ],
            [
                'name' => 'Rede e Conectividade',
                'description' => 'Problemas de internet, Wi-Fi e conectividade',
                'color' => '#10B981',
                'active' => true,
            ],
            [
                'name' => 'E-mail',
                'description' => 'Configuração e problemas de e-mail',
                'color' => '#F59E0B',
                'active' => true,
            ],
            [
                'name' => 'Impressoras',
                'description' => 'Configuração e manutenção de impressoras',
                'color' => '#8B5CF6',
                'active' => true,
            ],
            [
                'name' => 'Software',
                'description' => 'Instalação e problemas com softwares',
                'color' => '#EF4444',
                'active' => true,
            ],
            [
                'name' => 'Solicitações',
                'description' => 'Solicitações gerais e novas funcionalidades',
                'color' => '#06B6D4',
                'active' => true,
            ],
            [
                'name' => 'Segurança',
                'description' => 'Questões relacionadas à segurança da informação',
                'color' => '#DC2626',
                'active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
