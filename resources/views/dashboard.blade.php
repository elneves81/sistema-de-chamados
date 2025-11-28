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
                        <i class="bi bi-person-check"></i>
                        Técnico
                    </label>
                    <select class="filter-select" id="filter-technician">
                        <option value="">Todos os Técnicos</option>
                        @foreach(\App\Models\User::where('role', 'technician')->where('is_active', true)->orderBy('name')->get() as $tech)
                        <option value="{{ $tech->id }}">{{ $tech->name }}</option>
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
                        <button class="export-btn-modern pdf" id="export-pdf">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <span>PDF</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Pré-visualização de Exportação -->
    <div id="export-preview-modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; overflow:auto;">
        <div style="position:relative; max-width:1200px; margin:40px auto; background:#fff; border-radius:12px; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
            <div style="position:sticky; top:0; z-index:10; display:flex; justify-content:space-between; align-items:center; padding:16px 24px; background:#111827; color:#fff; border-radius:12px 12px 0 0;">
                <h3 style="margin:0; font-size:18px;"><i class="bi bi-eye"></i> Pré-visualização do Relatório</h3>
                <div style="display:flex; gap:10px;">
                    <button onclick="window.print()" style="padding:8px 16px; background:#10b981; color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
                        <i class="bi bi-printer"></i> Imprimir
                    </button>
                    <button id="export-modal-download-pdf" style="padding:8px 16px; background:#3b82f6; color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
                        <i class="bi bi-download"></i> Baixar PDF
                    </button>
                    <button id="close-export-modal" style="padding:8px 16px; background:#ef4444; color:#fff; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
                        <i class="bi bi-x-lg"></i> Fechar
                    </button>
                </div>
            </div>
            <div id="export-preview-content" style="padding:24px; min-height:400px; background:#fafafa;">
                <div style="text-align:center; padding:60px 20px; color:#64748b;">
                    <i class="bi bi-hourglass-split" style="font-size:48px; margin-bottom:16px;"></i>
                    <p>Carregando pré-visualização...</p>
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
            <a href="{{ route('tickets.index') }}" class="kpi-card-modern primary" style="text-decoration: none; color: inherit;">
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
            </a>
            
            <a href="{{ route('tickets.index', ['status' => 'open']) }}" class="kpi-card-modern warning" style="text-decoration: none; color: inherit;">
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
            </a>
            
            <a href="{{ route('tickets.index', ['status' => 'in_progress']) }}" class="kpi-card-modern info" style="text-decoration: none; color: inherit;">
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
            </a>
            
            <a href="{{ route('tickets.index', ['status' => 'resolved']) }}" class="kpi-card-modern success" style="text-decoration: none; color: inherit;">
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
            </a>
        </div>
    </div>

    <!-- KPIs Secundários -->
    <div class="secondary-kpi-section">
        <div class="secondary-kpi-grid">
            <a href="{{ route('tickets.index', ['overdue' => 'true']) }}" class="secondary-kpi-card danger" style="text-decoration: none; color: inherit;">
                <div class="secondary-kpi-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="secondary-kpi-content">
                    <div class="secondary-kpi-value">{{ $overdueTickets ?? 0 }}</div>
                    <div class="secondary-kpi-label">Vencidos</div>
                </div>
            </a>
            
            <a href="{{ route('tickets.index', ['sla' => 'ok']) }}" class="secondary-kpi-card primary" style="text-decoration: none; color: inherit;">
                <div class="secondary-kpi-icon">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div class="secondary-kpi-content">
                    <div class="secondary-kpi-value">94.5%</div>
                    <div class="secondary-kpi-label">SLA Cumprido</div>
                </div>
            </a>
            
            <a href="{{ route('tickets.index', ['reopened' => 'true']) }}" class="secondary-kpi-card warning" style="text-decoration: none; color: inherit;">
                <div class="secondary-kpi-icon">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </div>
                <div class="secondary-kpi-content">
                    <div class="secondary-kpi-value">{{ $reopenedTickets ?? 0 }}</div>
                    <div class="secondary-kpi-label">Reabertos</div>
                </div>
            </a>
            
            <a href="{{ route('tickets.index') }}" class="secondary-kpi-card info" style="text-decoration: none; color: inherit;">
                <div class="secondary-kpi-icon">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <div class="secondary-kpi-content">
                    <div class="secondary-kpi-value">2.3h</div>
                    <div class="secondary-kpi-label">Tempo Médio</div>
                </div>
            </a>
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
                    <div class="satisfaction-label" 
                         title="Calculado com base na taxa de resolução, tempo médio e taxa de retrabalho">
                        Net Promoter Score
                        <i class="bi bi-info-circle-fill text-muted" style="font-size: 0.875rem; cursor: help;"></i>
                    </div>
                    <div class="satisfaction-trend">
                        @php
                            $currentNPS = floatval($satisfaction ?? 9.2);
                            $previousNPS = 8.4; // Valor do mês anterior (pode ser armazenado)
                            $npsChange = $currentNPS - $previousNPS;
                        @endphp
                        @if($npsChange > 0)
                            <i class="bi bi-arrow-up text-success"></i>
                            <span>+{{ number_format($npsChange, 1) }} desde o mês passado</span>
                        @elseif($npsChange < 0)
                            <i class="bi bi-arrow-down text-danger"></i>
                            <span>{{ number_format($npsChange, 1) }} desde o mês passado</span>
                        @else
                            <i class="bi bi-dash text-muted"></i>
                            <span>Sem mudança desde o mês passado</span>
                        @endif
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
                    <div class="satisfaction-value">{{ $feedbacks ?? 0 }}</div>
                    <div class="satisfaction-label"
                         title="Total de comentários/interações recebidas este mês">
                        Interações Este Mês
                        <i class="bi bi-info-circle-fill text-muted" style="font-size: 0.875rem; cursor: help;"></i>
                    </div>
                    <div class="satisfaction-trend">
                        @php
                            $currentFeedbacks = intval($feedbacks ?? 0);
                            $feedbackChange = intval($feedbackChange ?? 0);
                        @endphp
                        @if($feedbackChange > 0)
                            <i class="bi bi-arrow-up text-success"></i>
                            <span>+{{ $feedbackChange }} desde o mês passado</span>
                        @elseif($feedbackChange < 0)
                            <i class="bi bi-arrow-down text-danger"></i>
                            <span>{{ $feedbackChange }} desde o mês passado</span>
                        @else
                            <i class="bi bi-dash text-muted"></i>
                            <span>Sem mudança</span>
                        @endif
                    </div>
                </div>
                <div class="satisfaction-breakdown">
                    <div class="rating-stars">
                        @php
                            $rating = floatval($averageRating ?? 4.6);
                            $fullStars = floor($rating);
                            $hasHalfStar = ($rating - $fullStars) >= 0.5;
                            $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                        @endphp
                        @for($i = 0; $i < $fullStars; $i++)
                            <i class="bi bi-star-fill"></i>
                        @endfor
                        @if($hasHalfStar)
                            <i class="bi bi-star-half"></i>
                        @endif
                        @for($i = 0; $i < $emptyStars; $i++)
                            <i class="bi bi-star"></i>
                        @endfor
                        <span class="rating-value">{{ $averageRating ?? '4.6' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
/* Efeitos de hover para cards clicáveis */
.kpi-card-modern {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
}

.kpi-card-modern:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15) !important;
}

.secondary-kpi-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    cursor: pointer;
}

.secondary-kpi-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.12) !important;
}

/* Garantir que o texto não mude de cor ao clicar */
.kpi-card-modern:active,
.kpi-card-modern:visited,
.secondary-kpi-card:active,
.secondary-kpi-card:visited {
    color: inherit !important;
}
</style>
@endpush

@push('scripts')
<script>
        window.DASHBOARD_EXPORT_URL = @json(route('dashboard.export'));
        window.DASHBOARD_METRICS_EXPORT_URL = @json(route('dashboard.metrics.export'));
        window.DASHBOARD_EXPORT_PREVIEW_URL = @json(route('dashboard.export.preview'));
    </script>
@endpush

@push('scripts')
<script>
function showToast(message, type = 'info') {
    const toast = document.createElement('div');
    const bgColors = {
        'success': 'bg-success',
        'info': 'bg-info',
        'warning': 'bg-warning',
        'danger': 'bg-danger'
    };
    
    toast.className = `toast align-items-center text-white ${bgColors[type]} border-0 position-fixed top-0 end-0 m-3`;
    toast.style.cssText = 'z-index: 9999;';
    toast.setAttribute('role', 'alert');
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    document.body.appendChild(toast);
    const bsToast = new bootstrap.Toast(toast, { delay: 3000 });
    bsToast.show();
    
    toast.addEventListener('hidden.bs.toast', () => {
        toast.remove();
    });
}
</script>
@endpush

@push('scripts')
<script>
// Remover função antiga do mapa de localizações
function refreshLocationMap() {
    const btn = event.currentTarget;
    const icon = btn.querySelector('i');
    
    // Adicionar animação de rotação
    icon.style.animation = 'spin 1s linear';
    
    // Simular atualização (recarregar página por enquanto)
    setTimeout(() => {
        window.location.reload();
    }, 500);
}

// Adicionar interatividade aos itens de localização
document.addEventListener('DOMContentLoaded', function() {
    const locationItems = document.querySelectorAll('.location-item-modern');
    
    locationItems.forEach(item => {
        item.addEventListener('click', function() {
            const locationName = this.dataset.location;
            const title = this.getAttribute('title');
            
            // Mostrar detalhes em um toast/alert
            showLocationDetails(locationName, title);
        });
    });
});

function showLocationDetails(name, details) {
    // Criar toast de notificação
    const toast = document.createElement('div');
    toast.className = 'alert alert-info alert-dismissible fade show position-fixed';
    toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 350px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    toast.innerHTML = `
        <strong><i class="bi bi-info-circle me-2"></i>${name}</strong><br>
        <small>${details}</small>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(toast);
    
    // Remover após 5 segundos
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

// Adicionar animação de spin
const style = document.createElement('style');
style.textContent = `
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);
</script>
@vite(['resources/js/app.js', 'resources/js/dashboard-modern.js'])
@endpush
