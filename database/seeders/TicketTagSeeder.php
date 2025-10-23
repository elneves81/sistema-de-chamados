<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TicketTagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            ['name' => 'Urgente', 'color' => '#dc3545', 'description' => 'Chamados que precisam de atenção imediata'],
            ['name' => 'Hardware', 'color' => '#6f42c1', 'description' => 'Problemas relacionados a hardware'],
            ['name' => 'Software', 'color' => '#0d6efd', 'description' => 'Problemas relacionados a software'],
            ['name' => 'Rede', 'color' => '#20c997', 'description' => 'Problemas de conectividade e rede'],
            ['name' => 'Email', 'color' => '#fd7e14', 'description' => 'Problemas relacionados a email'],
            ['name' => 'Impressora', 'color' => '#6c757d', 'description' => 'Problemas com impressoras'],
            ['name' => 'Acesso', 'color' => '#198754', 'description' => 'Problemas de acesso e permissões'],
            ['name' => 'VIP', 'color' => '#ffc107', 'description' => 'Chamados de usuários VIP'],
            ['name' => 'Treinamento', 'color' => '#0dcaf0', 'description' => 'Solicitações de treinamento'],
            ['name' => 'Backup', 'color' => '#495057', 'description' => 'Problemas relacionados a backup'],
        ];

        foreach ($tags as $tag) {
            \App\Models\TicketTag::create($tag);
        }
    }
}
