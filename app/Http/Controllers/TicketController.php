<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\TicketComment;
use App\Models\TicketTag;
use App\Models\User;
use App\Notifications\TicketCreated;
use App\Notifications\TicketStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class TicketController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        
        // Verificação de permissão baseada em roles
        if (!in_array($user->role, ['admin', 'technician', 'customer'])) {
            abort(403, 'Você não tem permissão para visualizar chamados.');
        }

        $query = Ticket::with(['user', 'category', 'assignedUser', 'location', 'tags'])
            ->orderBy('created_at', 'desc');

        // Busca avançada
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('category', function($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('tags', function($tq) use ($search) {
                      $tq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filtros específicos
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por tags
        if ($request->filled('tags')) {
            $tagIds = is_array($request->tags) ? $request->tags : explode(',', $request->tags);
            $query->whereHas('tags', function($q) use ($tagIds) {
                $q->whereIn('ticket_tags.id', $tagIds);
            });
        }

        // Filtro por técnico responsável
        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        // Filtro por data de criação
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Filtro por data de vencimento
        if ($request->filled('due_date_from')) {
            $query->whereDate('due_date', '>=', $request->due_date_from);
        }

        if ($request->filled('due_date_to')) {
            $query->whereDate('due_date', '<=', $request->due_date_to);
        }

        // Filtro para chamados vencidos
        if ($request->filled('overdue') && $request->overdue == '1') {
            $query->where('due_date', '<', now())
                  ->whereNotIn('status', ['resolved', 'closed']);
        }

        // Filtro por usuário baseado no role
        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'technician') {
            $query->where('assigned_to', $user->id);
        }
        // Admin vê todos os tickets

        $tickets = $query->paginate(15);
        $categories = Category::all();
        
        // Dados para filtros avançados
        $tags = TicketTag::where('is_active', true)->get();
        $technicians = \App\Models\User::where('role', 'technician')->get();
        
        // Usuários para ações em lote (admin e técnicos)
        $bulkUsers = \App\Models\User::whereIn('role', ['admin', 'technician'])->select('id', 'name')->get();
        
        // Estatísticas rápidas
        $stats = [
            'total' => Ticket::count(),
            'open' => Ticket::whereIn('status', ['open', 'in_progress'])->count(),
            'overdue' => Ticket::where('due_date', '<', now())
                              ->whereNotIn('status', ['resolved', 'closed'])->count(),
        ];

        return view('tickets.index', compact('tickets', 'categories', 'tags', 'technicians', 'stats', 'bulkUsers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('active', true)->get();
        $tags = TicketTag::where('is_active', true)->get();
        $locations = \App\Models\Location::where('is_active', true)->get();
        return view('tickets.create', compact('categories', 'tags', 'locations'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'priority' => 'required|in:low,medium,high,urgent',
            'location_id' => 'required|exists:locations,id',
            'local' => 'nullable|string|max:255',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:ticket_tags,id',
        ]);

        $user = Auth::user();
        
        // Se o usuário não tem localização definida, atribuir automaticamente a localização do chamado
        if (!$user->location_id) {
            User::where('id', $user->id)->update(['location_id' => $request->location_id]);
        }

        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'priority' => $request->priority,
            'location_id' => $request->location_id,
            'local' => $request->local,
            'user_id' => Auth::id(),
            'status' => 'open',
        ]);

        // Anexar tags se fornecidas
        if ($request->has('tags')) {
            $ticket->tags()->attach($request->tags);
        }

        // Enviar notificação para administradores e técnicos
        $adminUsers = User::whereIn('role', ['admin', 'technician'])->get();
        foreach ($adminUsers as $admin) {
            $admin->notify(new TicketCreated($ticket));
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Chamado criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Ticket $ticket)
    {
        // Verificar permissão
        $user = Auth::user();
        if ($user->role === 'customer' && $ticket->user_id !== $user->id) {
            abort(403, 'Acesso negado.');
        }

        $ticket->load(['user', 'category', 'assignedUser', 'location', 'comments.user']);
        
        return view('tickets.show', compact('ticket'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Ticket $ticket)
    {
        // Apenas admin e técnico podem editar
        $user = Auth::user();
        if (!in_array($user->role, ['admin', 'technician'])) {
            abort(403, 'Acesso negado.');
        }

        $categories = Category::where('active', true)->get();
        $technicians = \App\Models\User::where('role', 'technician')->get();
        $locations = \App\Models\Location::where('is_active', true)->get();

        return view('tickets.edit', compact('ticket', 'categories', 'technicians', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        $originalStatus = $ticket->status;
        
        // Validação baseada no role
        $rules = [];
        if (in_array($user->role, ['admin', 'technician'])) {
            $rules = [
                'status' => 'required|in:open,in_progress,waiting,resolved,closed',
                'priority' => 'required|in:low,medium,high,urgent',
                'assigned_to' => 'nullable|exists:users,id',
                'category_id' => 'required|exists:categories,id',
                'location_id' => 'nullable|exists:locations,id',
                'local' => 'nullable|string|max:255',
                'tags' => 'nullable|array',
                'tags.*' => 'exists:ticket_tags,id',
            ];
            
            if ($user->role === 'admin') {
                $rules['title'] = 'required|string|max:255';
                $rules['description'] = 'required|string';
            }
        }

        $request->validate($rules);

        $updateData = $request->only(['status', 'priority', 'assigned_to', 'category_id', 'location_id', 'local']);
        
        if ($user->role === 'admin') {
            $updateData = array_merge($updateData, $request->only(['title', 'description']));
        }
        
        $ticket->update($updateData);

        // Atualizar tags se fornecidas
        if ($request->has('tags')) {
            $ticket->tags()->sync($request->tags);
        }

        // Enviar notificação se o status mudou
        if ($originalStatus !== $ticket->status) {
            // Notificar o criador do ticket
            $ticket->user->notify(new TicketStatusChanged($ticket, $originalStatus, $ticket->status));
            
            // Notificar técnico assignado se houver
            if ($ticket->assignedUser && $ticket->assignedUser->id !== $ticket->user_id) {
                $ticket->assignedUser->notify(new TicketStatusChanged($ticket, $originalStatus, $ticket->status));
            }
        }

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Chamado atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Ticket $ticket)
    {
        // Apenas admin pode deletar
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Acesso negado.');
        }

        $ticket->delete();

        return redirect()->route('tickets.index')
            ->with('success', 'Chamado removido com sucesso!');
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => 'required|string'
        ]);

        TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return redirect()->route('tickets.show', $ticket)
            ->with('success', 'Comentário adicionado com sucesso!');
    }

    /**
     * API: Retorna todos os tickets agrupados por status para o painel TV
     */
    public function apiAll()
    {
        $tickets = [
            'open' => Ticket::with(['category', 'assignedUser'])->where('status', 'open')->get(),
            'in_progress' => Ticket::with(['category', 'assignedUser'])->where('status', 'in_progress')->get(),
            'resolved' => Ticket::with(['category', 'assignedUser'])->where('status', 'resolved')->get(),
            'closed' => Ticket::with(['category', 'assignedUser'])->where('status', 'closed')->get(),
        ];
        return response()->json($tickets);
    }

    /**
     * API: Retorna tickets criados após o último check para notificações
     */
    public function apiNew(Request $request)
    {
        $lastCheck = $request->input('last_check');
        $tickets = Ticket::with(['category', 'assignedUser'])
            ->where('created_at', '>', $lastCheck)
            ->get();
        return response()->json([
            'tickets' => $tickets,
            'last_check' => now()->toISOString()
        ]);
    }

    /**
     * API: Atualiza o status de um ticket (drag & drop)
     */
    public function apiUpdateStatus(Request $request)
    {
        $request->validate([
            'ticket_id' => 'required|exists:tickets,id',
            'status' => 'required|in:open,in_progress,resolved,closed',
        ]);
        $ticket = Ticket::findOrFail($request->ticket_id);
        $ticket->status = $request->status;
        $ticket->save();
        return response()->json(['success' => true]);
    }

    /**
     * API: Retorna métricas avançadas para o painel
     */
    public function apiMetrics()
    {
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();
        $metrics = [
            'today' => Ticket::where('created_at', '>=', $today)->count(),
            'yesterday' => Ticket::whereBetween('created_at', [$yesterday, $today])->count(),
            'priority' => [
                'high' => Ticket::where('priority', 'high')->count(),
                'medium' => Ticket::where('priority', 'medium')->count(),
                'low' => Ticket::where('priority', 'low')->count(),
            ],
            'avg_resolution_time' => Ticket::whereNotNull('resolved_at')->avg('resolution_time'),
        ];
        return response()->json($metrics);
    }

    /**
     * API: Exporta métricas em PDF (placeholder)
     */
    public function apiExportMetrics()
    {
        // Aqui você pode gerar um PDF real usando dompdf/snappy
        return response('PDF gerado (placeholder)', 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    /**
     * API: Retorna dados do dashboard executivo
     */
    public function apiDashboard()
    {
        $total = Ticket::count();
        $open = Ticket::where('status', 'open')->count();
        $resolvedToday = Ticket::where('status', 'resolved')->whereDate('updated_at', now()->toDateString())->count();
        $avgTime = Ticket::whereNotNull('resolved_at')->avg('resolution_time');
        $byCategory = Ticket::selectRaw('category_id, count(*) as total')->groupBy('category_id')->get();
        $byPriority = Ticket::selectRaw('priority, count(*) as total')->groupBy('priority')->get();
        return response()->json([
            'total' => $total,
            'open' => $open,
            'resolved_today' => $resolvedToday,
            'avg_time' => $avgTime,
            'by_category' => $byCategory,
            'by_priority' => $byPriority,
        ]);
    }

    /**
     * API: Exporta dashboard em PDF (placeholder)
     */
    public function apiExportDashboard()
    {
        // Aqui você pode gerar um PDF real usando dompdf/snappy
        return response('PDF do dashboard (placeholder)', 200, [
            'Content-Type' => 'application/pdf'
        ]);
    }

    /**
     * Exibe o Painel TV de chamados
     */
    public function boardTv()
    {
        $tickets = \App\Models\Ticket::with(['category', 'user', 'assignedUser'])
            ->orderByDesc('id')->get();
        return view('tickets.board-tv', compact('tickets'));
    }

    /**
     * Exibe o Painel TV de chamados melhorado
     */
    public function boardTvEnhanced()
    {
        $tickets = \App\Models\Ticket::with(['category', 'user', 'user.location', 'assignedUser'])
            ->orderByDesc('id')->get();
            
        // Estatísticas por localização
        $locationStats = \App\Models\Location::withCount([
            'tickets',
            'tickets as open_tickets_count' => function($query) {
                $query->where('status', 'open');
            },
            'tickets as in_progress_tickets_count' => function($query) {
                $query->where('status', 'in_progress');
            },
            'tickets as resolved_tickets_count' => function($query) {
                $query->where('status', 'resolved');
            }
        ])->get();
        
        return view('tickets.board-tv-enhanced', compact('tickets', 'locationStats'));
    }

    /**
     * Exibe o dashboard principal com todos os dados avançados
     */
    public function dashboard()
    {
        $totalTickets = Ticket::count();
        $openTickets = Ticket::where('status', 'open')->count();
        $inProgressTickets = Ticket::where('status', 'in_progress')->count();
        $resolvedTickets = Ticket::where('status', 'resolved')->count();
        $overdueTickets = Ticket::where('due_date', '<', now())->whereNotIn('status', ['resolved', 'closed'])->count();
        $reopenedTickets = 0;
        $categories = \App\Models\Category::withCount('tickets')->get();
        $priorityCount = [
            'high' => Ticket::where('priority','high')->count(),
            'medium' => Ticket::where('priority','medium')->count(),
            'low' => Ticket::where('priority','low')->count(),
        ];
        $latestTickets = Ticket::with(['category'])->orderByDesc('created_at')->limit(10)->get();
        // Ranking de técnicos (exemplo: por tickets resolvidos)
        $ranking = \App\Models\User::where('role','technician')
            ->withCount(['assignedTickets' => function($q){ $q->where('status','resolved'); }])
            ->get()->map(function($u){ $u->score = $u->assigned_tickets_count; return $u; })
            ->sortByDesc('score')->take(5);
        
        // Garantir que sempre temos ao menos um score > 0 ou uma coleção válida
        if ($ranking->isEmpty() || $ranking->max('score') == 0) {
            $ranking = collect();
        }
        // Timeline de atividades recentes (exemplo: últimos tickets criados)
        $activities = Ticket::orderByDesc('created_at')->limit(8)->get()->map(function($t){
            return (object)[
                'title' => $t->title,
                'description' => 'Criado por '.($t->user->name ?? 'Usuário').($t->category ? ' em '.$t->category->name : ''),
                'created_at' => $t->created_at
            ];
        });
        // Satisfação (exemplo: NPS e feedbacks)
        $satisfaction = 9.2; // valor mock
        $feedbacks = 17; // valor mock
        // Evolução dos chamados (mock)
        $evolutionLabels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul'];
        $evolutionData = [12,18,22,15,30,25,28];
        return view('dashboard', compact(
            'totalTickets','openTickets','inProgressTickets','resolvedTickets','overdueTickets','reopenedTickets',
            'categories','priorityCount','latestTickets','ranking','activities','satisfaction','feedbacks','evolutionLabels','evolutionData'
        ));
    }

    /**
     * Painel TV inteligente com lógica aprimorada
     */
    public function boardTvSmart()
    {
        return view('tickets.board-tv-smart');
    }

    /**
     * Painel TV repaginado - versão otimizada
     */
    public function boardTvRepaginado()
    {
        return view('tickets.board-tv-repaginado');
    }

    /**
     * Ações em lote para múltiplos tickets
     */
    public function bulkAction(Request $request)
    {
        $user = Auth::user();
        
        // Verificar permissões
        if (!in_array($user->role, ['admin', 'technician'])) {
            return response()->json([
                'success' => false,
                'message' => 'Você não tem permissão para realizar ações em lote.'
            ], 403);
        }

        $request->validate([
            'ticket_ids' => 'required|array|min:1',
            'ticket_ids.*' => 'exists:tickets,id',
            'action' => 'required|string|in:close,resolve,reopen,assign,change_status,change_priority',
            'assigned_user_id' => 'nullable|exists:users,id',
            'new_status' => 'nullable|string|in:open,in_progress,waiting_customer,resolved,closed,reopened',
            'new_priority' => 'nullable|string|in:low,medium,high,urgent',
            'comment' => 'nullable|string|max:1000'
        ]);

        $ticketIds = $request->ticket_ids;
        $action = $request->action;
        $comment = $request->comment;
        
        $tickets = Ticket::whereIn('id', $ticketIds)->get();
        
        // Verificar se o usuário tem permissão para alterar todos os tickets
        foreach ($tickets as $ticket) {
            if ($user->role === 'technician' && $ticket->assigned_to !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => "Você não tem permissão para alterar o chamado #{$ticket->id}."
                ], 403);
            }
        }

        $updatedCount = 0;
        $errors = [];

        foreach ($tickets as $ticket) {
            try {
                $oldStatus = $ticket->status;
                $updated = false;

                switch ($action) {
                    case 'close':
                        if ($ticket->status !== 'closed') {
                            $ticket->status = 'closed';
                            $ticket->closed_at = now();
                            $ticket->closed_by = $user->id;
                            $updated = true;
                        }
                        break;

                    case 'resolve':
                        if ($ticket->status !== 'resolved') {
                            $ticket->status = 'resolved';
                            $ticket->resolved_at = now();
                            $ticket->resolved_by = $user->id;
                            $updated = true;
                        }
                        break;

                    case 'reopen':
                        if (in_array($ticket->status, ['closed', 'resolved'])) {
                            $ticket->status = 'reopened';
                            $ticket->reopened_at = now();
                            $updated = true;
                        }
                        break;

                    case 'assign':
                        if ($request->assigned_user_id && $ticket->assigned_to !== $request->assigned_user_id) {
                            $ticket->assigned_to = $request->assigned_user_id;
                            $updated = true;
                        }
                        break;

                    case 'change_status':
                        if ($request->new_status && $ticket->status !== $request->new_status) {
                            $ticket->status = $request->new_status;
                            
                            // Definir campos específicos baseados no status
                            switch ($request->new_status) {
                                case 'resolved':
                                    $ticket->resolved_at = now();
                                    $ticket->resolved_by = $user->id;
                                    break;
                                case 'closed':
                                    $ticket->closed_at = now();
                                    $ticket->closed_by = $user->id;
                                    break;
                                case 'in_progress':
                                    if (!$ticket->assigned_to) {
                                        $ticket->assigned_to = $user->id;
                                    }
                                    break;
                            }
                            $updated = true;
                        }
                        break;

                    case 'change_priority':
                        if ($request->new_priority && $ticket->priority !== $request->new_priority) {
                            $ticket->priority = $request->new_priority;
                            $updated = true;
                        }
                        break;
                }

                if ($updated) {
                    $ticket->save();
                    
                    // Adicionar comentário se fornecido
                    if ($comment) {
                        TicketComment::create([
                            'ticket_id' => $ticket->id,
                            'user_id' => $user->id,
                            'comment' => $comment,
                            'is_internal' => false
                        ]);
                    }

                    // Adicionar comentário automático sobre a mudança
                    $autoComment = $this->generateBulkActionComment($action, $oldStatus, $ticket->status, $request);
                    if ($autoComment) {
                        TicketComment::create([
                            'ticket_id' => $ticket->id,
                            'user_id' => $user->id,
                            'comment' => $autoComment,
                            'is_internal' => true
                        ]);
                    }

                    // Notificar sobre mudança de status se necessário
                    if ($oldStatus !== $ticket->status) {
                        $ticket->user->notify(new TicketStatusChanged($ticket, $oldStatus, $ticket->status));
                    }

                    $updatedCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Erro ao atualizar chamado #{$ticket->id}: " . $e->getMessage();
            }
        }

        if ($updatedCount > 0) {
            $message = "✅ {$updatedCount} chamado(s) atualizado(s) com sucesso!";
            if (count($errors) > 0) {
                $message .= " ⚠️ " . count($errors) . " erro(s) encontrado(s).";
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'updated_count' => $updatedCount,
                'errors' => $errors
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Nenhum chamado foi atualizado. ' . implode(' ', $errors),
            'errors' => $errors
        ]);
    }

    /**
     * Gerar comentário automático para ações em lote
     */
    private function generateBulkActionComment($action, $oldStatus, $newStatus, $request)
    {
        switch ($action) {
            case 'close':
                return "📋 Status alterado automaticamente para 'Fechado' via ação em lote.";
            case 'resolve':
                return "✅ Status alterado automaticamente para 'Resolvido' via ação em lote.";
            case 'reopen':
                return "🔄 Chamado reaberto via ação em lote.";
            case 'assign':
                $assignedUser = User::find($request->assigned_user_id);
                return "👤 Chamado atribuído automaticamente para: " . $assignedUser->name . " via ação em lote.";
            case 'change_status':
                return "📊 Status alterado de '{$oldStatus}' para '{$newStatus}' via ação em lote.";
            case 'change_priority':
                return "⚡ Prioridade alterada para '{$request->new_priority}' via ação em lote.";
            default:
                return null;
        }
    }
}
