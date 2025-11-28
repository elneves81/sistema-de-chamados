@extends('layouts.app')

@section('title', 'Painel de Monitoramento')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">🔔 Painel de Monitoramento</h1>
                    <p class="text-muted">Monitoramento em tempo real dos tickets</p>
                </div>
                <div class="btn-group">
                    <button type="button" class="btn btn-outline-primary" id="soundToggle">
                        <i class="bi bi-volume-up" id="soundIcon"></i> Som: <span id="soundStatus">Ligado</span>
                    </button>
                    <button type="button" class="btn btn-outline-success" id="autoRefresh">
                        <i class="bi bi-arrow-clockwise" id="refreshIcon"></i> Auto: <span id="refreshStatus">Ligado</span>
                    </button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-people"></i> Usuários
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Cards -->
    <div class="row mb-4">
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-ticket fs-1 mb-2"></i>
                    <h3 class="mb-0" id="totalTickets">{{ $data['totalTickets'] }}</h3>
                    <small>Total de Tickets</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body text-center">
                    <i class="bi bi-clock fs-1 mb-2"></i>
                    <h3 class="mb-0" id="openTickets">{{ $data['openTickets'] }}</h3>
                    <small>Tickets Abertos</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <i class="bi bi-exclamation-triangle fs-1 mb-2"></i>
                    <h3 class="mb-0" id="urgentTickets">{{ $data['urgentTickets'] }}</h3>
                    <small>Urgentes</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-x fs-1 mb-2"></i>
                    <h3 class="mb-0" id="overdueTickets">{{ $data['overdueTickets'] }}</h3>
                    <small>Vencidos</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <i class="bi bi-people fs-1 mb-2"></i>
                    <h3 class="mb-0" id="totalUsers">{{ $data['totalUsers'] }}</h3>
                    <small>Total Usuários</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4 col-sm-6 mb-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <i class="bi bi-circle-fill text-success fs-1 mb-2"></i>
                    <h3 class="mb-0" id="onlineUsers">{{ $data['onlineUsers'] }}</h3>
                    <small>Online Agora</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Activity Feed -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-activity"></i> Feed de Atividades
                            <span class="badge bg-primary" id="newTicketsCount">0</span>
                        </h5>
                        <div>
                            <span class="text-muted">Última atualização: </span>
                            <span id="lastUpdate">{{ now()->format('H:i:s') }}</span>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0" style="max-height: 600px; overflow-y: auto;">
                    <div id="activityFeed">
                        <!-- Tickets will be loaded here -->
                        <div class="text-center p-4 text-muted">
                            <i class="bi bi-hourglass-split fs-2"></i>
                            <p>Carregando atividades...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-lg-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-lightning"></i> Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tickets.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Novo Ticket
                        </a>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-success">
                            <i class="bi bi-person-plus"></i> Novo Usuário
                        </a>
                        <a href="{{ route('categories.create') }}" class="btn btn-info">
                            <i class="bi bi-tag"></i> Nova Categoria
                        </a>
                        <a href="{{ route('admin.users.export') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-download"></i> Exportar Dados
                        </a>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-gear"></i> Status do Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Servidor:</span>
                        <span class="badge bg-success">Online</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Banco de Dados:</span>
                        <span class="badge bg-success">Conectado</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Notificações:</span>
                        <span class="badge bg-success">Ativas</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Uptime:</span>
                        <small class="text-muted" id="uptime">24h 30min</small>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-graph-up"></i> Estatísticas Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <h4 class="text-primary mb-0">{{ \App\Models\Ticket::whereDate('created_at', today())->count() }}</h4>
                            <small class="text-muted">Hoje</small>
                        </div>
                        <div class="col-6 mb-3">
                            <h4 class="text-success mb-0">{{ \App\Models\Ticket::where('created_at', '>=', now()->startOfWeek())->count() }}</h4>
                            <small class="text-muted">Esta Semana</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-warning mb-0">{{ \App\Models\Ticket::where('created_at', '>=', now()->startOfMonth())->count() }}</h4>
                            <small class="text-muted">Este Mês</small>
                        </div>
                        <div class="col-6">
                            <h4 class="text-info mb-0">{{ \App\Models\Ticket::where('status', 'resolved')->where('resolved_at', '>=', now()->startOfMonth())->count() }}</h4>
                            <small class="text-muted">Resolvidos</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Audio for notifications -->
<audio id="notificationSound" preload="auto">
    <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmEaBjaKzPHLdSgEJ3zN8N+PQg0Sk6rg6K1XFA1BmDo8" type="audio/wav">
</audio>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.activity-item {
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
    transition: background-color 0.3s;
}

.activity-item:hover {
    background-color: #f8f9fa;
}

.activity-item.new-ticket {
    border-left: 4px solid #007bff;
    background-color: #e7f3ff;
    animation: pulse 2s;
}

@keyframes pulse {
    0% { background-color: #e7f3ff; }
    50% { background-color: #cce7ff; }
    100% { background-color: #e7f3ff; }
}

.priority-urgent { color: #dc3545; }
.priority-high { color: #fd7e14; }
.priority-medium { color: #ffc107; }
.priority-low { color: #28a745; }

.status-open { background-color: #17a2b8; }
.status-in_progress { background-color: #ffc107; }
.status-waiting { background-color: #6c757d; }
.status-resolved { background-color: #28a745; }
.status-closed { background-color: #343a40; }
</style>

<script>
class RealtimeMonitoring {
    constructor() {
        this.lastTicketId = 0;
        this.soundEnabled = true;
        this.autoRefreshEnabled = true;
        this.refreshInterval = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.startAutoRefresh();
        this.loadInitialData();
    }

    setupEventListeners() {
        // Sound toggle
        document.getElementById('soundToggle').addEventListener('click', () => {
            this.toggleSound();
        });

        // Auto refresh toggle
        document.getElementById('autoRefresh').addEventListener('click', () => {
            this.toggleAutoRefresh();
        });
    }

    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
        const icon = document.getElementById('soundIcon');
        const status = document.getElementById('soundStatus');
        
        if (this.soundEnabled) {
            icon.className = 'bi bi-volume-up';
            status.textContent = 'Ligado';
        } else {
            icon.className = 'bi bi-volume-mute';
            status.textContent = 'Desligado';
        }
    }

    toggleAutoRefresh() {
        this.autoRefreshEnabled = !this.autoRefreshEnabled;
        const icon = document.getElementById('refreshIcon');
        const status = document.getElementById('refreshStatus');
        
        if (this.autoRefreshEnabled) {
            icon.className = 'bi bi-arrow-clockwise';
            status.textContent = 'Ligado';
            this.startAutoRefresh();
        } else {
            icon.className = 'bi bi-pause';
            status.textContent = 'Pausado';
            this.stopAutoRefresh();
        }
    }

    startAutoRefresh() {
        this.refreshInterval = setInterval(() => {
            this.fetchNewData();
        }, 5000); // Update every 5 seconds
    }

    stopAutoRefresh() {
        if (this.refreshInterval) {
            clearInterval(this.refreshInterval);
        }
    }

    loadInitialData() {
        this.fetchNewData();
    }

    async fetchNewData() {
        try {
            const response = await fetch(`{{ route('admin.api.monitoring.realtime') }}?last_ticket_id=${this.lastTicketId}`);
            const data = await response.json();

            this.updateStats(data.stats);
            this.updateLastTicketId(data.last_ticket_id);
            
            if (data.new_tickets && data.new_tickets.length > 0) {
                this.addNewTickets(data.new_tickets);
                this.playNotificationSound();
            }

            this.updateLastUpdateTime();
        } catch (error) {
            console.error('Error fetching realtime data:', error);
        }
    }

    updateStats(stats) {
        document.getElementById('totalTickets').textContent = stats.total;
        document.getElementById('openTickets').textContent = stats.open;
        document.getElementById('urgentTickets').textContent = stats.urgent;
        document.getElementById('overdueTickets').textContent = stats.overdue;
    }

    updateLastTicketId(lastTicketId) {
        if (lastTicketId > this.lastTicketId) {
            this.lastTicketId = lastTicketId;
        }
    }

    addNewTickets(tickets) {
        const feed = document.getElementById('activityFeed');
        const newTicketsCount = document.getElementById('newTicketsCount');
        
        tickets.forEach(ticket => {
            const ticketElement = this.createTicketElement(ticket);
            feed.insertAdjacentHTML('afterbegin', ticketElement);
        });

        // Update new tickets counter
        const currentCount = parseInt(newTicketsCount.textContent);
        newTicketsCount.textContent = currentCount + tickets.length;

        // Remove old items if too many
        const items = feed.querySelectorAll('.activity-item');
        if (items.length > 50) {
            for (let i = 50; i < items.length; i++) {
                items[i].remove();
            }
        }
    }

    createTicketElement(ticket) {
        const timeAgo = this.timeAgo(new Date(ticket.created_at));
        const priorityClass = `priority-${ticket.priority}`;
        const statusClass = `status-${ticket.status}`;

        return `
            <div class="activity-item new-ticket">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <strong class="me-2">#${ticket.id}</strong>
                            <span class="badge ${statusClass} me-2">${ticket.status_label}</span>
                            <span class="badge bg-light text-dark me-2">${ticket.category}</span>
                            <span class="${priorityClass} fw-bold">${ticket.priority_label}</span>
                        </div>
                        <h6 class="mb-1">
                            <a href="${ticket.url}" class="text-decoration-none">${ticket.title}</a>
                        </h6>
                        <small class="text-muted">
                            <i class="bi bi-person"></i> ${ticket.user_name} • 
                            <i class="bi bi-clock"></i> ${ticket.created_at}
                        </small>
                    </div>
                    <div class="text-end">
                        <span class="badge bg-primary">NOVO</span>
                    </div>
                </div>
            </div>
        `;
    }

    playNotificationSound() {
        if (this.soundEnabled) {
            const audio = document.getElementById('notificationSound');
            audio.play().catch(e => console.log('Could not play sound:', e));
        }
    }

    updateLastUpdateTime() {
        const now = new Date();
        document.getElementById('lastUpdate').textContent = now.toLocaleTimeString();
    }

    timeAgo(date) {
        const now = new Date();
        const diff = Math.floor((now - date) / 1000);

        if (diff < 60) return 'agora mesmo';
        if (diff < 3600) return `${Math.floor(diff / 60)}min atrás`;
        if (diff < 86400) return `${Math.floor(diff / 3600)}h atrás`;
        return `${Math.floor(diff / 86400)}d atrás`;
    }
}

// Initialize when page loads
document.addEventListener('DOMContentLoaded', function() {
    new RealtimeMonitoring();
});
</script>
@endsection
