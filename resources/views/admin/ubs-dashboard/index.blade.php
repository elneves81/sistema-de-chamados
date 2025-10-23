@extends('layouts.app')

@section('styles')
<style>
.dashboard-card {
    background: #fff;
    border-radius: 1rem;
    box-shadow: 0 2px 20px rgba(0,0,0,0.08);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    border: 1px solid #f0f2f5;
    transition: all 0.3s ease;
}

.dashboard-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 25px rgba(0,0,0,0.12);
}

.stat-card {
    text-align: center;
    padding: 2rem 1rem;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    color: #1976f2;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #6c757d;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.ubs-card {
    border-left: 4px solid #1976f2;
    transition: all 0.3s ease;
}

.ubs-card:hover {
    border-left-color: #0d47a1;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 50px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-excellent {
    background: #d4edda;
    color: #155724;
}

.status-good {
    background: #d1ecf1;
    color: #0c5460;
}

.status-warning {
    background: #fff3cd;
    color: #856404;
}

.status-critical {
    background: #f8d7da;
    color: #721c24;
}

.health-score {
    font-size: 1.2rem;
    font-weight: 700;
}

.chart-container {
    position: relative;
    height: 300px;
    margin: 1rem 0;
}

.sync-info {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 1rem;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.btn-refresh {
    background: linear-gradient(45deg, #1976f2, #42a5f5);
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
}

.btn-refresh:hover {
    background: linear-gradient(45deg, #1565c0, #1976f2);
    color: white;
}

.table-responsive {
    border-radius: 0.5rem;
    overflow: hidden;
}

.table th {
    background: #f8f9fa;
    border: none;
    font-weight: 600;
    color: #495057;
}

.progress-thin {
    height: 6px;
    border-radius: 3px;
    background: #f0f2f5;
    margin: 0.5rem 0;
}

.progress-thin .progress-bar {
    border-radius: 3px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h1 class="h3 mb-2">
                <i class="fas fa-hospital-user text-primary me-2"></i>
                Dashboard de Gestão - UBS Guarapuava
            </h1>
            <p class="text-muted">Visão geral das Unidades Básicas de Saúde e sistema de chamados</p>
        </div>
        <div class="col-md-4 text-end">
            <button class="btn btn-refresh me-2" onclick="refreshData()">
                <i class="fas fa-sync-alt me-1"></i>Atualizar Dados
            </button>
            <a href="{{ route('admin.ldap.import.form') }}" class="btn btn-outline-primary">
                <i class="fas fa-users-cog me-1"></i>Gerenciar LDAP
            </a>
        </div>
    </div>

    <!-- Informações de Sincronização -->
    @if($stats['ldap_sync_last'])
    <div class="sync-info">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h5 class="mb-1"><i class="fas fa-sync me-2"></i>Última Sincronização LDAP</h5>
                <p class="mb-0">{{ \Carbon\Carbon::parse($stats['ldap_sync_last'])->format('d/m/Y H:i:s') }} 
                    ({{ \Carbon\Carbon::parse($stats['ldap_sync_last'])->diffForHumans() }})</p>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-light" onclick="runLdapSync()">
                    <i class="fas fa-play me-1"></i>Executar Sync Agora
                </button>
            </div>
        </div>
    </div>
    @endif

    <!-- Estatísticas Gerais -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="dashboard-card stat-card">
                <div class="stat-number">{{ $stats['total_ubs'] }}</div>
                <div class="stat-label">UBS Cadastradas</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="dashboard-card stat-card">
                <div class="stat-number">{{ $stats['total_users'] }}</div>
                <div class="stat-label">Usuários Total</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="dashboard-card stat-card">
                <div class="stat-number">{{ $stats['users_with_ubs'] }}</div>
                <div class="stat-label">Usuários c/ UBS</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="dashboard-card stat-card">
                <div class="stat-number">{{ $stats['total_tickets'] }}</div>
                <div class="stat-label">Total Chamados</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="dashboard-card stat-card">
                <div class="stat-number text-warning">{{ $stats['tickets_open'] }}</div>
                <div class="stat-label">Chamados Abertos</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="dashboard-card stat-card">
                <div class="stat-number text-success">{{ $stats['tickets_today'] }}</div>
                <div class="stat-label">Hoje</div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-chart-bar text-primary me-2"></i>Chamados por UBS</h5>
                <div class="chart-container">
                    <canvas id="ticketsByUbsChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="dashboard-card">
                <h5 class="mb-3"><i class="fas fa-users text-primary me-2"></i>Usuários por UBS</h5>
                <div class="chart-container">
                    <canvas id="usersByUbsChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de UBS -->
    <div class="dashboard-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-hospital text-primary me-2"></i>Unidades Básicas de Saúde</h5>
            <div>
                <input type="text" class="form-control form-control-sm" id="searchUbs" 
                       placeholder="Buscar UBS..." style="width: 200px;">
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover" id="ubsTable">
                <thead>
                    <tr>
                        <th>UBS</th>
                        <th>Usuários</th>
                        <th>Chamados</th>
                        <th>Tempo Médio</th>
                        <th>Status</th>
                        <th>Score</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ubsData as $ubs)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ $ubs['name'] }}</strong>
                                @if($ubs['address'])
                                    <br><small class="text-muted">{{ Str::limit($ubs['address'], 40) }}</small>
                                @endif
                                @if($ubs['phone'])
                                    <br><small class="text-info"><i class="fas fa-phone fa-xs"></i> {{ $ubs['phone'] }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <span class="me-2">{{ $ubs['active_users_count'] }}/{{ $ubs['users_count'] }}</span>
                                <div class="progress-thin flex-grow-1">
                                    <div class="progress-bar bg-primary" style="width: {{ $ubs['users_count'] > 0 ? ($ubs['active_users_count'] / $ubs['users_count']) * 100 : 0 }}%"></div>
                                </div>
                            </div>
                            <small class="text-muted">{{ round($ubs['users_count'] > 0 ? ($ubs['active_users_count'] / $ubs['users_count']) * 100 : 0) }}% ativos</small>
                        </td>
                        <td>
                            <div>
                                <span class="badge bg-secondary">{{ $ubs['total_tickets'] }} total</span>
                                @if($ubs['open_tickets'] > 0)
                                    <span class="badge bg-warning text-dark">{{ $ubs['open_tickets'] }} abertos</span>
                                @endif
                                @if($ubs['tickets_this_month'] > 0)
                                    <br><small class="text-muted">{{ $ubs['tickets_this_month'] }} este mês</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            @if($ubs['avg_resolution_hours'] > 0)
                                <span class="badge {{ $ubs['avg_resolution_hours'] > 48 ? 'bg-danger' : ($ubs['avg_resolution_hours'] > 24 ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ $ubs['avg_resolution_hours'] }}h
                                </span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="status-badge status-{{ $ubs['status'] }}">
                                {{ ucfirst($ubs['status']) }}
                            </span>
                        </td>
                        <td>
                            <div class="health-score {{ $ubs['health_score'] >= 80 ? 'text-success' : ($ubs['health_score'] >= 60 ? 'text-warning' : 'text-danger') }}">
                                {{ $ubs['health_score'] }}%
                            </div>
                        </td>
                        <td>
                            <a href="{{ route('admin.ubs.show', $ubs['id']) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye"></i> Detalhes
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Dados dos gráficos
const chartsData = @json($charts);

// Gráfico de Chamados por UBS
const ticketsCtx = document.getElementById('ticketsByUbsChart').getContext('2d');
new Chart(ticketsCtx, {
    type: 'bar',
    data: {
        labels: chartsData.tickets_by_ubs.map(item => item.ubs_name),
        datasets: [{
            label: 'Total',
            data: chartsData.tickets_by_ubs.map(item => item.tickets_count),
            backgroundColor: 'rgba(25, 118, 242, 0.8)',
            borderColor: 'rgba(25, 118, 242, 1)',
            borderWidth: 1
        }, {
            label: 'Abertos',
            data: chartsData.tickets_by_ubs.map(item => item.open_tickets),
            backgroundColor: 'rgba(255, 193, 7, 0.8)',
            borderColor: 'rgba(255, 193, 7, 1)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de Usuários por UBS
const usersCtx = document.getElementById('usersByUbsChart').getContext('2d');
new Chart(usersCtx, {
    type: 'doughnut',
    data: {
        labels: chartsData.users_by_ubs.map(item => item.ubs_name),
        datasets: [{
            data: chartsData.users_by_ubs.map(item => item.total_users),
            backgroundColor: [
                '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF',
                '#FF9F40', '#FF6384', '#C9CBCF', '#4BC0C0', '#FF6384',
                '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'right'
            }
        }
    }
});

// Busca em tempo real na tabela
document.getElementById('searchUbs').addEventListener('keyup', function() {
    const searchTerm = this.value.toLowerCase();
    const table = document.getElementById('ubsTable');
    const rows = table.getElementsByTagName('tr');
    
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const ubsName = row.cells[0].textContent.toLowerCase();
        
        if (ubsName.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    }
});

// Função para atualizar dados
function refreshData() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Atualizando...';
    btn.disabled = true;
    
    // Recarregar a página após um delay
    setTimeout(() => {
        window.location.reload();
    }, 1000);
}

// Função para executar sync LDAP
function runLdapSync() {
    if (!confirm('Deseja executar a sincronização LDAP agora? Isso pode demorar alguns minutos.')) {
        return;
    }
    
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Executando...';
    btn.disabled = true;
    
    // Implementar chamada AJAX para executar sync
    fetch('{{ route("admin.ldap.sync") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Sincronização executada com sucesso!');
            window.location.reload();
        } else {
            alert('Erro na sincronização: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erro ao executar sincronização.');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}
</script>
@endsection
