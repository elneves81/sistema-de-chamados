@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-pencil"></i> Editar Chamado #{{ $ticket->id }}
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações do Chamado</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.update', $ticket) }}">
                    @csrf
                    @method('PUT')

                    @if(auth()->user()->role === 'admin')
                    <!-- Admin pode editar título e descrição -->
                    <div class="mb-3">
                        <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title', $ticket->title) }}" 
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="6" 
                                  required>{{ old('description', $ticket->description) }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    @else
                    <!-- Técnicos veem apenas as informações -->
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <div class="form-control-plaintext">{{ $ticket->title }}</div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Descrição</label>
                        <div class="form-control-plaintext bg-light p-3 rounded">
                            {!! nl2br(e($ticket->description)) !!}
                        </div>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ (old('category_id', $ticket->category_id) == $category->id) ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Prioridade <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" 
                                        name="priority" 
                                        required>
                                    <option value="low" {{ old('priority', $ticket->priority) === 'low' ? 'selected' : '' }}>
                                        🟢 Baixa
                                    </option>
                                    <option value="medium" {{ old('priority', $ticket->priority) === 'medium' ? 'selected' : '' }}>
                                        🟡 Média
                                    </option>
                                    <option value="high" {{ old('priority', $ticket->priority) === 'high' ? 'selected' : '' }}>
                                        🟠 Alta
                                    </option>
                                    <option value="urgent" {{ old('priority', $ticket->priority) === 'urgent' ? 'selected' : '' }}>
                                        🔴 Urgente
                                    </option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status" 
                                        required>
                                    <option value="open" {{ old('status', $ticket->status) === 'open' ? 'selected' : '' }}>
                                        🔵 Aberto
                                    </option>
                                    <option value="in_progress" {{ old('status', $ticket->status) === 'in_progress' ? 'selected' : '' }}>
                                        🟡 Em Andamento
                                    </option>
                                    <option value="waiting" {{ old('status', $ticket->status) === 'waiting' ? 'selected' : '' }}>
                                        🟣 Aguardando
                                    </option>
                                    <option value="resolved" {{ old('status', $ticket->status) === 'resolved' ? 'selected' : '' }}>
                                        🟢 Resolvido
                                    </option>
                                    <option value="closed" {{ old('status', $ticket->status) === 'closed' ? 'selected' : '' }}>
                                        ⚫ Fechado
                                    </option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="assigned_to" class="form-label">Atribuir para</label>
                                <select class="form-select @error('assigned_to') is-invalid @enderror" 
                                        id="assigned_to" 
                                        name="assigned_to">
                                    <option value="">Não atribuído</option>
                                    @foreach($technicians as $technician)
                                    <option value="{{ $technician->id }}" 
                                            {{ (old('assigned_to', $ticket->assigned_to) == $technician->id) ? 'selected' : '' }}>
                                        {{ $technician->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('assigned_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Informações Atuais -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> Informações Atuais
                </h6>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>ID:</strong></div>
                    <div class="col-sm-7">#{{ $ticket->id }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Status:</strong></div>
                    <div class="col-sm-7">
                        <span class="status-badge status-{{ $ticket->status }}">
                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Prioridade:</strong></div>
                    <div class="col-sm-7">
                        <span class="priority-badge priority-{{ $ticket->priority }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Solicitante:</strong></div>
                    <div class="col-sm-7">{{ $ticket->user->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Criado em:</strong></div>
                    <div class="col-sm-7">{{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                </div>
                <div class="row mb-0">
                    <div class="col-sm-5"><strong>Atualizado:</strong></div>
                    <div class="col-sm-7">{{ $ticket->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>

        <!-- Dicas para Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb"></i> Status - Quando usar?
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <span class="status-badge status-open">Aberto</span>
                    <small class="text-muted d-block">Chamado criado, aguardando análise</small>
                </div>
                <div class="mb-2">
                    <span class="status-badge status-in_progress">Em Andamento</span>
                    <small class="text-muted d-block">Problema sendo analisado/resolvido</small>
                </div>
                <div class="mb-2">
                    <span class="status-badge status-waiting">Aguardando</span>
                    <small class="text-muted d-block">Aguardando informações do solicitante</small>
                </div>
                <div class="mb-2">
                    <span class="status-badge status-resolved">Resolvido</span>
                    <small class="text-muted d-block">Problema solucionado</small>
                </div>
                <div class="mb-0">
                    <span class="status-badge status-closed">Fechado</span>
                    <small class="text-muted d-block">Chamado finalizado</small>
                </div>
            </div>
        </div>

        @if(auth()->user()->role === 'admin')
        <!-- Ações Administrativas -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0 text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Ações Administrativas
                </h6>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" 
                      onsubmit="return confirm('Tem certeza que deseja excluir este chamado? Esta ação não pode ser desfeita.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-sm">
                        <i class="bi bi-trash"></i> Excluir Chamado
                    </button>
                </form>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Destacar mudanças de status
    const statusSelect = document.getElementById('status');
    const originalStatus = '{{ $ticket->status }}';
    
    statusSelect.addEventListener('change', function() {
        if (this.value !== originalStatus) {
            this.style.borderColor = '#fd7e14';
            this.style.borderWidth = '2px';
            this.style.boxShadow = '0 0 0 0.2rem rgba(253, 126, 20, 0.25)';
        } else {
            this.style.borderColor = '';
            this.style.borderWidth = '';
            this.style.boxShadow = '';
        }
    });

    // Destacar mudanças de prioridade
    const prioritySelect = document.getElementById('priority');
    const originalPriority = '{{ $ticket->priority }}';
    
    prioritySelect.addEventListener('change', function() {
        if (this.value !== originalPriority) {
            this.style.borderColor = '#fd7e14';
            this.style.borderWidth = '2px';
            this.style.boxShadow = '0 0 0 0.2rem rgba(253, 126, 20, 0.25)';
        } else {
            this.style.borderColor = '';
            this.style.borderWidth = '';
            this.style.boxShadow = '';
        }
    });
});
</script>
@endpush
