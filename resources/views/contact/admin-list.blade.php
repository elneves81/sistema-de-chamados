@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">
            <i class="bi bi-chat-dots"></i> Mensagens de Contato
            @php
                // Contar apenas mensagens pendentes (não respondidas)
                $pendingUnreplied = $messages->filter(function($msg) {
                    return $msg->status === 'pendente' && is_null($msg->responded_at);
                })->count();
            @endphp
            @if($pendingUnreplied > 0)
                <span class="badge bg-danger ms-2">{{ $pendingUnreplied }}</span>
            @endif
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                    <i class="bi bi-arrow-clockwise"></i> Atualizar
                </button>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filtros -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <select class="form-select" id="filterStatus">
                                <option value="">Todos os Status</option>
                                <option value="pendente">Pendente</option>
                                <option value="em_andamento">Em Andamento</option>
                                <option value="resolvido">Resolvido</option>
                                <option value="arquivado">Arquivado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterReplied">
                                <option value="">Todas</option>
                                <option value="unreplied">🔴 Não Respondidas</option>
                                <option value="replied">✅ Respondidas</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <select class="form-select" id="filterType">
                                <option value="">Todos os Tipos</option>
                                <option value="emergencia">🚨 Emergência</option>
                                <option value="suporte">🔧 Suporte</option>
                                <option value="duvida">❓ Dúvida</option>
                                <option value="sugestao">💡 Sugestão</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <input type="text" class="form-control" id="searchInput" placeholder="Buscar por nome, email ou assunto...">
                        </div>
                        <div class="col-md-2">
                            <button class="btn btn-primary w-100" onclick="applyFilters()">
                                <i class="bi bi-funnel"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Mensagens -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if($messages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Status</th>
                                        <th>Tipo</th>
                                        <th>Remetente</th>
                                        <th>Assunto</th>
                                        <th>Data</th>
                                        <th>Responsável</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($messages as $message)
                                        <tr class="message-row {{ $message->status === 'pendente' && is_null($message->responded_at) ? 'table-warning' : '' }}" 
                                            data-status="{{ $message->status }}" 
                                            data-type="{{ $message->type }}"
                                            data-replied="{{ $message->responded_at ? 'true' : 'false' }}"
                                            data-search="{{ strtolower($message->name . ' ' . $message->email . ' ' . $message->subject) }}">
                                            <td>
                                                @if($message->responded_at)
                                                    <span class="badge bg-{{ $message->status === 'resolvido' ? 'success' : 'info' }}">
                                                        <i class="bi bi-check-circle"></i> {{ $message->getStatusLabel() }}
                                                    </span>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="bi bi-exclamation-circle"></i> {{ $message->getStatusLabel() }}
                                                    </span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ $message->priority_badge }}">
                                                    {{ $message->getTypeLabel() }}
                                                </span>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $message->name }}</strong><br>
                                                    <small class="text-muted">{{ $message->email }}</small>
                                                    @if($message->user)
                                                        <br><small class="text-info">👤 Usuário logado</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="text-truncate" style="max-width: 300px;">
                                                    {{ $message->subject }}
                                                </div>
                                                <small class="text-muted">
                                                    {{ Str::limit($message->message, 50) }}
                                                </small>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $message->created_at->format('d/m/Y H:i') }}<br>
                                                    <span class="text-muted">{{ $message->created_at->diffForHumans() }}</span>
                                                </small>
                                            </td>
                                            <td>
                                                @if($message->assignedTo)
                                                    <small>
                                                        <i class="bi bi-person-check"></i>
                                                        {{ $message->assignedTo->name }}
                                                    </small>
                                                @else
                                                    <small class="text-muted">Não atribuído</small>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.contact.show', $message) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($message->status !== 'resolvido')
                                                        <button class="btn btn-outline-success btn-sm" 
                                                                onclick="quickResolve({{ $message->id }})">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Paginação -->
                        <div class="d-flex justify-content-center">
                            {{ $messages->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-chat-dots display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">Nenhuma mensagem encontrada</h4>
                            <p class="text-muted">Ainda não há mensagens de contato.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function applyFilters() {
    const status = document.getElementById('filterStatus').value;
    const type = document.getElementById('filterType').value;
    const replied = document.getElementById('filterReplied').value;
    const search = document.getElementById('searchInput').value.toLowerCase();
    
    const rows = document.querySelectorAll('.message-row');
    
    rows.forEach(row => {
        let show = true;
        
        // Filtro por status
        if (status && row.dataset.status !== status) {
            show = false;
        }
        
        // Filtro por tipo
        if (type && row.dataset.type !== type) {
            show = false;
        }
        
        // Filtro por respondidas/não respondidas
        if (replied === 'unreplied' && row.dataset.replied === 'true') {
            show = false;
        }
        if (replied === 'replied' && row.dataset.replied === 'false') {
            show = false;
        }
        
        // Filtro por busca
        if (search && !row.dataset.search.includes(search)) {
            show = false;
        }
        
        row.style.display = show ? '' : 'none';
    });
}

function quickResolve(messageId) {
    if (confirm('Marcar esta mensagem como resolvida?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/contact-messages/${messageId}/status`;
        
        form.innerHTML = `
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="resolvido">
            <input type="hidden" name="admin_notes" value="Resolvido rapidamente pelo admin">
        `;
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Aplicar filtros em tempo real
document.getElementById('searchInput').addEventListener('input', applyFilters);
document.getElementById('filterStatus').addEventListener('change', applyFilters);
document.getElementById('filterType').addEventListener('change', applyFilters);
document.getElementById('filterReplied').addEventListener('change', applyFilters);
</script>
@endsection
