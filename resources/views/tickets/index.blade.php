@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-ticket-perforated"></i> Chamados
        @if(auth()->user()->role === 'customer')
            <small class="text-muted">Meus Chamados</small>
        @elseif(auth()->user()->role === 'technician')
            <small class="text-muted">Atribuídos a mim</small>
        @else
            <small class="text-muted">Todos os Chamados</small>
        @endif
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('tickets.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus"></i> Novo Chamado
            </a>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('tickets.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Status</label>
                <select name="status" id="status" class="form-select">
                    <option value="">Todos os Status</option>
                    <option value="open" {{ request('status') === 'open' ? 'selected' : '' }}>Aberto</option>
                    <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>Em Andamento</option>
                    <option value="waiting" {{ request('status') === 'waiting' ? 'selected' : '' }}>Aguardando</option>
                    <option value="resolved" {{ request('status') === 'resolved' ? 'selected' : '' }}>Resolvido</option>
                    <option value="closed" {{ request('status') === 'closed' ? 'selected' : '' }}>Fechado</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="priority" class="form-label">Prioridade</label>
                <select name="priority" id="priority" class="form-select">
                    <option value="">Todas as Prioridades</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Média</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="category_id" class="form-label">Categoria</label>
                <select name="category_id" id="category_id" class="form-select">
                    <option value="">Todas as Categorias</option>
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                        {{ $category->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="bi bi-search"></i> Filtrar
                </button>
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x-circle"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Chamados -->
<div class="card">
    <div class="card-body">
        @if($tickets->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="80">#</th>
                            <th>Título</th>
                            <th width="150">Categoria</th>
                            <th width="120">Status</th>
                            <th width="120">Prioridade</th>
                            @if(auth()->user()->role !== 'customer')
                            <th width="150">Solicitante</th>
                            @endif
                            @if(auth()->user()->role === 'admin')
                            <th width="150">Atribuído</th>
                            @endif
                            <th width="120">Criado em</th>
                            <th width="100">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="fw-bold text-decoration-none">
                                    #{{ $ticket->id }}
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">
                                    {{ Str::limit($ticket->title, 60) }}
                                </a>
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $ticket->category->color }}; color: white;">
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
                            @if(auth()->user()->role !== 'customer')
                            <td>
                                <small>{{ $ticket->user->name }}</small>
                            </td>
                            @endif
                            @if(auth()->user()->role === 'admin')
                            <td>
                                <small>
                                    @if($ticket->assignedUser)
                                        {{ $ticket->assignedUser->name }}
                                    @else
                                        <span class="text-muted">Não atribuído</span>
                                    @endif
                                </small>
                            </td>
                            @endif
                            <td>
                                <small>{{ $ticket->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-primary btn-sm" title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(in_array(auth()->user()->role, ['admin', 'technician']))
                                    <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-outline-secondary btn-sm" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $tickets->firstItem() ?? 0 }} a {{ $tickets->lastItem() ?? 0 }} 
                        de {{ $tickets->total() }} chamados
                    </small>
                </div>
                <div>
                    {{ $tickets->appends(request()->query())->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-ticket-perforated display-1 text-muted"></i>
                <h4 class="text-muted mt-3">Nenhum chamado encontrado</h4>
                <p class="text-muted">
                    @if(request()->hasAny(['status', 'priority', 'category_id']))
                        Tente ajustar os filtros ou 
                        <a href="{{ route('tickets.index') }}">limpar todos os filtros</a>.
                    @else
                        Que tal criar seu primeiro chamado?
                    @endif
                </p>
                @if(!request()->hasAny(['status', 'priority', 'category_id']))
                <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Criar Chamado
                </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
