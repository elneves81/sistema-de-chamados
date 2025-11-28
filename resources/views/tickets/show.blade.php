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

        <!-- Histórico de Atividades -->
        <div class="card mb-4">
            <div class="card-header bg-light">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock-history"></i> Histórico de Atividades
                    @if($ticket->activityLogs->count() > 0)
                        <span class="badge bg-secondary">{{ $ticket->activityLogs->count() }}</span>
                    @endif
                </h5>
            </div>
            <div class="card-body">
                @if($ticket->activityLogs->count() > 0)
                    <div class="activity-timeline">
                        @foreach($ticket->activityLogs as $log)
                        <div class="activity-item">
                            <div class="activity-icon">
                                <i class="{{ $log->icon }}"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-header">
                                    <strong>{{ $log->description }}</strong>
                                </div>
                                <div class="activity-meta">
                                    <small class="text-muted">
                                        <i class="bi bi-clock"></i> {{ $log->created_at->format('d/m/Y H:i') }}
                                        ({{ $log->created_at->diffForHumans() }})
                                    </small>
                                </div>
                                @if($log->changes && count($log->changes) > 0)
                                <div class="activity-details mt-1">
                                    <small class="text-muted">
                                        @if(isset($log->changes['from']) && isset($log->changes['to']))
                                            <span class="badge bg-danger">{{ $log->changes['from'] }}</span>
                                            <i class="bi bi-arrow-right"></i>
                                            <span class="badge bg-success">{{ $log->changes['to'] }}</span>
                                        @endif
                                    </small>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted text-center py-3 mb-0">
                        <i class="bi bi-hourglass-split"></i>
                        Nenhuma atividade registrada ainda.
                    </p>
                @endif
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
                        @if(!empty($comment->attachments))
                        <div class="mt-2">
                            <small class="text-muted d-block"><i class="bi bi-paperclip"></i> Anexos:</small>
                            <ul class="list-unstyled mb-0">
                                @foreach($comment->attachments as $file)
                                <li class="d-flex align-items-center mb-1">
                                    <i class="bi bi-file-earmark me-2"></i>
                                    <a href="{{ $file['url'] ?? (Storage::disk('public')->url($file['path'] ?? '')) }}" target="_blank">
                                        {{ $file['name'] ?? basename($file['path'] ?? '') }}
                                    </a>
                                    @if(isset($file['size']))
                                    <small class="text-muted ms-2">({{ number_format(($file['size'] ?? 0) / 1024, 0) }} KB)</small>
                                    @endif
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        @endif
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
                <form method="POST" action="{{ route('tickets.comments.store', $ticket) }}" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="comment" class="form-label">Adicionar Comentário:</label>
                        <textarea class="form-control @error('comment') is-invalid @enderror" 
                                  id="comment" 
                                  name="comment" 
                                  rows="4" 
                                  placeholder="Digite seu comentário..."
                                  >{{ old('comment') }}</textarea>
                        @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Anexos (opcional)</label>
                        <input type="file" 
                               class="form-control @error('attachments.*') is-invalid @enderror" 
                               id="attachments" 
                               name="attachments[]" 
                               multiple
                               accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                        <small class="text-muted">Você pode selecionar múltiplos arquivos. Tamanho máximo por arquivo: 10MB.</small>
                        @error('attachments.*')
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
                @if($ticket->location || $ticket->local)
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Localização:</strong></div>
                    <div class="col-sm-7">
                        @if($ticket->location)
                            <i class="bi bi-geo-alt"></i> {{ $ticket->location->name }}
                            @if($ticket->local) - {{ $ticket->local }} @endif
                        @else
                            {{ $ticket->local }}
                        @endif
                    </div>
                </div>
                @endif
                @if($ticket->assignedUser)
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Técnico Principal:</strong></div>
                    <div class="col-sm-7">
                        <i class="bi bi-person-check-fill text-primary"></i> {{ $ticket->assignedUser->name }}
                    </div>
                </div>
                @endif
                
                <!-- Equipe de Suporte (Múltiplos Técnicos) -->
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Equipe de Suporte:</strong></div>
                    <div class="col-sm-7">
                        <div id="support-technicians-list">
                            @forelse($ticket->supportTechnicians as $supportTech)
                                <div class="d-flex align-items-center mb-2 support-tech-item" data-tech-id="{{ $supportTech->id }}">
                                    <i class="bi bi-person-plus-fill text-info me-2"></i>
                                    <span>{{ $supportTech->name }}</span>
                                    @if(in_array(Auth::user()->role, ['admin', 'technician']))
                                        <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                                                onclick="removeSupportTechnician({{ $supportTech->id }})" 
                                                title="Remover da equipe">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    @endif
                                </div>
                            @empty
                                <span class="text-muted" id="no-support-text">Nenhum técnico de suporte</span>
                            @endforelse
                        </div>
                        
                        @if(in_array(Auth::user()->role, ['admin', 'technician']))
                            <button type="button" class="btn btn-sm btn-outline-success mt-2" 
                                    data-bs-toggle="modal" data-bs-target="#supportTechnicianModal">
                                <i class="bi bi-plus-circle"></i> Adicionar Técnico
                            </button>
                        @endif
                    </div>
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

        <!-- SLA Info -->
        @if($ticket->category && $ticket->category->sla_hours)
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-stopwatch"></i> SLA
                </h6>
            </div>
            <div class="card-body">
                @php
                    // Prefer the stored due_date, fallback to created_at + sla_hours (without mutating created_at)
                    $dueAt = $ticket->due_date ?? $ticket->created_at->copy()->addHours($ticket->category->sla_hours);
                    $remainingMinutes = now()->diffInMinutes($dueAt, false); // > 0 means overdue, < 0 means time left
                    $isOverdue = $ticket->is_overdue ?? ($remainingMinutes > 0);
                @endphp

                <div class="mb-2">
                    <strong>Prazo:</strong> {{ $ticket->category->sla_hours }}h
                </div>
                <div class="mb-2">
                    <strong>Vencimento:</strong>
                    {{ $dueAt->format('d/m/Y H:i') }}
                </div>
                <div>
                    <strong>Status:</strong>
                    @if(in_array($ticket->status, ['resolved', 'closed']))
                        <span class="text-success">Concluído</span>
                    @elseif($isOverdue)
                        <span class="text-danger">Vencido</span>
                    @else
                        @php
                            $hoursLeft = (int) ceil(abs($remainingMinutes) / 60);
                        @endphp
                        <span class="text-warning">{{ $hoursLeft }}h restantes</span>
                    @endif
                </div>
            </div>
        </div>
        @endif

        @if(!empty($ticket->attachments))
        <div class="card mt-4">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-paperclip"></i> Anexos do Chamado
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    @foreach($ticket->attachments as $file)
                        <li class="mb-2 d-flex align-items-center">
                            <i class="bi bi-file-earmark me-2"></i>
                            <a href="{{ $file['url'] ?? (Storage::disk('public')->url($file['path'] ?? '')) }}" target="_blank">
                                {{ $file['name'] ?? basename($file['path'] ?? '') }}
                            </a>
                            @if(isset($file['size']))
                                <small class="text-muted ms-2">({{ number_format($file['size'] / 1024, 0) }} KB)</small>
                            @endif
                        </li>
                    @endforeach
                </ul>
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

    // Mostrar lista de arquivos selecionados (opcional)
    const fileInput = document.getElementById('attachments');
    if (fileInput) {
        fileInput.addEventListener('change', function() {
            // No-op, placeholder para futuras melhorias de preview
        });
    }
});
</script>
@endpush

@push('styles')
<style>
.activity-timeline {
    position: relative;
    padding-left: 30px;
}

.activity-timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 10px;
    bottom: 10px;
    width: 2px;
    background: linear-gradient(to bottom, #e0e0e0 0%, #e0e0e0 100%);
}

.activity-item {
    position: relative;
    padding-bottom: 20px;
    display: flex;
    align-items: flex-start;
}

.activity-item:last-child {
    padding-bottom: 0;
}

.activity-icon {
    position: absolute;
    left: -30px;
    top: 0;
    width: 18px;
    height: 18px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1;
}

.activity-icon i {
    font-size: 18px;
}

.activity-icon i.bi-plus-circle-fill {
    color: #28a745;
}

.activity-icon i.bi-pencil-fill,
.activity-icon i.bi-arrow-left-right {
    color: #ffc107;
}

.activity-icon i.bi-person-fill-check {
    color: #17a2b8;
}

.activity-icon i.bi-chat-left-text-fill {
    color: #6c757d;
}

.activity-icon i.bi-flag-fill {
    color: #fd7e14;
}

.activity-icon i.bi-check-circle-fill {
    color: #20c997;
}

.activity-icon i.bi-arrow-clockwise {
    color: #6f42c1;
}

.activity-icon i.bi-x-circle-fill {
    color: #dc3545;
}

.activity-content {
    flex: 1;
    padding-left: 8px;
}

.activity-header {
    margin-bottom: 3px;
    line-height: 1.4;
}

.activity-meta {
    margin-bottom: 5px;
}

.activity-details .badge {
    font-size: 0.75rem;
    padding: 0.25em 0.5em;
}

.activity-details i.bi-arrow-right {
    margin: 0 0.5rem;
    color: #6c757d;
}

/* Fix para modal de técnico de suporte */
.modal-backdrop.show {
    z-index: 1400 !important;
    opacity: 0.5 !important;
}

#supportTechnicianModal {
    z-index: 1500 !important;
    display: none;
}

#supportTechnicianModal.show {
    display: block !important;
    z-index: 1500 !important;
}

#supportTechnicianModal .modal-dialog {
    z-index: 1501 !important;
    position: relative;
    margin: 1.75rem auto;
    max-width: 500px;
}

#supportTechnicianModal .modal-content {
    position: relative;
    z-index: 1502 !important;
    pointer-events: auto !important;
    background-color: #fff;
    border: 1px solid rgba(0,0,0,.2);
    border-radius: 0.5rem;
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15);
}

#supportTechnicianModal .modal-header {
    position: relative;
    z-index: 1503 !important;
    pointer-events: auto !important;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #dee2e6;
}

#supportTechnicianModal .modal-body {
    position: relative;
    z-index: 1503 !important;
    pointer-events: auto !important;
    padding: 1.25rem;
}

#supportTechnicianModal .modal-footer {
    position: relative;
    z-index: 1503 !important;
    pointer-events: auto !important;
    padding: 0.75rem 1.25rem;
    border-top: 1px solid #dee2e6;
}

#supportTechnicianModal .modal-title {
    font-size: 1.1rem;
    font-weight: 600;
}

#supportTechnicianModal .form-label {
    font-weight: 500;
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

#supportTechnicianModal .form-text {
    font-size: 0.85rem;
    margin-top: 0.5rem;
}

#supportTechnicianModal .form-select {
    padding: 0.5rem 0.75rem;
    font-size: 0.95rem;
}

/* Estilos para lista de técnicos de suporte */
.support-tech-item {
    padding: 0.5rem;
    background-color: #f8f9fa;
    border-radius: 0.25rem;
    transition: background-color 0.2s;
}

.support-tech-item:hover {
    background-color: #e9ecef;
}

.support-tech-item span {
    flex: 1;
}

#support-technicians-list {
    max-height: 300px;
    overflow-y: auto;
}

#supportTechnicianModal select,
#supportTechnicianModal button,
#supportTechnicianModal input,
#supportTechnicianModal .btn,
#supportTechnicianModal .btn-close,
#supportTechnicianModal .form-select {
    position: relative;
    z-index: 1504 !important;
    pointer-events: auto !important;
    cursor: pointer !important;
}

#supportTechnicianModal select:focus,
#supportTechnicianModal .form-select:focus {
    z-index: 1505 !important;
}
</style>
@endpush

<!-- Modal para Adicionar Técnico de Suporte -->
<div class="modal fade" id="supportTechnicianModal" tabindex="-1" aria-labelledby="supportTechnicianModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supportTechnicianModalLabel">
                    <i class="bi bi-person-plus"></i> Adicionar Técnico de Suporte
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="supportTechnicianForm">
                    @csrf
                    <div class="mb-2">
                        <label for="support_technician_id" class="form-label">Selecione o Técnico de Suporte</label>
                        <select class="form-select" id="support_technician_id" name="support_technician_id" required>
                            <option value="">Selecione um técnico...</option>
                            @foreach(\App\Models\User::whereIn('role', ['admin', 'technician'])->where('is_active', true)->orderBy('name')->get() as $tech)
                                @if($tech->id != $ticket->assigned_to)
                                    <option value="{{ $tech->id }}">{{ $tech->name }}</option>
                                @endif
                            @endforeach
                        </select>
                        <div class="form-text">
                            <i class="bi bi-info-circle"></i> O técnico auxiliará no atendimento deste chamado.
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="assignSupportTechnician()">
                    <i class="bi bi-check-circle"></i> Adicionar
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Fix para garantir que o modal funcione corretamente
document.addEventListener('DOMContentLoaded', function() {
    const modalElement = document.getElementById('supportTechnicianModal');
    
    if (modalElement) {
        // Configurar modal para funcionar corretamente
        modalElement.addEventListener('show.bs.modal', function(event) {
            // Remover backdrop antigo
            const oldBackdrops = document.querySelectorAll('.modal-backdrop');
            oldBackdrops.forEach(backdrop => backdrop.remove());
            
            // Garantir z-index correto
            setTimeout(() => {
                const modal = document.getElementById('supportTechnicianModal');
                if (modal) {
                    modal.style.zIndex = '1500';
                    modal.classList.add('show');
                }
                
                // Ajustar backdrop
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.style.zIndex = '1400';
                    backdrop.style.pointerEvents = 'none'; // Permitir cliques através do backdrop
                }
                
                // Garantir que o conteúdo do modal seja clicável
                const modalContent = modal.querySelector('.modal-content');
                if (modalContent) {
                    modalContent.style.pointerEvents = 'auto';
                    modalContent.style.zIndex = '1502';
                }
            }, 100);
        });
        
        // Garantir foco no select quando abrir
        modalElement.addEventListener('shown.bs.modal', function() {
            const select = document.getElementById('support_technician_id');
            if (select) {
                select.style.pointerEvents = 'auto';
                select.style.cursor = 'pointer';
                select.focus();
            }
            
            // Garantir que todos os elementos sejam clicáveis
            const clickableElements = modalElement.querySelectorAll('button, select, input, .btn, .form-select');
            clickableElements.forEach(el => {
                el.style.pointerEvents = 'auto';
                el.style.cursor = 'pointer';
            });
        });
        
        // Limpar ao fechar
        modalElement.addEventListener('hidden.bs.modal', function() {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });
    }
});

function assignSupportTechnician() {
    const supportTechnicianId = document.getElementById('support_technician_id').value;
    const selectElement = document.getElementById('support_technician_id');
    
    if (!supportTechnicianId) {
        alert('Por favor, selecione um técnico de suporte.');
        return;
    }

    const techName = selectElement.options[selectElement.selectedIndex].text;

    fetch('{{ route("tickets.support.assign", $ticket->id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            support_technician_id: supportTechnicianId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('supportTechnicianModal'));
            modal.hide();
            
            // Atualizar lista de técnicos sem reload
            updateSupportTechniciansList(data.ticket.support_technicians);
            
            // Limpar select
            selectElement.value = '';
            
            // Mostrar mensagem de sucesso
            showSuccessMessage(data.message);
        } else {
            alert(data.error || 'Erro ao adicionar técnico de suporte.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao adicionar técnico de suporte. Tente novamente.');
    });
}

function removeSupportTechnician(technicianId) {
    if (!confirm('Tem certeza que deseja remover este técnico da equipe de suporte?')) {
        return;
    }

    fetch('{{ route("tickets.support.remove", $ticket->id) }}', {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            support_technician_id: technicianId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remover elemento da lista
            const techItem = document.querySelector(`.support-tech-item[data-tech-id="${technicianId}"]`);
            if (techItem) {
                techItem.remove();
            }
            
            // Verificar se lista ficou vazia
            const listContainer = document.getElementById('support-technicians-list');
            if (listContainer.querySelectorAll('.support-tech-item').length === 0) {
                listContainer.innerHTML = '<span class="text-muted" id="no-support-text">Nenhum técnico de suporte</span>';
            }
            
            showSuccessMessage(data.message);
        } else {
            alert(data.error || 'Erro ao remover técnico de suporte.');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao remover técnico de suporte. Tente novamente.');
    });
}

function updateSupportTechniciansList(technicians) {
    const listContainer = document.getElementById('support-technicians-list');
    
    if (!technicians || technicians.length === 0) {
        listContainer.innerHTML = '<span class="text-muted" id="no-support-text">Nenhum técnico de suporte</span>';
        return;
    }
    
    // Remover mensagem "nenhum técnico"
    const noSupportText = document.getElementById('no-support-text');
    if (noSupportText) {
        noSupportText.remove();
    }
    
    // Adicionar novo técnico
    const lastTech = technicians[technicians.length - 1];
    const canManage = {{ in_array(Auth::user()->role, ['admin', 'technician']) ? 'true' : 'false' }};
    
    const techHTML = `
        <div class="d-flex align-items-center mb-2 support-tech-item" data-tech-id="${lastTech.id}">
            <i class="bi bi-person-plus-fill text-info me-2"></i>
            <span>${lastTech.name}</span>
            ${canManage ? `
                <button type="button" class="btn btn-sm btn-outline-danger ms-2" 
                        onclick="removeSupportTechnician(${lastTech.id})" 
                        title="Remover da equipe">
                    <i class="bi bi-x-circle"></i>
                </button>
            ` : ''}
        </div>
    `;
    
    listContainer.insertAdjacentHTML('beforeend', techHTML);
}

function showSuccessMessage(message) {
    // Criar alerta de sucesso
    const alert = document.createElement('div');
    alert.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
    alert.style.zIndex = '9999';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);
    
    // Remover após 3 segundos
    setTimeout(() => {
        alert.remove();
    }, 3000);
}
</script>
@endpush
