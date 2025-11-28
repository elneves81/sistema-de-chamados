<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TechnicianDashboardController extends Controller
{
    /**
     * Monta as coleções de tickets por coluna, aplicando as regras de deduplicação
     * e escopo por técnico vs admin.
     */
    private function buildKanbanTickets(User $user): array
    {
        $isAdmin = $user->role === 'admin';

        return [
            'available' => Ticket::with(['user', 'category', 'location'])
                ->whereNull('assigned_to')
                ->where('status', 'open')
                ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 END")
                ->orderBy('created_at', 'asc')
                ->get(),

            'my_tickets' => Ticket::with(['user', 'category', 'location'])
                ->where('assigned_to', $user->id)
                ->where('status', 'open')
                ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 END")
                ->orderBy('created_at', 'asc')
                ->get(),

            'in_progress' => Ticket::with(['user', 'category', 'assignedUser'])
                ->where('status', 'in_progress')
                ->when(!$isAdmin, function ($q) use ($user) { $q->where('assigned_to', $user->id); })
                ->orderByRaw("CASE priority WHEN 'urgent' THEN 1 WHEN 'high' THEN 2 WHEN 'medium' THEN 3 WHEN 'low' THEN 4 END")
                ->orderBy('created_at', 'asc')
                ->get(),

            'waiting' => Ticket::with(['user', 'category', 'assignedUser'])
                ->where('status', 'waiting')
                ->when(!$isAdmin, function ($q) use ($user) { $q->where('assigned_to', $user->id); })
                ->orderBy('created_at', 'asc')
                ->get(),

            'resolved' => Ticket::with(['user', 'category', 'assignedUser'])
                ->where('status', 'resolved')
                ->when(!$isAdmin, function ($q) use ($user) { $q->where('assigned_to', $user->id); })
                // Keep resolved tickets only for the last 24 hours
                ->where(function ($q) {
                    $q->where(function ($q2) {
                        $q2->whereNotNull('resolved_at')
                           ->where('resolved_at', '>=', now()->subDay());
                    })->orWhere(function ($q3) {
                        $q3->whereNull('resolved_at')
                           ->where('updated_at', '>=', now()->subDay());
                    });
                })
                ->orderBy('updated_at', 'desc')
                ->limit(20)
                ->get(),
        ];
    }
    /**
     * Dashboard Kanban para técnicos
     */
    public function index()
    {
        $user = Auth::user();
        
        // Apenas técnicos e admin podem acessar
        if (!in_array($user->role, ['technician', 'admin'])) {
            abort(403, 'Acesso negado.');
        }

        // Buscar tickets agrupados por status através de método compartilhado (evita duplicação e divergência)
        $tickets = $this->buildKanbanTickets($user);

        // Estatísticas do técnico
        $stats = [
            'my_open' => Ticket::where('assigned_to', $user->id)
                ->whereIn('status', ['open', 'in_progress'])
                ->count(),
            'my_completed_today' => Ticket::where('assigned_to', $user->id)
                ->where('status', 'resolved')
                ->whereDate('updated_at', today())
                ->count(),
            'available_tickets' => Ticket::whereNull('assigned_to')
                ->where('status', 'open')
                ->count(),
            'urgent_count' => Ticket::where('priority', 'urgent')
                ->whereIn('status', ['open', 'in_progress'])
                ->count(),
        ];

        return view('technician.dashboard', compact('tickets', 'stats'));
    }

    /**
     * Endpoint AJAX para atualizar as colunas do Kanban sem recarregar a página
     */
    public function refresh()
    {
        $user = Auth::user();
        if (!in_array($user->role, ['technician', 'admin'])) {
            return response()->json(['success' => false, 'message' => 'Acesso negado.'], 403);
        }

        $tickets = $this->buildKanbanTickets($user);

        // Renderizar HTML dos cards por coluna
        $renderColumn = function ($collection) {
            if ($collection->isEmpty()) {
                return '<div class="empty-state"><i class="bi bi-inbox"></i><p>Nenhum chamado</p></div>';
            }
            return $collection->map(function ($ticket) {
                return view('technician.partials.ticket-card', compact('ticket'))->render();
            })->implode('');
        };

        $columns = [
            'available'    => ['html' => $renderColumn($tickets['available']),    'count' => $tickets['available']->count()],
            'my_tickets'   => ['html' => $renderColumn($tickets['my_tickets']),   'count' => $tickets['my_tickets']->count()],
            'in_progress'  => ['html' => $renderColumn($tickets['in_progress']),  'count' => $tickets['in_progress']->count()],
            'waiting'      => ['html' => $renderColumn($tickets['waiting']),      'count' => $tickets['waiting']->count()],
            'resolved'     => ['html' => $renderColumn($tickets['resolved']),     'count' => $tickets['resolved']->count()],
        ];

        return response()->json(['success' => true, 'columns' => $columns]);
    }

    /**
     * API: Atribuir ticket ao técnico via drag and drop
     */
    public function assignTicket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
        ]);

        $user = Auth::user();
        $ticket = Ticket::findOrFail($request->ticket_id);

        // Atribuir ao técnico logado
        $ticket->assigned_to = $user->id;
        $ticket->status = 'in_progress';
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Chamado atribuído com sucesso!',
            'ticket' => $ticket->load(['user', 'category', 'assignedUser'])
        ]);
    }

    /**
     * API: Atualizar status do ticket via drag and drop
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'status' => 'required|in:open,in_progress,waiting,resolved',
        ]);

        $user = Auth::user();
        $ticket = Ticket::findOrFail($request->ticket_id);

        // Verificar se o técnico pode alterar este ticket
        if ($ticket->assigned_to !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para alterar este chamado.'
            ], 403);
        }

        $ticket->status = $request->status;
        
        // Se marcar como resolvido, adicionar data
        if ($request->status === 'resolved') {
            $ticket->resolved_at = now();
        }
        
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Status atualizado com sucesso!',
            'ticket' => $ticket->load(['user', 'category', 'assignedUser'])
        ]);
    }

    /**
     * API: Desatribuir ticket (devolver para disponíveis)
     */
    public function unassignTicket(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
        ]);

        $user = Auth::user();
        $ticket = Ticket::findOrFail($request->ticket_id);

        // Verificar se o técnico pode desatribuir
        if ($ticket->assigned_to !== $user->id && $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para desatribuir este chamado.'
            ], 403);
        }

        $ticket->assigned_to = null;
        $ticket->status = 'open';
        $ticket->save();

        return response()->json([
            'success' => true,
            'message' => 'Chamado devolvido para disponíveis!',
            'ticket' => $ticket->load(['user', 'category'])
        ]);
    }
}
