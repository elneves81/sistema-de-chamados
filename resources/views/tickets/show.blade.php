@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-ticket-perforated"></i> Chamado #{{ $ticket->id }}
        <span class="status-badge status-{{ $ticket->status }} ms-2">
            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
        </span>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            @if(in_array(auth()->user()->role, ['admin', 'technician']))
            <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-primary">
                <i class="bi bi-pencil"></i> Editar
            </a>
            @endif
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Detalhes do Chamado -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">{{ $ticket->title }}</h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <h6 class="text-muted">Descrição:</h6>
                    <div class="bg-light p-3 rounded">
                        {!! nl2br(e($ticket->description)) !!}
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <h6 class="text-muted">Categoria:</h6>
                        <span class="badge" style="background-color: {{ $ticket->category->color }}; color: white;">
                            {{ $ticket->category->name }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <h6 class="text-muted">Prioridade:</h6>
                        <span class="priority-badge priority-{{ $ticket->priority }}">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comentários -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-chat-dots"></i> Comentários
                    @if($ticket->comments->count() > 0)
                        <span class="badge bg-secondary">{{ $ticket->comments->count() }}</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($ticket->comments->count() > 0)
                    @foreach($ticket->comments as $comment)
                    <div class="border-start border-3 ps-3 mb-3 {{ $comment->user->role === 'customer' ? 'border-primary' : 'border-success' }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <strong>{{ $comment->user->name }}</strong>
                                <span class="badge bg-light text-dark">{{ ucfirst($comment->user->role) }}</span>
                            </div>
                            <small class="text-muted">{{ $comment->created_at->format('d/m/Y H:i') }}</small>
                        </div>
                        <div class="text-muted">
                            {!! nl2br(e($comment->comment)) !!}
                        </div>
                    </div>
                    @if(!$loop->last)
                        <hr class="my-3">
                    @endif
                    @endforeach
                @else
                    <p class="text-muted text-center py-3">
                        <i class="bi bi-chat-square-dots display-6 d-block mb-2"></i>
                        Nenhum comentário ainda. Seja o primeiro a comentar!
                    </p>
                @endif

                <!-- Formulário para adicionar comentário -->
                <hr class="my-4">
                <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="comment" class="form-label">Adicionar Comentário:</label>
                        <textarea class="form-control @error('comment') is-invalid @enderror" 
                                  id="comment" 
                                  name="comment" 
                                  rows="4" 
                                  placeholder="Digite seu comentário..."
                                  required>{{ old('comment') }}</textarea>
                        @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Enviar Comentário
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Informações do Chamado -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> Informações
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
                    <div class="col-sm-5"><strong>Categoria:</strong></div>
                    <div class="col-sm-7">
                        <span class="badge" style="background-color: {{ $ticket->category->color }}; color: white;">
                            {{ $ticket->category->name }}
                        </span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Solicitante:</strong></div>
                    <div class="col-sm-7">{{ $ticket->user->name }}</div>
                </div>
                @if($ticket->assignedUser)
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Atribuído:</strong></div>
                    <div class="col-sm-7">{{ $ticket->assignedUser->name }}</div>
                </div>
                @endif
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

        <!-- Histórico de Status (Placeholder) -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clock-history"></i> Histórico
                </h6>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <small class="text-muted">{{ $ticket->created_at->format('d/m/Y H:i') }}</small>
                            <div>Chamado criado por {{ $ticket->user->name }}</div>
                        </div>
                    </div>
                    @if($ticket->assignedUser)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <small class="text-muted">{{ $ticket->updated_at->format('d/m/Y H:i') }}</small>
                            <div>Atribuído para {{ $ticket->assignedUser->name }}</div>
                        </div>
                    </div>
                    @endif
                    @if($ticket->status !== 'open')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <small class="text-muted">{{ $ticket->updated_at->format('d/m/Y H:i') }}</small>
                            <div>Status alterado para {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- SLA Info (Placeholder) -->
        @if($ticket->category->sla_hours)
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-stopwatch"></i> SLA
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Prazo:</strong> {{ $ticket->category->sla_hours }}h
                </div>
                <div class="mb-2">
                    <strong>Vencimento:</strong> 
                    {{ $ticket->created_at->addHours($ticket->category->sla_hours)->format('d/m/Y H:i') }}
                </div>
                @php
                    $hoursLeft = $ticket->created_at->addHours($ticket->category->sla_hours)->diffInHours(now(), false);
                    $isOverdue = $hoursLeft < 0;
                @endphp
                <div>
                    <strong>Status:</strong>
                    @if($ticket->status === 'resolved' || $ticket->status === 'closed')
                        <span class="text-success">Concluído</span>
                    @elseif($isOverdue)
                        <span class="text-danger">Vencido</span>
                    @else
                        <span class="text-warning">{{ abs($hoursLeft) }}h restantes</span>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<style>
.timeline {
    position: relative;
    padding-left: 1.5rem;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 0.5rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 1rem;
}

.timeline-marker {
    position: absolute;
    left: -1rem;
    top: 0.25rem;
    width: 0.75rem;
    height: 0.75rem;
    border-radius: 50%;
    border: 2px solid white;
}

.timeline-content {
    margin-left: 0.5rem;
}

.border-left-primary { border-left-color: #007bff !important; }
.border-left-warning { border-left-color: #ffc107 !important; }
.border-left-success { border-left-color: #28a745 !important; }
.border-left-info { border-left-color: #17a2b8 !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea para comentários
    const textarea = document.getElementById('comment');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});
</script>
@endpush
