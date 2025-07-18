@extends('layouts.app')

@section('title', 'Relatório de Tickets')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                        Relatório de Tickets
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Filtros -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <form method="GET" action="{{ route('reports.tickets') }}">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label">Período</label>
                                        <select name="date_range" class="form-select">
                                            <option value="last7" {{ $dateRange == 'last7' ? 'selected' : '' }}>Últimos 7 dias</option>
                                            <option value="last30" {{ $dateRange == 'last30' ? 'selected' : '' }}>Últimos 30 dias</option>
                                            <option value="last90" {{ $dateRange == 'last90' ? 'selected' : '' }}>Últimos 90 dias</option>
                                            <option value="year" {{ $dateRange == 'year' ? 'selected' : '' }}>Este ano</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Status</label>
                                        <select name="status" class="form-select">
                                            <option value="">Todos os status</option>
                                            <option value="open">Aberto</option>
                                            <option value="in_progress">Em Andamento</option>
                                            <option value="waiting">Aguardando</option>
                                            <option value="resolved">Resolvido</option>
                                            <option value="closed">Fechado</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">Prioridade</label>
                                        <select name="priority" class="form-select">
                                            <option value="">Todas as prioridades</option>
                                            <option value="low">Baixa</option>
                                            <option value="medium">Média</option>
                                            <option value="high">Alta</option>
                                            <option value="urgent">Urgente</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label">&nbsp;</label>
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-search"></i> Filtrar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Estatísticas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $ticketsCount }}</h4>
                                            <small>Total de Tickets</small>
                                        </div>
                                        <div>
                                            <i class="fas fa-ticket-alt fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $statusStats['resolved'] ?? 0 }}</h4>
                                            <small>Resolvidos</small>
                                        </div>
                                        <div>
                                            <i class="fas fa-check-circle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ $statusStats['in_progress'] ?? 0 }}</h4>
                                            <small>Em Andamento</small>
                                        </div>
                                        <div>
                                            <i class="fas fa-clock fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-danger text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <h4 class="mb-0">{{ number_format($slaComplianceRate, 1) }}%</h4>
                                            <small>Conformidade SLA</small>
                                        </div>
                                        <div>
                                            <i class="fas fa-chart-line fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Tickets por Status</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Tickets por Prioridade</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="priorityChart" width="400" height="200"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Tickets -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Lista de Tickets</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Título</th>
                                                    <th>Status</th>
                                                    <th>Prioridade</th>
                                                    <th>Solicitante</th>
                                                    <th>Responsável</th>
                                                    <th>Categoria</th>
                                                    <th>Criado em</th>
                                                    <th>Ações</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($tickets as $ticket)
                                                    <tr>
                                                        <td>#{{ $ticket->id }}</td>
                                                        <td>
                                                            <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">
                                                                {{ Str::limit($ticket->title, 50) }}
                                                            </a>
                                                        </td>
                                                        <td>
                                                            <span class="badge {{ $ticket->status_color }}">
                                                                {{ $ticket->status_label }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="{{ $ticket->priority_color }}">
                                                                <i class="fas fa-circle fa-sm"></i>
                                                                {{ $ticket->priority_label }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                                                        <td>{{ $ticket->assignedTo->name ?? 'Não atribuído' }}</td>
                                                        <td>{{ $ticket->category->name ?? 'N/A' }}</td>
                                                        <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="9" class="text-center">Nenhum ticket encontrado.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>

                                    <!-- Paginação -->
                                    <div class="d-flex justify-content-center">
                                        {{ $tickets->links() }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gráfico de Status
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Aberto', 'Em Andamento', 'Aguardando', 'Resolvido', 'Fechado'],
            datasets: [{
                data: [
                    {{ $statusStats['open'] ?? 0 }},
                    {{ $statusStats['in_progress'] ?? 0 }},
                    {{ $statusStats['waiting'] ?? 0 }},
                    {{ $statusStats['resolved'] ?? 0 }},
                    {{ $statusStats['closed'] ?? 0 }}
                ],
                backgroundColor: [
                    '#17a2b8',
                    '#ffc107',
                    '#6c757d',
                    '#28a745',
                    '#343a40'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de Prioridade
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: ['Baixa', 'Média', 'Alta', 'Urgente'],
            datasets: [{
                label: 'Tickets',
                data: [
                    {{ $priorityStats['low'] ?? 0 }},
                    {{ $priorityStats['medium'] ?? 0 }},
                    {{ $priorityStats['high'] ?? 0 }},
                    {{ $priorityStats['urgent'] ?? 0 }}
                ],
                backgroundColor: [
                    '#28a745',
                    '#17a2b8',
                    '#ffc107',
                    '#dc3545'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
});
</script>
@endsection
