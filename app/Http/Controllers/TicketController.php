<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\TicketComment;
use App\Models\TicketTag;
use App\Models\User;
use App\Events\TicketCreated;
use App\Events\TicketAssigned;
use App\Events\TicketStatusChanged;
use App\Events\SupportTechnicianAssigned;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $query = Ticket::with(['user', 'category', 'assignedUser', 'supportTechnician', 'supportTechnicians', 'location', 'tags'])
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
        $locations = \App\Models\Location::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();
        
        // Para técnicos e admins: lista de usuários para abrir chamado em nome de
        $users = [];
        $currentUser = Auth::user();
        if ($currentUser && in_array($currentUser->role, ['admin', 'technician'])) {
            $users = User::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'name', 'email']);
        }
        
        return view('tickets.create', compact('categories', 'tags', 'locations', 'users'));
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
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240',
            'requester_user_id' => 'nullable|exists:users,id'
        ]);

        $currentUser = Auth::user();
        
        // Determina quem é o solicitante (user_id do ticket)
        $requesterId = Auth::id();
        if ($request->filled('requester_user_id') && in_array($currentUser->role, ['admin', 'technician'])) {
            // Técnico/Admin pode abrir chamado em nome de outro usuário
            $requesterId = $request->requester_user_id;
        }
        
        // Se o usuário solicitante não tem localização definida, atribuir automaticamente
        $requester = User::find($requesterId);
        if ($requester && !$requester->location_id) {
            $requester->update(['location_id' => $request->location_id]);
        }

        $ticket = Ticket::create([
            'title' => $request->title,
            'description' => $request->description,
            'category_id' => $request->category_id,
            'priority' => $request->priority,
            'location_id' => $request->location_id,
            'local' => $request->local,
            'user_id' => $requesterId,
            'status' => 'open',
        ]);

        // Anexar tags se fornecidas
        if ($request->has('tags')) {
            $ticket->tags()->attach($request->tags);
        }

        // Upload de anexos (opcional)
        if ($request->hasFile('attachments')) {
            $stored = [];
            foreach ($request->file('attachments') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store("tickets/{$ticket->id}", 'public');
                    $stored[] = [
                        'path' => $path,
                        'url' => Storage::disk('public')->url($path),
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now()->toISOString(),
                    ];
                }
            }
            if (!empty($stored)) {
                $existing = $ticket->attachments ?? [];
                $ticket->attachments = array_values(array_merge($existing, $stored));
                $ticket->save();
            }
        }

        // Registrar log de criação
        $createdBy = $currentUser->name;
        if ($requesterId != Auth::id()) {
            // Chamado criado em nome de outro usuário
            $requesterName = $requester->name;
            \App\Models\TicketActivityLog::log(
                $ticket->id,
                'created',
                "{$createdBy} criou este chamado em nome de {$requesterName}",
                [
                    'created_by' => Auth::id(),
                    'created_by_name' => $createdBy,
                    'requester_id' => $requesterId,
                    'requester_name' => $requesterName,
                    'priority' => $request->priority,
                    'category_id' => $request->category_id,
                ],
                $requesterId
            );
        } else {
            // Chamado criado pelo próprio usuário
            \App\Models\TicketActivityLog::log(
                $ticket->id,
                'created',
                "{$createdBy} criou este chamado",
                [
                    'priority' => $request->priority,
                    'category_id' => $request->category_id,
                ]
            );
        }

        // Disparar evento para notificações
        event(new TicketCreated($ticket));

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

        $ticket->load([
            'user', 
            'category', 
            'assignedUser',
            'supportTechnician',
            'supportTechnicians',
            'location', 
            'comments.user',
            'activityLogs.user',
            'activityLogs.targetUser'
        ]);
        
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
        $locations = \App\Models\Location::where('is_active', true)
            ->orderBy('name', 'asc')
            ->get();

        return view('tickets.edit', compact('ticket', 'categories', 'technicians', 'locations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        $originalStatus = $ticket->status;
        $originalAssignedTo = $ticket->assigned_to;
        $originalPriority = $ticket->priority;
        $originalCategory = $ticket->category_id;
        
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

        // Registrar logs de atividade
        $changes = [];
        
        // Log de atribuição/transferência
        if ($originalAssignedTo !== $ticket->assigned_to) {
            if ($ticket->assigned_to) {
                $technician = User::find($ticket->assigned_to);
                if ($originalAssignedTo) {
                    // Transferido
                    $oldTechnician = User::find($originalAssignedTo);
                    \App\Models\TicketActivityLog::log(
                        $ticket->id,
                        'transferred',
                        "{$user->name} transferiu o chamado de {$oldTechnician->name} para {$technician->name}",
                        [
                            'from_user_id' => $originalAssignedTo,
                            'from_user_name' => $oldTechnician->name,
                            'to_user_id' => $ticket->assigned_to,
                            'to_user_name' => $technician->name,
                        ],
                        $ticket->assigned_to
                    );
                } else {
                    // Atribuído pela primeira vez
                    \App\Models\TicketActivityLog::log(
                        $ticket->id,
                        'assigned',
                        "{$user->name} atribuiu o chamado para {$technician->name}",
                        ['assigned_to_id' => $ticket->assigned_to, 'assigned_to_name' => $technician->name],
                        $ticket->assigned_to
                    );
                }
                event(new TicketAssigned($ticket, $technician));
            }
        }

        // Log de mudança de status
        if ($originalStatus !== $ticket->status) {
            $statusLabels = [
                'open' => 'Aberto',
                'in_progress' => 'Em Andamento',
                'waiting' => 'Aguardando',
                'resolved' => 'Resolvido',
                'closed' => 'Fechado'
            ];
            \App\Models\TicketActivityLog::log(
                $ticket->id,
                'status_changed',
                "{$user->name} alterou o status de '{$statusLabels[$originalStatus]}' para '{$statusLabels[$ticket->status]}'",
                ['from' => $originalStatus, 'to' => $ticket->status]
            );
            event(new TicketStatusChanged($ticket, $originalStatus, $ticket->status));
        }

        // Log de mudança de prioridade
        if ($originalPriority !== $ticket->priority) {
            $priorityLabels = ['low' => 'Baixa', 'medium' => 'Média', 'high' => 'Alta', 'urgent' => 'Urgente'];
            \App\Models\TicketActivityLog::log(
                $ticket->id,
                'priority_changed',
                "{$user->name} alterou a prioridade de '{$priorityLabels[$originalPriority]}' para '{$priorityLabels[$ticket->priority]}'",
                ['from' => $originalPriority, 'to' => $ticket->priority]
            );
        }

        // Log de atualização geral (se houver outras mudanças)
        if ($user->role === 'admin' && ($request->has('title') || $request->has('description') || $originalCategory !== $ticket->category_id)) {
            \App\Models\TicketActivityLog::log(
                $ticket->id,
                'updated',
                "{$user->name} atualizou as informações do chamado",
                ['fields_updated' => array_keys($updateData)]
            );
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
     * Assign support technician to ticket (new version - multiple support)
     */
    public function assignSupportTechnician(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        
        // Apenas admin e técnicos podem atribuir suporte
        if (!in_array($user->role, ['admin', 'technician'])) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

        $request->validate([
            'support_technician_id' => 'required|exists:users,id'
        ]);

        $supportTechnicianId = $request->support_technician_id;
        
        // Validar que o técnico de suporte não é o mesmo que o principal
        if ($ticket->assigned_to == $supportTechnicianId) {
            return response()->json([
                'error' => 'O técnico de suporte não pode ser o mesmo que o técnico principal.'
            ], 422);
        }

        // Verificar se já está na lista de suporte
        if ($ticket->supportTechnicians()->where('user_id', $supportTechnicianId)->exists()) {
            return response()->json([
                'error' => 'Este técnico já está na equipe de suporte.'
            ], 422);
        }

        // Validar que o usuário selecionado é técnico
        $supportTechnician = User::findOrFail($supportTechnicianId);
        if (!in_array($supportTechnician->role, ['admin', 'technician'])) {
            return response()->json([
                'error' => 'O usuário selecionado não é um técnico.'
            ], 422);
        }

        // Adicionar à equipe de suporte
        $ticket->supportTechnicians()->attach($supportTechnicianId, [
            'assigned_at' => now(),
            'assigned_by' => $user->id
        ]);

        // Registrar log de atividade
        \App\Models\TicketActivityLog::log(
            $ticket->id,
            'support_assigned',
            "{$user->name} adicionou {$supportTechnician->name} à equipe de suporte",
            [
                'support_technician_id' => $supportTechnicianId,
                'support_technician_name' => $supportTechnician->name
            ],
            $supportTechnicianId
        );

        // Disparar evento de notificação
        event(new SupportTechnicianAssigned($ticket, $supportTechnician));

        return response()->json([
            'success' => true,
            'message' => 'Técnico adicionado à equipe de suporte!',
            'ticket' => $ticket->fresh()->load(['supportTechnicians', 'assignedUser'])
        ]);
    }

    /**
     * Remove support technician from ticket (new version - multiple support)
     */
    public function removeSupportTechnician(Request $request, Ticket $ticket)
    {
        $user = Auth::user();
        
        // Apenas admin e técnicos podem remover suporte
        if (!in_array($user->role, ['admin', 'technician'])) {
            return response()->json(['error' => 'Acesso negado.'], 403);
        }

        $request->validate([
            'support_technician_id' => 'required|exists:users,id'
        ]);

        $supportTechnicianId = $request->support_technician_id;

        // Verificar se o técnico está na equipe de suporte
        if (!$ticket->supportTechnicians()->where('user_id', $supportTechnicianId)->exists()) {
            return response()->json([
                'error' => 'Este técnico não está na equipe de suporte.'
            ], 422);
        }

        $supportTechnician = User::find($supportTechnicianId);
        
        // Remover da equipe de suporte
        $ticket->supportTechnicians()->detach($supportTechnicianId);

        // Registrar log de atividade
        \App\Models\TicketActivityLog::log(
            $ticket->id,
            'support_removed',
            "{$user->name} removeu {$supportTechnician->name} da equipe de suporte",
            [
                'removed_user_id' => $supportTechnician->id,
                'removed_user_name' => $supportTechnician->name
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Técnico removido da equipe de suporte!',
            'ticket' => $ticket->fresh()->load(['supportTechnicians'])
        ]);
    }

    /**
     * Add comment to ticket
     */
    public function addComment(Request $request, Ticket $ticket)
    {
        $request->validate([
            'comment' => 'nullable|string',
            'attachments' => 'nullable|array',
            'attachments.*' => 'file|max:10240'
        ]);

        if (!$request->filled('comment') && !$request->hasFile('attachments')) {
            return redirect()->back()->withErrors(['comment' => 'Informe um comentário ou selecione ao menos um arquivo.'])->withInput();
        }

        $comment = TicketComment::create([
            'ticket_id' => $ticket->id,
            'user_id' => Auth::id(),
            'comment' => $request->input('comment', ''),
        ]);

        if ($request->hasFile('attachments')) {
            $stored = [];
            foreach ($request->file('attachments') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store("tickets/{$ticket->id}/comments/{$comment->id}", 'public');
                    $stored[] = [
                        'path' => $path,
                        'url' => Storage::disk('public')->url($path),
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getClientMimeType(),
                        'size' => $file->getSize(),
                        'uploaded_by' => Auth::id(),
                        'uploaded_at' => now()->toISOString(),
                    ];
                }
            }
            if (!empty($stored)) {
                $comment->attachments = $stored;
                $comment->save();
            }
        }

        // Registrar log de comentário
        $user = Auth::user();
        $commentPreview = strlen($request->input('comment', '')) > 50 
            ? substr($request->input('comment', ''), 0, 50) . '...' 
            : $request->input('comment', '');
        
        \App\Models\TicketActivityLog::log(
            $ticket->id,
            'commented',
            "{$user->name} adicionou um comentário" . ($request->hasFile('attachments') ? ' com anexos' : ''),
            ['comment_id' => $comment->id, 'comment_preview' => $commentPreview]
        );

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
    public function apiExportMetrics(Request $request)
    {
        // Normaliza filtros vindos do dashboard
        $status = $request->query('status');
        $priority = $request->query('priority');
        $categoryId = $request->query('category_id', $request->query('category'));
        $dateFrom = $request->query('date_from', $request->query('date_start'));
        $dateTo = $request->query('date_to', $request->query('date_end'));
        $search = $request->query('search');

        // Base query com filtros aplicados
        $base = Ticket::query();
        if ($status) { $base->where('status', $status); }
        if ($priority) { $base->where('priority', $priority); }
        if ($categoryId) { $base->where('category_id', $categoryId); }
        if ($dateFrom) { $base->whereDate('created_at', '>=', $dateFrom); }
        if ($dateTo) { $base->whereDate('created_at', '<=', $dateTo); }
        if ($search) {
            $base->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Dados de métricas (alinhados ao apiMetrics) respeitando filtros + janelas de data
        $today = now()->startOfDay();
        $yesterday = now()->subDay()->startOfDay();

        $metrics = [
            'today' => (clone $base)->where('created_at', '>=', $today)->count(),
            'yesterday' => (clone $base)->whereBetween('created_at', [$yesterday, $today])->count(),
            'priority' => [
                'urgent' => (clone $base)->where('priority', 'urgent')->count(),
                'high' => (clone $base)->where('priority', 'high')->count(),
                'medium' => (clone $base)->where('priority', 'medium')->count(),
                'low' => (clone $base)->where('priority', 'low')->count(),
            ],
            'avg_resolution_time' => round((float) (clone $base)->whereNotNull('resolved_at')->avg('resolution_time'), 2),
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        $format = strtolower($request->query('format', 'pdf'));
        $filename = 'metricas_chamados_'.now()->format('Ymd_His');

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($metrics) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Métrica', 'Valor']);
                fputcsv($out, ['Hoje', $metrics['today']]);
                fputcsv($out, ['Ontem', $metrics['yesterday']]);
                fputcsv($out, ['Urgente', $metrics['priority']['urgent']]);
                fputcsv($out, ['Alta', $metrics['priority']['high']]);
                fputcsv($out, ['Média', $metrics['priority']['medium']]);
                fputcsv($out, ['Baixa', $metrics['priority']['low']]);
                fputcsv($out, ['Tempo médio resolução (h)', $metrics['avg_resolution_time']]);
                fclose($out);
            }, $filename.'.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8'
            ]);
        }

        // PDF por padrão
        $pdf = Pdf::loadView('exports.metrics', [
            'metrics' => $metrics,
            'appName' => config('app.name'),
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename.'.pdf');
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
    public function apiExportDashboard(Request $request)
    {
        // Normaliza filtros vindos do dashboard
        $status = $request->query('status');
        $priority = $request->query('priority');
        $categoryId = $request->query('category_id', $request->query('category'));
        $assignedTo = $request->query('assigned_to');
        $dateFrom = $request->query('date_from', $request->query('date_start'));
        $dateTo = $request->query('date_to', $request->query('date_end'));
        $search = $request->query('search');

        // Base query com filtros aplicados
        $base = Ticket::query();
        if ($status) { $base->where('status', $status); }
        if ($priority) { $base->where('priority', $priority); }
        if ($categoryId) { $base->where('category_id', $categoryId); }
        if ($assignedTo) { $base->where('assigned_to', $assignedTo); }
        if ($dateFrom) { $base->whereDate('created_at', '>=', $dateFrom); }
        if ($dateTo) { $base->whereDate('created_at', '<=', $dateTo); }
        if ($search) {
            $base->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Monta dados do dashboard (alinhados ao apiDashboard) aplicando filtros
        $total = (clone $base)->count();
        $open = (clone $base)->where('status', 'open')->count();
        $inProgress = (clone $base)->where('status', 'in_progress')->count();
        $resolvedToday = (clone $base)->where('status', 'resolved')->whereDate('updated_at', now()->toDateString())->count();
        $avgTime = round((float) (clone $base)->whereNotNull('resolved_at')->avg('resolution_time'), 2);
        $byCategory = (clone $base)->selectRaw('category_id, count(*) as total')
                                   ->groupBy('category_id')->get();
        $byPriority = (clone $base)->selectRaw('priority, count(*) as total')
                                   ->groupBy('priority')->get();

        $data = [
            'totals' => compact('total','open','inProgress','resolvedToday','avgTime'),
            'byCategory' => $byCategory,
            'byPriority' => $byPriority,
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        $format = strtolower($request->query('format', 'pdf'));
        $filename = 'dashboard_chamados_'.now()->format('Ymd_His');

        if ($format === 'csv') {
            return response()->streamDownload(function () use ($data) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Resumo', 'Valor']);
                fputcsv($out, ['Total', $data['totals']['total']]);
                fputcsv($out, ['Abertos', $data['totals']['open']]);
                fputcsv($out, ['Em andamento', $data['totals']['inProgress']]);
                fputcsv($out, ['Resolvidos hoje', $data['totals']['resolvedToday']]);
                fputcsv($out, ['Tempo médio resolução (h)', $data['totals']['avgTime']]);
                fputcsv($out, []);
                fputcsv($out, ['Por Categoria', 'Qtd']);
                foreach ($data['byCategory'] as $row) {
                    $name = optional(\App\Models\Category::find($row->category_id))->name ?? 'Sem categoria';
                    fputcsv($out, [$name, $row->total]);
                }
                fputcsv($out, []);
                fputcsv($out, ['Por Prioridade', 'Qtd']);
                foreach ($data['byPriority'] as $row) {
                    fputcsv($out, [ucfirst($row->priority), $row->total]);
                }
                fclose($out);
            }, $filename.'.csv', [
                'Content-Type' => 'text/csv; charset=UTF-8'
            ]);
        }

        $pdf = Pdf::loadView('exports.dashboard', [
            'data' => $data,
            'appName' => config('app.name'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download($filename.'.pdf');
    }

    /**
     * Preview em tela para exportação do dashboard (template para impressão)
     */
    public function dashboardExportPreview(Request $request)
    {
        // Reutiliza a lógica de filtros do export para manter consistência
        $status = $request->query('status');
        $priority = $request->query('priority');
        $categoryId = $request->query('category_id', $request->query('category'));
        $assignedTo = $request->query('assigned_to');
        $dateFrom = $request->query('date_from', $request->query('date_start'));
        $dateTo = $request->query('date_to', $request->query('date_end'));
        $search = $request->query('search');

        $base = Ticket::query();
        if ($status) { $base->where('status', $status); }
        if ($priority) { $base->where('priority', $priority); }
        if ($categoryId) { $base->where('category_id', $categoryId); }
        if ($assignedTo) { $base->where('assigned_to', $assignedTo); }
        if ($dateFrom) { $base->whereDate('created_at', '>=', $dateFrom); }
        if ($dateTo) { $base->whereDate('created_at', '<=', $dateTo); }
        if ($search) {
            $base->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $total = (clone $base)->count();
        $open = (clone $base)->where('status', 'open')->count();
        $inProgress = (clone $base)->where('status', 'in_progress')->count();
        $resolvedToday = (clone $base)->where('status', 'resolved')->whereDate('updated_at', now()->toDateString())->count();
        $avgTime = round((float) (clone $base)->whereNotNull('resolved_at')->avg('resolution_time'), 2);
        $byCategory = (clone $base)->selectRaw('category_id, count(*) as total')->groupBy('category_id')->get();
        $byPriority = (clone $base)->selectRaw('priority, count(*) as total')->groupBy('priority')->get();

        // Monta dataset + resumo de filtros para exibir no template
        $data = [
            'totals' => compact('total','open','inProgress','resolvedToday','avgTime'),
            'byCategory' => $byCategory,
            'byPriority' => $byPriority,
            'generated_at' => now()->format('d/m/Y H:i'),
        ];

        $appliedFilters = array_filter([
            'status' => $status,
            'priority' => $priority,
            'category_id' => $categoryId,
            'assigned_to' => $assignedTo,
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'search' => $search,
        ], function($v) { return !is_null($v) && $v !== '';} );
        
        // Se tem técnico selecionado, busca o nome para exibir
        $technicianName = null;
        if ($assignedTo) {
            $technician = \App\Models\User::find($assignedTo);
            $technicianName = $technician ? $technician->name : null;
        }

        return view('exports.dashboard_preview', [
            'data' => $data,
            'filters' => $appliedFilters,
            'technicianName' => $technicianName,
            'appName' => config('app.name'),
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
        $tickets = \App\Models\Ticket::with(['category', 'user', 'location', 'assignedUser'])
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
        
        // Calcular Satisfação baseada em métricas reais
        // NPS (Net Promoter Score) simulado baseado em:
        // - Taxa de resolução de chamados
        // - Tempo médio de resolução
        // - Chamados reabertos (indicador negativo)
        
        $totalResolved = Ticket::where('status', 'resolved')->count();
        $totalClosed = Ticket::where('status', 'closed')->count();
        $totalReopened = Ticket::where('status', 'reopened')->count();
        $allTickets = Ticket::count();
        
        // Taxa de resolução (peso 40%)
        $resolutionRate = $allTickets > 0 ? (($totalResolved + $totalClosed) / $allTickets) * 40 : 0;
        
        // Tempo médio de resolução (peso 30%)
        // Quanto menor o tempo, maior a satisfação
        $avgResolutionTime = Ticket::whereNotNull('resolution_time')
            ->where('resolution_time', '>', 0)
            ->avg('resolution_time') ?? 48; // default 48h
        
        // Escala: < 24h = 30 pontos, 24-48h = 25, 48-72h = 20, > 72h = 15
        if ($avgResolutionTime < 24) {
            $timeScore = 30;
        } elseif ($avgResolutionTime < 48) {
            $timeScore = 25;
        } elseif ($avgResolutionTime < 72) {
            $timeScore = 20;
        } else {
            $timeScore = 15;
        }
        
        // Taxa de retrabalho (peso 30%)
        // Quanto menos chamados reabertos, melhor
        $reopenRate = $allTickets > 0 ? ($totalReopened / $allTickets) : 0;
        $reopenScore = (1 - $reopenRate) * 30;
        
        // NPS final (escala 0-10)
        $npsScore = ($resolutionRate + $timeScore + $reopenScore) / 10;
        $satisfaction = number_format($npsScore, 1);
        
        // Contar feedbacks/avaliações (usar comentários como proxy)
        $feedbacks = TicketComment::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
        
        // Calcular mudança de feedbacks em relação ao mês anterior
        $previousFeedbacks = TicketComment::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();
        $feedbackChange = $feedbacks - $previousFeedbacks;
        
        // Calcular rating médio baseado em resolução rápida
        // Tickets resolvidos em menos de 24h = 5 estrelas
        // 24-48h = 4.5, 48-72h = 4, > 72h = 3.5
        $recentResolved = Ticket::whereNotNull('resolution_time')
            ->whereMonth('resolved_at', now()->month)
            ->whereYear('resolved_at', now()->year)
            ->get();
        
        $ratingSum = 0;
        $ratingCount = $recentResolved->count();
        
        foreach ($recentResolved as $ticket) {
            if ($ticket->resolution_time < 24) {
                $ratingSum += 5.0;
            } elseif ($ticket->resolution_time < 48) {
                $ratingSum += 4.5;
            } elseif ($ticket->resolution_time < 72) {
                $ratingSum += 4.0;
            } else {
                $ratingSum += 3.5;
            }
        }
        
        $averageRating = $ratingCount > 0 ? number_format($ratingSum / $ratingCount, 1) : '4.6';
        
        // Dados para o Mapa de Chamados (distribuição por localização)
        $locationData = \App\Models\Location::withCount([
            'tickets as total_tickets',
            'tickets as open_tickets' => function($query) {
                $query->where('status', 'open');
            },
            'tickets as in_progress_tickets' => function($query) {
                $query->where('status', 'in_progress');
            },
            'tickets as resolved_tickets' => function($query) {
                $query->where('status', 'resolved');
            }
        ])
        ->where('is_active', true)
        ->having('total_tickets', '>', 0)
        ->orderByDesc('total_tickets')
        ->limit(10)
        ->get()
        ->map(function($location) {
            return [
                'name' => $location->short_name ?? $location->name,
                'full_name' => $location->name,
                'city' => $location->city,
                'total' => $location->total_tickets,
                'open' => $location->open_tickets,
                'in_progress' => $location->in_progress_tickets,
                'resolved' => $location->resolved_tickets,
                'address' => $location->full_address
            ];
        });
        
        // Evolução dos chamados (mock)
        $evolutionLabels = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul'];
        $evolutionData = [12,18,22,15,30,25,28];
        
        return view('dashboard', compact(
            'totalTickets','openTickets','inProgressTickets','resolvedTickets','overdueTickets','reopenedTickets',
            'categories','priorityCount','latestTickets','ranking','activities','satisfaction','feedbacks','feedbackChange','evolutionLabels','evolutionData','averageRating','locationData'
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

                    // Disparar evento para mudança de status
                    if ($oldStatus !== $ticket->status) {
                        event(new TicketStatusChanged($ticket, $oldStatus, $ticket->status));
                    }

                    // Disparar evento para atribuição
                    if ($action === 'assign' && $request->assigned_user_id) {
                        $technician = User::find($request->assigned_user_id);
                        event(new TicketAssigned($ticket, $technician));
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
