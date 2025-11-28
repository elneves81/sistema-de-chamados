<div class="ticket-card" draggable="true" data-ticket-id="{{ $ticket->id }}">
    <!-- Priority indicator -->
    <div class="priority-indicator priority-{{ $ticket->priority }}"></div>
    
    <div class="card-header-custom">
        <div class="d-flex justify-content-between align-items-start mb-2">
            <span class="ticket-id">#{{ $ticket->id }}</span>
            <span class="badge priority-badge priority-{{ $ticket->priority }}">
                @if($ticket->priority === 'urgent')
                    <i class="bi bi-exclamation-triangle-fill"></i> Urgente
                @elseif($ticket->priority === 'high')
                    <i class="bi bi-exclamation-circle"></i> Alta
                @elseif($ticket->priority === 'medium')
                    <i class="bi bi-dash-circle"></i> Média
                @else
                    <i class="bi bi-circle"></i> Baixa
                @endif
            </span>
        </div>
        
        <h6 class="ticket-title">{{ Str::limit($ticket->title, 50) }}</h6>
    </div>

    <div class="card-body-custom">
        <!-- Category -->
        <div class="ticket-info">
            <i class="bi bi-tag"></i>
            <span>{{ $ticket->category->name ?? 'Sem categoria' }}</span>
        </div>

        <!-- User -->
        <div class="ticket-info">
            <i class="bi bi-person"></i>
            <span>{{ $ticket->user->name ?? 'N/A' }}</span>
        </div>

        <!-- Location -->
        @if($ticket->location)
        <div class="ticket-info">
            <i class="bi bi-geo-alt"></i>
            <span>{{ Str::limit($ticket->location->name, 30) }}</span>
        </div>
        @elseif($ticket->local)
        <div class="ticket-info">
            <i class="bi bi-geo-alt"></i>
            <span>{{ Str::limit($ticket->local, 30) }}</span>
        </div>
        @endif

        <!-- Assigned to -->
        @if($ticket->assignedUser)
        <div class="ticket-info assigned-to">
            <i class="bi bi-person-check-fill"></i>
            <span>{{ $ticket->assignedUser->name }}</span>
        </div>
        @endif

        <!-- Created time -->
        <div class="ticket-info time-info">
            <i class="bi bi-clock"></i>
            <span>{{ $ticket->created_at->diffForHumans() }}</span>
        </div>

        @if(!empty($ticket->attachments))
        <div class="ticket-info">
            <i class="bi bi-paperclip"></i>
            <span>{{ is_array($ticket->attachments) ? count($ticket->attachments) : 0 }} anexo(s)</span>
        </div>
        @endif
    </div>

    <div class="card-footer-custom">
        <a href="{{ route('tickets.show', $ticket->id) }}" class="btn btn-sm btn-outline-primary w-100" target="_blank">
            <i class="bi bi-eye"></i> Ver Detalhes
        </a>
    </div>
</div>

<style>
.ticket-card {
    background: white;
    border-radius: 10px;
    padding: 0;
    margin-bottom: 0.75rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    cursor: move;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.ticket-card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    transform: translateY(-2px);
}

.ticket-card.dragging {
    opacity: 0.5;
    transform: rotate(3deg);
}

.priority-indicator {
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
}

.priority-indicator.priority-urgent { background: #dc3545; }
.priority-indicator.priority-high { background: #fd7e14; }
.priority-indicator.priority-medium { background: #ffc107; }
.priority-indicator.priority-low { background: #28a745; }

.card-header-custom {
    padding: 1rem 1rem 0.5rem 1.25rem;
}

.ticket-id {
    font-size: 0.75rem;
    font-weight: 700;
    color: #6c757d;
    letter-spacing: 0.5px;
}

.ticket-title {
    margin: 0;
    font-size: 0.95rem;
    font-weight: 600;
    color: #212529;
    line-height: 1.3;
}

.priority-badge {
    font-size: 0.7rem;
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-weight: 600;
}

.priority-badge.priority-urgent {
    background: rgba(220, 53, 69, 0.1);
    color: #dc3545;
}

.priority-badge.priority-high {
    background: rgba(253, 126, 20, 0.1);
    color: #fd7e14;
}

.priority-badge.priority-medium {
    background: rgba(255, 193, 7, 0.1);
    color: #856404;
}

.priority-badge.priority-low {
    background: rgba(40, 167, 69, 0.1);
    color: #28a745;
}

.card-body-custom {
    padding: 0.75rem 1rem 0.75rem 1.25rem;
}

.ticket-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
    color: #6c757d;
}

.ticket-info:last-child {
    margin-bottom: 0;
}

.ticket-info i {
    width: 16px;
    font-size: 0.9rem;
}

.ticket-info.assigned-to {
    color: #0d6efd;
    font-weight: 500;
}

.ticket-info.time-info {
    color: #adb5bd;
    font-size: 0.8rem;
}

.card-footer-custom {
    padding: 0.75rem 1rem 1rem 1.25rem;
    border-top: 1px solid #f1f3f5;
}

.card-footer-custom .btn {
    font-size: 0.85rem;
    padding: 0.4rem 0.5rem;
}
</style>
