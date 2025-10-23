<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Location;
use App\Models\User;
use App\Models\Ticket;
use Carbon\Carbon;

class UbsDashboardController extends Controller
{
    public function index()
    {
        // Estatísticas gerais
        $stats = $this->getGeneralStats();
        
        // Dados por UBS
        $ubsData = $this->getUbsData();
        
        // Gráficos e métricas
        $charts = $this->getChartsData();
        
        return view('admin.ubs-dashboard.index', compact('stats', 'ubsData', 'charts'));
    }

    public function show($id)
    {
        $ubs = Location::findOrFail($id);
        
        // Estatísticas específicas da UBS
        $statistics = $this->getUbsSpecificStats($ubs);
        
        // Chamados da UBS
        $tickets = $this->getUbsTickets($ubs);
        
        // Usuários da UBS
        $users = $this->getUbsUsers($ubs);
        
        return view('admin.ubs-dashboard.show', compact('ubs', 'statistics', 'tickets', 'users'));
    }

    public function api()
    {
        return response()->json([
            'general_stats' => $this->getGeneralStats(),
            'ubs_data' => $this->getUbsData(),
            'charts' => $this->getChartsData(),
        ]);
    }

    private function getGeneralStats()
    {
        return [
            'total_ubs' => Location::count(),
            'total_users' => User::count(),
            'users_with_ubs' => User::whereNotNull('location_id')->count(),
            'total_tickets' => Ticket::count(),
            'tickets_open' => Ticket::where('status', 'aberto')->count(),
            'tickets_today' => Ticket::whereDate('created_at', today())->count(),
            'ldap_sync_last' => cache('ldap_sync_last_run'),
        ];
    }

    private function getUbsData()
    {
        return Location::select([
                'id',
                'name',
                'address',
                'phone',
                'created_at'
            ])
            ->withCount([
                'users',
                'users as active_users_count' => function($query) {
                    $query->where('is_active', true);
                }
            ])
            ->with(['users' => function($query) {
                $query->select('id', 'location_id', 'name', 'email', 'is_active', 'last_login_at')
                      ->latest('last_login_at')
                      ->limit(5);
            }])
            ->addSelect([
                'total_tickets' => Ticket::select(DB::raw('count(*)'))
                    ->join('users', 'tickets.user_id', '=', 'users.id')
                    ->whereColumn('users.location_id', 'locations.id'),
                    
                'open_tickets' => Ticket::select(DB::raw('count(*)'))
                    ->join('users', 'tickets.user_id', '=', 'users.id')
                    ->whereColumn('users.location_id', 'locations.id')
                    ->where('tickets.status', 'aberto'),
                    
                'tickets_this_month' => Ticket::select(DB::raw('count(*)'))
                    ->join('users', 'tickets.user_id', '=', 'users.id')
                    ->whereColumn('users.location_id', 'locations.id')
                    ->where('tickets.created_at', '>=', now()->startOfMonth()),
                    
                'avg_resolution_hours' => Ticket::select(DB::raw('AVG(TIMESTAMPDIFF(HOUR, tickets.created_at, COALESCE(tickets.resolved_at, tickets.updated_at)))'))
                    ->join('users', 'tickets.user_id', '=', 'users.id')
                    ->whereColumn('users.location_id', 'locations.id')
                    ->whereNotNull('tickets.resolved_at')
            ])
            ->orderBy('name')
            ->get()
            ->map(function($ubs) {
                return [
                    'id' => $ubs->id,
                    'name' => $ubs->name,
                    'address' => $ubs->address,
                    'phone' => $ubs->phone,
                    'users_count' => $ubs->users_count,
                    'active_users_count' => $ubs->active_users_count,
                    'total_tickets' => $ubs->total_tickets ?? 0,
                    'open_tickets' => $ubs->open_tickets ?? 0,
                    'tickets_this_month' => $ubs->tickets_this_month ?? 0,
                    'avg_resolution_hours' => round($ubs->avg_resolution_hours ?? 0, 1),
                    'recent_users' => $ubs->users,
                    'health_score' => $this->calculateHealthScore($ubs),
                    'status' => $this->getUbsStatus($ubs),
                ];
            });
    }

    private function getChartsData()
    {
        return [
            'tickets_by_ubs' => $this->getTicketsByUbs(),
            'users_by_ubs' => $this->getUsersByUbs(),
            'tickets_timeline' => $this->getTicketsTimeline(),
            'resolution_time_by_ubs' => $this->getResolutionTimeByUbs(),
        ];
    }

    private function getTicketsByUbs()
    {
        return DB::table('locations')
            ->leftJoin('users', 'locations.id', '=', 'users.location_id')
            ->leftJoin('tickets', 'users.id', '=', 'tickets.user_id')
            ->select(
                'locations.name as ubs_name',
                DB::raw('COUNT(tickets.id) as tickets_count'),
                DB::raw('COUNT(CASE WHEN tickets.status = "aberto" THEN 1 END) as open_tickets'),
                DB::raw('COUNT(CASE WHEN tickets.status = "fechado" THEN 1 END) as closed_tickets')
            )
            ->groupBy('locations.id', 'locations.name')
            ->orderBy('tickets_count', 'desc')
            ->get();
    }

    private function getUsersByUbs()
    {
        return Location::withCount(['users as total_users', 'users as active_users' => function($query) {
            $query->where('is_active', true);
        }])
        ->get()
        ->map(function($ubs) {
            return [
                'ubs_name' => $ubs->name,
                'total_users' => $ubs->total_users,
                'active_users' => $ubs->active_users,
                'inactive_users' => $ubs->total_users - $ubs->active_users,
            ];
        });
    }

    private function getTicketsTimeline()
    {
        return DB::table('tickets')
            ->join('users', 'tickets.user_id', '=', 'users.id')
            ->join('locations', 'users.location_id', '=', 'locations.id')
            ->select(
                DB::raw('DATE(tickets.created_at) as date'),
                'locations.name as ubs_name',
                DB::raw('COUNT(*) as tickets_count')
            )
            ->where('tickets.created_at', '>=', now()->subDays(30))
            ->groupBy('date', 'locations.id', 'locations.name')
            ->orderBy('date')
            ->get();
    }

    private function getResolutionTimeByUbs()
    {
        return DB::table('locations')
            ->leftJoin('users', 'locations.id', '=', 'users.location_id')
            ->leftJoin('tickets', 'users.id', '=', 'tickets.user_id')
            ->select(
                'locations.name as ubs_name',
                DB::raw('AVG(TIMESTAMPDIFF(HOUR, tickets.created_at, tickets.resolved_at)) as avg_hours'),
                DB::raw('COUNT(tickets.id) as resolved_tickets')
            )
            ->whereNotNull('tickets.resolved_at')
            ->groupBy('locations.id', 'locations.name')
            ->having('resolved_tickets', '>', 0)
            ->orderBy('avg_hours')
            ->get();
    }

    private function getUbsSpecificStats($ubs)
    {
        $usersQuery = User::where('location_id', $ubs->id);
        $ticketsQuery = Ticket::whereHas('user', function($query) use ($ubs) {
            $query->where('location_id', $ubs->id);
        });

        return [
            'total_users' => $usersQuery->count(),
            'active_users' => $usersQuery->where('is_active', true)->count(),
            'users_with_tickets' => $usersQuery->whereHas('tickets')->count(),
            'total_tickets' => $ticketsQuery->count(),
            'open_tickets' => $ticketsQuery->where('status', 'aberto')->count(),
            'closed_tickets' => $ticketsQuery->where('status', 'fechado')->count(),
            'tickets_today' => $ticketsQuery->whereDate('created_at', today())->count(),
            'tickets_this_week' => $ticketsQuery->where('created_at', '>=', now()->startOfWeek())->count(),
            'tickets_this_month' => $ticketsQuery->where('created_at', '>=', now()->startOfMonth())->count(),
            'avg_resolution_time' => $ticketsQuery->whereNotNull('resolved_at')
                ->selectRaw('AVG(TIMESTAMPDIFF(HOUR, created_at, resolved_at)) as avg_hours')
                ->value('avg_hours'),
        ];
    }

    private function getUbsTickets($ubs)
    {
        return Ticket::whereHas('user', function($query) use ($ubs) {
                $query->where('location_id', $ubs->id);
            })
            ->with(['user', 'category'])
            ->latest()
            ->paginate(10);
    }

    private function getUbsUsers($ubs)
    {
        return User::where('location_id', $ubs->id)
            ->withCount('tickets')
            ->latest('last_login_at')
            ->paginate(15);
    }

    private function calculateHealthScore($ubs): int
    {
        $score = 100;
        
        // Penalizar por chamados abertos em excesso
        $openTickets = $ubs->open_tickets ?? 0;
        if ($openTickets > 10) $score -= 20;
        elseif ($openTickets > 5) $score -= 10;
        
        // Penalizar por tempo de resolução alto
        $avgHours = $ubs->avg_resolution_hours ?? 0;
        if ($avgHours > 48) $score -= 15;
        elseif ($avgHours > 24) $score -= 10;
        
        // Penalizar por poucos usuários ativos
        $activeRatio = $ubs->users_count > 0 ? ($ubs->active_users_count / $ubs->users_count) : 1;
        if ($activeRatio < 0.5) $score -= 15;
        elseif ($activeRatio < 0.8) $score -= 10;
        
        return max(0, min(100, $score));
    }

    private function getUbsStatus($ubs): string
    {
        $healthScore = $this->calculateHealthScore($ubs);
        
        if ($healthScore >= 80) return 'excellent';
        if ($healthScore >= 60) return 'good';
        if ($healthScore >= 40) return 'warning';
        return 'critical';
    }
}
