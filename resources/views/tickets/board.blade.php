@extends('layouts.app')

@section('title', 'Painel de Tickets')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-tachometer-alt me-2"></i>
            Painel Visual de Tickets
        </h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tickets.board.tv') }}" class="btn btn-outline-success">
                <i class="fas fa-tv me-2"></i> Modo TV
            </a>
            <button id="soundToggle" class="btn btn-outline-primary">
                <i class="fas fa-volume-up"></i> Som: ON
            </button>
            <button id="refreshBoard" class="btn btn-outline-secondary">
                <i class="fas fa-sync-alt"></i> Atualizar
            </button>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Abertos</h5>
                            <h3 id="open-count">{{ $ticketsByStatus['open']->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-exclamation-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Em Andamento</h5>
                            <h3 id="progress-count">{{ $ticketsByStatus['in_progress']->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Resolvidos</h5>
                            <h3 id="resolved-count">{{ $ticketsByStatus['resolved']->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Fechados</h5>
                            <h3 id="closed-count">{{ $ticketsByStatus['closed']->count() }}</h3>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Painel de Tickets -->
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        Abertos
                    </h5>
                </div>
                <div class="card-body p-2" style="max-height: 600px; overflow-y: auto;">
                    <div id="open-tickets">
                        @foreach($ticketsByStatus['open'] as $ticket)
                            @include('tickets.board-card', ['ticket' => $ticket])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Em Andamento
                    </h5>
                </div>
                <div class="card-body p-2" style="max-height: 600px; overflow-y: auto;">
                    <div id="progress-tickets">
                        @foreach($ticketsByStatus['in_progress'] as $ticket)
                            @include('tickets.board-card', ['ticket' => $ticket])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle me-2"></i>
                        Resolvidos
                    </h5>
                </div>
                <div class="card-body p-2" style="max-height: 600px; overflow-y: auto;">
                    <div id="resolved-tickets">
                        @foreach($ticketsByStatus['resolved'] as $ticket)
                            @include('tickets.board-card', ['ticket' => $ticket])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-times-circle me-2"></i>
                        Fechados
                    </h5>
                </div>
                <div class="card-body p-2" style="max-height: 600px; overflow-y: auto;">
                    <div id="closed-tickets">
                        @foreach($ticketsByStatus['closed'] as $ticket)
                            @include('tickets.board-card', ['ticket' => $ticket])
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audio element for notifications -->
<audio id="notificationSound" preload="auto">
    <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
    <source src="{{ asset('sounds/notification.ogg') }}" type="audio/ogg">
    <source src="{{ asset('sounds/notification.wav') }}" type="audio/wav">
</audio>

@endsection

@push('styles')
<style>
    .ticket-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 10px;
        padding: 10px;
        background: white;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .ticket-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .ticket-card.new-ticket {
        animation: newTicketPulse 2s infinite;
        border-color: #007bff;
    }
    
    @keyframes newTicketPulse {
        0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(0, 123, 255, 0); }
        100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
    }
    
    .priority-high {
        border-left: 4px solid #dc3545;
    }
    
    .priority-medium {
        border-left: 4px solid #ffc107;
    }
    
    .priority-low {
        border-left: 4px solid #28a745;
    }
    
    .ticket-title {
        font-weight: bold;
        font-size: 0.9em;
        margin-bottom: 5px;
        color: #2c3e50;
    }
    
    .ticket-description {
        font-size: 0.8em;
        color: #555;
        margin-bottom: 10px;
        background-color: #f8f9fa;
        padding: 8px;
        border-radius: 4px;
        border-left: 3px solid #007bff;
    }
    
    .ticket-description p {
        margin: 3px 0 0 0;
        line-height: 1.3;
        font-style: italic;
    }
    
    .ticket-meta {
        font-size: 0.8em;
        color: #666;
    }
    
    .ticket-category {
        background: #e9ecef;
        padding: 2px 6px;
        border-radius: 12px;
        font-size: 0.7em;
        display: inline-block;
    }
</style>
@endpush

@push('scripts')
<script>
let lastCheck = new Date().toISOString();
let soundEnabled = true;
let refreshInterval;

$(document).ready(function() {
    // Iniciar polling para novos tickets
    startTicketPolling();
    
    // Toggle do som
    $('#soundToggle').click(function() {
        soundEnabled = !soundEnabled;
        $(this).html(soundEnabled ? 
            '<i class="fas fa-volume-up"></i> Som: ON' : 
            '<i class="fas fa-volume-off"></i> Som: OFF'
        );
        $(this).toggleClass('btn-outline-primary btn-outline-secondary');
    });
    
    // Refresh manual
    $('#refreshBoard').click(function() {
        refreshBoard();
    });
    
    // Click no ticket para abrir
    $(document).on('click', '.ticket-card', function() {
        let ticketId = $(this).data('ticket-id');
        window.openTicket(ticketId);
    });
});

function startTicketPolling() {
    // Atualizar a cada 30 segundos
    refreshInterval = setInterval(function() {
        checkForNewTickets();
    }, 30000);
}

function checkForNewTickets() {
    $.ajax({
        url: '{{ route("api.tickets.new") }}',
        method: 'GET',
        data: { last_check: lastCheck },
        success: function(response) {
            if (response.count > 0) {
                // Reproduzir som se habilitado
                if (soundEnabled) {
                    playNotificationSound();
                }
                
                // Atualizar o painel
                refreshBoard();
                
                // Mostrar notificação
                showNotification(`${response.count} novo(s) ticket(s) recebido(s)!`);
            }
            
            lastCheck = response.last_check;
        },
        error: function(xhr, status, error) {
            console.error('Erro ao verificar novos tickets:', error);
        }
    });
}

function refreshBoard() {
    $.ajax({
        url: '{{ route("api.tickets.all") }}',
        method: 'GET',
        success: function(response) {
            updateTicketColumns(response.tickets);
            updateCounters(response.tickets);
            lastCheck = response.last_check;
        },
        error: function(xhr, status, error) {
            console.error('Erro ao atualizar painel:', error);
        }
    });
}

function updateTicketColumns(tickets) {
    // Atualizar cada coluna
    Object.keys(tickets).forEach(function(status) {
        let columnId = status.replace('_', '-') + '-tickets';
        let column = $('#' + columnId);
        
        column.empty();
        
        tickets[status].forEach(function(ticket) {
            let card = createTicketCard(ticket);
            column.append(card);
        });
    });
}

function updateCounters(tickets) {
    $('#open-count').text(tickets.open.length);
    $('#progress-count').text(tickets.in_progress.length);
    $('#resolved-count').text(tickets.resolved.length);
    $('#closed-count').text(tickets.closed.length);
}

function createTicketCard(ticket) {
    let priorityClass = 'priority-' + ticket.priority;
    let categoryName = ticket.category ? ticket.category.name : 'Sem categoria';
    let assignedTo = ticket.assigned_to ? ticket.assigned_to.name : 'Não atribuído';
    
    return `
        <div class="ticket-card ${priorityClass}" data-ticket-id="${ticket.id}">
            <div class="ticket-title">#${ticket.id} - ${ticket.title}</div>
            <div class="ticket-meta">
                <div class="mb-1">
                    <span class="ticket-category">${categoryName}</span>
                </div>
                <div><strong>Solicitante:</strong> ${ticket.user.name}</div>
                <div><strong>Atribuído:</strong> ${assignedTo}</div>
                <div><strong>Prioridade:</strong> ${ticket.priority}</div>
                <div><strong>Criado:</strong> ${new Date(ticket.created_at).toLocaleString()}</div>
            </div>
        </div>
    `;
}

function playNotificationSound() {
    try {
        // Tentar reproduzir arquivo de áudio primeiro
        let audio = document.getElementById('notificationSound');
        if (audio) {
            audio.play().catch(function(error) {
                console.log('Erro ao reproduzir arquivo de áudio:', error);
                // Fallback para beep gerado
                createBeepSound();
            });
        } else {
            // Fallback para beep gerado
            createBeepSound();
        }
    } catch (error) {
        console.log('Som não disponível:', error);
        // Fallback para beep gerado
        createBeepSound();
    }
}

// Função para gerar um beep usando Web Audio API
function createBeepSound() {
    try {
        const audioContext = new (window.AudioContext || window.webkitAudioContext)();
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.value = 800; // Frequência do beep
        oscillator.type = 'sine';
        
        gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
        gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.5);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.5);
        
        return true;
    } catch (error) {
        console.error('Erro ao criar beep:', error);
        return false;
    }
}

function showNotification(message) {
    // Verificar se o navegador suporta notificações
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Sistema de Tickets', {
            body: message,
            icon: '/favicon.ico'
        });
    } else {
        // Fallback para toast
        alert(message);
    }
}

// Solicitar permissão para notificações
if ('Notification' in window && Notification.permission === 'default') {
    Notification.requestPermission();
}
</script>
@endpush
