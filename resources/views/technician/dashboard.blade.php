@extends('layouts.app')

@section('title', 'Dashboard Técnico - Kanban')

@section('content')
<div class="container-fluid px-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-kanban"></i> Central de Atendimentos
                    </h1>
                    <p class="text-muted mb-0">Arraste os cards para gerenciar seus chamados</p>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <button class="btn btn-outline-primary" id="refreshBtn">
                        <i class="bi bi-arrow-clockwise"></i> Atualizar
                    </button>
                    <small id="lastUpdated" class="text-muted ms-1">
                        Atualizado às {{ now()->setTimezone(config('app.timezone'))->format('H:i:s') }}
                    </small>
                    <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary ms-auto">
                        <i class="bi bi-list"></i> Ver Lista
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card stat-card-primary">
                <div class="stat-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['available_tickets'] }}</div>
                    <div class="stat-label">Disponíveis</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card stat-card-info">
                <div class="stat-icon">
                    <i class="bi bi-person-check"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['my_open'] }}</div>
                    <div class="stat-label">Em Atendimento</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card stat-card-success">
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['my_completed_today'] }}</div>
                    <div class="stat-label">Resolvidos Hoje</div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="stat-card stat-card-danger">
                <div class="stat-icon">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-value">{{ $stats['urgent_count'] }}</div>
                    <div class="stat-label">Urgentes</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Kanban Board -->
    <div class="kanban-board">
        <!-- Coluna: Disponíveis -->
        <div class="kanban-column">
            <div class="kanban-column-header kanban-header-available">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-inbox"></i>
                        <span class="fw-bold">Disponíveis</span>
                    </div>
                    <span class="badge bg-secondary">{{ $tickets['available']->count() }}</span>
                </div>
                <small class="text-muted d-block mt-1">Arraste para pegar o chamado</small>
            </div>
            <div class="kanban-column-body" id="available-column" data-status="available">
                @forelse($tickets['available'] as $ticket)
                    @include('technician.partials.ticket-card', ['ticket' => $ticket])
                @empty
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>Nenhum chamado disponível</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Coluna: Atribuídos -->
        <div class="kanban-column">
            <div class="kanban-column-header kanban-header-my-tickets">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-person-check"></i>
                        <span class="fw-bold">Atribuídos</span>
                    </div>
                    <span class="badge bg-primary">{{ $tickets['my_tickets']->count() }}</span>
                </div>
                <small class="text-muted d-block mt-1">Chamados em atendimento</small>
            </div>
            <div class="kanban-column-body" id="my-tickets-column" data-status="my_tickets">
                @forelse($tickets['my_tickets'] as $ticket)
                    @include('technician.partials.ticket-card', ['ticket' => $ticket])
                @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Nenhum chamado atribuído</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Coluna: Em Andamento -->
        <div class="kanban-column">
            <div class="kanban-column-header kanban-header-progress">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-arrow-repeat"></i>
                        <span class="fw-bold">Em Andamento</span>
                    </div>
                    <span class="badge bg-warning">{{ $tickets['in_progress']->count() }}</span>
                </div>
                <small class="text-muted d-block mt-1">Trabalhando agora</small>
            </div>
            <div class="kanban-column-body" id="in-progress-column" data-status="in_progress">
                @forelse($tickets['in_progress'] as $ticket)
                    @include('technician.partials.ticket-card', ['ticket' => $ticket])
                @empty
                    <div class="empty-state">
                        <i class="bi bi-hourglass-split"></i>
                        <p>Nenhum chamado em andamento</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Coluna: Aguardando -->
        <div class="kanban-column">
            <div class="kanban-column-header kanban-header-waiting">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-clock-history"></i>
                        <span class="fw-bold">Aguardando</span>
                    </div>
                    <span class="badge bg-secondary">{{ $tickets['waiting']->count() }}</span>
                </div>
                <small class="text-muted d-block mt-1">Aguardando resposta</small>
            </div>
            <div class="kanban-column-body" id="waiting-column" data-status="waiting">
                @forelse($tickets['waiting'] as $ticket)
                    @include('technician.partials.ticket-card', ['ticket' => $ticket])
                @empty
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <p>Nenhum chamado aguardando</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Coluna: Resolvidos -->
        <div class="kanban-column">
            <div class="kanban-column-header kanban-header-resolved">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <i class="bi bi-check-circle"></i>
                        <span class="fw-bold">Resolvidos</span>
                    </div>
                    <span class="badge bg-success">{{ $tickets['resolved']->count() }}</span>
                </div>
                <small class="text-muted d-block mt-1">Últimos 20 resolvidos</small>
            </div>
            <div class="kanban-column-body" id="resolved-column" data-status="resolved">
                @forelse($tickets['resolved'] as $ticket)
                    @include('technician.partials.ticket-card', ['ticket' => $ticket])
                @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>Nenhum chamado resolvido</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
/* Stats Cards */
.stat-card {
    background: white;
    border-radius: 12px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    transition: transform 0.2s, box-shadow 0.2s;
    border-left: 4px solid;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.stat-card-primary { border-color: #0d6efd; }
.stat-card-info { border-color: #0dcaf0; }
.stat-card-success { border-color: #198754; }
.stat-card-danger { border-color: #dc3545; }

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}

.stat-card-primary .stat-icon { background: rgba(13, 110, 253, 0.1); color: #0d6efd; }
.stat-card-info .stat-icon { background: rgba(13, 202, 240, 0.1); color: #0dcaf0; }
.stat-card-success .stat-icon { background: rgba(25, 135, 84, 0.1); color: #198754; }
.stat-card-danger .stat-icon { background: rgba(220, 53, 69, 0.1); color: #dc3545; }

.stat-content {
    flex: 1;
}

.stat-value {
    font-size: 2rem;
    font-weight: 700;
    line-height: 1;
    margin-bottom: 0.25rem;
}

.stat-label {
    color: #6c757d;
    font-size: 0.9rem;
}

/* Kanban Board */
.kanban-board {
    display: flex;
    gap: 1rem;
    overflow-x: auto;
    padding-bottom: 1rem;
    min-height: 600px;
}

.kanban-column {
    flex: 0 0 320px;
    min-width: 320px;
    display: flex;
    flex-direction: column;
    background: #f8f9fa;
    border-radius: 12px;
    max-height: calc(100vh - 300px);
}

.kanban-column-header {
    padding: 1rem;
    border-radius: 12px 12px 0 0;
    color: white;
    font-size: 0.95rem;
}

.kanban-header-available { background: linear-gradient(135deg, #6c757d, #495057); }
.kanban-header-my-tickets { background: linear-gradient(135deg, #0d6efd, #0a58ca); }
.kanban-header-progress { background: linear-gradient(135deg, #ffc107, #ff9800); }
.kanban-header-waiting { background: linear-gradient(135deg, #6c757d, #5a6268); }
.kanban-header-resolved { background: linear-gradient(135deg, #198754, #146c43); }

.kanban-column-body {
    flex: 1;
    padding: 1rem;
    overflow-y: auto;
    min-height: 200px;
}

.kanban-column-body.drag-over {
    background: rgba(13, 110, 253, 0.1);
    border: 2px dashed #0d6efd;
}

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.3;
}

.empty-state p {
    margin: 0;
    font-size: 0.9rem;
}

/* Scrollbar customizado */
.kanban-column-body::-webkit-scrollbar {
    width: 6px;
}

.kanban-column-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.kanban-column-body::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.kanban-column-body::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Loading overlay durante refresh */
.kanban-column-body.loading {
    position: relative;
}

.kanban-column-body.loading::after {
    content: '';
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,0.6);
    z-index: 5;
}

.kanban-column-body.loading::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    border: 0.3rem solid #0d6efd;
    border-top-color: transparent;
    transform: translate(-50%, -50%);
    animation: kanban-spin 0.8s linear infinite;
    z-index: 6;
}

@keyframes kanban-spin {
    from { transform: translate(-50%, -50%) rotate(0); }
    to { transform: translate(-50%, -50%) rotate(360deg); }
}

/* Responsivo */
@media (max-width: 1400px) {
    .kanban-column {
        flex: 0 0 280px;
        min-width: 280px;
    }
}

@media (max-width: 768px) {
    .kanban-board {
        flex-direction: column;
    }
    
    .kanban-column {
        flex: 1 1 auto;
        min-width: 100%;
        max-height: none;
    }
}
</style>

<script>
// Drag and Drop functionality
class KanbanBoard {
    constructor() {
        this.draggedCard = null;
        this.init();
    }

    init() {
        this.setupDragAndDrop();
        this.setupRefresh();
    }

    setupDragAndDrop() {
        // Todas as colunas que aceitam drop
        const columns = document.querySelectorAll('.kanban-column-body');
        
        columns.forEach(column => {
            column.addEventListener('dragover', (e) => this.handleDragOver(e));
            column.addEventListener('drop', (e) => this.handleDrop(e));
            column.addEventListener('dragleave', (e) => this.handleDragLeave(e));
        });

        // Setup inicial dos cards
        this.setupCards();
    }

    setupCards() {
        const cards = document.querySelectorAll('.ticket-card');
        cards.forEach(card => {
            card.setAttribute('draggable', true);
            card.addEventListener('dragstart', (e) => this.handleDragStart(e));
            card.addEventListener('dragend', (e) => this.handleDragEnd(e));
        });
    }

    handleDragStart(e) {
        this.draggedCard = e.currentTarget;
        e.currentTarget.style.opacity = '0.5';
        e.dataTransfer.effectAllowed = 'move';
    }

    handleDragEnd(e) {
        e.currentTarget.style.opacity = '1';
    }

    handleDragOver(e) {
        if (e.preventDefault) {
            e.preventDefault();
        }
        e.dataTransfer.dropEffect = 'move';
        e.currentTarget.classList.add('drag-over');
        return false;
    }

    handleDragLeave(e) {
        e.currentTarget.classList.remove('drag-over');
    }

    async handleDrop(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
        }

        e.currentTarget.classList.remove('drag-over');

        if (!this.draggedCard) return false;

        const targetColumn = e.currentTarget;
        const sourceColumn = this.draggedCard.parentElement;
        const targetStatus = targetColumn.dataset.status;
        const sourceStatus = sourceColumn.dataset.status;

        // Se soltou na mesma coluna, não faz nada
        if (targetStatus === sourceStatus) {
            return false;
        }

        const ticketId = this.draggedCard.dataset.ticketId;

        // Mostrar loading
        this.showLoading(this.draggedCard);

        try {
            let response;

            // Lógica baseada na coluna destino
            if (targetStatus === 'my_tickets' || targetStatus === 'in_progress') {
                // Atribuir ao técnico
                response = await this.assignTicket(ticketId);
            } else if (targetStatus === 'available') {
                // Desatribuir
                response = await this.unassignTicket(ticketId);
            } else {
                // Atualizar status
                response = await this.updateStatus(ticketId, targetStatus);
            }

            if (response.success) {
                // Mover o card visualmente
                this.moveCard(this.draggedCard, targetColumn);
                this.showSuccess(response.message);
                
                // Atualizar badges
                this.updateBadges();
            } else {
                this.showError(response.message);
            }

        } catch (error) {
            console.error('Erro:', error);
            this.showError('Erro ao mover o chamado. Tente novamente.');
        } finally {
            this.hideLoading(this.draggedCard);
        }

        return false;
    }

    moveCard(card, targetColumn) {
        // Remove empty state se existir
        const emptyState = targetColumn.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }

        // Move o card
        targetColumn.appendChild(card);

        // Adiciona empty state na coluna de origem se ficou vazia
        const sourceColumn = card.parentElement;
        if (sourceColumn.querySelectorAll('.ticket-card').length === 0) {
            this.addEmptyState(sourceColumn);
        }
    }

    addEmptyState(column) {
        const emptyState = document.createElement('div');
        emptyState.className = 'empty-state';
        emptyState.innerHTML = '<i class="bi bi-inbox"></i><p>Nenhum chamado</p>';
        column.appendChild(emptyState);
    }

    async assignTicket(ticketId) {
        const response = await fetch('{{ route("technician.assign") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ticket_id: ticketId })
        });
        return await response.json();
    }

    async unassignTicket(ticketId) {
        const response = await fetch('{{ route("technician.unassign") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ticket_id: ticketId })
        });
        return await response.json();
    }

    async updateStatus(ticketId, status) {
        const response = await fetch('{{ route("technician.update-status") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ ticket_id: ticketId, status: status })
        });
        return await response.json();
    }

    updateBadges() {
        document.querySelectorAll('.kanban-column').forEach(column => {
            const badge = column.querySelector('.badge');
            const count = column.querySelectorAll('.ticket-card').length;
            badge.textContent = count;
        });
    }

    showLoading(card) {
        card.style.opacity = '0.5';
        card.style.pointerEvents = 'none';
    }

    hideLoading(card) {
        card.style.opacity = '1';
        card.style.pointerEvents = 'auto';
    }

    showSuccess(message) {
        // Toast notification
        this.showToast(message, 'success');
    }

    showError(message) {
        this.showToast(message, 'danger');
    }

    showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
        toast.style.zIndex = '9999';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    setupRefresh() {
    const btn = document.getElementById('refreshBtn');
    const columnBodies = () => document.querySelectorAll('.kanban-column-body');
        const lastUpdated = document.getElementById('lastUpdated');
        const updateTimestamp = () => {
            const ts = new Date();
            if (lastUpdated) {
                try {
                    lastUpdated.textContent = 'Atualizado às ' + ts.toLocaleTimeString('pt-BR', { hour12: false });
                } catch (_) {
                    lastUpdated.textContent = 'Atualizado às ' + ts.getHours().toString().padStart(2,'0') + ':' + ts.getMinutes().toString().padStart(2,'0') + ':' + ts.getSeconds().toString().padStart(2,'0');
                }
            }
        };
        const doRefresh = async (silent = false) => {
            try {
                if (!silent && btn) { btn.disabled = true; btn.innerHTML = '<i class="bi bi-arrow-repeat"></i> Atualizando...'; }
        // Mostrar overlay de loading em todas as colunas
        columnBodies().forEach(el => el.classList.add('loading'));
                const res = await fetch('{{ route('technician.refresh') }}', { headers: { 'X-Requested-With': 'XMLHttpRequest' }});
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Falha ao atualizar');

                // Atualiza cada coluna substituindo o conteúdo por novo HTML (evita duplicação)
                const map = {
                    available: 'available-column',
                    my_tickets: 'my-tickets-column',
                    in_progress: 'in-progress-column',
                    waiting: 'waiting-column',
                    resolved: 'resolved-column'
                };
                Object.keys(map).forEach(key => {
                    const el = document.getElementById(map[key]);
                    if (el && data.columns[key]) {
                        el.innerHTML = data.columns[key].html;
                    }
                });

                // Atualiza badges de contagem
                const columnOrder = ['available','my_tickets','in_progress','waiting','resolved'];
                const columnElems = document.querySelectorAll('.kanban-column');
                columnElems.forEach((col, idx) => {
                    const badge = col.querySelector('.badge');
                    const key = columnOrder[idx];
                    if (badge && data.columns[key]) {
                        badge.textContent = data.columns[key].count;
                    }
                });

                // Reconfigura drag and drop nos novos cards
                this.setupCards();
                updateTimestamp();
            } catch (e) {
                console.error(e);
                this.showError('Não foi possível atualizar o quadro agora.');
            } finally {
                if (btn) { btn.disabled = false; btn.innerHTML = '<i class="bi bi-arrow-clockwise"></i> Atualizar'; }
                // Remover overlay de loading
                columnBodies().forEach(el => el.classList.remove('loading'));
            }
        };

        if (btn) btn.addEventListener('click', () => doRefresh(false));
        // Auto refresh a cada 30s, silencioso
        this._autoInterval = setInterval(() => doRefresh(true), 30000);
        // Atualiza timestamp inicial ao carregar
        updateTimestamp();
    }
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    new KanbanBoard();
});
</script>
@endsection
