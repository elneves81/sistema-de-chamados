@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="/painel-tv" target="_blank" class="btn btn-sm btn-primary">
                <i class="bi bi-tv"></i> Painel TV
            </a>
            <a href="{{ route('admin.ldap.import.form') }}" class="btn btn-sm btn-success">
                <i class="bi bi-people"></i> Importar LDAP
            </a>
            <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus"></i> Novo Chamado
            </a>
        </div>
    </div>
</div>

<!-- KPIs Row -->
<div class="row mb-4">
    @if(auth()->user()->role === 'customer')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Chamados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-ticket-perforated fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Chamados Abertos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $openTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolvidos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $resolvedTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tempo Médio de Resposta</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgResponseTime ?? 0 }}h</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @elseif(auth()->user()->role === 'technician')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Chamados Atribuídos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $assignedTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-person-check fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Em Andamento</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $openTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-gear fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Resolvidos Este Mês</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $resolvedThisMonth ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-trophy fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                Tempo Médio de Resolução</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $avgResolutionTime ?? 0 }}h</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-stopwatch fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @else {{-- admin --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total de Chamados</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-ticket-perforated fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Chamados Abertos</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $openTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Total de Usuários</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $totalUsers ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-people fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Chamados Urgentes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $urgentTickets ?? 0 }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="bi bi-exclamation-octagon fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Charts Row -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Status dos Chamados</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Chamados - Últimos 7 Dias</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="weeklyChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@if(auth()->user()->role === 'admin')
<!-- Priority Chart (only for admin) -->
<div class="row mb-4">
    <div class="col-lg-6">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Chamados por Prioridade</h6>
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="priorityChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Recent Tickets -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Chamados Recentes</h6>
    </div>
    <div class="card-body">
        @if($recentTickets && $recentTickets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Título</th>
                            <th>Categoria</th>
                            <th>Status</th>
                            <th>Prioridade</th>
                            <th>Criado em</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTickets as $ticket)
                        <tr>
                            <td><a href="{{ route('tickets.show', $ticket) }}">#{{ $ticket->id }}</a></td>
                            <td>{{ Str::limit($ticket->title, 50) }}</td>
                            <td>
                                <span class="badge" style="background-color: {{ $ticket->category->color }};">
                                    {{ $ticket->category->name }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge status-{{ $ticket->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                </span>
                            </td>
                            <td>
                                <span class="priority-badge priority-{{ $ticket->priority }}">
                                    {{ ucfirst($ticket->priority) }}
                                </span>
                            </td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-muted">Nenhum chamado encontrado.</p>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status Chart
    @if(isset($chartData['statusChart']))
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($chartData['statusChart']['labels'] ?? []) !!},
            datasets: [{
                data: {!! json_encode($chartData['statusChart']['data'] ?? []) !!},
                backgroundColor: [
                    '#36A2EB',
                    '#FFCE56',
                    '#FF6384',
                    '#4BC0C0',
                    '#9966FF'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
    @endif

    // Weekly Chart
    @if(isset($chartData['weeklyChart']))
    const weeklyCtx = document.getElementById('weeklyChart').getContext('2d');
    new Chart(weeklyCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($chartData['weeklyChart']['labels'] ?? []) !!},
            datasets: [{
                label: 'Chamados',
                data: {!! json_encode($chartData['weeklyChart']['data'] ?? []) !!},
                borderColor: '#36A2EB',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.4
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
    @endif

    // Priority Chart (only for admin)
    @if(auth()->user()->role === 'admin' && isset($chartData['priorityChart']))
    const priorityCtx = document.getElementById('priorityChart').getContext('2d');
    new Chart(priorityCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($chartData['priorityChart']['labels'] ?? []) !!},
            datasets: [{
                label: 'Chamados',
                data: {!! json_encode($chartData['priorityChart']['data'] ?? []) !!},
                backgroundColor: [
                    '#4BC0C0', // Low
                    '#FFCE56', // Medium  
                    '#FF6384', // High
                    '#FF4444'  // Urgent
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
            }
        }
    });
    @endif
});
</script>
@endpush
