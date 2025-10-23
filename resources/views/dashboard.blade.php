@extends('layouts.app')

@section('styles')
@vite('resources/css/dashboard.css')
@endsection

@section('content')
<div class="modern-dashboard">
    <!-- Header Principal -->
    <div class="dashboard-header-modern">
        <div class="header-content">
            <div class="header-left">
                <div class="dashboard-logo">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div class="header-text">
                    <h1 class="dashboard-title-modern">Painel de Administração</h1>
                    <p class="dashboard-subtitle">Sistema de Gestão de Chamados</p>
                </div>
            </div>
            <div class="header-right">
                <div class="header-stats">
                    <div class="mini-stat">
                        <span class="mini-stat-value">{{ $totalTickets }}</span>
                        <span class="mini-stat-label">Total</span>
                    </div>
                    <div class="mini-stat urgent">
                        <span class="mini-stat-value">{{ $overdueTickets ?? 0 }}</span>
                        <span class="mini-stat-label">Urgentes</span>
                    </div>
                </div>
                <div class="header-actions">
                    <button class="header-btn" id="refresh-dashboard" title="Atualizar Dashboard">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                    <button class="header-btn" id="toggle-dark-mode" title="Alternar Modo Escuro">
                        <i class="bi bi-moon"></i>
                    </button>
                    <button class="header-btn" id="fullscreen-dashboard" title="Tela Cheia">
                        <i class="bi bi-arrows-fullscreen"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Barra de Filtros Moderna -->
    <div class="filter-section-modern">
        <div class="filter-header">
            <h3 class="filter-title">
                <i class="bi bi-funnel"></i>
                Filtros e Ações
            </h3>
            <div class="filter-toggle">
                <button class="btn-filter-toggle" id="toggle-filters">
                    <i class="bi bi-chevron-down"></i>
                </button>
            </div>
        </div>
        <div class="filter-content" id="filter-content">
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bi bi-circle-fill"></i>
                        Status
                    </label>
                    <select class="filter-select" id="filter-status">
                        <option value="">Todos os Status</option>
                        <option value="open">Aberto</option>
                        <option value="in_progress">Em Andamento</option>
                        <option value="resolved">Resolvido</option>
                        <option value="closed">Fechado</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bi bi-exclamation-triangle"></i>
                        Prioridade
                    </label>
                    <select class="filter-select" id="filter-priority">
                        <option value="">Todas as Prioridades</option>
                        <option value="urgent">Urgente</option>
                        <option value="high">Alta</option>
                        <option value="medium">Média</option>
                        <option value="low">Baixa</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bi bi-tag"></i>
                        Categoria
                    </label>
                    <select class="filter-select" id="filter-category">
                        <option value="">Todas as Categorias</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bi bi-calendar"></i>
                        Período
                    </label>
                    <input type="date" class="filter-input" id="filter-date-start" placeholder="Data inicial">
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">
                        <i class="bi bi-calendar-check"></i>
                        Até
                    </label>
                    <input type="date" class="filter-input" id="filter-date-end" placeholder="Data final">
                </div>
                
                <div class="filter-group span-2">
                    <label class="filter-label">
                        <i class="bi bi-search"></i>
                        Buscar
                    </label>
                    <input type="text" class="filter-input" id="filter-search" placeholder="Título, solicitante, técnico...">
                </div>
            </div>
            
            <div class="action-grid">
                <div class="action-group">
                    <h4 class="action-title">Ações Rápidas</h4>
                    <div class="quick-actions-modern">
                        <a href="{{ route('tickets.create') }}" class="quick-btn primary">
                            <i class="bi bi-plus-circle"></i>
                            <span>Novo Chamado</span>
                        </a>
                        <a href="{{ route('tickets.index', ['status' => 'waiting']) }}" class="quick-btn info">
                            <i class="bi bi-reply"></i>
                            <span>Responder</span>
                        </a>
                        <a href="{{ route('tickets.index', ['assigned' => 'none']) }}" class="quick-btn warning">
                            <i class="bi bi-person-plus"></i>
                            <span>Atribuir</span>
                        </a>
                        <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}" class="quick-btn success">
                            <i class="bi bi-check2-circle"></i>
                            <span>Fechar</span>
                        </a>
                    </div>
                </div>
                
                <div class="action-group">
                    <h4 class="action-title">Exportação</h4>
                    <div class="export-actions-modern">
                        <button class="export-btn-modern excel" id="export-excel">
                            <i class="bi bi-file-earmark-excel"></i>
                            <span>Excel</span>
                        </button>
                        <button class="export-btn-modern pdf" id="export-pdf">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <span>PDF</span>
                        </button>
                        <button class="export-btn-modern csv" id="export-csv">
                            <i class="bi bi-filetype-csv"></i>
                            <span>CSV</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- KPIs Principais Modernizados -->
    <div class="kpi-section-modern">
        <div class="section-header">
            <h2 class="section-title">
                <i class="bi bi-graph-up"></i>
                Indicadores Principais
            </h2>
            <div class="section-actions">
                <button class="section-btn" id="refresh-kpis" title="Atualizar Indicadores">
                    <i class="bi bi-arrow-clockwise"></i>
                </button>
            </div>
        </div>
        
        <div class="kpi-grid-modern">
            <div class="kpi-card-modern primary">
                <div class="kpi-header">
                    <div class="kpi-icon-wrapper">
                        <i class="bi bi-collection"></i>
                    </div>
                    <div class="kpi-trend up">
                        <i class="bi bi-arrow-up"></i>
                        <span>+12%</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value">{{ $totalTickets }}</div>
                    <div class="kpi-label">Total de Chamados</div>
                    <div class="kpi-description">Todos os tickets do sistema</div>
                </div>
                <div class="kpi-chart">
                    <canvas id="totalChart" width="100" height="30"></canvas>
                </div>
            </div>
            
            <div class="kpi-card-modern warning">
                <div class="kpi-header">
                    <div class="kpi-icon-wrapper">
                        <i class="bi bi-lightning-charge"></i>
                    </div>
                    <div class="kpi-trend up">
                        <i class="bi bi-arrow-up"></i>
                        <span>+8%</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value">{{ $openTickets }}</div>
                    <div class="kpi-label">Chamados Abertos</div>
                    <div class="kpi-description">Aguardando atendimento</div>
                </div>
                <div class="kpi-chart">
                    <canvas id="openChart" width="100" height="30"></canvas>
                </div>
            </div>
            
            <div class="kpi-card-modern info">
                <div class="kpi-header">
                    <div class="kpi-icon-wrapper">
                        <i class="bi bi-arrow-repeat"></i>
                    </div>
                    <div class="kpi-trend down">
                        <i class="bi bi-arrow-down"></i>
                        <span>-5%</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value">{{ $inProgressTickets }}</div>
                    <div class="kpi-label">Em Andamento</div>
                    <div class="kpi-description">Sendo processados</div>
                </div>
                <div class="kpi-chart">
                    <canvas id="progressChart" width="100" height="30"></canvas>
                </div>
            </div>
            
            <div class="kpi-card-modern success">
                <div class="kpi-header">
                    <div class="kpi-icon-wrapper">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div class="kpi-trend up">
                        <i class="bi bi-arrow-up"></i>
                        <span>+23%</span>
                    </div>
                </div>
                <div class="kpi-content">
                    <div class="kpi-value">{{ $resolvedTickets }}</div>
                    <div class="kpi-label">Resolvidos</div>
                    <div class="kpi-description">Finalizados com sucesso</div>
                </div>
                <div class="kpi-chart">
                    <canvas id="resolvedChart" width="100" height="30"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Secundários -->
    <div class="secondary-kpi-section">
        <div class="secondary-kpi-grid">
            <div class="secondary-kpi-card danger">
                <div class="secondary-kpi-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="secondary-kpi-content">
                    <div class="secondary-kpi-value">{{ $overdueTickets ?? 0 }}</div>
                    <div class="secondary-kpi-label">Vencidos</div>
                </div>
            </div>
            
            <div class="secondary-kpi-card primary">
                <div class="secondary-kpi-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="secondary-kpi-content">
                    <div class="secondary-kpi-value">94.5%</div>
                    <div class="secondary-kpi-label">SLA Cumprido</div>
                </div>
            </div>
            
            <div class="secondary-kpi-card warning">
                <div class="secondary-kpi-icon">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </div>
                <div class="secondary-kpi-content">
                    <div class="secondary-kpi-value">{{ $reopenedTickets ?? 0 }}</div>
                    <div class="secondary-kpi-label">Reabertos</div>
                </div>
            </div>
            
            <div class="secondary-kpi-card info">
                <div class="secondary-kpi-icon">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div class="secondary-kpi-content">
                    <div class="secondary-kpi-value">2.3h</div>
                    <div class="secondary-kpi-label">Tempo Médio</div>
                </div>
            </div>
        </div>
    </div>
    <!-- Gráficos Modernizados -->
    <div class="charts-section-modern">
        <div class="section-header">
            <h2 class="section-title">
                <i class="bi bi-bar-chart"></i>
                Análises e Relatórios
            </h2>
            <div class="section-actions">
                <select class="period-selector" id="chart-period">
                    <option value="7">Últimos 7 dias</option>
                    <option value="30" selected>Últimos 30 dias</option>
                    <option value="90">Últimos 90 dias</option>
                    <option value="365">Último ano</option>
                </select>
            </div>
        </div>
        
        <div class="charts-grid-modern">
            <div class="chart-card-modern primary">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="bi bi-pie-chart"></i>
                        <span>Chamados por Categoria</span>
                    </div>
                    <div class="chart-actions">
                        <button class="chart-btn" title="Expandir">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                        <button class="chart-btn" title="Exportar">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="categoryChart"></canvas>
                </div>
                <div class="chart-footer">
                    <div class="chart-insights">
                        <span class="insight-text">Hardware representa 45% dos chamados</span>
                    </div>
                </div>
            </div>
            
            <div class="chart-card-modern warning">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="bi bi-bar-chart"></i>
                        <span>Chamados por Prioridade</span>
                    </div>
                    <div class="chart-actions">
                        <button class="chart-btn" title="Expandir">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                        <button class="chart-btn" title="Exportar">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="priorityChart"></canvas>
                </div>
                <div class="chart-footer">
                    <div class="chart-insights">
                        <span class="insight-text">8% são de prioridade urgente</span>
                    </div>
                </div>
            </div>
            
            <div class="chart-card-modern info">
                <div class="chart-header">
                    <div class="chart-title">
                        <i class="bi bi-activity"></i>
                        <span>Evolução dos Chamados</span>
                    </div>
                    <div class="chart-actions">
                        <button class="chart-btn" title="Expandir">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                        <button class="chart-btn" title="Exportar">
                            <i class="bi bi-download"></i>
                        </button>
                    </div>
                </div>
                <div class="chart-body">
                    <canvas id="evolutionChart"></canvas>
                </div>
                <div class="chart-footer">
                    <div class="chart-insights">
                        <span class="insight-text">Tendência de crescimento de 15%</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Seção de Widgets Avançados -->
    <div class="widgets-section-modern">
        <div class="widgets-grid">
            <!-- Mapa de Chamados -->
            <div class="widget-card-modern map-widget">
                <div class="widget-header">
                    <div class="widget-title">
                        <i class="bi bi-geo-alt"></i>
                        <span>Mapa de Chamados</span>
                    </div>
                    <div class="widget-actions">
                        <button class="widget-btn" title="Atualizar">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                        <button class="widget-btn" title="Configurações">
                            <i class="bi bi-gear"></i>
                        </button>
                    </div>
                </div>
                <div class="widget-body">
                    <div class="map-container-modern" id="map">
                        <div class="map-placeholder">
                            <i class="bi bi-geo-alt-fill"></i>
                            <p>Mapa será carregado aqui</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Ranking de Técnicos -->
            <div class="widget-card-modern ranking-widget">
                <div class="widget-header">
                    <div class="widget-title">
                        <i class="bi bi-trophy"></i>
                        <span>Ranking de Técnicos</span>
                    </div>
                    <div class="widget-actions">
                        <button class="widget-btn" title="Ver Mais">
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </div>
                </div>
                <div class="widget-body">
                    <div class="ranking-list-modern">
                        @if($ranking->count() > 0)
                            @foreach($ranking as $index => $user)
                            <div class="ranking-item-modern">
                                <div class="ranking-position">
                                    @if($index === 0)
                                        <i class="bi bi-trophy-fill text-warning"></i>
                                    @elseif($index === 1)
                                        <i class="bi bi-award-fill text-secondary"></i>
                                    @elseif($index === 2)
                                        <i class="bi bi-award-fill text-warning"></i>
                                    @else
                                        <span class="position-number">{{ $index + 1 }}</span>
                                    @endif
                                </div>
                                <div class="ranking-avatar-modern">
                                    {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                </div>
                                <div class="ranking-info">
                                    <div class="ranking-name">{{ $user->name }}</div>
                                    <div class="ranking-stats">{{ $user->score }} chamados resolvidos</div>
                                </div>
                                <div class="ranking-score-modern">
                                    <span class="score-value">{{ $user->score }}</span>
                                    <div class="score-bar">
                                        @php
                                            $maxScore = max($ranking->pluck('score')->toArray());
                                            $percentage = $maxScore > 0 ? ($user->score / $maxScore) * 100 : 0;
                                        @endphp
                                        <div class="score-fill" style="width: {{ $percentage }}%"></div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        @else
                            <div class="text-center p-4 text-muted">
                                <i class="bi bi-info-circle mb-2" style="font-size: 2rem;"></i>
                                <p>Nenhum técnico com chamados resolvidos ainda.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Atividades Recentes -->
            <div class="widget-card-modern activities-widget">
                <div class="widget-header">
                    <div class="widget-title">
                        <i class="bi bi-clock-history"></i>
                        <span>Atividades Recentes</span>
                    </div>
                    <div class="widget-actions">
                        <button class="widget-btn" title="Ver Todas">
                            <i class="bi bi-list"></i>
                        </button>
                    </div>
                </div>
                <div class="widget-body">
                    <div class="timeline-modern">
                        @foreach($activities as $act)
                        <div class="timeline-item-modern">
                            <div class="timeline-marker"></div>
                            <div class="timeline-content">
                                <div class="timeline-header">
                                    <span class="timeline-title-modern">{{ $act->title }}</span>
                                    <span class="timeline-time">{{ $act->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="timeline-description">{{ $act->description }}</div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Satisfação dos Usuários -->
    <div class="satisfaction-section-modern">
        <div class="section-header">
            <h2 class="section-title">
                <i class="bi bi-emoji-smile"></i>
                Satisfação dos Usuários
            </h2>
        </div>
        
        <div class="satisfaction-grid">
            <div class="satisfaction-card-modern nps">
                <div class="satisfaction-icon">
                    <i class="bi bi-emoji-smile-fill"></i>
                </div>
                <div class="satisfaction-content">
                    <div class="satisfaction-value">{{ $satisfaction ?? '9.2' }}</div>
                    <div class="satisfaction-label">Net Promoter Score</div>
                    <div class="satisfaction-trend">
                        <i class="bi bi-arrow-up text-success"></i>
                        <span>+0.8 desde o mês passado</span>
                    </div>
                </div>
                <div class="satisfaction-chart">
                    <canvas id="npsChart" width="80" height="80"></canvas>
                </div>
            </div>
            
            <div class="satisfaction-card-modern feedback">
                <div class="satisfaction-icon">
                    <i class="bi bi-chat-dots-fill"></i>
                </div>
                <div class="satisfaction-content">
                    <div class="satisfaction-value">{{ $feedbacks ?? 147 }}</div>
                    <div class="satisfaction-label">Avaliações Recebidas</div>
                    <div class="satisfaction-trend">
                        <i class="bi bi-arrow-up text-success"></i>
                        <span>+23 este mês</span>
                    </div>
                </div>
                <div class="satisfaction-breakdown">
                    <div class="rating-stars">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-half"></i>
                        <span class="rating-value">4.6</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@vite(['resources/js/app.js', 'resources/js/dashboard-modern.js'])
@endpush
