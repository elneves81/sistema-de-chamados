@extends('layouts.app')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="container">
    <div class="row">
        <!-- Header -->
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">👤 {{ $user->name }}</h1>
                    <p class="text-muted">Detalhes completos do usuário</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.users.export-pdf', $user) }}" class="btn btn-outline-success">
                        <i class="bi bi-filetype-pdf"></i> Exportar PDF
                    </a>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>
        </div>

        <!-- Informações do Usuário -->
        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle"></i> Informações Pessoais
                    </h5>
                </div>
                <div class="card-body text-center">
                    <!-- Avatar -->
                    <div class="avatar-large mb-3">
                        <div class="avatar-circle-large">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    </div>
                    
                    <h4 class="mb-1">{{ $user->name }}</h4>
                    <p class="text-muted mb-3">{{ $user->email }}</p>
                    
                    <!-- Role Badge -->
                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'technician' ? 'warning' : 'info') }} mb-3">
                        @if($user->role === 'admin')
                            <i class="bi bi-shield-check"></i> Administrador
                        @elseif($user->role === 'technician')
                            <i class="bi bi-gear"></i> Técnico
                        @else
                            <i class="bi bi-person"></i> Cliente
                        @endif
                    </span>
                    
                    <!-- Contact Info -->
                    <div class="mt-3">
                        <p class="mb-1">
                            <i class="bi bi-person-badge text-primary"></i>
                            <strong>Username:</strong> {{ $user->username }}
                        </p>
                        @if($user->phone)
                        <p class="mb-1">
                            <i class="bi bi-telephone text-primary"></i> 
                            <strong>Telefone:</strong> {{ $user->phone }}
                        </p>
                        @endif
                        
                        @if($user->department)
                        <p class="mb-1">
                            <i class="bi bi-building text-primary"></i> 
                            <strong>Departamento:</strong> {{ $user->department }}
                        </p>
                        @endif
                        
                        <p class="mb-0">
                            <i class="bi bi-calendar text-primary"></i> 
                            <strong>Cadastrado em:</strong> {{ $user->created_at->format('d/m/Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Status Card -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-activity"></i> Status da Conta
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Status:</span>
                        <span class="badge bg-success">Ativo</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span>Último acesso:</span>
                        <small class="text-muted">{{ $user->updated_at->format('d/m/Y H:i') }}</small>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span>Tempo no sistema:</span>
                        <small class="text-muted">{{ $user->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas e Tickets -->
        <div class="col-lg-8">
            <!-- Estatísticas -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-ticket fs-1 mb-2"></i>
                            <h3 class="mb-0">{{ $user->tickets()->count() }}</h3>
                            <small>Tickets Criados</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-check-circle fs-1 mb-2"></i>
                            <h3 class="mb-0">{{ $user->assignedTickets()->count() }}</h3>
                            <small>Tickets Atribuídos</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-clock fs-1 mb-2"></i>
                            <h3 class="mb-0">{{ $user->assignedTickets()->whereIn('status', ['open', 'in_progress'])->count() }}</h3>
                            <small>Em Andamento</small>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body text-center">
                            <i class="bi bi-graph-up fs-1 mb-2"></i>
                            <h3 class="mb-0">{{ $user->assignedTickets()->where('status', 'resolved')->count() }}</h3>
                            <small>Resolvidos</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tickets Criados -->
            <div class="card mb-4">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-plus-circle"></i> Tickets Criados ({{ $user->tickets()->count() }})
                        </h5>
                        <a href="{{ route('tickets.index', ['user_id' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                            Ver Todos
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($user->tickets()->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Status</th>
                                    <th>Prioridade</th>
                                    <th>Categoria</th>
                                    <th>Criado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->tickets()->latest()->limit(5)->get() as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">
                                            #{{ $ticket->id }} - {{ Str::limit($ticket->title, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ticket->status_color }}">
                                            {{ $ticket->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $ticket->priority_color }}">
                                            {{ $ticket->priority_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $ticket->category->name }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $ticket->created_at->format('d/m/Y') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-ticket fs-2"></i>
                        <p>Nenhum ticket criado ainda</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Tickets Atribuídos (apenas para técnicos) -->
            @if($user->role === 'technician' || $user->role === 'admin')
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="bi bi-gear"></i> Tickets Atribuídos ({{ $user->assignedTickets()->count() }})
                        </h5>
                        <a href="{{ route('tickets.index', ['assigned_to' => $user->id]) }}" class="btn btn-sm btn-outline-primary">
                            Ver Todos
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if($user->assignedTickets()->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Status</th>
                                    <th>Prioridade</th>
                                    <th>Cliente</th>
                                    <th>Atualizado em</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($user->assignedTickets()->latest()->limit(5)->get() as $ticket)
                                <tr>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}" class="text-decoration-none">
                                            #{{ $ticket->id }} - {{ Str::limit($ticket->title, 30) }}
                                        </a>
                                    </td>
                                    <td>
                                        <span class="badge {{ $ticket->status_color }}">
                                            {{ $ticket->status_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="{{ $ticket->priority_color }}">
                                            {{ $ticket->priority_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <small>{{ $ticket->user->name }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $ticket->updated_at->format('d/m/Y') }}</small>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center text-muted py-3">
                        <i class="bi bi-gear fs-2"></i>
                        <p>Nenhum ticket atribuído ainda</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.avatar-circle-large {
    width: 100px;
    height: 100px;
    border-radius: 50%;
    background: linear-gradient(45deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 36px;
    margin: 0 auto;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.table td {
    vertical-align: middle;
}

.bg-primary { background-color: #007bff !important; }
.bg-success { background-color: #28a745 !important; }
.bg-warning { background-color: #ffc107 !important; }
.bg-info { background-color: #17a2b8 !important; }
</style>
@endsection
