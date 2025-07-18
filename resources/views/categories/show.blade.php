@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <span class="badge me-2" style="background-color: {{ $category->color }}; color: white;">
            <i class="bi bi-tag"></i>
        </span>
        {{ $category->name }}
        @if(!$category->active)
            <span class="badge bg-secondary ms-2">Inativa</span>
        @endif
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('categories.edit', $category) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Informações da Categoria -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações da Categoria</h5>
            </div>
            <div class="card-body">
                @if($category->description)
                <div class="mb-4">
                    <h6 class="text-muted">Descrição:</h6>
                    <div class="bg-light p-3 rounded">
                        {{ $category->description }}
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-4">
                        <h6 class="text-muted">Slug:</h6>
                        <code>{{ $category->slug }}</code>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">SLA:</h6>
                        <span class="badge bg-secondary">{{ $category->sla_hours }} horas</span>
                    </div>
                    <div class="col-md-4">
                        <h6 class="text-muted">Status:</h6>
                        @if($category->active)
                            <span class="badge bg-success">Ativa</span>
                        @else
                            <span class="badge bg-secondary">Inativa</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas dos Chamados -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-bar-chart"></i> Estatísticas dos Chamados
                </h5>
            </div>
            <div class="card-body">
                @if($category->tickets && $category->tickets->count() > 0)
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary">{{ $category->tickets->count() }}</h4>
                                <small class="text-muted">Total</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning">{{ $category->tickets->whereIn('status', ['open', 'in_progress'])->count() }}</h4>
                                <small class="text-muted">Abertos</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">{{ $category->tickets->where('status', 'resolved')->count() }}</h4>
                                <small class="text-muted">Resolvidos</small>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-secondary">{{ $category->tickets->where('status', 'closed')->count() }}</h4>
                                <small class="text-muted">Fechados</small>
                            </div>
                        </div>
                    </div>

                    <!-- Distribuição por Prioridade -->
                    <h6 class="mb-3">Distribuição por Prioridade:</h6>
                    <div class="row mb-4">
                        @php
                            $priorities = $category->tickets->groupBy('priority');
                        @endphp
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="priority-badge priority-low me-2">Baixa</span>
                                <span>{{ $priorities->get('low', collect())->count() }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="priority-badge priority-medium me-2">Média</span>
                                <span>{{ $priorities->get('medium', collect())->count() }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="priority-badge priority-high me-2">Alta</span>
                                <span>{{ $priorities->get('high', collect())->count() }}</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="d-flex align-items-center">
                                <span class="priority-badge priority-urgent me-2">Urgente</span>
                                <span>{{ $priorities->get('urgent', collect())->count() }}</span>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-ticket-perforated display-4 text-muted"></i>
                        <h6 class="text-muted mt-2">Nenhum chamado nesta categoria ainda</h6>
                    </div>
                @endif
            </div>
        </div>

        <!-- Chamados Recentes -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-ticket-perforated"></i> Chamados Recentes
                </h5>
                @if($category->tickets && $category->tickets->count() > 10)
                <a href="{{ route('tickets.index', ['category_id' => $category->id]) }}" class="btn btn-sm btn-outline-primary">
                    Ver Todos
                </a>
                @endif
            </div>
            <div class="card-body">
                @if($category->tickets && $category->tickets->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="80">#</th>
                                    <th>Título</th>
                                    <th width="120">Status</th>
                                    <th width="120">Prioridade</th>
                                    <th width="150">Solicitante</th>
                                    <th width="120">Criado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($category->tickets as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}" class="fw-bold text-decoration-none">
                                            #{{ $ticket->id }}
                                        </a>
                                    </td>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">
                                            {{ Str::limit($ticket->title, 50) }}
                                        </a>
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
                                    <td>
                                        <small>{{ $ticket->user->name }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $ticket->created_at->format('d/m/Y') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-ticket-perforated display-6 text-muted"></i>
                        <h6 class="text-muted mt-2">Nenhum chamado encontrado</h6>
                        <p class="text-muted">Esta categoria ainda não possui chamados associados.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Informações Técnicas -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> Informações Técnicas
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>ID:</strong></div>
                    <div class="col-sm-7">{{ $category->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Slug:</strong></div>
                    <div class="col-sm-7"><code>{{ $category->slug }}</code></div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Cor:</strong></div>
                    <div class="col-sm-7">
                        <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                            {{ $category->color }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>SLA:</strong></div>
                    <div class="col-sm-7">{{ $category->sla_hours }} horas</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Status:</strong></div>
                    <div class="col-sm-7">
                        @if($category->active)
                            <span class="badge bg-success">Ativa</span>
                        @else
                            <span class="badge bg-secondary">Inativa</span>
                        @endif
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Criada em:</strong></div>
                    <div class="col-sm-7">{{ $category->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="row mb-0">
                    <div class="col-sm-5"><strong>Atualizada:</strong></div>
                    <div class="col-sm-7">{{ $category->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Ações Rápidas -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightning"></i> Ações Rápidas
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('tickets.create', ['category_id' => $category->id]) }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus"></i> Criar Chamado nesta Categoria
                    </a>
                    <a href="{{ route('tickets.index', ['category_id' => $category->id]) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-list"></i> Ver Todos os Chamados
                    </a>
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-pencil"></i> Editar Categoria
                    </a>
                </div>
            </div>
        </div>

        <!-- Alerta de SLA -->
        @if($category->tickets && $category->tickets->count() > 0)
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clock"></i> Status do SLA
                </h6>
            </div>
            <div class="card-body">
                @php
                    $openTickets = $category->tickets->whereIn('status', ['open', 'in_progress']);
                    $overdueTickets = $openTickets->filter(function($ticket) use ($category) {
                        return $ticket->created_at->addHours($category->sla_hours)->isPast();
                    });
                @endphp
                
                @if($overdueTickets->count() > 0)
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>{{ $overdueTickets->count() }} chamado(s) vencido(s)</strong>
                </div>
                @endif

                <div class="mb-2">
                    <small class="text-muted">Chamados abertos:</small>
                    <div class="progress">
                        @php
                            $total = $openTickets->count();
                            $overdue = $overdueTickets->count();
                            $onTime = $total - $overdue;
                            $onTimePercent = $total > 0 ? ($onTime / $total) * 100 : 100;
                            $overduePercent = $total > 0 ? ($overdue / $total) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $onTimePercent }}%"></div>
                        <div class="progress-bar bg-danger" style="width: {{ $overduePercent }}%"></div>
                    </div>
                    <small class="text-muted">
                        {{ $onTime }} no prazo, {{ $overdue }} vencidos
                    </small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
