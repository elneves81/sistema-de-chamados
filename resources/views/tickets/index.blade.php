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
    <div class="card-header">
        <h6 class="mb-0">
            <i class="bi bi-funnel"></i> Filtros Avançados
            <small class="text-muted">({{ $tickets->total() }} {{ $tickets->total() === 1 ? 'chamado encontrado' : 'chamados encontrados' }})</small>
        </h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('tickets.index') }}" class="row g-3">
            <!-- Busca Textual -->
            <div class="col-12 col-lg-6">
                <label for="search" class="form-label">Buscar</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-control" 
                       placeholder="Título, descrição, ID, usuário, categoria ou tag..."
                       value="{{ request('search') }}">
            </div>
            
            <!-- Status -->
            <div class="col-12 col-md-6 col-lg-3">
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
            
            <!-- Prioridade -->
            <div class="col-12 col-md-6 col-lg-3">
                <label for="priority" class="form-label">Prioridade</label>
                <select name="priority" id="priority" class="form-select">
                    <option value="">Todas as Prioridades</option>
                    <option value="low" {{ request('priority') === 'low' ? 'selected' : '' }}>Baixa</option>
                    <option value="medium" {{ request('priority') === 'medium' ? 'selected' : '' }}>Média</option>
                    <option value="high" {{ request('priority') === 'high' ? 'selected' : '' }}>Alta</option>
                    <option value="urgent" {{ request('priority') === 'urgent' ? 'selected' : '' }}>Urgente</option>
                </select>
            </div>
            
            <!-- Categoria -->
            <div class="col-12 col-md-6 col-xl-4">
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
            
            <!-- Tags -->
            <div class="col-md-4">
                <label for="tag_id" class="form-label">Tag</label>
                <select name="tag_id" id="tag_id" class="form-select">
                    <option value="">Todas as Tags</option>
                    @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" {{ request('tag_id') == $tag->id ? 'selected' : '' }}>
                        {{ $tag->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            
            <!-- Período -->
            <div class="col-md-4">
                <label for="date_range" class="form-label">Período</label>
                <select name="date_range" id="date_range" class="form-select">
                    <option value="">Qualquer período</option>
                    <option value="today" {{ request('date_range') === 'today' ? 'selected' : '' }}>Hoje</option>
                    <option value="week" {{ request('date_range') === 'week' ? 'selected' : '' }}>Esta semana</option>
                    <option value="month" {{ request('date_range') === 'month' ? 'selected' : '' }}>Este mês</option>
                    <option value="overdue" {{ request('date_range') === 'overdue' ? 'selected' : '' }}>Atrasados</option>
                </select>
            </div>
            
            <!-- Botões -->
            <div class="col-12 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                <div class="btn-group">
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
                
                <!-- Estatísticas Rápidas -->
                <div class="d-flex gap-3">
                    <small class="text-muted">
                        <i class="bi bi-ticket-perforated text-primary"></i> Total: <strong>{{ $stats['total'] ?? 0 }}</strong>
                    </small>
                    <small class="text-muted">
                        <i class="bi bi-hourglass-split text-warning"></i> Abertos: <strong>{{ $stats['open'] ?? 0 }}</strong>
                    </small>
                    <small class="text-muted">
                        <i class="bi bi-exclamation-triangle text-danger"></i> Atrasados: <strong>{{ $stats['overdue'] ?? 0 }}</strong>
                    </small>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Chamados -->
<div class="card">
    <div class="card-body">
        @if($tickets->count() > 0)
            <!-- Barra de Ações em Lote -->
            @if(auth()->user()->role !== 'customer')
            <div class="mb-3 p-3 bg-light rounded" id="bulk-actions-bar" style="display: none;">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="fw-bold">
                            <span id="selected-count">0</span> chamado(s) selecionado(s)
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <select id="bulk-action" class="form-select form-select-sm">
                                    <option value="">Selecionar ação...</option>
                                    <option value="change_status">Alterar Status</option>
                                    <option value="change_priority">Alterar Prioridade</option>
                                    <option value="assign">Atribuir Técnico</option>
                                    <option value="close">Fechar</option>
                                    <option value="resolve">Resolver</option>
                                    <option value="reopen">Reabrir</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <select id="bulk-value" class="form-select form-select-sm" style="display: none;">
                                    <!-- Preenchido dinamicamente via JavaScript -->
                                </select>
                            </div>
                            <div class="col-md-4">
                                <button type="button" id="execute-bulk-action" class="btn btn-primary btn-sm">
                                    <i class="bi bi-play-fill"></i> Executar
                                </button>
                                <button type="button" id="clear-selection" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-x"></i> Limpar
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <input type="text" id="bulk-comment" class="form-control form-control-sm" placeholder="Comentário opcional..." maxlength="1000">
                    </div>
                </div>
            </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            @if(auth()->user()->role !== 'customer')
                            <th width="50">
                                <input type="checkbox" id="select-all" class="form-check-input">
                            </th>
                            @endif
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
                            <th width="200">Equipe de Suporte</th>
                            @endif
                            <th width="120">Localização</th>
                            <th width="120">Criado em</th>
                            <th width="100">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                        <tr>
                            @if(auth()->user()->role !== 'customer')
                            <td>
                                <input type="checkbox" class="form-check-input ticket-checkbox" 
                                       value="{{ $ticket->id }}" data-ticket-id="{{ $ticket->id }}">
                            </td>
                            @endif
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
                            <td>
                                <small>
                                    @if($ticket->supportTechnicians && $ticket->supportTechnicians->count() > 0)
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($ticket->supportTechnicians as $supportTech)
                                                <span class="badge bg-info text-white" style="font-size: 0.75rem;" title="{{ $supportTech->name }}">
                                                    <i class="bi bi-person-plus"></i> {{ Str::limit($supportTech->name, 15) }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </small>
                            </td>
                            @endif
                            <td>
                                <small>
                                    @if($ticket->location)
                                        <i class="bi bi-geo-alt"></i> {{ Str::limit($ticket->location->name, 15) }}
                                        @if($ticket->local) 
                                            <br><span class="text-muted">{{ Str::limit($ticket->local, 20) }}</span>
                                        @endif
                                    @elseif($ticket->local)
                                        <span class="text-muted">{{ Str::limit($ticket->local, 20) }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </small>
                            </td>
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
            <!-- Pagination Wrapper -->
            <div class="pagination-wrapper">
                <div class="pagination-info">
                    <small class="text-muted">
                        Mostrando <span class="fw-semibold">{{ $tickets->firstItem() ?? 0 }}</span> a 
                        <span class="fw-semibold">{{ $tickets->lastItem() ?? 0 }}</span> 
                        de <span class="fw-semibold">{{ $tickets->total() }}</span> chamados
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

@if(auth()->user()->role !== 'customer')
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('select-all');
    const ticketCheckboxes = document.querySelectorAll('.ticket-checkbox');
    const bulkActionsBar = document.getElementById('bulk-actions-bar');
    const selectedCountSpan = document.getElementById('selected-count');
    const bulkActionSelect = document.getElementById('bulk-action');
    const bulkValueSelect = document.getElementById('bulk-value');
    const executeBulkActionBtn = document.getElementById('execute-bulk-action');
    const clearSelectionBtn = document.getElementById('clear-selection');

    // Configurar opções para diferentes ações
    const actionOptions = {
        'change_status': [
            { value: 'open', text: 'Aberto' },
            { value: 'in_progress', text: 'Em Progresso' },
            { value: 'waiting_customer', text: 'Aguardando Cliente' },
            { value: 'resolved', text: 'Resolvido' },
            { value: 'closed', text: 'Fechado' },
            { value: 'reopened', text: 'Reaberto' }
        ],
        'change_priority': [
            { value: 'low', text: 'Baixa' },
            { value: 'medium', text: 'Média' },
            { value: 'high', text: 'Alta' },
            { value: 'urgent', text: 'Urgente' }
        ],
        'assign': [
            @foreach($bulkUsers as $user)
                { value: '{{ $user->id }}', text: '{{ $user->name }}' }{{ !$loop->last ? ',' : '' }}
            @endforeach
        ]
    };

    // Selecionar/deselecionar todos
    selectAllCheckbox?.addEventListener('change', function() {
        ticketCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionsBar();
    });

    // Atualizar quando checkboxes individuais mudam
    ticketCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActionsBar();
            updateSelectAllState();
        });
    });

    // Atualizar estado do "selecionar todos"
    function updateSelectAllState() {
        const totalCheckboxes = ticketCheckboxes.length;
        const checkedCheckboxes = document.querySelectorAll('.ticket-checkbox:checked').length;
        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checkedCheckboxes === totalCheckboxes;
            selectAllCheckbox.indeterminate = checkedCheckboxes > 0 && checkedCheckboxes < totalCheckboxes;
        }
    }

    // Mostrar/ocultar barra de ações em lote
    function updateBulkActionsBar() {
        const selectedTickets = document.querySelectorAll('.ticket-checkbox:checked');
        const count = selectedTickets.length;
        
        if (selectedCountSpan) selectedCountSpan.textContent = count;
        
        if (bulkActionsBar) {
            bulkActionsBar.style.display = count > 0 ? 'block' : 'none';
        }
    }

    // Quando a ação muda, mostrar/ocultar select de valores
    bulkActionSelect?.addEventListener('change', function() {
        const action = this.value;
        
        if (bulkValueSelect) {
            if (action === 'change_status' || action === 'change_priority' || action === 'assign') {
                bulkValueSelect.style.display = 'block';
                updateBulkValueOptions(action);
            } else {
                bulkValueSelect.style.display = 'none';
            }
        }
    });

    // Atualizar opções do select de valores
    function updateBulkValueOptions(action) {
        if (!bulkValueSelect) return;
        
        bulkValueSelect.innerHTML = '<option value="">Selecione...</option>';
        
        if (actionOptions[action]) {
            actionOptions[action].forEach(option => {
                const optionElement = document.createElement('option');
                optionElement.value = option.value || option.id;
                optionElement.textContent = option.text || option.name;
                bulkValueSelect.appendChild(optionElement);
            });
        }
    }

    // Executar ação em lote
    executeBulkActionBtn?.addEventListener('click', function() {
        const selectedTickets = Array.from(document.querySelectorAll('.ticket-checkbox:checked'))
            .map(cb => cb.value);
        
        if (selectedTickets.length === 0) {
            alert('Selecione pelo menos um chamado.');
            return;
        }

        const action = bulkActionSelect.value;
        if (!action) {
            alert('Selecione uma ação.');
            return;
        }

        // Verificar se precisa de valor adicional
        let additionalValue = null;
        if (['change_status', 'change_priority', 'assign'].includes(action)) {
            additionalValue = bulkValueSelect.value;
            if (!additionalValue) {
                alert('Selecione um valor para a ação escolhida.');
                return;
            }
        }

        const comment = document.getElementById('bulk-comment')?.value || '';

        // Confirmar ação
        const actionNames = {
            'close': 'fechar',
            'resolve': 'resolver',
            'reopen': 'reabrir',
            'assign': 'atribuir técnico aos',
            'change_status': 'alterar status dos',
            'change_priority': 'alterar prioridade dos'
        };

        const actionName = actionNames[action] || action;
        if (!confirm(`Tem certeza que deseja ${actionName} ${selectedTickets.length} chamado(s)?`)) {
            return;
        }

        // Preparar dados
        const formData = new FormData();
        selectedTickets.forEach(id => formData.append('ticket_ids[]', id));
        formData.append('action', action);
        if (comment) formData.append('comment', comment);

        if (action === 'assign') {
            formData.append('assigned_user_id', additionalValue);
        } else if (action === 'change_status') {
            formData.append('new_status', additionalValue);
        } else if (action === 'change_priority') {
            formData.append('new_priority', additionalValue);
        }

        // Enviar requisição
        fetch('{{ route("tickets.bulk-action") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload(); // Recarregar página para ver as mudanças
            } else {
                alert('Erro: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao executar ação em lote. Tente novamente.');
        });
    });

    // Limpar seleção
    clearSelectionBtn?.addEventListener('click', function() {
        ticketCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
        }
        updateBulkActionsBar();
    });
});
</script>
@endpush
@endif

@push('styles')
<style>
/* Estilos para badges de equipe de suporte */
.badge.bg-info {
    font-weight: 500;
    padding: 0.35em 0.5em;
}

.badge.bg-info i {
    font-size: 0.85em;
}

/* Ajustar gap entre badges */
.gap-1 {
    gap: 0.25rem !important;
}

/* Melhorar visualização em células pequenas */
td small .d-flex {
    line-height: 1.8;
}
</style>
@endpush

@endsection
