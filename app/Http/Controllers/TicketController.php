<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\TicketComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        // Verifica se o usuário pode ver algum tipo de ticket
        if (!$user->hasPermission('tickets.view.own') && !$user->hasPermission('tickets.view.all')) {
            abort(403, 'Você não tem permissão para visualizar chamados.');
        }

        $query = Ticket::with(['user', 'category', 'assignedUser'])
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filtro por usuário baseado nas permissões
        if ($user->hasPermission('tickets.view.all')) {
            // Pode ver todos os chamados
        } elseif ($user->hasPermission('tickets.view.own')) {
            // Só pode ver os próprios chamados
            $query->where('user_id', $user->id);
        } else {
            // Fallback para o sistema antigo baseado em roles
            if ($user->role === 'customer') {
                $query->where('user_id', $user->id);
            } elseif ($user->role === 'technician') {
                $query->where('assigned_to', $user->id);
            }
        }

        $tickets = $query->paginate(15);
        $categories = Category::all();

        return view('tickets.index', compact('tickets', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::where('active', true)->get();
        return view('tickets.create', compact('categories'));
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
        ]);

        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'priority' => $request->priority,
            'user_id' => Auth::id(),
            'status' => 'open',
        ]);

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

        $ticket->load(['user', 'category', 'assignedUser', 'comments.user']);
        
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

        return view('tickets.edit', compact('ticket', 'categories', 'technicians'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        
        // Validação baseada no role
        $rules = [];
        if (in_array($user->role, ['admin', 'technician'])) {
            $rules = [
                'status' => 'required|in:open,in_progress,waiting,resolved,closed',
                'priority' => 'required|in:low,medium,high,urgent',
                'assigned_to' => 'nullable|exists:users,id',
                'category_id' => 'required|exists:categories,id',
            ];
            
            if ($user->role === 'admin') {
                $rules['title'] = 'required|string|max:255';
                $rules['description'] = 'required|string';
            }
        }

        $request->validate($rules);

        $updateData = $request->only(array_keys($rules));
        $ticket->update($updateData);

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
        $tickets = \App\Models\Ticket::with(['category', 'user', 'assignedTo'])
            ->orderByDesc('id')->get();
        return view('tickets.board-tv', compact('tickets'));
    }

    /**
     * Exibe o Painel TV de chamados melhorado
     */
    public function boardTvEnhanced()
    {
        $tickets = \App\Models\Ticket::with(['category', 'user', 'assignedTo'])
            ->orderByDesc('id')->get();
        return view('tickets.board-tv-enhanced', compact('tickets'));
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
            ->get()->map(function($u){ $u->score = $u->assigned_tickets_count; return $u; })->sortByDesc('score')->take(5);
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
}
