@extends('layouts.app')

@section('title', 'Detalhes da UBS - ' . $ubs->name)

@section('styles')
<style>
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 15px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        transition: transform 0.3s ease;
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
    }
    
    .stats-value {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    
    .stats-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    
    .info-card {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
        border-left: 4px solid #667eea;
    }
    
    .table-responsive {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
    }
    
    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }
    
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 2rem;
        border-radius: 15px;
        margin-bottom: 2rem;
    }
    
    .chart-container {
        background: white;
        border-radius: 15px;
        padding: 1.5rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 1rem;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header da Página -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="mb-0">
                    <i class="bi bi-hospital me-2"></i>{{ $ubs->name }}
                </h1>
                <p class="mb-0 mt-2 opacity-75">Detalhes e estatísticas da unidade de saúde</p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.ubs.index') }}" class="btn btn-light">
                    <i class="bi bi-arrow-left me-2"></i>Voltar ao Dashboard
                </a>
            </div>
        </div>
    </div>

    <!-- Informações da UBS -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="info-card">
                <h5 class="mb-3">
                    <i class="bi bi-info-circle me-2 text-primary"></i>Informações da UBS
                </h5>
                <div class="row">
                    <div class="col-sm-6">
                        <strong>Nome:</strong><br>
                        <span class="text-muted">{{ $ubs->name }}</span>
                    </div>
                    <div class="col-sm-6">
                        <strong>ID:</strong><br>
                        <span class="text-muted">#{{ $ubs->id }}</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <strong>Endereço:</strong><br>
                        <span class="text-muted">{{ $ubs->address ?? 'Não informado' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <strong>Telefone:</strong><br>
                        <span class="text-muted">{{ $ubs->phone ?? 'Não informado' }}</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6">
                        <strong>Criado em:</strong><br>
                        <span class="text-muted">{{ $ubs->created_at ? $ubs->created_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <strong>Atualizado em:</strong><br>
                        <span class="text-muted">{{ $ubs->updated_at ? $ubs->updated_at->format('d/m/Y H:i') : 'N/A' }}</span>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <!-- Health Score -->
            <div class="info-card">
                <h5 class="mb-3">
                    <i class="bi bi-heart-pulse me-2 text-success"></i>Health Score
                </h5>
                @php
                    $healthScore = 0;
                    $totalUsers = $statistics['total_users'] ?? 0;
                    $activeUsers = $statistics['active_users'] ?? 0;
                    $totalTickets = $statistics['total_tickets'] ?? 0;
                    $openTickets = $statistics['open_tickets'] ?? 0;
                    
                    // Calcular score baseado em métricas
                    if ($totalUsers > 0) {
                        $activeUserRatio = $activeUsers / $totalUsers;
                        $healthScore += $activeUserRatio * 30; // 30% do score
                    }
                    
                    if ($totalTickets > 0) {
                        $resolvedRatio = ($totalTickets - $openTickets) / $totalTickets;
                        $healthScore += $resolvedRatio * 40; // 40% do score
                    } else {
                        $healthScore += 40; // Se não há tickets, é bom
                    }
                    
                    // Atividade recente (30% do score)
                    $recentActivity = min(($statistics['tickets_this_month'] ?? 0) / 10, 1) * 30;
                    $healthScore += $recentActivity;
                    
                    $healthScore = round($healthScore);
                    
                    if ($healthScore >= 80) {
                        $scoreColor = 'success';
                        $scoreIcon = 'heart-fill';
                        $scoreText = 'Excelente';
                    } elseif ($healthScore >= 60) {
                        $scoreColor = 'warning';
                        $scoreIcon = 'heart-half';
                        $scoreText = 'Bom';
                    } else {
                        $scoreColor = 'danger';
                        $scoreIcon = 'heart';
                        $scoreText = 'Precisa Atenção';
                    }
                @endphp
                
                <div class="text-center">
                    <div class="progress mb-3" style="height: 20px;">
                        <div class="progress-bar bg-{{ $scoreColor }}" role="progressbar" 
                             style="width: {{ $healthScore }}%" 
                             aria-valuenow="{{ $healthScore }}" 
                             aria-valuemin="0" 
                             aria-valuemax="100">
                            {{ $healthScore }}%
                        </div>
                    </div>
                    <h4 class="text-{{ $scoreColor }}">
                        <i class="bi bi-{{ $scoreIcon }} me-2"></i>{{ $scoreText }}
                    </h4>
                    <small class="text-muted">Score baseado em usuários ativos, resolução de tickets e atividade recente</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-value">{{ $statistics['total_users'] ?? 0 }}</div>
                <div class="stats-label">
                    <i class="bi bi-people me-1"></i>Total de Usuários
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-value">{{ $statistics['active_users'] ?? 0 }}</div>
                <div class="stats-label">
                    <i class="bi bi-person-check me-1"></i>Usuários Ativos
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-value">{{ $statistics['total_tickets'] ?? 0 }}</div>
                <div class="stats-label">
                    <i class="bi bi-ticket-perforated me-1"></i>Total de Tickets
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-value">{{ $statistics['open_tickets'] ?? 0 }}</div>
                <div class="stats-label">
                    <i class="bi bi-exclamation-circle me-1"></i>Tickets Abertos
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-value">{{ $statistics['tickets_today'] ?? 0 }}</div>
                <div class="stats-label">
                    <i class="bi bi-calendar-day me-1"></i>Tickets Hoje
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-value">{{ $statistics['tickets_this_week'] ?? 0 }}</div>
                <div class="stats-label">
                    <i class="bi bi-calendar-week me-1"></i>Esta Semana
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-value">{{ $statistics['tickets_this_month'] ?? 0 }}</div>
                <div class="stats-label">
                    <i class="bi bi-calendar-month me-1"></i>Este Mês
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card text-center">
                <div class="stats-value">
                    @if($statistics['avg_resolution_time'])
                        {{ round($statistics['avg_resolution_time']) }}h
                    @else
                        N/A
                    @endif
                </div>
                <div class="stats-label">
                    <i class="bi bi-clock me-1"></i>Tempo Médio Resolução
                </div>
            </div>
        </div>
    </div>

    <!-- Tickets Recentes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="table-responsive">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-list-task me-2 text-primary"></i>Tickets Recentes
                    </h5>
                    @if($tickets->count() > 0)
                        <span class="badge bg-primary">{{ $tickets->total() }} tickets</span>
                    @endif
                </div>
                
                @if($tickets->count() > 0)
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Título</th>
                                <th>Usuário</th>
                                <th>Status</th>
                                <th>Prioridade</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                            <tr>
                                <td><strong>#{{ $ticket->id }}</strong></td>
                                <td>
                                    <div class="text-truncate" style="max-width: 200px;" title="{{ $ticket->title }}">
                                        {{ $ticket->title }}
                                    </div>
                                </td>
                                <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge badge-custom status-{{ $ticket->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-custom priority-{{ $ticket->priority }}">
                                        {{ ucfirst($ticket->priority) }}
                                    </span>
                                </td>
                                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('tickets.show', $ticket->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Paginação -->
                    <div class="d-flex justify-content-center">
                        {{ $tickets->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox display-4 text-muted"></i>
                        <p class="text-muted mt-2">Nenhum ticket encontrado para esta UBS</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Usuários da UBS -->
    <div class="row">
        <div class="col-12">
            <div class="table-responsive">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">
                        <i class="bi bi-people me-2 text-primary"></i>Usuários da UBS
                    </h5>
                    @if($users->count() > 0)
                        <span class="badge bg-primary">{{ $users->total() }} usuários</span>
                    @endif
                </div>
                
                @if($users->count() > 0)
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Perfil</th>
                                <th>Status</th>
                                <th>Tickets</th>
                                <th>Último Login</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        {{ $user->name }}
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($user->role ?? 'user') }}</span>
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-danger">Inativo</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info">{{ $user->tickets_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($user->last_login_at)
                                        {{ $user->last_login_at->format('d/m/Y H:i') }}
                                    @else
                                        <span class="text-muted">Nunca</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.users.show', $user->id) }}" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    
                    <!-- Paginação -->
                    <div class="d-flex justify-content-center">
                        {{ $users->links() }}
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-people display-4 text-muted"></i>
                        <p class="text-muted mt-2">Nenhum usuário encontrado para esta UBS</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Adicionar qualquer JavaScript específico da página aqui
document.addEventListener('DOMContentLoaded', function() {
    // Tooltip para elementos truncados
    const truncatedElements = document.querySelectorAll('.text-truncate');
    truncatedElements.forEach(element => {
        if (element.scrollWidth > element.clientWidth) {
            element.style.cursor = 'help';
        }
    });
});
</script>
@endsection
