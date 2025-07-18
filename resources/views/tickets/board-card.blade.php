<div class="ticket-card priority-{{ $ticket->priority }}" data-ticket-id="{{ $ticket->id }}">
    <div class="ticket-title">
        #{{ $ticket->id }} - {{ Str::limit($ticket->title, 50) }}
    </div>
    <div class="ticket-description">
        <strong>Descrição do Problema:</strong>
        <p>{{ Str::limit($ticket->description, 120) }}</p>
    </div>
    <div class="ticket-meta">
        <div class="mb-1">
            <span class="ticket-category">
                {{ $ticket->category ? $ticket->category->name : 'Sem categoria' }}
            </span>
        </div>
        <div><strong>Solicitante:</strong> {{ $ticket->user->name }}</div>
        <div><strong>Atribuído:</strong> {{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Não atribuído' }}</div>
        <div><strong>Prioridade:</strong> 
            @switch($ticket->priority)
                @case('high')
                    <span class="badge bg-danger">Alta</span>
                    @break
                @case('medium')
                    <span class="badge bg-warning">Média</span>
                    @break
                @case('low')
                    <span class="badge bg-success">Baixa</span>
                    @break
            @endswitch
        </div>
        <div><strong>Criado:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
    </div>
</div>
