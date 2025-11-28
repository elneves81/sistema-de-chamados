<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            // Infraestrutura e Equipamentos
            [
                'name' => 'Computador/Notebook',
                'description' => 'Problemas com computadores, notebooks, lentidão, travamento, não liga',
                'color' => '#007bff',
                'sla_hours' => 24,
            ],
            [
                'name' => 'Impressora/Scanner',
                'description' => 'Problemas com impressoras, scanners, não imprime, papel atolado, configuração',
                'color' => '#6c757d',
                'sla_hours' => 24,
            ],
            [
                'name' => 'Internet/Rede',
                'description' => 'Problemas de conexão, internet lenta, sem acesso à rede, Wi-Fi',
                'color' => '#dc3545',
                'sla_hours' => 8,
            ],
            [
                'name' => 'Telefone/Ramal',
                'description' => 'Problemas com telefone fixo, ramal não funciona, sem linha',
                'color' => '#28a745',
                'sla_hours' => 24,
            ],
            
            // Sistemas e Software
            [
                'name' => 'Sistema E-SUS',
                'description' => 'Problemas no sistema E-SUS, lentidão, erros, não abre',
                'color' => '#17a2b8',
                'sla_hours' => 12,
            ],
            [
                'name' => 'Sistema PEC (Prontuário Eletrônico)',
                'description' => 'Problemas com prontuário eletrônico, não salva, erro ao abrir',
                'color' => '#ffc107',
                'sla_hours' => 8,
            ],
            [
                'name' => 'Sistema de Agendamento',
                'description' => 'Problemas no sistema de agendamento de consultas',
                'color' => '#20c997',
                'sla_hours' => 12,
            ],
            [
                'name' => 'Sistema de Estoque/Farmácia',
                'description' => 'Problemas no sistema de controle de estoque e farmácia',
                'color' => '#fd7e14',
                'sla_hours' => 24,
            ],
            [
                'name' => 'E-mail',
                'description' => 'Problemas com e-mail, não envia/recebe, esqueceu senha',
                'color' => '#6610f2',
                'sla_hours' => 24,
            ],
            [
                'name' => 'Instalação de Software',
                'description' => 'Solicitação de instalação de programas, aplicativos',
                'color' => '#e83e8c',
                'sla_hours' => 48,
            ],
            
            // Acessos e Senhas
            [
                'name' => 'Senha/Bloqueio de Usuário',
                'description' => 'Esqueceu senha, usuário bloqueado, precisa resetar senha',
                'color' => '#dc3545',
                'sla_hours' => 8,
            ],
            [
                'name' => 'Criação de Usuário',
                'description' => 'Solicitação de criação de novo usuário em sistemas',
                'color' => '#28a745',
                'sla_hours' => 24,
            ],
            [
                'name' => 'Permissões/Acessos',
                'description' => 'Solicitação de permissões, liberar acesso a sistemas/pastas',
                'color' => '#ffc107',
                'sla_hours' => 24,
            ],
            
            // Equipamentos Médicos
            [
                'name' => 'Equipamento Médico',
                'description' => 'Problemas com equipamentos médicos, não funciona',
                'color' => '#dc3545',
                'sla_hours' => 4,
            ],
            
            // Manutenção
            [
                'name' => 'Manutenção Preventiva',
                'description' => 'Solicitação de manutenção preventiva em equipamentos',
                'color' => '#17a2b8',
                'sla_hours' => 72,
            ],
            [
                'name' => 'Manutenção Corretiva',
                'description' => 'Solicitação de reparo em equipamentos com defeito',
                'color' => '#fd7e14',
                'sla_hours' => 24,
            ],
            
            // Outros
            [
                'name' => 'Treinamento/Capacitação',
                'description' => 'Solicitação de treinamento em sistemas ou equipamentos',
                'color' => '#6610f2',
                'sla_hours' => 72,
            ],
            [
                'name' => 'Suporte Presencial',
                'description' => 'Solicitação de atendimento presencial na unidade',
                'color' => '#20c997',
                'sla_hours' => 24,
            ],
            [
                'name' => 'Outros',
                'description' => 'Outros problemas não categorizados',
                'color' => '#6c757d',
                'sla_hours' => 48,
            ],
        ];

        $now = Carbon::now();
        
        foreach ($categories as $category) {
            $slug = Str::slug($category['name']);
            
            // Verifica se já existe
            $exists = DB::table('categories')
                ->where('slug', $slug)
                ->exists();
                
            if (!$exists) {
                DB::table('categories')->insert([
                    'name' => $category['name'],
                    'slug' => $slug,
                    'description' => $category['description'],
                    'color' => $category['color'],
                    'sla_hours' => $category['sla_hours'],
                    'active' => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
        
        $this->command->info('Categorias adicionadas com sucesso!');
    }
}
