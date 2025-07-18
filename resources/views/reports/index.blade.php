@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0 text-gray-800">Relatórios</h1>
                <div class="d-flex align-items-center">
                    <span class="badge badge-primary mr-2">Sistema Online</span>
                    <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Cards de Relatórios -->
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Relatório de Chamados
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Análise completa de tickets
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('reports.tickets') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-eye mr-1"></i>
                            Visualizar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Relatório de Ativos
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                Inventário e status
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-desktop fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('reports.assets') }}" class="btn btn-success btn-sm">
                            <i class="fas fa-eye mr-1"></i>
                            Visualizar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Relatório de Performance
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                SLA e métricas
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('reports.performance') }}" class="btn btn-info btn-sm">
                            <i class="fas fa-eye mr-1"></i>
                            Visualizar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo Executivo -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Resumo Executivo</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <h2>{{ \App\Models\Ticket::count() }}</h2>
                                    <p class="mb-0">Total de Chamados</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <h2>{{ \App\Models\Asset::count() }}</h2>
                                    <p class="mb-0">Total de Ativos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <h2>{{ \App\Models\User::count() }}</h2>
                                    <p class="mb-0">Total de Usuários</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body text-center">
                                    <h2>{{ \App\Models\Category::count() }}</h2>
                                    <p class="mb-0">Total de Categorias</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos Resumo -->
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Chamados por Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Ativos por Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="assetsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Status dos Chamados
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: ['Aberto', 'Em Andamento', 'Fechado', 'Pendente'],
        datasets: [{
            data: [
                {{ \App\Models\Ticket::where('status', 'open')->count() }},
                {{ \App\Models\Ticket::where('status', 'in_progress')->count() }},
                {{ \App\Models\Ticket::where('status', 'closed')->count() }},
                {{ \App\Models\Ticket::where('status', 'pending')->count() }}
            ],
            backgroundColor: [
                '#f6c23e',
                '#36b9cc',
                '#1cc88a',
                '#e74a3b'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom',
            }
        }
    }
});

// Gráfico de Status dos Ativos
const assetsCtx = document.getElementById('assetsChart').getContext('2d');
new Chart(assetsCtx, {
    type: 'bar',
    data: {
        labels: ['Em Uso', 'Em Manutenção', 'Em Estoque', 'Defeituoso'],
        datasets: [{
            label: 'Quantidade',
            data: [
                {{ \App\Models\Asset::whereHas('status', function($q) { $q->where('name', 'Em Uso'); })->count() }},
                {{ \App\Models\Asset::whereHas('status', function($q) { $q->where('name', 'Em Manutenção'); })->count() }},
                {{ \App\Models\Asset::whereHas('status', function($q) { $q->where('name', 'Em Estoque'); })->count() }},
                {{ \App\Models\Asset::whereHas('status', function($q) { $q->where('name', 'Defeituoso'); })->count() }}
            ],
            backgroundColor: [
                '#1cc88a',
                '#f6c23e',
                '#36b9cc',
                '#e74a3b'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});
</script>
@endpush
@endsection
