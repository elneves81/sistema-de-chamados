<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class AdditionalCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeder.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            // Categorias de TI existentes mantidas, novas categorias adicionadas:
            [
                'name' => 'Hardware',
                'description' => 'Problemas com equipamentos físicos (computadores, notebooks, monitores, etc)',
                'color' => '#3B82F6',
                'sla_hours' => 24,
                'active' => true
            ],
            [
                'name' => 'Telefonia',
                'description' => 'Problemas com telefones, ramais e comunicação',
                'color' => '#10B981',
                'sla_hours' => 12,
                'active' => true
            ],
            [
                'name' => 'Sistemas de Saúde',
                'description' => 'Problemas com sistemas específicos de saúde (prontuário eletrônico, ESUS, etc)',
                'color' => '#EF4444',
                'sla_hours' => 4,
                'active' => true
            ],
            [
                'name' => 'Acesso e Senhas',
                'description' => 'Problemas de acesso, senhas bloqueadas, redefinição de credenciais',
                'color' => '#F59E0B',
                'sla_hours' => 8,
                'active' => true
            ],
            [
                'name' => 'Backup e Recuperação',
                'description' => 'Solicitações de backup, recuperação de arquivos perdidos',
                'color' => '#8B5CF6',
                'sla_hours' => 48,
                'active' => true
            ],
            [
                'name' => 'Equipamentos Médicos',
                'description' => 'Suporte técnico para equipamentos médicos com componentes de TI',
                'color' => '#EC4899',
                'sla_hours' => 2,
                'active' => true
            ],
            [
                'name' => 'Instalação e Configuração',
                'description' => 'Instalação de novos equipamentos, configuração de sistemas',
                'color' => '#06B6D4',
                'sla_hours' => 48,
                'active' => true
            ],
            [
                'name' => 'Manutenção Preventiva',
                'description' => 'Manutenção programada e preventiva de equipamentos',
                'color' => '#84CC16',
                'sla_hours' => 168,
                'active' => true
            ],
            [
                'name' => 'Treinamento',
                'description' => 'Solicitações de treinamento e capacitação em sistemas',
                'color' => '#14B8A6',
                'sla_hours' => 168,
                'active' => true
            ],
            [
                'name' => 'Licenças de Software',
                'description' => 'Solicitação, renovação e problemas com licenças de software',
                'color' => '#F97316',
                'sla_hours' => 72,
                'active' => true
            ],
            [
                'name' => 'Servidores',
                'description' => 'Problemas com servidores e infraestrutura',
                'color' => '#DC2626',
                'sla_hours' => 4,
                'active' => true
            ],
            [
                'name' => 'Banco de Dados',
                'description' => 'Problemas com bancos de dados, consultas e integridade',
                'color' => '#7C3AED',
                'sla_hours' => 8,
                'active' => true
            ],
            [
                'name' => 'Outros',
                'description' => 'Outros problemas não categorizados',
                'color' => '#6B7280',
                'sla_hours' => 48,
                'active' => true
            ],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['name' => $category['name']],
                $category
            );
        }

        $this->command->info('Categorias adicionais criadas com sucesso!');
        $this->command->info('Total de categorias: ' . Category::count());
    }
}
