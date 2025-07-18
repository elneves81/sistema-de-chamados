<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ticket;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;

class TicketSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        $categories = Category::all();

        if ($users->isEmpty() || $categories->isEmpty()) {
            return;
        }

        $tickets = [
            [
                'title' => 'Problema no Sistema de Login',
                'description' => 'Usuário não consegue fazer login no sistema',
                'priority' => 'high',
                'status' => 'open',
                'user_id' => $users->where('role', 'client')->first()->id ?? $users->first()->id,
                'category_id' => $categories->where('name', 'Técnico')->first()->id ?? $categories->first()->id,
                'due_date' => Carbon::now()->addDays(2)
            ],
            [
                'title' => 'Solicitação de Nova Funcionalidade',
                'description' => 'Implementar relatório de vendas mensal',
                'priority' => 'medium',
                'status' => 'in_progress',
                'user_id' => $users->where('role', 'client')->first()->id ?? $users->first()->id,
                'assigned_to' => $users->where('role', 'technician')->first()->id ?? null,
                'category_id' => $categories->where('name', 'Funcionalidade')->first()->id ?? $categories->first()->id,
                'due_date' => Carbon::now()->addWeek()
            ],
            [
                'title' => 'Dúvida sobre Configuração',
                'description' => 'Como configurar notificações por email?',
                'priority' => 'low',
                'status' => 'waiting',
                'user_id' => $users->where('role', 'client')->first()->id ?? $users->first()->id,
                'category_id' => $categories->where('name', 'Suporte')->first()->id ?? $categories->first()->id,
                'due_date' => Carbon::now()->addDays(5)
            ],
            [
                'title' => 'Sistema Fora do Ar - URGENTE',
                'description' => 'Sistema principal não está respondendo, afetando todos os usuários',
                'priority' => 'urgent',
                'status' => 'open',
                'user_id' => $users->where('role', 'client')->first()->id ?? $users->first()->id,
                'category_id' => $categories->where('name', 'Técnico')->first()->id ?? $categories->first()->id,
                'due_date' => Carbon::now()->addHours(4)
            ],
            [
                'title' => 'Ticket Resolvido',
                'description' => 'Problema já foi solucionado',
                'priority' => 'medium',
                'status' => 'resolved',
                'user_id' => $users->where('role', 'client')->first()->id ?? $users->first()->id,
                'assigned_to' => $users->where('role', 'technician')->first()->id ?? null,
                'category_id' => $categories->where('name', 'Suporte')->first()->id ?? $categories->first()->id,
                'resolved_at' => Carbon::now()->subDays(1),
                'due_date' => Carbon::now()->addDays(3)
            ],
            [
                'title' => 'Ticket Fechado',
                'description' => 'Ticket finalizado pelo cliente',
                'priority' => 'low',
                'status' => 'closed',
                'user_id' => $users->where('role', 'client')->first()->id ?? $users->first()->id,
                'assigned_to' => $users->where('role', 'technician')->first()->id ?? null,
                'category_id' => $categories->where('name', 'Suporte')->first()->id ?? $categories->first()->id,
                'resolved_at' => Carbon::now()->subDays(2),
                'closed_at' => Carbon::now()->subDays(1),
                'due_date' => Carbon::now()->addDays(1)
            ]
        ];

        foreach ($tickets as $ticketData) {
            Ticket::create($ticketData);
        }
    }
}
