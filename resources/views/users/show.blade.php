@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informações do Usuário</h3>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <div class="avatar avatar-xl mb-3">
                            <span class="avatar-initial rounded-circle bg-primary">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </span>
                        </div>
                        <h4>{{ $user->name }}</h4>
                        <p class="text-muted">
                            @php
                                $roleText = [
                                    'admin' => 'Administrador',
                                    'technician' => 'Técnico',
                                    'customer' => 'Cliente'
                                ][$user->role] ?? 'Desconhecido';
                            @endphp
                            {{ $roleText }}
                        </p>
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Email:</span>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Telefone:</span>
                            <span>{{ $user->phone ?: 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Departamento:</span>
                            <span>{{ $user->department ?: 'N/A' }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Status:</span>
                            <span>
                                @if($user->trashed())
                                    <span class="badge bg-danger">Inativo</span>
                                @else
                                    <span class="badge bg-success">Ativo</span>
                                @endif
                            </span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Cadastrado em:</span>
                            <span>{{ $user->created_at->format('d/m/Y') }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <span>Último login:</span>
                            <span>{{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}</span>
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-between">
                        <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Chamados Criados</h3>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        @if($user->tickets->count() > 0)
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Título</th>
                                        <th>Status</th>
                                        <th>Prioridade</th>
                                        <th>Data</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($user->tickets as $ticket)
                                        <tr>
                                            <td>{{ $ticket->id }}</td>
                                            <td>{{ $ticket->title }}</td>
                                            <td>
                                                @php
                                                    $statusClass = [
                                                        'open' => 'primary',
                                                        'in_progress' => 'info',
                                                        'pending' => 'warning',
                                                        'resolved' => 'success',
                                                        'closed' => 'secondary'
                                                    ][$ticket->status] ?? 'secondary';
                                                    
                                                    $statusText = [
                                                        'open' => 'Aberto',
                                                        'in_progress' => 'Em Progresso',
                                                        'pending' => 'Pendente',
                                                        'resolved' => 'Resolvido',
                                                        'closed' => 'Fechado'
                                                    ][$ticket->status] ?? 'Desconhecido';
                                                @endphp
                                                <span class="badge bg-{{ $statusClass }}">
                                                    {{ $statusText }}
                                                </span>
                                            </td>
                                            <td>
                                                @php
                                                    $priorityClass = [
                                                        'low' => 'success',
                                                        'normal' => 'info',
                                                        'high' => 'warning',
                                                        'critical' => 'danger'
                                                    ][$ticket->priority] ?? 'secondary';
                                                    
                                                    $priorityText = [
                                                        'low' => 'Baixa',
                                                        'normal' => 'Normal',
                                                        'high' => 'Alta',
                                                        'critical' => 'Crítica'
                                                    ][$ticket->priority] ?? 'Desconhecido';
                                                @endphp
                                                <span class="badge bg-{{ $priorityClass }}">
                                                    {{ $priorityText }}
                                                </span>
                                            </td>
                                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                            <td>
                                                <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="text-muted">Este usuário não criou nenhum chamado.</p>
                        @endif
                    </div>
                </div>
            </div>

            @if($user->role == 'technician' || $user->role == 'admin')
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Chamados Atribuídos</h3>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            @if($user->assignedTickets->count() > 0)
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Título</th>
                                            <th>Status</th>
                                            <th>Prioridade</th>
                                            <th>Criado por</th>
                                            <th>Data</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($user->assignedTickets as $ticket)
                                            <tr>
                                                <td>{{ $ticket->id }}</td>
                                                <td>{{ $ticket->title }}</td>
                                                <td>
                                                    @php
                                                        $statusClass = [
                                                            'open' => 'primary',
                                                            'in_progress' => 'info',
                                                            'pending' => 'warning',
                                                            'resolved' => 'success',
                                                            'closed' => 'secondary'
                                                        ][$ticket->status] ?? 'secondary';
                                                        
                                                        $statusText = [
                                                            'open' => 'Aberto',
                                                            'in_progress' => 'Em Progresso',
                                                            'pending' => 'Pendente',
                                                            'resolved' => 'Resolvido',
                                                            'closed' => 'Fechado'
                                                        ][$ticket->status] ?? 'Desconhecido';
                                                    @endphp
                                                    <span class="badge bg-{{ $statusClass }}">
                                                        {{ $statusText }}
                                                    </span>
                                                </td>
                                                <td>
                                                    @php
                                                        $priorityClass = [
                                                            'low' => 'success',
                                                            'normal' => 'info',
                                                            'high' => 'warning',
                                                            'critical' => 'danger'
                                                        ][$ticket->priority] ?? 'secondary';
                                                        
                                                        $priorityText = [
                                                            'low' => 'Baixa',
                                                            'normal' => 'Normal',
                                                            'high' => 'Alta',
                                                            'critical' => 'Crítica'
                                                        ][$ticket->priority] ?? 'Desconhecido';
                                                    @endphp
                                                    <span class="badge bg-{{ $priorityClass }}">
                                                        {{ $priorityText }}
                                                    </span>
                                                </td>
                                                <td>{{ $ticket->user->name ?? 'N/A' }}</td>
                                                <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                                                <td>
                                                    <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-info btn-sm">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p class="text-muted">Este usuário não tem chamados atribuídos.</p>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
