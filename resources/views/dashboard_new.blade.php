@extends('layouts.app')

@section('styles')
<style>
    /* Reset e base */
    body {
        background-color: #f4f6f9;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    }
    
    .container-fluid {
        padding: 0 2rem;
    }
    
    /* Header do Dashboard */
    .dashboard-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 32px rgba(102, 126, 234, 0.2);
    }
    
    .dashboard-title {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .dashboard-subtitle {
        opacity: 0.9;
        font-size: 1.1rem;
        margin: 0;
    }
    
    /* Cards Modernos */
    .modern-card {
        background: white;
        border-radius: 16px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        transition: all 0.3s ease;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    
    .modern-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
    }
    
    .modern-card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #e9ecef;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        font-size: 0.95rem;
        color: #495057;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .modern-card-body {
        padding: 1.5rem;
    }
    
    /* KPI Cards */
    .kpi-card {
        background: white;
        border-radius: 20px;
        padding: 2rem 1.5rem;
        text-align: center;
        border: none;
        position: relative;
        overflow: hidden;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    }
    
    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--card-color);
    }
    
    .kpi-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
    }
    
    .kpi-number {
        font-size: 3rem;
        font-weight: 800;
        margin: 0;
        color: var(--card-color);
        line-height: 1;
    }
    
    .kpi-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
        margin: 0.5rem 0 0 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .kpi-icon {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        font-size: 2rem;
        opacity: 0.1;
        color: var(--card-color);
    }
    
    /* Variações de cores dos KPIs */
    .kpi-total { --card-color: #6366f1; }
    .kpi-new { --card-color: #f59e0b; }
    .kpi-progress { --card-color: #3b82f6; }
    .kpi-pending { --card-color: #8b5cf6; }
    .kpi-resolved { --card-color: #10b981; }
    .kpi-overdue { --card-color: #ef4444; }
    
    /* Charts containers */
    .chart-container {
        position: relative;
        height: 280px;
        padding: 1rem;
    }
    
    .chart-small {
        height: 200px;
    }
    
    /* Lista moderna */
    .modern-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .modern-list-item {
        display: flex;
        align-items: center;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f3f4;
        transition: all 0.2s ease;
    }
    
    .modern-list-item:hover {
        background: #f8f9fa;
        border-radius: 8px;
        margin: 0 -1rem;
        padding: 1rem;
    }
    
    .modern-list-item:last-child {
        border-bottom: none;
    }
    
    .modern-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        margin-right: 1rem;
        font-size: 1.1rem;
    }
    
    .modern-content {
        flex: 1;
        min-width: 0;
    }
    
    .modern-title {
        font-weight: 600;
        margin: 0 0 0.25rem 0;
        color: #1f2937;
        font-size: 0.95rem;
    }
    
    .modern-subtitle {
        color: #6b7280;
        margin: 0;
        font-size: 0.85rem;
    }
    
    .modern-badge {
        background: #e5e7eb;
        color: #374151;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    
    /* Status badges */
    .badge-new { background: #fef3c7; color: #92400e; }
    .badge-progress { background: #dbeafe; color: #1e40af; }
    .badge-resolved { background: #d1fae5; color: #065f46; }
    .badge-pending { background: #e0e7ff; color: #4338ca; }
    
    /* Métricas de performance */
    .metric-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-top: 1rem;
    }
    
    .metric-item {
        text-align: center;
        padding: 1.5rem;
        background: #f8f9fa;
        border-radius: 12px;
        border-left: 4px solid var(--metric-color);
    }
    
    .metric-value {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        color: var(--metric-color);
    }
    
    .metric-label {
        font-size: 0.85rem;
        color: #6b7280;
        margin: 0.5rem 0 0 0;
        font-weight: 500;
    }
    
    .metric-time { --metric-color: #3b82f6; }
    .metric-sla { --metric-color: #10b981; }
    .metric-response { --metric-color: #f59e0b; }
    .metric-satisfaction { --metric-color: #8b5cf6; }
    
    /* Responsive */
    @media (max-width: 768px) {
        .container-fluid {
            padding: 0 1rem;
        }
        
        .dashboard-title {
            font-size: 2rem;
        }
        
        .kpi-number {
            font-size: 2.5rem;
        }
        
        .metric-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        
        .chart-container {
            height: 200px;
        }
    }
    
    /* Animações */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease-out;
    }
</style>
@endsection

@section('content')
@php
use Illuminate\Support\Str;
@endphp

<div class="container-fluid">
    <!-- Header Principal -->
    <div class="dashboard-header animate-fadeInUp">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="dashboard-title">Sistema de Chamados</h1>
                <p class="dashboard-subtitle">Painel de controle e gestão de tickets</p>
            </div>
            <div class="col-md-4 text-md-right">
                <div class="d-flex flex-column align-items-md-end">
                    <div class="text-white-50 mb-1">
                        <i class="fas fa-calendar-alt mr-2"></i>{{ now()->format('d/m/Y') }}
                    </div>
                    <div class="text-white">
                        <i class="fas fa-clock mr-2"></i>{{ now()->format('H:i') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Principais -->
    <div class="row mb-4">
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="kpi-card kpi-total animate-fadeInUp">
                <div class="kpi-number">{{ $ticketStats['total'] }}</div>
                <div class="kpi-label">Total de Chamados</div>
                <i class="fas fa-ticket-alt kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="kpi-card kpi-new animate-fadeInUp" style="animation-delay: 0.1s">
                <div class="kpi-number">{{ $ticketStats['new'] }}</div>
                <div class="kpi-label">Novos</div>
                <i class="fas fa-plus-circle kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="kpi-card kpi-progress animate-fadeInUp" style="animation-delay: 0.2s">
                <div class="kpi-number">{{ $ticketStats['in_progress'] }}</div>
                <div class="kpi-label">Em Andamento</div>
                <i class="fas fa-cog kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="kpi-card kpi-pending animate-fadeInUp" style="animation-delay: 0.3s">
                <div class="kpi-number">{{ $ticketStats['pending'] }}</div>
                <div class="kpi-label">Pendentes</div>
                <i class="fas fa-pause-circle kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="kpi-card kpi-resolved animate-fadeInUp" style="animation-delay: 0.4s">
                <div class="kpi-number">{{ $ticketStats['resolved'] }}</div>
                <div class="kpi-label">Resolvidos</div>
                <i class="fas fa-check-circle kpi-icon"></i>
            </div>
        </div>
        <div class="col-xl-2 col-lg-4 col-md-6 mb-3">
            <div class="kpi-card kpi-overdue animate-fadeInUp" style="animation-delay: 0.5s">
                <div class="kpi-number">{{ $overdueSLA ?? $ticketStats['closed'] }}</div>
                <div class="kpi-label">SLA Vencido</div>
                <i class="fas fa-exclamation-triangle kpi-icon"></i>
            </div>
        </div>
    </div>

    <!-- Gráficos e Análises -->
    <div class="row mb-4">
        <!-- Chamados por Prioridade -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="modern-card animate-fadeInUp" style="animation-delay: 0.6s">
                <div class="modern-card-header">
                    <i class="fas fa-chart-bar mr-2"></i>Chamados por Prioridade
                </div>
                <div class="modern-card-body">
                    <div class="chart-container chart-small">
                        <canvas id="ticketsByPriority"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Status dos Chamados -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="modern-card animate-fadeInUp" style="animation-delay: 0.7s">
                <div class="modern-card-header">
                    <i class="fas fa-chart-pie mr-2"></i>Status dos Chamados
                </div>
                <div class="modern-card-body">
                    <div class="chart-container chart-small">
                        <canvas id="ticketsByStatus"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas de Performance -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="modern-card animate-fadeInUp" style="animation-delay: 0.8s">
                <div class="modern-card-header">
                    <i class="fas fa-tachometer-alt mr-2"></i>Métricas de Performance
                </div>
                <div class="modern-card-body">
                    <div class="metric-grid">
                        <div class="metric-item metric-time">
                            <div class="metric-value">2.5h</div>
                            <div class="metric-label">Tempo Médio</div>
                        </div>
                        <div class="metric-item metric-sla">
                            <div class="metric-value">95%</div>
                            <div class="metric-label">SLA Atendido</div>
                        </div>
                        <div class="metric-item metric-response">
                            <div class="metric-value">1.2h</div>
                            <div class="metric-label">Primeira Resposta</div>
                        </div>
                        <div class="metric-item metric-satisfaction">
                            <div class="metric-value">4.8</div>
                            <div class="metric-label">Satisfação</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Informações Detalhadas -->
    <div class="row mb-4">
        <!-- Top Técnicos -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="modern-card animate-fadeInUp" style="animation-delay: 0.9s">
                <div class="modern-card-header">
                    <i class="fas fa-user-cog mr-2"></i>Top Técnicos
                </div>
                <div class="modern-card-body">
                    @if(isset($topTechnicians) && $topTechnicians->count() > 0)
                        <ul class="modern-list">
                            @foreach($topTechnicians->take(5) as $tech)
                            <li class="modern-list-item">
                                <div class="modern-avatar kpi-progress">
                                    {{ substr($tech->name, 0, 1) }}
                                </div>
                                <div class="modern-content">
                                    <div class="modern-title">{{ $tech->name }}</div>
                                    <div class="modern-subtitle">{{ $tech->total }} chamados atendidos</div>
                                </div>
                                <div class="modern-badge">{{ $tech->total }}</div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-user-cog fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhum técnico encontrado</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Ativos com Atenção -->
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="modern-card animate-fadeInUp" style="animation-delay: 1s">
                <div class="modern-card-header">
                    <i class="fas fa-exclamation-triangle mr-2"></i>Ativos Requerem Atenção
                </div>
                <div class="modern-card-body">
                    @if(isset($warningAssets) && $warningAssets->count() > 0)
                        <ul class="modern-list">
                            @foreach($warningAssets->take(5) as $asset)
                            <li class="modern-list-item">
                                <div class="modern-avatar kpi-overdue">
                                    <i class="fas fa-desktop"></i>
                                </div>
                                <div class="modern-content">
                                    <div class="modern-title">{{ $asset->name }}</div>
                                    <div class="modern-subtitle">
                                        Garantia: {{ $asset->warranty_expires ? $asset->warranty_expires->diffForHumans() : 'N/A' }}
                                    </div>
                                </div>
                                <div class="modern-badge badge-new">Atenção</div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                            <p class="text-muted">Todos os ativos estão em ordem</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Atividade Recente -->
        <div class="col-lg-4 col-md-12 mb-4">
            <div class="modern-card animate-fadeInUp" style="animation-delay: 1.1s">
                <div class="modern-card-header">
                    <i class="fas fa-clock mr-2"></i>Atividade Recente
                </div>
                <div class="modern-card-body">
                    @if(isset($recentTickets) && $recentTickets->count() > 0)
                        <ul class="modern-list">
                            @foreach($recentTickets->take(5) as $ticket)
                            <li class="modern-list-item">
                                <div class="modern-avatar 
                                    @if($ticket->status == 'resolved') kpi-resolved
                                    @elseif($ticket->status == 'in_progress') kpi-progress
                                    @else kpi-new @endif">
                                    <i class="fas fa-ticket-alt"></i>
                                </div>
                                <div class="modern-content">
                                    <div class="modern-title">{{ Str::limit($ticket->title, 30) }}</div>
                                    <div class="modern-subtitle">{{ $ticket->created_at->diffForHumans() }}</div>
                                </div>
                                <div class="modern-badge 
                                    @if($ticket->status == 'resolved') badge-resolved
                                    @elseif($ticket->status == 'in_progress') badge-progress
                                    @else badge-new @endif">
                                    {{ ucfirst($ticket->status) }}
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Nenhum chamado recente</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Base de Conhecimento -->
    @if(isset($knowledgeStats))
    <div class="row mb-4">
        <div class="col-12">
            <div class="modern-card animate-fadeInUp" style="animation-delay: 1.2s">
                <div class="modern-card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-book mr-2"></i>Base de Conhecimento</span>
                    <a href="{{ route('knowledge.index') }}" class="btn btn-sm btn-primary">Ver Todos</a>
                </div>
                <div class="modern-card-body">
                    <div class="row mb-3">
                        <div class="col-md-3 mb-3">
                            <div class="metric-item metric-time">
                                <div class="metric-value">{{ $knowledgeStats['total_articles'] ?? 0 }}</div>
                                <div class="metric-label">Total de Artigos</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="metric-item metric-sla">
                                <div class="metric-value">{{ $knowledgeStats['published_articles'] ?? 0 }}</div>
                                <div class="metric-label">Publicados</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="metric-item metric-response">
                                <div class="metric-value">{{ $knowledgeStats['total_categories'] ?? 0 }}</div>
                                <div class="metric-label">Categorias</div>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="metric-item metric-satisfaction">
                                <div class="metric-value">{{ $knowledgeStats['total_views'] ?? 0 }}</div>
                                <div class="metric-label">Visualizações</div>
                            </div>
                        </div>
                    </div>
                    
                    @if(isset($popularKnowledgeArticles) && $popularKnowledgeArticles->count() > 0)
                    <h6 class="mb-3">Artigos Mais Populares</h6>
                    <div class="row">
                        @foreach($popularKnowledgeArticles as $article)
                        <div class="col-md-4 mb-3">
                            <div class="modern-card">
                                <div class="modern-card-body p-3">
                                    <h6 class="modern-title">
                                        <a href="{{ route('knowledge.show', $article) }}" class="text-decoration-none">
                                            {{ Str::limit($article->title, 50) }}
                                        </a>
                                    </h6>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="modern-badge badge-progress">{{ $article->category->name }}</span>
                                        <small class="text-muted">
                                            <i class="fas fa-eye mr-1"></i>{{ $article->views }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurações globais para gráficos modernos
    Chart.defaults.font.family = "'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.responsive = true;
    Chart.defaults.maintainAspectRatio = false;

    // Cores modernas
    const colors = {
        primary: '#6366f1',
        success: '#10b981',
        warning: '#f59e0b',
        danger: '#ef4444',
        info: '#3b82f6',
        purple: '#8b5cf6'
    };

    // Gráfico de Chamados por Status
    new Chart(document.getElementById('ticketsByStatus'), {
        type: 'doughnut',
        data: {
            labels: ['Novos', 'Em Andamento', 'Pendentes', 'Resolvidos', 'Fechados'],
            datasets: [{
                data: [
                    {{ $ticketStats['new'] }},
                    {{ $ticketStats['in_progress'] }},
                    {{ $ticketStats['pending'] }},
                    {{ $ticketStats['resolved'] }},
                    {{ $ticketStats['closed'] }}
                ],
                backgroundColor: [
                    colors.warning,
                    colors.info,
                    colors.purple,
                    colors.success,
                    colors.danger
                ],
                borderWidth: 0,
                hoverBorderWidth: 2,
                hoverBorderColor: '#fff'
            }]
        },
        options: {
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 12,
                        padding: 15,
                        usePointStyle: true
                    }
                }
            },
            cutout: '65%'
        }
    });

    // Gráfico de Chamados por Prioridade
    new Chart(document.getElementById('ticketsByPriority'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($ticketsByPriority)) !!},
            datasets: [{
                label: 'Quantidade',
                data: {!! json_encode(array_values($ticketsByPriority)) !!},
                backgroundColor: [
                    colors.info,
                    colors.warning,
                    colors.danger
                ],
                borderRadius: 8,
                borderSkipped: false,
                barThickness: 40
            }]
        },
        options: {
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#f1f3f4',
                        drawBorder: false
                    },
                    ticks: {
                        stepSize: 1,
                        color: '#6b7280'
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        color: '#6b7280'
                    }
                }
            }
        }
    });
});
</script>
@endpush
