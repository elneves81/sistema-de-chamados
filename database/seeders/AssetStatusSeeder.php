<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AssetStatus;

class AssetStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Em Uso',
                'color' => '#28a745',
                'is_active' => true
            ],
            [
                'name' => 'Em Manutenção',
                'color' => '#ffc107',
                'is_active' => true
            ],
            [
                'name' => 'Em Estoque',
                'color' => '#17a2b8',
                'is_active' => true
            ],
            [
                'name' => 'Aposentado',
                'color' => '#6c757d',
                'is_active' => true
            ],
            [
                'name' => 'Defeituoso',
                'color' => '#dc3545',
                'is_active' => true
            ]
        ];

        foreach ($statuses as $status) {
            AssetStatus::create($status);
        }
    }
}
