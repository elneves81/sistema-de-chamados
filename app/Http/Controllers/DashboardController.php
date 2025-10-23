<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['realtimeTickets']);
    }

    public function index()
    {
        $user = Auth::user();
        
        // KPIs baseados no role do usuário
        $data = $this->getKPIs($user);
        
        // Gráficos e estatísticas
        $data['chartData'] = $this->getChartData($user);
        
        // Tickets recentes
        $data['recentTickets'] = $this->getRecentTickets($user);
        
        return view('dashboard.index', $data);
    }

    private function getKPIs($user)
    {
        $data = [];
        
        if ($user->role === 'customer') {
            // KPIs para cliente
            $data['totalTickets'] = Ticket::where('user_id', $user->id)->count();
            $data['openTickets'] = Ticket::where('user_id', $user->id)
                ->whereIn('status', ['open', 'in_progress'])->count();
            $data['resolvedTickets'] = Ticket::where('user_id', $user->id)
                ->where('status', 'resolved')->count();
            $data['avgResponseTime'] = $this->getAvgResponseTime($user->id);
            
        } elseif ($user->role === 'technician') {
            // KPIs para técnico
            $data['assignedTickets'] = Ticket::where('assigned_to', $user->id)->count();
            $data['openTickets'] = Ticket::where('assigned_to', $user->id)
                ->whereIn('status', ['open', 'in_progress'])->count();
            $data['resolvedThisMonth'] = Ticket::where('assigned_to', $user->id)
                ->where('status', 'resolved')
                ->whereMonth('updated_at', now()->month)->count();
            $data['avgResolutionTime'] = $this->getAvgResolutionTime($user->id);
            
        } else { // admin
            // KPIs para admin
            $data['totalTickets'] = Ticket::count();
            $data['openTickets'] = Ticket::whereIn('status', ['open', 'in_progress'])->count();
            $data['totalUsers'] = User::where('role', '!=', 'admin')->count();
            $data['totalCategories'] = Category::where('active', true)->count();
            $data['urgentTickets'] = Ticket::where('priority', 'urgent')
                ->whereIn('status', ['open', 'in_progress'])->count();
            
            // Métricas avançadas
            $data['avgResolutionTime'] = $this->getAvgResolutionTimeHours();
            $data['slaCompliance'] = $this->getSLACompliance();
            $data['customerSatisfaction'] = $this->getCustomerSatisfaction();
            $data['overdueTickets'] = $this->getOverdueTickets();
            $data['ticketsThisMonth'] = $this->getTicketsThisMonth();
            $data['recentGrowth'] = $this->getRecentGrowth();
        }
        
        return $data;
    }

    private function getChartData($user)
    {
        $data = [];
        
        // Tickets por status
        if ($user->role === 'customer') {
            $statusData = Ticket::where('user_id', $user->id)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
        } elseif ($user->role === 'technician') {
            $statusData = Ticket::where('assigned_to', $user->id)
                ->select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
        } else {
            $statusData = Ticket::select('status', DB::raw('count(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');
        }
        
        $data['statusChart'] = [
            'labels' => $statusData->keys()->map(function($status) {
                return ucfirst(str_replace('_', ' ', $status));
            }),
            'data' => $statusData->values()
        ];
        
        // Tickets por prioridade
        if ($user->role === 'admin') {
            $priorityData = Ticket::select('priority', DB::raw('count(*) as total'))
                ->groupBy('priority')
                ->pluck('total', 'priority');
                
            $data['priorityChart'] = [
                'labels' => $priorityData->keys()->map(function($priority) {
                    return ucfirst($priority);
                }),
                'data' => $priorityData->values()
            ];
        }
        
        // Tickets criados nos últimos 7 dias
        $last7Days = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = Ticket::whereDate('created_at', $date);
            
            if ($user->role === 'customer') {
                $count = $count->where('user_id', $user->id);
            } elseif ($user->role === 'technician') {
                $count = $count->where('assigned_to', $user->id);
            }
            
            $last7Days[$date] = $count->count();
        }
        
        $data['weeklyChart'] = [
            'labels' => array_keys($last7Days),
            'data' => array_values($last7Days)
        ];
        
        return $data;
    }

    private function getRecentTickets($user)
    {
        $query = Ticket::with(['user', 'category', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->limit(5);
            
        if ($user->role === 'customer') {
            $query->where('user_id', $user->id);
        } elseif ($user->role === 'technician') {
            $query->where('assigned_to', $user->id);
        }
        
        return $query->get();
    }

    private function getAvgResponseTime($userId)
    {
        // Tempo médio de primeira resposta (em horas)
        $avg = Ticket::where('user_id', $userId)
            ->whereNotNull('assigned_to')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
            ->value('avg_hours');
            
        return $avg ? round($avg, 1) : 0;
    }

    private function getAvgResolutionTime($technicianId)
    {
        // Tempo médio de resolução (em horas)
        $avg = Ticket::where('assigned_to', $technicianId)
            ->where('status', 'resolved')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, updated_at)) as avg_hours')
            ->value('avg_hours');
            
        return $avg ? round($avg, 1) : 0;
    }

    /**
     * Painel de monitoramento em tempo real
     */
    public function monitoring()
    {
        // Verificar se é admin
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }

        $data = [
            'totalTickets' => Ticket::count(),
            'openTickets' => Ticket::where('status', 'open')->count(),
            'urgentTickets' => Ticket::where('priority', 'urgent')->count(),
            'overdueTickets' => Ticket::whereDate('due_date', '<', now())
                                   ->whereNotIn('status', ['resolved', 'closed'])
                                   ->count(),
            'totalUsers' => User::count(),
            'onlineUsers' => User::where('updated_at', '>', now()->subMinutes(5))->count(),
        ];

        return view('admin.monitoring', compact('data'));
    }

    /**
     * API para dados em tempo real do painel TV inteligente
     */
    public function realtimeTickets()
    {
        try {
            // Buscar todos os tickets ativos com informações completas
            $tickets = Ticket::with(['user.location', 'category', 'assignedUser'])
                ->whereNotIn('status', ['closed'])
                ->orderByRaw("
                    CASE priority 
                        WHEN 'urgent' THEN 1 
                        WHEN 'high' THEN 2 
                        WHEN 'medium' THEN 3 
                        WHEN 'low' THEN 4 
                        ELSE 5 
                    END, created_at DESC
                ")
                ->get();

            // Estatísticas gerais
            $stats = [
                'total_tickets' => $tickets->count(),
                'urgent_tickets' => $tickets->where('priority', 'urgent')->count(),
                'high_tickets' => $tickets->where('priority', 'high')->count(),
                'open_tickets' => $tickets->where('status', 'open')->count(),
                'in_progress_tickets' => $tickets->where('status', 'in_progress')->count(),
                'active_ubs' => $tickets->pluck('user.location.id')->filter()->unique()->count()
            ];

            // Estatísticas por UBS
            $ubsStats = \App\Models\Location::with(['users.tickets' => function($query) {
                    $query->whereNotIn('status', ['closed']);
                }])
                ->get()
                ->map(function($location) {
                    $allTickets = $location->users->flatMap->tickets;
                    $urgentTickets = $allTickets->where('priority', 'urgent')->count();
                    $openTickets = $allTickets->where('status', 'open')->count();
                    
                    return [
                        'id' => $location->id,
                        'name' => $location->name,
                        'total_tickets' => $allTickets->count(),
                        'open_tickets' => $openTickets,
                        'urgent_tickets' => $urgentTickets,
                        'in_progress_tickets' => $allTickets->where('status', 'in_progress')->count(),
                        'has_urgent' => $urgentTickets > 0,
                        'has_tickets' => $allTickets->count() > 0
                    ];
                })
                ->filter(function($ubs) {
                    return $ubs['total_tickets'] > 0 || $ubs['has_tickets'];
                })
                ->values();

            // Formatar tickets para o frontend
            $formattedTickets = $tickets->map(function($ticket) {
                return [
                    'id' => $ticket->id,
                    'title' => $ticket->title,
                    'description' => substr($ticket->description, 0, 100) . '...',
                    'priority' => $ticket->priority,
                    'status' => $ticket->status,
                    'user_name' => $ticket->user->name ?? 'N/A',
                    'ubs_name' => $ticket->user->location->name ?? null,
                    'category_name' => $ticket->category->name ?? 'Sem categoria',
                    'assigned_to' => $ticket->assignedUser->name ?? null,
                    'created_at' => $ticket->created_at->toISOString(),
                    'updated_at' => $ticket->updated_at->toISOString(),
                    'age_hours' => $ticket->created_at->diffInHours(now()),
                    'age_text' => $this->formatTicketAge($ticket->created_at)
                ];
            });

            return response()->json([
                'success' => true,
                'stats' => $stats,
                'tickets' => $formattedTickets,
                'ubs_stats' => $ubsStats,
                'last_updated' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Erro na API realtimeTickets: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Erro interno do servidor',
                'stats' => [
                    'total_tickets' => 0,
                    'urgent_tickets' => 0,
                    'active_ubs' => 0
                ],
                'tickets' => [],
                'ubs_stats' => []
            ], 500);
        }
    }

    /**
     * Formatar idade do ticket de forma legível
     */
    private function formatTicketAge($createdAt)
    {
        $hours = $createdAt->diffInHours(now());
        $minutes = $createdAt->diffInMinutes(now());
        
        if ($hours >= 24) {
            $days = floor($hours / 24);
            return $days . 'd';
        } elseif ($hours >= 1) {
            return $hours . 'h';
        } else {
            return $minutes . 'min';
        }
    }

    /**
     * Métricas Avançadas
     */
    private function getAvgResolutionTimeHours()
    {
        $avgHours = DB::table('tickets')
            ->whereNotNull('closed_at')
            ->whereNotNull('created_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, closed_at)) as avg_hours')
            ->value('avg_hours');

        return round($avgHours ?? 0, 1);
    }

    private function getSLACompliance()
    {
        $totalResolved = Ticket::whereNotNull('closed_at')->count();
        
        if ($totalResolved == 0) return 0;
        
        $onTime = Ticket::whereNotNull('closed_at')
            ->whereColumn('closed_at', '<=', 'due_date')
            ->count();

        return round(($onTime / $totalResolved) * 100, 1);
    }

    private function getCustomerSatisfaction()
    {
        // Simulado - implementar quando tiver sistema de avaliação
        return 4.2; // Nota média de 1-5
    }

    private function getOverdueTickets()
    {
        return Ticket::whereDate('due_date', '<', now())
            ->whereNotIn('status', ['resolved', 'closed'])
            ->count();
    }

    private function getTicketsThisMonth()
    {
        return Ticket::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function getRecentGrowth()
    {
        $thisMonth = Ticket::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
            
        $lastMonth = Ticket::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth == 0) return 100;
        
        return round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1);
    }
}
