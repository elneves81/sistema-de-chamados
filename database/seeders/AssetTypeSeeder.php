<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\AssetType;

class AssetTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $assetTypes = [
            [
                'name' => 'Computador Desktop',
                'description' => 'Computadores de mesa para uso geral',
                'icon' => 'fas fa-desktop',
                'is_active' => true,
            ],
            [
                'name' => 'Notebook',
                'description' => 'Laptops e notebooks portáteis',
                'icon' => 'fas fa-laptop',
                'is_active' => true,
            ],
            [
                'name' => 'Monitor',
                'description' => 'Monitores e telas',
                'icon' => 'fas fa-tv',
                'is_active' => true,
            ],
            [
                'name' => 'Impressora',
                'description' => 'Impressoras e multifuncionais',
                'icon' => 'fas fa-print',
                'is_active' => true,
            ],
            [
                'name' => 'Smartphone',
                'description' => 'Telefones celulares corporativos',
                'icon' => 'fas fa-mobile-alt',
                'is_active' => true,
            ],
            [
                'name' => 'Tablet',
                'description' => 'Tablets e dispositivos móveis',
                'icon' => 'fas fa-tablet-alt',
                'is_active' => true,
            ],
            [
                'name' => 'Servidor',
                'description' => 'Servidores e equipamentos de rede',
                'icon' => 'fas fa-server',
                'is_active' => true,
            ],
            [
                'name' => 'Projetor',
                'description' => 'Projetores e equipamentos audiovisuais',
                'icon' => 'fas fa-video',
                'is_active' => true,
            ],
        ];

        foreach ($assetTypes as $assetType) {
            AssetType::create($assetType);
        }
    }
}
