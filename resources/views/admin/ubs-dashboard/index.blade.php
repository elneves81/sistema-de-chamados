@extends('layouts.app')

@section('title', 'Dashboard UBS - Gestão Unidades de Saúde')

@section('styles')
<style>
:root {
    --primary-color: #0d6efd;
    --success-color: #198754;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #0dcaf0;
}

.dashboard-card {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #e9ecef;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.dashboard-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
    border-color: #dee2e6;
}

.stat-card {
    text-align: center;
    padding: 1.75rem 1.25rem;
    border-radius: 12px;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, var(--primary-color), var(--info-color));
}

.stat-card-primary::before { background: linear-gradient(90deg, #0d6efd, #6610f2); }
.stat-card-success::before { background: linear-gradient(90deg, #198754, #20c997); }
.stat-card-warning::before { background: linear-gradient(90deg, #ffc107, #fd7e14); }
.stat-card-danger::before { background: linear-gradient(90deg, #dc3545, #e83e8c); }
.stat-card-info::before { background: linear-gradient(90deg, #0dcaf0, #6f42c1); }

.stat-icon {
    font-size: 2.5rem;
    opacity: 0.15;
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
}

.stat-number {
    font-size: 2.25rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    position: relative;
    z-index: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
    letter-spacing: 0.3px;
}

.stat-change {
    font-size: 0.75rem;
    margin-top: 0.5rem;
}

.page-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 2rem;
    margin-bottom: 2rem;
    color: white;
}

.page-header h1 {
    font-size: 1.75rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.sync-badge {
    background: rgba(255,255,255,0.2);
    border-radius: 50px;
    padding: 0.5rem 1rem;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.btn-action {
    border-radius: 8px;
    padding: 0.625rem 1.25rem;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.2s;
}

.btn-action:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.status-badge {
    padding: 0.375rem 0.875rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    letter-spacing: 0.3px;
}

.status-excellent {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-good {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    color: #0c5460;
    border: 1px solid #bee5eb;
}

.status-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-critical {
    background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.health-score {
    font-size: 1.5rem;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 60px;
    height: 60px;
    border-radius: 50%;
    position: relative;
}

.health-score::before {
    content: '';
    position: absolute;
    inset: -3px;
    border-radius: 50%;
    padding: 3px;
    background: linear-gradient(135deg, currentColor, transparent);
    -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    -webkit-mask-composite: xor;
    mask-composite: exclude;
}

.chart-container {
    position: relative;
    height: 320px;
    padding: 1rem 0;
}

.table-ubs {
    font-size: 0.9rem;
}

.table-ubs thead th {
    background: linear-gradient(180deg, #f8f9fa 0%, #e9ecef 100%);
    border: none;
    font-weight: 600;
    color: #495057;
    padding: 1rem 0.75rem;
    text-transform: uppercase;
    font-size: 0.75rem;
    letter-spacing: 0.5px;
}

.table-ubs tbody tr {
    transition: all 0.2s;
}

.table-ubs tbody tr:hover {
    background: #f8f9fa;
    transform: scale(1.01);
}

.table-ubs td {
    padding: 1rem 0.75rem;
    vertical-align: middle;
}

.progress-modern {
    height: 8px;
    border-radius: 10px;
    background: #e9ecef;
    overflow: hidden;
}

.progress-modern .progress-bar {
    border-radius: 10px;
    transition: width 0.6s ease;
}

.ubs-name {
    font-weight: 600;
    color: #212529;
    font-size: 0.95rem;
}

.ubs-info {
    color: #6c757d;
    font-size: 0.8rem;
    margin-top: 0.25rem;
}

.badge-modern {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    font-weight: 600;
    font-size: 0.75rem;
}

.search-box {
    position: relative;
}

.search-box input {
    padding-left: 2.5rem;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    transition: all 0.2s;
}

.search-box input:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(13,110,253,.15);
}

.search-box i {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 700;
    color: #212529;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.section-title i {
    color: var(--primary-color);
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 4rem;
    opacity: 0.3;
    margin-bottom: 1rem;
}
</style>
@endsection

@section('content')
<div class="container-fluid px-4 py-3">
    
    <!-- Page Header -->
    <div class="page-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1><i class="bi bi-hospital me-2"></i>Dashboard UBS Guarapuava</h1>
                <p class="mb-0 opacity-90">Gestão e monitoramento das Unidades Básicas de Saúde</p>
            </div>
            <div class="col-md-4 text-end">
                @if($stats['ldap_sync_last'])
                <div class="sync-badge mb-2">
                    <i class="bi bi-clock-history"></i>
                    <span>Última sync: {{ \Carbon\Carbon::parse($stats['ldap_sync_last'])->format('d/m H:i') }}</span>
                </div>
                @endif
                <div class="mt-2">
                    <button class="btn btn-light btn-action me-2" onclick="refreshData()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Atualizar
                    </button>
                    <a href="{{ route('admin.ldap.import.form') }}" class="btn btn-light btn-action">
                        <i class="bi bi-people me-1"></i>LDAP
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas Gerais -->
    <div class="row g-3 mb-4">
        <div class="col-md-2 col-sm-6">
            <div class="dashboard-card stat-card stat-card-primary">
                <i class="bi bi-buildings stat-icon"></i>
                <div class="stat-number text-primary">{{ $stats['total_ubs'] }}</div>
                <div class="stat-label">UBS Cadastradas</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="dashboard-card stat-card stat-card-info">
                <i class="bi bi-people-fill stat-icon"></i>
                <div class="stat-number text-info">{{ $stats['total_users'] }}</div>
                <div class="stat-label">Total de Usuários</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="dashboard-card stat-card stat-card-success">
                <i class="bi bi-person-check-fill stat-icon"></i>
                <div class="stat-number text-success">{{ $stats['users_with_ubs'] }}</div>
                <div class="stat-label">Vinculados UBS</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="dashboard-card stat-card stat-card-primary">
                <i class="bi bi-ticket-detailed-fill stat-icon"></i>
                <div class="stat-number text-primary">{{ $stats['total_tickets'] }}</div>
                <div class="stat-label">Total Chamados</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="dashboard-card stat-card stat-card-warning">
                <i class="bi bi-exclamation-circle-fill stat-icon"></i>
                <div class="stat-number text-warning">{{ $stats['tickets_open'] }}</div>
                <div class="stat-label">Abertos</div>
            </div>
        </div>
        <div class="col-md-2 col-sm-6">
            <div class="dashboard-card stat-card stat-card-success">
                <i class="bi bi-calendar-check-fill stat-icon"></i>
                <div class="stat-number text-success">{{ $stats['tickets_today'] }}</div>
                <div class="stat-label">Hoje</div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="dashboard-card">
                <h5 class="section-title">
                    <i class="bi bi-bar-chart-fill"></i>
                    Chamados por Unidade
                </h5>
                @php
                    $hasTicketsData = isset($charts['tickets_by_ubs']) && count($charts['tickets_by_ubs']) > 0;
                @endphp
                @if($hasTicketsData)
                    <div class="chart-container">
                        <canvas id="ticketsByUbsChart"></canvas>
                    </div>
                @else
                    <div class="empty-state py-5">
                        <i class="bi bi-inbox"></i>
                        <p class="mb-0 mt-2">Nenhum chamado registrado ainda</p>
                        <small class="text-muted">Os gráficos aparecerão quando houver dados</small>
                    </div>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="dashboard-card">
                <h5 class="section-title">
                    <i class="bi bi-pie-chart-fill"></i>
                    Distribuição de Usuários
                </h5>
                @php
                    $hasUsersData = isset($charts['users_by_ubs']) && count($charts['users_by_ubs']) > 0;
                @endphp
                @if($hasUsersData)
                    <div class="chart-container">
                        <canvas id="usersByUbsChart"></canvas>
                    </div>
                @else
                    <div class="empty-state py-5">
                        <i class="bi bi-people"></i>
                        <p class="mb-0 mt-2">Nenhum usuário cadastrado em UBS</p>
                        <small class="text-muted">Sincronize o LDAP para importar usuários</small>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Debug temporário - remover depois --}}
    @if(config('app.debug'))
    <div class="alert alert-info mb-4">
        <strong>Debug Info:</strong><br>
        Tickets UBS Count: {{ count($charts['tickets_by_ubs'] ?? []) }}<br>
        Users UBS Count: {{ count($charts['users_by_ubs'] ?? []) }}<br>
        @if(count($charts['tickets_by_ubs'] ?? []) > 0)
            Primeira UBS (Tickets): {{ $charts['tickets_by_ubs'][0]->ubs_name ?? 'N/A' }} 
            - {{ $charts['tickets_by_ubs'][0]->tickets_count ?? 0 }} chamados<br>
        @endif
        @if(count($charts['users_by_ubs'] ?? []) > 0)
            Primeira UBS (Users): {{ $charts['users_by_ubs'][0]['ubs_name'] ?? 'N/A' }} 
            - {{ $charts['users_by_ubs'][0]['total_users'] ?? 0 }} usuários<br>
        @endif
    </div>
    @endif

    <!-- Lista de UBS -->
    <div class="dashboard-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="section-title mb-0">
                <i class="bi bi-hospital"></i>
                Unidades Básicas de Saúde
            </h5>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="form-control" id="searchUbs" 
                       placeholder="Buscar UBS..." style="width: 280px;">
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-ubs table-hover align-middle" id="ubsTable">
                <thead>
                    <tr>
                        <th style="width: 25%;">Unidade</th>
                        <th style="width: 15%;">Usuários</th>
                        <th style="width: 20%;">Chamados</th>
                        <th style="width: 12%;" class="text-center">Tempo Médio</th>
                        <th style="width: 13%;" class="text-center">Status Geral</th>
                        <th style="width: 10%;" class="text-center">Score</th>
                        <th style="width: 5%;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ubsData as $ubs)
                    <tr>
                        <td>
                            <div class="ubs-name">{{ $ubs['name'] }}</div>
                            @if($ubs['address'])
                                <div class="ubs-info">
                                    <i class="bi bi-geo-alt"></i> {{ Str::limit($ubs['address'], 45) }}
                                </div>
                            @endif
                            @if($ubs['phone'])
                                <div class="ubs-info">
                                    <i class="bi bi-telephone"></i> {{ $ubs['phone'] }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="mb-1">
                                <strong>{{ $ubs['active_users_count'] }}</strong> / {{ $ubs['users_count'] }}
                            </div>
                            <div class="progress progress-modern" style="max-width: 150px;">
                                <div class="progress-bar bg-success" 
                                     style="width: {{ $ubs['users_count'] > 0 ? ($ubs['active_users_count'] / $ubs['users_count']) * 100 : 0 }}%">
                                </div>
                            </div>
                            <small class="text-muted">
                                {{ round($ubs['users_count'] > 0 ? ($ubs['active_users_count'] / $ubs['users_count']) * 100 : 0) }}% ativos
                            </small>
                        </td>
                        <td>
                            <div class="d-flex flex-wrap gap-1 mb-1">
                                <span class="badge bg-secondary badge-modern">
                                    {{ $ubs['total_tickets'] }} total
                                </span>
                                @if($ubs['open_tickets'] > 0)
                                    <span class="badge bg-warning text-dark badge-modern">
                                        {{ $ubs['open_tickets'] }} abertos
                                    </span>
                                @endif
                            </div>
                            @if($ubs['tickets_this_month'] > 0)
                                <small class="text-muted">
                                    <i class="bi bi-calendar-month"></i> {{ $ubs['tickets_this_month'] }} este mês
                                </small>
                            @endif
                        </td>
                        <td class="text-center">
                            @if($ubs['avg_resolution_hours'] > 0)
                                <span class="badge badge-modern {{ $ubs['avg_resolution_hours'] > 48 ? 'bg-danger' : ($ubs['avg_resolution_hours'] > 24 ? 'bg-warning text-dark' : 'bg-success') }}">
                                    <i class="bi bi-clock"></i> {{ $ubs['avg_resolution_hours'] }}h
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <span class="status-badge status-{{ $ubs['status'] }}">
                                @if($ubs['status'] == 'excellent') ⭐ Excelente
                                @elseif($ubs['status'] == 'good') ✓ Bom
                                @elseif($ubs['status'] == 'warning') ⚠ Atenção
                                @else ⛔ Crítico
                                @endif
                            </span>
                        </td>
                        <td class="text-center">
                            <div class="health-score {{ $ubs['health_score'] >= 80 ? 'text-success' : ($ubs['health_score'] >= 60 ? 'text-warning' : 'text-danger') }}">
                                {{ $ubs['health_score'] }}
                            </div>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('admin.ubs.show', $ubs['id']) }}" 
                               class="btn btn-sm btn-outline-primary"
                               title="Ver detalhes">
                                <i class="bi bi-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <p class="mb-0">Nenhuma UBS cadastrada no sistema</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
(function() {
    'use strict';
    
    console.log('🚀 Script de gráficos iniciado');
    
    // Aguarda o carregamento completo
    function initCharts() {
        console.log('🔄 Função initCharts executada');
        
        const chartsData = @json($charts);

        console.group('📊 Dashboard UBS - Debug Info');
        console.log('Charts Data:', chartsData);
        console.log('Tickets by UBS length:', chartsData?.tickets_by_ubs?.length || 0);
        console.log('Users by UBS length:', chartsData?.users_by_ubs?.length || 0);
        console.groupEnd();

        // Verifica se Chart.js foi carregado
        if (typeof Chart === 'undefined') {
            console.error('❌ Chart.js não foi carregado!');
            setTimeout(initCharts, 500);
            return;
        }
        
        console.log('✅ Chart.js versão:', Chart.version);
        Chart.defaults.font.family = "'Inter', 'system-ui', sans-serif";
        Chart.defaults.color = '#6c757d';

        // Gráfico de Chamados por UBS
        const ticketsCanvas = document.getElementById('ticketsByUbsChart');
        console.log('🎨 Canvas Tickets:', ticketsCanvas ? 'Encontrado' : 'NÃO encontrado');

        if (ticketsCanvas && chartsData?.tickets_by_ubs?.length > 0) {
            console.log('🔄 Criando gráfico de chamados...');
            
            const ticketsData = chartsData.tickets_by_ubs;
            const labels = ticketsData.map(item => {
                const name = item.ubs_name || 'Sem nome';
                return name.length > 25 ? name.substring(0, 25) + '...' : name;
            });
            const totalTickets = ticketsData.map(item => parseInt(item.tickets_count) || 0);
            const openTickets = ticketsData.map(item => parseInt(item.open_tickets) || 0);
            
            try {
                new Chart(ticketsCanvas.getContext('2d'), {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total de Chamados',
                            data: totalTickets,
                            backgroundColor: 'rgba(13, 110, 253, 0.85)',
                            borderColor: 'rgba(13, 110, 253, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                        }, {
                            label: 'Chamados Abertos',
                            data: openTickets,
                            backgroundColor: 'rgba(255, 193, 7, 0.85)',
                            borderColor: 'rgba(255, 193, 7, 1)',
                            borderWidth: 2,
                            borderRadius: 6,
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { padding: 15, font: { size: 12, weight: '600' }, usePointStyle: true }
                            }
                        },
                        scales: {
                            x: { grid: { display: false }, ticks: { font: { size: 11 }, maxRotation: 45, minRotation: 0 } },
                            y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' }, ticks: { font: { size: 11 }, precision: 0 } }
                        }
                    }
                });
                console.log('✅ Gráfico de chamados criado');
            } catch (error) {
                console.error('❌ Erro ao criar gráfico de chamados:', error);
            }
        }

        // Gráfico de Usuários por UBS
        const usersCanvas = document.getElementById('usersByUbsChart');
        console.log('🎨 Canvas Users:', usersCanvas ? 'Encontrado' : 'NÃO encontrado');

        if (usersCanvas && chartsData?.users_by_ubs?.length > 0) {
            console.log('🔄 Criando gráfico de usuários...');
            
            const usersData = chartsData.users_by_ubs;
            const labels = usersData.map(item => item.ubs_name || 'Sem nome');
            const totalUsers = usersData.map(item => parseInt(item.total_users) || 0);
            
            const colors = [];
            const baseColors = ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#0dcaf0', '#fd7e14', '#20c997', '#e83e8c', '#6610f2'];
            for (let i = 0; i < labels.length; i++) {
                colors.push(baseColors[i % baseColors.length]);
            }
            
            try {
                new Chart(usersCanvas.getContext('2d'), {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            data: totalUsers,
                            backgroundColor: colors,
                            borderWidth: 3,
                            borderColor: '#fff',
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'right',
                                labels: {
                                    padding: 12,
                                    font: { size: 11 },
                                    usePointStyle: true,
                                    generateLabels: function(chart) {
                                        const data = chart.data;
                                        if (!data.labels) return [];
                                        return data.labels.map((label, i) => {
                                            const name = label.length > 22 ? label.substring(0, 22) + '...' : label;
                                            return {
                                                text: `${name} (${data.datasets[0].data[i]})`,
                                                fillStyle: data.datasets[0].backgroundColor[i],
                                                hidden: false,
                                                index: i
                                            };
                                        });
                                    }
                                }
                            }
                        }
                    }
                });
                console.log('✅ Gráfico de usuários criado');
            } catch (error) {
                console.error('❌ Erro ao criar gráfico de usuários:', error);
            }
        }
        
        console.log('✅ Inicialização de gráficos concluída');
    }

    // Executa quando o DOM estiver pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        initCharts();
    }
})();

// Função para atualizar dados
function refreshData() {
    const btn = event.target.closest('button');
    if (!btn) return;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise fa-spin me-1"></i>Atualizando...';
    setTimeout(() => window.location.reload(), 600);
}

// Busca na tabela
(function() {
    const searchInput = document.getElementById('searchUbs');
    const table = document.getElementById('ubsTable');
    
    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const tbody = table.getElementsByTagName('tbody')[0];
            if (!tbody) return;
            
            const rows = tbody.getElementsByTagName('tr');
            let visibleCount = 0;
            
            Array.from(rows).forEach(row => {
                if (row.classList.contains('no-results')) return;
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            const emptyRow = tbody.querySelector('.no-results');
            if (emptyRow) emptyRow.remove();
            
            if (visibleCount === 0 && searchTerm !== '') {
                const noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results';
                noResultsRow.innerHTML = `<td colspan="7" class="empty-state"><i class="bi bi-search"></i><p class="mb-0">Nenhuma UBS encontrada</p></td>`;
                tbody.appendChild(noResultsRow);
            }
        });
    }
})();
</script>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Aguarda o carregamento completo do Chart.js
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 DOMContentLoaded - Iniciando Dashboard UBS');
    
    const chartsData = @json($charts);

    console.group('📊 Dashboard UBS - Debug Info');
    console.log('Charts Data:', chartsData);
    console.log('Tickets by UBS:', chartsData?.tickets_by_ubs);
    console.log('Users by UBS:', chartsData?.users_by_ubs);
    console.log('Tickets Count:', chartsData?.tickets_by_ubs?.length || 0);
    console.log('Users Count:', chartsData?.users_by_ubs?.length || 0);
    console.groupEnd();

    // Verifica se Chart.js foi carregado
    if (typeof Chart !== 'undefined') {
        Chart.defaults.font.family = "'Inter', 'system-ui', sans-serif";
        Chart.defaults.color = '#6c757d';
        console.log('✅ Chart.js versão:', Chart.version);
    } else {
        console.error('❌ Chart.js não foi carregado!');
        return;
    }

    // Gráfico de Chamados por UBS
    const ticketsCanvas = document.getElementById('ticketsByUbsChart');
    console.log('🎨 Canvas Tickets:', ticketsCanvas ? 'Encontrado' : 'NÃO encontrado');

    if (ticketsCanvas && chartsData?.tickets_by_ubs && Array.isArray(chartsData.tickets_by_ubs) && chartsData.tickets_by_ubs.length > 0) {
        console.log('🔄 Preparando gráfico de chamados com', chartsData.tickets_by_ubs.length, 'UBS');
        
        const ticketsData = chartsData.tickets_by_ubs;
        
        const labels = ticketsData.map(item => {
            const name = item.ubs_name || 'Sem nome';
            return name.length > 25 ? name.substring(0, 25) + '...' : name;
        });
        
        const totalTickets = ticketsData.map(item => parseInt(item.tickets_count) || 0);
        const openTickets = ticketsData.map(item => parseInt(item.open_tickets) || 0);
        
        console.log('📊 Dados do gráfico de chamados:');
        console.log('  - Labels:', labels.length, 'itens');
        console.log('  - Total Tickets:', totalTickets);
        console.log('  - Open Tickets:', openTickets);
        
        try {
            const ticketsChart = new Chart(ticketsCanvas.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total de Chamados',
                        data: totalTickets,
                        backgroundColor: 'rgba(13, 110, 253, 0.85)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                    }, {
                        label: 'Chamados Abertos',
                        data: openTickets,
                        backgroundColor: 'rgba(255, 193, 7, 0.85)',
                        borderColor: 'rgba(255, 193, 7, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top',
                            labels: {
                                padding: 15,
                                font: { size: 12, weight: '600' },
                                usePointStyle: true,
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.85)',
                            padding: 14,
                            borderWidth: 1,
                            borderColor: '#dee2e6',
                            titleFont: { size: 14, weight: 'bold' },
                            bodyFont: { size: 13 },
                            cornerRadius: 8,
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false },
                            ticks: { 
                                font: { size: 11 },
                                maxRotation: 45,
                                minRotation: 0
                            }
                        },
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(0,0,0,0.05)' },
                            ticks: { 
                                font: { size: 11 },
                                precision: 0
                            }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de chamados criado:', ticketsChart);
        } catch (error) {
            console.error('❌ Erro ao criar gráfico de chamados:', error);
        }
    } else {
        console.warn('⚠️ Gráfico de chamados NÃO criado. Motivos:', {
            'Canvas existe': !!ticketsCanvas,
            'Dados existem': !!chartsData?.tickets_by_ubs,
            'É array': Array.isArray(chartsData?.tickets_by_ubs),
            'Tem itens': chartsData?.tickets_by_ubs?.length > 0
        });
    }

    // Gráfico de Usuários por UBS
    const usersCanvas = document.getElementById('usersByUbsChart');
    console.log('🎨 Canvas Users:', usersCanvas ? 'Encontrado' : 'NÃO encontrado');

    if (usersCanvas && chartsData?.users_by_ubs && Array.isArray(chartsData.users_by_ubs) && chartsData.users_by_ubs.length > 0) {
        console.log('🔄 Preparando gráfico de usuários com', chartsData.users_by_ubs.length, 'UBS');
        
        const usersData = chartsData.users_by_ubs;
        
        const labels = usersData.map(item => item.ubs_name || 'Sem nome');
        const totalUsers = usersData.map(item => parseInt(item.total_users) || 0);
        
        console.log('📊 Dados do gráfico de usuários:');
        console.log('  - Labels:', labels.length, 'itens');
        console.log('  - Total Users:', totalUsers);
        
        try {
            const usersChart = new Chart(usersCanvas.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: totalUsers,
                        backgroundColor: [
                            '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
                            '#0dcaf0', '#fd7e14', '#20c997', '#e83e8c', '#6610f2',
                            '#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6c757d',
                            '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
                            // Repetindo cores para suportar mais UBS
                            '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
                            '#0dcaf0', '#fd7e14', '#20c997', '#e83e8c', '#6610f2',
                            '#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6c757d',
                            '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
                            '#0dcaf0', '#fd7e14', '#20c997', '#e83e8c', '#6610f2',
                            '#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6c757d',
                            '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1',
                            '#0dcaf0', '#fd7e14', '#20c997', '#e83e8c', '#6610f2',
                            '#17a2b8', '#28a745', '#ffc107', '#dc3545', '#6c757d',
                            '#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1'
                        ],
                        borderWidth: 3,
                        borderColor: '#fff',
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                padding: 12,
                                font: { size: 11 },
                                usePointStyle: true,
                                generateLabels: function(chart) {
                                    const data = chart.data;
                                    if (!data.labels || data.labels.length === 0) return [];
                                    
                                    return data.labels.map((label, i) => {
                                        const name = label.length > 22 ? label.substring(0, 22) + '...' : label;
                                        return {
                                            text: `${name} (${data.datasets[0].data[i]})`,
                                            fillStyle: data.datasets[0].backgroundColor[i],
                                            hidden: false,
                                            index: i
                                        };
                                    });
                                }
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0,0,0,0.85)',
                            padding: 14,
                            borderWidth: 1,
                            borderColor: '#dee2e6',
                            cornerRadius: 8,
                            titleFont: { size: 13, weight: 'bold' },
                            bodyFont: { size: 12 },
                            callbacks: {
                                label: function(context) {
                                    const label = context.label || '';
                                    const value = context.parsed || 0;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                    return ` ${label}: ${value} usuários (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });
            console.log('✅ Gráfico de usuários criado:', usersChart);
        } catch (error) {
            console.error('❌ Erro ao criar gráfico de usuários:', error);
        }
    } else {
        console.warn('⚠️ Gráfico de usuários NÃO criado. Motivos:', {
            'Canvas existe': !!usersCanvas,
            'Dados existem': !!chartsData?.users_by_ubs,
            'É array': Array.isArray(chartsData?.users_by_ubs),
            'Tem itens': chartsData?.users_by_ubs?.length > 0
        });
    }

    // Busca na tabela
    const searchInput = document.getElementById('searchUbs');
    const table = document.getElementById('ubsTable');

    if (searchInput && table) {
        searchInput.addEventListener('keyup', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const tbody = table.getElementsByTagName('tbody')[0];
            if (!tbody) return;
            
            const rows = tbody.getElementsByTagName('tr');
            let visibleCount = 0;
            
            Array.from(rows).forEach(row => {
                if (row.classList.contains('no-results')) return;
                
                const text = row.textContent.toLowerCase();
                if (text.includes(searchTerm)) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });
            
            const emptyRow = tbody.querySelector('.no-results');
            if (emptyRow) emptyRow.remove();
            
            if (visibleCount === 0 && searchTerm !== '') {
                const noResultsRow = document.createElement('tr');
                noResultsRow.className = 'no-results';
                noResultsRow.innerHTML = `
                    <td colspan="7" class="empty-state">
                        <i class="bi bi-search"></i>
                        <p class="mb-0">Nenhuma UBS encontrada para "${searchTerm}"</p>
                    </td>
                `;
                tbody.appendChild(noResultsRow);
            }
        });
    }

    console.log('✅ Dashboard UBS carregado completamente');
});

// Função para atualizar dados
function refreshData() {
    const btn = event.target.closest('button');
    if (!btn) return;
    
    const originalHTML = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-arrow-clockwise fa-spin me-1"></i>Atualizando...';
    
    setTimeout(() => {
        window.location.reload();
    }, 600);
}

// Auto-refresh a cada 5 minutos
let autoRefreshInterval = setInterval(() => {
    console.log('🔄 Auto-refresh: recarregando dashboard...');
    window.location.reload();
}, 300000);

window.addEventListener('beforeunload', () => {
    clearInterval(autoRefreshInterval);
});
</script>
@endsection