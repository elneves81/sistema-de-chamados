@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-envelope"></i> Mensagens
        @if($unreadCount > 0)
        <span class="badge bg-danger ms-2">{{ $unreadCount }}</span>
        @endif
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        @if(in_array(auth()->user()->role, ['admin', 'technician']))
        <div class="btn-group me-2">
            <a href="{{ route('messages.compose') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Nova Mensagem
            </a>
        </div>
        @endif
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-secondary" id="markAllAsRead">
                <i class="bi bi-check2-all"></i> Marcar Todas como Lidas
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="location.reload()">
                <i class="bi bi-arrow-clockwise"></i> Atualizar
            </button>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <!-- Tabs de navegação -->
        <ul class="nav nav-tabs" id="messagesTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="received-tab" data-bs-toggle="tab" data-bs-target="#received" type="button" role="tab">
                    <i class="bi bi-inbox"></i> Recebidas 
                    @if($unreadCount > 0)
                    <span class="badge bg-danger ms-1">{{ $unreadCount }}</span>
                    @endif
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="sent-tab" data-bs-toggle="tab" data-bs-target="#sent" type="button" role="tab">
                    <i class="bi bi-send"></i> Enviadas
                </button>
            </li>
        </ul>

        <!-- Conteúdo das tabs -->
        <div class="tab-content" id="messagesTabContent">
            <!-- Mensagens Recebidas -->
            <div class="tab-pane fade show active" id="received" role="tabpanel">
                <div class="card">
                    <div class="card-body p-0">
                        @if($receivedMessages->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($receivedMessages as $message)
                            <div class="list-group-item list-group-item-action {{ !$message->is_read ? 'bg-light border-start border-primary border-3' : '' }}" 
                                 onclick="openMessage({{ $message->id }})" style="cursor: pointer;">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="message-avatar me-3">
                                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 {{ !$message->is_read ? 'fw-bold' : '' }}">
                                                    {{ $message->subject }}
                                                    @if(!$message->is_read)
                                                    <span class="badge bg-primary ms-2">Nova</span>
                                                    @endif
                                                </h6>
                                                <p class="text-muted mb-1">
                                                    <strong>De:</strong> {{ $message->fromUser->name }}
                                                </p>
                                            </div>
                                        </div>
                                        <p class="mb-2 text-truncate" style="max-width: 70%;">
                                            {{ Str::limit($message->message, 100) }}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $message->time_ago }}</small><br>
                                        <span class="badge bg-{{ $message->priority_color }}">{{ $message->priority_label }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Paginação -->
                        <div class="p-3">
                            {{ $receivedMessages->appends(request()->query())->links() }}
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">Nenhuma mensagem recebida</h4>
                            <p class="text-muted">Você ainda não recebeu nenhuma mensagem.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Mensagens Enviadas -->
            <div class="tab-pane fade" id="sent" role="tabpanel">
                <div class="card">
                    <div class="card-body p-0">
                        @if($sentMessages->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($sentMessages as $message)
                            <div class="list-group-item list-group-item-action" onclick="openMessage({{ $message->id }})" style="cursor: pointer;">
                                <div class="d-flex w-100 justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-1">
                                            <div class="message-avatar me-3">
                                                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                    <i class="bi bi-send"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $message->subject }}</h6>
                                                <p class="text-muted mb-1">
                                                    <strong>Para:</strong> {{ $message->toUser->name }}
                                                    @if($message->is_read)
                                                    <span class="badge bg-success ms-2">Lida</span>
                                                    @else
                                                    <span class="badge bg-warning ms-2">Não lida</span>
                                                    @endif
                                                </p>
                                            </div>
                                        </div>
                                        <p class="mb-2 text-truncate" style="max-width: 70%;">
                                            {{ Str::limit($message->message, 100) }}
                                        </p>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $message->time_ago }}</small><br>
                                        <span class="badge bg-{{ $message->priority_color }}">{{ $message->priority_label }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <!-- Paginação -->
                        <div class="p-3">
                            {{ $sentMessages->appends(request()->query())->links() }}
                        </div>
                        @else
                        <div class="text-center py-5">
                            <i class="bi bi-send display-1 text-muted"></i>
                            <h4 class="text-muted mt-3">Nenhuma mensagem enviada</h4>
                            <p class="text-muted">Você ainda não enviou nenhuma mensagem.</p>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nova Mensagem (apenas para administradores) -->
@if(auth()->user()->hasPermission('users.manage'))
<div class="modal fade" id="newMessageModal" tabindex="-1" aria-labelledby="newMessageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="newMessageModalLabel">
                    <i class="bi bi-envelope-plus"></i> Nova Mensagem
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="newMessageForm">
                    @csrf
                    <div class="mb-3">
                        <label for="to_user_id" class="form-label">Destinatário <span class="text-danger">*</span></label>
                        <select class="form-select" id="to_user_id" name="to_user_id" required>
                            <option value="">Selecione um usuário</option>
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="subject" class="form-label">Assunto <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Prioridade <span class="text-danger">*</span></label>
                                <select class="form-select" id="priority" name="priority" required>
                                    <option value="low">🟢 Baixa</option>
                                    <option value="medium" selected>🟡 Média</option>
                                    <option value="high">🟠 Alta</option>
                                    <option value="urgent">🔴 Urgente</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="message" class="form-label">Mensagem <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="message" name="message" rows="6" required maxlength="5000"
                                  placeholder="Digite sua mensagem aqui..."></textarea>
                        <div class="form-text">Máximo 5000 caracteres.</div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="sendMessageBtn">
                    <i class="bi bi-send"></i> Enviar Mensagem
                </button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.message-avatar {
    flex-shrink: 0;
}

.list-group-item {
    border-left: none;
    border-right: none;
}

.list-group-item:first-child {
    border-top: none;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.tab-content .card {
    border-top: none;
    border-top-left-radius: 0;
    border-top-right-radius: 0;
}

.nav-tabs .nav-link.active {
    border-bottom-color: transparent;
}

.badge {
    font-size: 0.75em;
}

.text-truncate {
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Carregar usuários para o select de destinatários
    loadUsers();
    
    // Event listeners
    document.getElementById('sendMessageBtn')?.addEventListener('click', sendMessage);
    document.getElementById('markAllAsRead').addEventListener('click', markAllAsRead);
    
    // Auto-refresh a cada 30 segundos
    setInterval(updateUnreadCount, 30000);
});

// Carregar lista de usuários
function loadUsers() {
    const userSelect = document.getElementById('to_user_id');
    if (!userSelect) return;
    
    fetch('/ajax/messages/users', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            userSelect.innerHTML = '<option value="">Selecione um usuário</option>';
            data.users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = `${user.name} (${user.email})`;
                userSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Erro ao carregar usuários:', error);
        });
}

// Enviar nova mensagem
function sendMessage() {
    const form = document.getElementById('newMessageForm');
    const btn = document.getElementById('sendMessageBtn');
    const formData = new FormData(form);
    
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
    
    fetch('/messages', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('Mensagem enviada com sucesso!', 'success');
            form.reset();
            
            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('newMessageModal'));
            modal.hide();
            
            // Recarregar página após um tempo
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message || 'Erro ao enviar mensagem.', 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('Erro ao enviar mensagem. Tente novamente.', 'danger');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send"></i> Enviar Mensagem';
    });
}

// Abrir mensagem
function openMessage(messageId) {
    window.location.href = `/messages/${messageId}`;
}

// Marcar todas como lidas
function markAllAsRead() {
    if (!confirm('Marcar todas as mensagens como lidas?')) return;
    
    fetch('/messages/mark-all-read', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Erro:', error);
    });
}

// Atualizar contagem de não lidas
function updateUnreadCount() {
    fetch('/ajax/messages/unread-count', {
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(response => response.json())
        .then(data => {
            const badges = document.querySelectorAll('.badge');
            badges.forEach(badge => {
                if (badge.textContent.match(/^\d+$/)) {
                    badge.textContent = data.count;
                    badge.style.display = data.count > 0 ? 'inline' : 'none';
                }
            });
        })
        .catch(error => {
            console.error('Erro ao atualizar contagem:', error);
        });
}

// Mostrar alerta
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv && alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endpush
