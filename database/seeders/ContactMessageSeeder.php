<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ContactMessageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Models\ContactMessage::create([
            'name' => 'João Silva',
            'email' => 'joao.silva@email.com',
            'subject' => 'Problema com acesso ao sistema',
            'message' => 'Olá, estou com dificuldades para acessar o sistema de chamados. Quando tento fazer login, aparece uma mensagem de erro. Poderiam me ajudar?',
            'type' => 'suporte',
            'status' => 'pendente',
            'created_at' => now()->subHours(2)
        ]);

        \App\Models\ContactMessage::create([
            'name' => 'Maria Santos',
            'email' => 'maria.santos@hospital.com',
            'subject' => '🚨 URGENTE - Sistema fora do ar',
            'message' => 'O sistema de chamados está completamente fora do ar! Não conseguimos abrir novos chamados e os técnicos não estão recebendo notificações. Isso está impactando todo o nosso atendimento!',
            'type' => 'emergencia',
            'status' => 'em_andamento',
            'assigned_to' => 1,
            'created_at' => now()->subMinutes(30)
        ]);

        \App\Models\ContactMessage::create([
            'name' => 'Pedro Oliveira',
            'email' => 'pedro@empresa.com',
            'subject' => 'Dúvida sobre categorias',
            'message' => 'Gostaria de saber como criar novas categorias de chamados. Tentei acessar o menu administrativo mas não encontrei a opção.',
            'type' => 'duvida',
            'status' => 'resolvido',
            'assigned_to' => 1,
            'responded_at' => now()->subHours(1),
            'admin_notes' => 'Explicado que precisa ter permissão categories.manage',
            'created_at' => now()->subHours(4)
        ]);

        \App\Models\ContactMessage::create([
            'name' => 'Ana Costa',
            'email' => 'ana.costa@ubs.gov.br',
            'subject' => 'Sugestão de melhoria',
            'message' => 'Seria interessante ter um relatório mensal automatizado dos chamados por UBS. Isso facilitaria muito nossa gestão.',
            'type' => 'sugestao',
            'status' => 'pendente',
            'created_at' => now()->subDays(1)
        ]);

        \App\Models\ContactMessage::create([
            'name' => 'Carlos Pereira',
            'email' => 'carlos.pereira@ti.com',
            'subject' => 'Integração com sistema externo',
            'message' => 'Estamos precisando integrar o sistema de chamados com nosso ERP. Vocês têm API disponível para isso?',
            'type' => 'suporte',
            'status' => 'pendente',
            'created_at' => now()->subHours(6)
        ]);
    }
}
