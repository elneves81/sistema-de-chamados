@extends('layouts.app')

@section('content')
@push('head')
<!-- Font Awesome CDN para ícones -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-dyZtM3C6Q+6QwQKfQk5QwQKfQk5QwQKfQk5QwQKfQk5QwQKfQk5QwQKfQk5QwQKfQk5QwQKfQk5QwQKfQk5QwQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
@endpush
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    --warning-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    --danger-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    --info-gradient: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
    --dark-gradient: linear-gradient(135deg, #434343 0%, #000000 100%);
    --light-gradient: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
    --card-shadow: 0 15px 35px rgba(50, 50, 93, 0.1), 0 5px 15px rgba(0, 0, 0, 0.07);
    --card-hover-shadow: 0 20px 40px rgba(50, 50, 93, 0.15), 0 8px 25px rgba(0, 0, 0, 0.1);
    --glass-bg: rgba(255, 255, 255, 0.25);
    --glass-border: rgba(255, 255, 255, 0.18);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    margin: 0;
    padding: 0;
}

.dashboard-container {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    min-height: 100vh;
    padding: 2rem;
    position: relative;
    max-width: 1400px;
    margin: 0 auto;
}

.dashboard-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 20%, rgba(120, 119, 198, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255, 118, 117, 0.1) 0%, transparent 50%),
        radial-gradient(circle at 40% 40%, rgba(99, 102, 241, 0.1) 0%, transparent 50%);
    pointer-events: none;
}

/* Header Premium */
.dashboard-header {
    background: var(--primary-gradient);
    color: white;
    padding: 3rem;
    border-radius: 24px;
    margin-bottom: 2rem;
    box-shadow: var(--card-shadow);
    position: relative;
    overflow: hidden;
}

.dashboard-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 4s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.3; }
    50% { transform: scale(1.2); opacity: 0.6; }
}

.header-content {
    position: relative;
    z-index: 2;
}

.welcome-title {
    font-size: 3.5rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    background: linear-gradient(45deg, #fff, #f0f8ff);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.welcome-subtitle {
    font-size: 1.2rem;
    opacity: 0.9;
    font-weight: 300;
    letter-spacing: 0.5px;
}

.header-stats {
    display: flex;
    align-items: center;
    gap: 2rem;
    margin-top: 1rem;
}

.status-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    padding: 0.75rem 1.5rem;
    border-radius: 50px;
    font-weight: 500;
}

.status-dot {
    width: 8px;
    height: 8px;
    background: #00ff88;
    border-radius: 50%;
    animation: blink 2s infinite;
}

@keyframes blink {
    0%, 50% { opacity: 1; }
    51%, 100% { opacity: 0.3; }
}

.header-widgets {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    flex-wrap: wrap;
}

.widget-mini {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    padding: 1rem 1.5rem;
    border-radius: 16px;
    color: white;
    min-width: 120px;
}

.widget-mini i {
    font-size: 1.5rem;
    opacity: 0.9;
}

.widget-info {
    text-align: left;
}

.widget-value {
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.widget-label {
    font-size: 0.7rem;
    opacity: 0.8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.time-widget .widget-value {
    font-family: 'Courier New', monospace;
    font-size: 1.2rem;
    font-weight: 800;
}

.time-widget .widget-label {
    font-size: 0.65rem;
    opacity: 0.9;
}

/* KPI Cards Premium */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.kpi-card {
    background: white;
    border-radius: 24px;
    padding: 2.5rem;
    box-shadow: var(--card-shadow);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    border: 1px solid rgba(255,255,255,0.5);
    position: relative;
    overflow: hidden;
}

.kpi-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 5px;
    background: var(--card-gradient);
    border-radius: 24px 24px 0 0;
}

.kpi-card.primary { --card-gradient: var(--primary-gradient); }
.kpi-card.success { --card-gradient: var(--success-gradient); }
.kpi-card.warning { --card-gradient: var(--warning-gradient); }
.kpi-card.danger { --card-gradient: var(--danger-gradient); }
.kpi-card.info { --card-gradient: var(--info-gradient); }

.kpi-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: var(--card-hover-shadow);
}

.kpi-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.5rem;
}

.kpi-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.kpi-icon {
    width: 60px;
    height: 60px;
    background: var(--card-gradient);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.kpi-value {
    font-size: 3.5rem;
    font-weight: 900;
    background: var(--card-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    line-height: 1;
    margin-bottom: 0.5rem;
}

.kpi-change {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    font-weight: 600;
}

.change-positive { color: #10b981; }
.change-negative { color: #ef4444; }

/* Content Cards */
.content-section {
    margin-bottom: 3rem;
}

.section-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.section-title::after {
    content: '';
    flex: 1;
    height: 2px;
    background: linear-gradient(90deg, #667eea, transparent);
}

.content-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 2rem;
}

.content-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--card-shadow);
    transition: all 0.3s ease;
    border: 1px solid rgba(255,255,255,0.5);
}

.content-card:hover {
    box-shadow: var(--card-hover-shadow);
    transform: translateY(-5px);
}

.card-header {
    background: var(--primary-gradient);
    color: white;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0;
}

.btn-card {
    background: rgba(255,255,255,0.2);
    color: white;
    border: 1px solid rgba(255,255,255,0.3);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-card:hover {
    background: rgba(255,255,255,0.3);
    color: white;
    transform: translateY(-2px);
    text-decoration: none;
}

.card-body {
    padding: 0;
}

/* Table Moderna */
.table-modern {
    width: 100%;
    margin: 0;
}

.table-modern thead th {
    background: #f8fafc;
    color: #374151;
    font-weight: 600;
    font-size: 0.8rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 1.25rem 2rem;
    border: none;
}

.table-modern tbody td {
    padding: 1.25rem 2rem;
    border: none;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: middle;
}

.table-modern tbody tr {
    transition: all 0.2s ease;
}

.table-modern tbody tr:hover {
    background: #f8fafc;
    transform: scale(1.01);
}

/* Badges Modernos */
.badge-modern {
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}

.badge-success { background: var(--success-gradient); color: white; }
.badge-warning { background: var(--warning-gradient); color: #92400e; }
.badge-danger { background: var(--danger-gradient); color: white; }
.badge-info { background: var(--info-gradient); color: #0c4a6e; }
.badge-secondary { background: #e5e7eb; color: #374151; }

/* Knowledge Base Cards */
.knowledge-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 1.5rem;
}

.knowledge-card {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: 0 4px 6px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    position: relative;
    overflow: hidden;
}

.knowledge-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background: var(--primary-gradient);
    transition: width 0.3s ease;
}

.knowledge-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 12px 28px rgba(0,0,0,0.12);
}

.knowledge-card:hover::before {
    width: 8px;
}

.knowledge-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.75rem;
    line-height: 1.4;
}

.knowledge-content {
    color: #6b7280;
    font-size: 0.9rem;
    line-height: 1.6;
    margin-bottom: 1rem;
}

.knowledge-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1rem;
    border-top: 1px solid #f3f4f6;
}

.category-tag {
    background: var(--info-gradient);
    color: white;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.views-count {
    color: #9ca3af;
    font-size: 0.8rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Empty States */
.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: #6b7280;
}

.empty-icon {
    font-size: 4rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-title {
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-message {
    font-size: 0.9rem;
    opacity: 0.8;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .dashboard-container { padding: 1.5rem; }
    .kpi-grid { grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); }
    .content-grid { grid-template-columns: 1fr; }
    .knowledge-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .dashboard-container { padding: 1rem; }
    .dashboard-header { padding: 2rem; }
    .welcome-title { font-size: 2.5rem; }
    .kpi-grid { grid-template-columns: 1fr; gap: 1rem; }
    .kpi-card { padding: 1.5rem; }
    .kpi-value { font-size: 2.5rem; }
    .header-stats { flex-direction: column; gap: 1rem; }
    .header-widgets { 
        justify-content: center; 
        margin-top: 1rem;
    }
    .widget-mini { 
        min-width: 100px; 
        padding: 0.75rem 1rem;
    }
    .table-modern thead th,
    .table-modern tbody td { padding: 1rem; }
}

/* Loading Animation */
@keyframes shimmer {
    0% { background-position: -200px 0; }
    100% { background-position: calc(200px + 100%) 0; }
}

.skeleton {
    background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
    background-size: 200px 100%;
    animation: shimmer 1.5s infinite;
}

/* Scroll Animations */
.fade-in-up {
    animation: fadeInUp 0.8s ease-out forwards;
}

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

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}

/* SLA Progress Bar */
.sla-bar-container {
    background: white;
    border-radius: 16px;
    padding: 2rem;
    box-shadow: var(--card-shadow);
    margin-bottom: 2rem;
    border: 1px solid rgba(255,255,255,0.5);
}

.sla-bar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.sla-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: #374151;
}

.sla-percentage {
    font-size: 1.5rem;
    font-weight: 800;
    background: var(--success-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.sla-progress-bar {
    height: 12px;
    background: #e5e7eb;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 1rem;
}

.sla-progress-fill {
    height: 100%;
    background: var(--success-gradient);
    border-radius: 6px;
    transition: width 2s ease;
    position: relative;
}

.sla-progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    animation: shimmer 2s infinite;
}

.sla-indicators {
    display: flex;
    gap: 2rem;
    justify-content: center;
}

.sla-indicator {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.85rem;
    color: #6b7280;
}

.sla-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
}

.sla-indicator.excellent .sla-dot { background: #10b981; }
.sla-indicator.good .sla-dot { background: #f59e0b; }
.sla-indicator.warning .sla-dot { background: #ef4444; }
</style>

<div class="dashboard-container">
    <!-- Header Premium -->
    <div class="dashboard-header fade-in-up">
        <div class="header-content">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="welcome-title">Sistema de Chamados</h1>
                    <p class="welcome-subtitle">DITIS - Departamento de Informação, Tecnologia e Inovação em Saúde</p>
                    <div class="header-stats">
                        <div class="status-indicator">
                            <div class="status-dot"></div>
                            <span>Sistema Online</span>
                        </div>
                        <div class="status-indicator">
                            <i class="fas fa-clock"></i>
                            <span>{{ now()->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="status-indicator">
                            <i class="fas fa-user"></i>
                            <span>{{ auth()->user()->name }}</span>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 text-right">
                    <div class="header-widgets">
                        <!-- Botões do topo -->
                        <button class="btn-top" id="btnTvMode" title="Modo TV (F11)"><i class="fas fa-tv"></i></button>
                        <button class="btn-top" id="btnRefresh" title="Atualizar"><i class="fas fa-sync-alt"></i></button>
                        <button class="btn-top" id="btnTheme" title="Alternar tema"><i class="fas fa-moon"></i></button>
                        <button class="btn-top" id="btnNotify" title="Notificações"><i class="fas fa-bell"></i><span class="notify-badge" id="notifyBadge">{{ rand(1, 9) }}</span></button>
                    </div>
                    <div class="header-widgets" style="margin-top:1rem;">
                        <div class="widget-mini time-widget">
                            <i class="fas fa-clock"></i>
                            <div class="widget-info">
                                <div class="widget-value" id="currentTime">--:--</div>
                                <div class="widget-label" id="currentDate">--/--/----</div>
                            </div>
                        </div>
                        <div class="widget-mini">
                            <i class="fas fa-bell"></i>
                            <div class="widget-info">
                                <div class="widget-value">{{ rand(1, 9) }}</div>
                                <div class="widget-label">Notificações</div>
                            </div>
                        </div>
                        <div class="widget-mini">
                            <i class="fas fa-heartbeat"></i>
                            <div class="widget-info">
                                <div class="widget-value">99.{{ rand(1, 9) }}%</div>
                                <div class="widget-label">Uptime</div>
                            </div>
                        </div>
                        <div class="widget-mini">
                            <i class="fas fa-users"></i>
                            <div class="widget-info">
                                <div class="widget-value">{{ Auth::user()->name }}</div>
                                <div class="widget-label">Usuários Online</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- KPIs Premium -->
    <div class="kpi-grid fade-in-up">
        <div class="kpi-card primary">
            <div class="kpi-header">
                <div class="kpi-title">Total de Chamados</div>
                <div class="kpi-icon">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $totalTickets ?? 0 }}</div>
            <div class="kpi-change change-positive">
                <i class="fas fa-arrow-up"></i>
                <span>+12% vs mês anterior</span>
            </div>
        </div>

        <div class="kpi-card warning">
            <div class="kpi-header">
                <div class="kpi-title">Chamados Abertos</div>
                <div class="kpi-icon">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $openTickets ?? 0 }}</div>
            <div class="kpi-change change-negative">
                <i class="fas fa-arrow-down"></i>
                <span>-8% vs mês anterior</span>
            </div>
        </div>

        <div class="kpi-card success">
            <div class="kpi-header">
                <div class="kpi-title">Chamados Fechados</div>
                <div class="kpi-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $closedTickets ?? 0 }}</div>
            <div class="kpi-change change-positive">
                <i class="fas fa-arrow-up"></i>
                <span>+23% vs mês anterior</span>
            </div>
        </div>

        <div class="kpi-card info">
            <div class="kpi-header">
                <div class="kpi-title">Total de Ativos</div>
                <div class="kpi-icon">
                    <i class="fas fa-desktop"></i>
                </div>
            </div>
            <div class="kpi-value">{{ $totalAssets ?? 0 }}</div>
            <div class="kpi-change change-positive">
                <i class="fas fa-arrow-up"></i>
                <span>+5% vs mês anterior</span>
            </div>
        </div>

        <div class="kpi-card danger">
            <div class="kpi-header">
                <div class="kpi-title">SLA Crítico</div>
                <div class="kpi-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
            </div>
            <div class="kpi-value">{{ rand(0, 5) }}</div>
            <div class="kpi-change change-{{ rand(0, 5) > 2 ? 'negative' : 'positive' }}">
                <i class="fas fa-arrow-{{ rand(0, 5) > 2 ? 'up' : 'down' }}"></i>
                <span>{{ rand(0, 5) > 2 ? '+' : '-' }}{{ rand(1, 3) }} vs ontem</span>
            </div>
        </div>

        <div class="kpi-card success">
            <div class="kpi-header">
                <div class="kpi-title">Satisfação</div>
                <div class="kpi-icon">
                    <i class="fas fa-star"></i>
                </div>
            </div>
            <div class="kpi-value">4.{{ rand(6, 9) }}</div>
            <div class="kpi-change change-positive">
                <i class="fas fa-arrow-up"></i>
                <span>+0.{{ rand(1, 3) }} vs mês anterior</span>
            </div>
        </div>

        <div class="kpi-card info">
            <div class="kpi-header">
                <div class="kpi-title">Tempo Médio</div>
                <div class="kpi-icon">
                    <i class="fas fa-clock"></i>
                </div>
            </div>
            <div class="kpi-value">{{ rand(1, 4) }}.{{ rand(1, 9) }}h</div>
            <div class="kpi-change change-positive">
                <i class="fas fa-arrow-down"></i>
                <span>-{{ rand(10, 30) }}min vs semana anterior</span>
            </div>
        </div>

        <div class="kpi-card warning">
            <div class="kpi-header">
                <div class="kpi-title">Performance</div>
                <div class="kpi-icon">
                    <i class="fas fa-tachometer-alt"></i>
                </div>
            </div>
            <div class="kpi-value">{{ rand(85, 99) }}%</div>
            <div class="kpi-change change-positive">
                <i class="fas fa-arrow-up"></i>
                <span>+{{ rand(2, 8) }}% vs mês anterior</span>
            </div>
        </div>
    </div>

    <!-- Conteúdo Principal -->
    <div class="content-section fade-in-up">
        <h2 class="section-title">
            <i class="fas fa-chart-line"></i>
            Atividade Recente
        </h2>
        
        <!-- Barra de SLA -->
        <div class="sla-bar-container">
            <div class="sla-bar-header">
                <span class="sla-title">SLA Geral do Sistema</span>
                <span class="sla-percentage">{{ rand(85, 99) }}%</span>
            </div>
            <div class="sla-progress-bar">
                <div class="sla-progress-fill" style="width: {{ rand(85, 99) }}%"></div>
            </div>
            <div class="sla-indicators">
                <div class="sla-indicator excellent">
                    <span class="sla-dot"></span>
                    <span>Excelente (>95%)</span>
                </div>
                <div class="sla-indicator good">
                    <span class="sla-dot"></span>
                    <span>Bom (85-95%)</span>
                </div>
                <div class="sla-indicator warning">
                    <span class="sla-dot"></span>
                    <span>Atenção (<85%)</span>
                </div>
            </div>
        </div>
        
        <div class="content-grid">
            <!-- Chamados Recentes -->
            <div class="content-card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="fas fa-clock mr-2"></i>
                        Chamados Recentes
                    </h6>
                    <a href="{{ route('tickets.index') }}" class="btn-card">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($recentTickets) && $recentTickets->count() > 0)
                        <div class="table-responsive">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentTickets as $ticket)
                                    <tr>
                                        <td><strong>#{{ $ticket->id }}</strong></td>
                                        <td>{{ Str::limit($ticket->title, 40) }}</td>
                                        <td>
                                            <span class="badge-modern badge-{{ $ticket->status == 'open' ? 'warning' : ($ticket->status == 'closed' ? 'success' : 'info') }}">
                                                {{ $ticket->status == 'open' ? 'Aberto' : ($ticket->status == 'closed' ? 'Fechado' : 'Em Andamento') }}
                                            </span>
                                        </td>
                                        <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-inbox"></i>
                            </div>
                            <div class="empty-title">Nenhum chamado recente</div>
                            <div class="empty-message">Os chamados aparecerão aqui conforme forem criados</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Ativos Recentes -->
            <div class="content-card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="fas fa-laptop mr-2"></i>
                        Ativos Atualizados
                    </h6>
                    <a href="{{ route('assets.index') }}" class="btn-card">
                        Ver Todos
                    </a>
                </div>
                <div class="card-body">
                    @if(isset($recentAssets) && $recentAssets->count() > 0)
                        <div class="table-responsive">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>Tag</th>
                                        <th>Nome</th>
                                        <th>Status</th>
                                        <th>Atualizado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentAssets as $asset)
                                    <tr>
                                        <td><strong>{{ $asset->asset_tag }}</strong></td>
                                        <td>{{ Str::limit($asset->name, 35) }}</td>
                                        <td>
                                            <span class="badge-modern badge-{{ $asset->status == 'active' ? 'success' : ($asset->status == 'maintenance' ? 'warning' : 'secondary') }}">
                                                {{ $asset->status == 'active' ? 'Ativo' : ($asset->status == 'maintenance' ? 'Manutenção' : 'Inativo') }}
                                            </span>
                                        </td>
                                        <td>{{ $asset->updated_at->format('d/m/Y') }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-box"></i>
                            </div>
                            <div class="empty-title">Nenhum ativo recente</div>
                            <div class="empty-message">Os ativos aparecerão aqui conforme forem atualizados</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Base de Conhecimento -->
    @if(isset($knowledgeArticles) && $knowledgeArticles->count() > 0)
    <div class="content-section fade-in-up">
        <h2 class="section-title">
            <i class="fas fa-graduation-cap"></i>
            Base de Conhecimento
        </h2>
        
        <div class="content-card">
            <div class="card-header">
                <h6 class="card-title">
                    <i class="fas fa-fire mr-2"></i>
                    Artigos Populares
                </h6>
                <a href="{{ route('knowledge.index') }}" class="btn-card">
                    Ver Base Completa
                </a>
            </div>
            <div class="card-body" style="padding: 2rem;">
                <div class="knowledge-grid">
                    @foreach($knowledgeArticles as $article)
                    <div class="knowledge-card">
                        <h6 class="knowledge-title">{{ Str::limit($article->title, 50) }}</h6>
                        <p class="knowledge-content">{{ Str::limit($article->content, 120) }}</p>
                        <div class="knowledge-meta">
                            <span class="category-tag">{{ $article->category->name ?? 'Geral' }}</span>
                            <span class="views-count">
                                <i class="fas fa-eye"></i>
                                {{ $article->views }} views
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Gráfico de Performance -->
    <div class="content-section fade-in-up">
        <h2 class="section-title">
            <i class="fas fa-chart-area"></i>
            Análise de Performance
        </h2>
        
        <div class="content-grid">
            <div class="content-card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="fas fa-chart-line mr-2"></i>
                        Chamados por Mês
                    </h6>
                </div>
                <div class="card-body" style="padding: 2rem;">
                    <canvas id="monthlyChart" height="300"></canvas>
                </div>
            </div>
            
            <div class="content-card">
                <div class="card-header">
                    <h6 class="card-title">
                        <i class="fas fa-chart-pie mr-2"></i>
                        Distribuição por Status
                    </h6>
                </div>
                <div class="card-body" style="padding: 2rem;">
                    <canvas id="statusChart" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mensagem de tela cheia -->
<div id="fullscreenMsg" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;z-index:2000;background:rgba(0,0,0,0.7);color:#fff;align-items:center;justify-content:center;font-size:2rem;font-weight:700;text-align:center;">
    Modo TV Ativado<br><span style="font-size:1.2rem;font-weight:400;">Pressione ESC para sair do modo TV</span>
</div>

<!-- Notificações Toast -->
<div class="toast-container">
    <div class="toast toast-success" id="successToast">
        <i class="fas fa-check-circle"></i>
        <span class="toast-message">Sistema funcionando perfeitamente!</span>
        <button class="toast-close">&times;</button>
    </div>
</div>

<style>
.toast-container {
    position: fixed;
    top: 2rem;
    right: 2rem;
    z-index: 1100;
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.toast {
    background: white;
    border-radius: 12px;
    padding: 1rem 1.5rem;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border-left: 4px solid #10b981;
    display: flex;
    align-items: center;
    gap: 1rem;
    min-width: 300px;
    transform: translateX(400px);
    opacity: 0;
    transition: all 0.3s ease;
}

.toast.show {
    transform: translateX(0);
    opacity: 1;
}

.toast-success {
    border-left-color: #10b981;
}

.toast-success i {
    color: #10b981;
}

.toast-message {
    flex: 1;
    font-weight: 500;
    color: #374151;
}

.toast-close {
    background: none;
    border: none;
    font-size: 1.2rem;
    color: #9ca3af;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.toast-close:hover {
    color: #6b7280;
}

.btn-top {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    color: #fff;
    border-radius: 50%;
    width: 44px;
    height: 44px;
    margin-left: 0.5rem;
    font-size: 1.3rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: background 0.2s, box-shadow 0.2s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    cursor: pointer;
    position: relative;
}
.btn-top:hover {
    background: var(--primary-gradient);
    color: #fff;
}
.notify-badge {
    position: absolute;
    top: 6px;
    right: 6px;
    background: #ef4444;
    color: #fff;
    border-radius: 50%;
    font-size: 0.7rem;
    padding: 2px 6px;
    font-weight: 700;
    box-shadow: 0 1px 4px rgba(0,0,0,0.15);
    display: inline-block;
}
.dark-theme {
    background: #18181b !important;
}
.dark-theme .dashboard-container,
.dark-theme .dashboard-header,
.dark-theme .kpi-card,
.dark-theme .content-card,
.dark-theme .sla-bar-container {
    background: #23232b !important;
    color: #fff !important;
    border-color: #333 !important;
}
.dark-theme .kpi-title,
.dark-theme .section-title,
.dark-theme .card-title,
.dark-theme .sla-title {
    color: #fff !important;
}
.dark-theme .kpi-value,
.dark-theme .sla-percentage {
    -webkit-text-fill-color: #fff !important;
}
</style>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<!-- Scripts para animações -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animação de entrada dos elementos
    const animateElements = document.querySelectorAll('.fade-in-up');
    animateElements.forEach((element, index) => {
        element.style.animationDelay = `${index * 0.1}s`;
    });

    // Animação dos valores KPI
    const kpiValues = document.querySelectorAll('.kpi-value');
    kpiValues.forEach(element => {
        const finalValue = parseInt(element.textContent);
        const duration = 2000;
        const stepTime = 50;
        const steps = duration / stepTime;
        const increment = finalValue / steps;
        let currentValue = 0;
        
        const timer = setInterval(() => {
            currentValue += increment;
            if (currentValue >= finalValue) {
                currentValue = finalValue;
                clearInterval(timer);
            }
            element.textContent = Math.floor(currentValue);
        }, stepTime);
    });

    // Efeito hover nos cards
    const cards = document.querySelectorAll('.kpi-card, .content-card, .knowledge-card');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = this.classList.contains('kpi-card') 
                ? 'translateY(-10px) scale(1.02)' 
                : 'translateY(-5px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Gráfico de chamados por mês
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'],
                datasets: [{
                    label: 'Chamados Abertos',
                    data: [12, 19, 15, 17, 25, 32, 28, 35, 29, 31, 24, 18],
                    borderColor: 'rgb(102, 126, 234)',
                    backgroundColor: 'rgba(102, 126, 234, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }, {
                    label: 'Chamados Fechados',
                    data: [8, 15, 12, 14, 22, 28, 25, 32, 26, 28, 21, 16],
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    },
                    x: {
                        grid: {
                            color: 'rgba(0,0,0,0.1)'
                        }
                    }
                }
            }
        });
    }

    // Gráfico de distribuição por status
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['Abertos', 'Em Andamento', 'Fechados', 'Pendentes'],
                datasets: [{
                    data: [{{ $openTickets ?? 0 }}, 15, {{ $closedTickets ?? 0 }}, 5],
                    backgroundColor: [
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(156, 163, 175, 0.8)'
                    ],
                    borderColor: [
                        'rgb(245, 158, 11)',
                        'rgb(59, 130, 246)',
                        'rgb(16, 185, 129)',
                        'rgb(156, 163, 175)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }
});

// Função Modo TV (Tela Cheia)
const btnTvMode = document.getElementById('btnTvMode');
const fullscreenMsg = document.getElementById('fullscreenMsg');
let isFullscreen = false;

function enterFullscreen() {
    const el = document.documentElement;
    if (el.requestFullscreen) el.requestFullscreen();
    else if (el.mozRequestFullScreen) el.mozRequestFullScreen();
    else if (el.webkitRequestFullscreen) el.webkitRequestFullscreen();
    else if (el.msRequestFullscreen) el.msRequestFullscreen();
    isFullscreen = true;
    fullscreenMsg.style.display = 'flex';
}

function exitFullscreen() {
    if (document.exitFullscreen) document.exitFullscreen();
    else if (document.mozCancelFullScreen) document.mozCancelFullScreen();
    else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
    else if (document.msExitFullscreen) document.msExitFullscreen();
    isFullscreen = false;
    fullscreenMsg.style.display = 'none';
}

btnTvMode.addEventListener('click', function() {
    if (!isFullscreen) enterFullscreen();
    else exitFullscreen();
});

document.addEventListener('fullscreenchange', function() {
    isFullscreen = !!document.fullscreenElement;
    fullscreenMsg.style.display = isFullscreen ? 'flex' : 'none';
});
document.addEventListener('keydown', function(e) {
    if (isFullscreen && e.key === 'Escape') {
        exitFullscreen();
    }
});

// Botão Atualizar
const btnRefresh = document.getElementById('btnRefresh');
btnRefresh.addEventListener('click', function() {
    location.reload();
});

// Botão Alternar Tema
const btnTheme = document.getElementById('btnTheme');
function setTheme(dark) {
    if (dark) {
        document.body.classList.add('dark-theme');
        localStorage.setItem('theme', 'dark');
        btnTheme.innerHTML = '<i class="fas fa-sun"></i>';
    } else {
        document.body.classList.remove('dark-theme');
        localStorage.setItem('theme', 'light');
        btnTheme.innerHTML = '<i class="fas fa-moon"></i>';
    }
}
btnTheme.addEventListener('click', function() {
    const isDark = document.body.classList.toggle('dark-theme');
    setTheme(isDark);
});
// Inicializa tema salvo
if (localStorage.getItem('theme') === 'dark') setTheme(true);
else setTheme(false);

// Botão Notificações
const btnNotify = document.getElementById('btnNotify');
const notifyBadge = document.getElementById('notifyBadge');
btnNotify.addEventListener('click', function() {
    showToast('Você tem novas notificações!');
    notifyBadge.style.display = 'none';
});

// Toast simples
function showToast(msg) {
    const toast = document.createElement('div');
    toast.className = 'toast show';
    toast.innerHTML = '<i class="fas fa-bell"></i><span class="toast-message">'+msg+'</span>';
    document.body.appendChild(toast);
    setTimeout(()=>{ toast.classList.remove('show'); toast.remove(); }, 3000);
}
</script>
@endsection
