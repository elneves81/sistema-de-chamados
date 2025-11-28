@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-envelope-open"></i> {{ $message->subject }}
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('messages.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
        @php
            $canReply = $message->to_user_id === auth()->id() || 
                        (in_array(auth()->user()->role, ['admin', 'technician']) && $message->from_user_id === auth()->id());
        @endphp
        @if($canReply)
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#replyModal">
                <i class="bi bi-reply"></i> Responder
            </button>
        </div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="message-avatar me-3">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                                    <i class="bi bi-person"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-1">{{ $message->fromUser->name }}</h5>
                                <p class="text-muted mb-0">{{ $message->fromUser->email }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-end">
                        <span class="badge bg-{{ $message->priority_color }} fs-6">{{ $message->priority_label }}</span>
                        <br>
                        <small class="text-muted">{{ $message->created_at->format('d/m/Y H:i') }}</small>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="message-content">
                    <div class="mb-3">
                        <strong>Para:</strong> {{ $message->toUser->name }} ({{ $message->toUser->email }})
                    </div>
                    <div class="mb-3">
                        <strong>Assunto:</strong> {{ $message->subject }}
                    </div>
                    <div class="mb-3">
                        <strong>Data:</strong> {{ $message->created_at->format('d/m/Y H:i:s') }}
                        <span class="text-muted">({{ $message->time_ago }})</span>
                    </div>
                    <hr>
                    <div class="message-text">
                        {!! nl2br(e($message->message)) !!}
                    </div>
                </div>
                
                @if($message->is_read && $message->read_at)
                <div class="mt-4 pt-3 border-top">
                    <small class="text-success">
                        <i class="bi bi-check2-circle"></i> 
                        Lida em {{ $message->read_at->format('d/m/Y H:i') }}
                    </small>
                </div>
                @elseif($message->to_user_id === auth()->id())
                <div class="mt-4 pt-3 border-top">
                    <small class="text-warning">
                        <i class="bi bi-circle"></i> 
                        Mensagem não lida
                    </small>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal de Resposta -->
@php
    $canReply = $message->to_user_id === auth()->id() || 
                (in_array(auth()->user()->role, ['admin', 'technician']) && $message->from_user_id === auth()->id());
@endphp
@if($canReply)
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="replyModalLabel">
                    <i class="bi bi-reply-fill"></i> Responder Mensagem
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="replyForm">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info mb-3">
                        @php
                            // Se for o destinatário, responde para o remetente
                            // Se for o remetente (admin/técnico), responde para o destinatário
                            $replyTo = $message->to_user_id === auth()->id() 
                                        ? $message->fromUser 
                                        : $message->toUser;
                        @endphp
                        <div><strong><i class="bi bi-person"></i> Para:</strong> {{ $replyTo->name }}</div>
                        <div><strong><i class="bi bi-envelope"></i> Assunto:</strong> Re: {{ $message->subject }}</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="reply_message" class="form-label fw-bold">
                            Sua Resposta <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="reply_message" name="message" rows="6" required maxlength="5000"
                                  placeholder="Digite sua resposta aqui..." style="min-height: 150px;"></textarea>
                        <div class="d-flex justify-content-between mt-1">
                            <small class="text-muted">Máximo 5000 caracteres</small>
                            <small id="char-counter" class="text-muted">0 / 5000</small>
                        </div>
                    </div>
                    
                    <!-- Mensagem original -->
                    <div class="mt-3">
                        <h6 class="fw-bold"><i class="bi bi-chat-left-text"></i> Mensagem Original:</h6>
                        <div class="border rounded p-3 bg-light">
                            <div class="text-muted small mb-2">
                                <i class="bi bi-calendar"></i> {{ $message->created_at->format('d/m/Y H:i') }} - 
                                <i class="bi bi-person"></i> {{ $message->fromUser->name }}
                            </div>
                            <div class="original-message">
                                {!! nl2br(e(Str::limit($message->message, 500))) !!}
                                @if(strlen($message->message) > 500)
                                <br><em class="text-muted">... (mensagem truncada)</em>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg" id="sendReplyBtn">
                        <i class="bi bi-send-fill"></i> Enviar Resposta
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif
@endsection

@push('styles')
<style>
.message-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 2rem;
    border-radius: 0.375rem 0.375rem 0 0;
    margin: -1.5rem -1.5rem 1.5rem -1.5rem;
}

.message-content {
    font-size: 1rem;
    line-height: 1.6;
}

.message-text {
    background: #f8f9fa;
    border-left: 4px solid #007bff;
    padding: 1.5rem;
    border-radius: 0.375rem;
    font-size: 1.1rem;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.original-message {
    font-size: 0.9rem;
    color: #6c757d;
    max-height: 200px;
    overflow-y: auto;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.badge.fs-6 {
    font-size: 1rem !important;
    padding: 0.5rem 0.75rem;
}

/* Modal de resposta - garantir funcionamento correto */
#replyModal {
    z-index: 9999 !important;
}

#replyModal .modal-backdrop {
    z-index: 9998 !important;
}

#replyModal .modal-dialog {
    max-height: 90vh;
    z-index: 10000 !important;
    position: relative;
}

#replyModal .modal-content {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    position: relative;
    z-index: 10001 !important;
}

#replyModal .modal-body {
    max-height: calc(90vh - 180px);
    overflow-y: auto;
}

#replyModal textarea {
    min-height: 150px;
    resize: vertical;
    transition: border-color 0.15s ease-in-out;
    position: relative;
    z-index: 10002 !important;
}

#replyModal textarea:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}

/* Garantir que backdrop não bloqueie o modal */
.modal-backdrop.show {
    z-index: 9998 !important;
}

#sendReplyBtn {
    min-width: 150px;
    font-weight: 600;
}

/* Notificações */
.reply-notification {
    animation: slideInRight 0.3s ease-out;
    border-left: 4px solid currentColor;
}

.reply-notification.alert-success {
    border-left-color: #198754;
}

.reply-notification.alert-danger {
    border-left-color: #dc3545;
}

.reply-notification.alert-warning {
    border-left-color: #ffc107;
}

@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Prevenir scroll durante modal */
body.modal-open {
    overflow: hidden !important;
}
</style>
@endpush

@push('scripts')
<script>
// Variáveis globais
let isSubmittingReply = false;
let replyModalInstance = null;

console.log('✅ Message reply system loaded');

// Inicializar quando o DOM estiver pronto
document.addEventListener('DOMContentLoaded', function() {
    initReplySystem();
});

function initReplySystem() {
    console.log('🔧 Inicializando sistema de resposta...');
    
    // Obter elementos
    const modalElement = document.getElementById('replyModal');
    
    // Verificar se o modal existe (só existe para destinatários)
    if (!modalElement) {
        console.log('ℹ️ Modal de resposta não disponível (você é o remetente desta mensagem)');
        return;
    }
    
    const replyForm = document.getElementById('replyForm');
    const replyTextarea = document.getElementById('reply_message');
    const sendButton = document.getElementById('sendReplyBtn');
    const charCounter = document.getElementById('char-counter');
    
    if (!replyForm || !replyTextarea || !sendButton) {
        console.error('❌ Elementos do modal não encontrados');
        return;
    }
    
    // Inicializar modal Bootstrap
    replyModalInstance = new bootstrap.Modal(modalElement, {
        backdrop: true,
        keyboard: true,
        focus: true
    });
    
    console.log('✅ Modal inicializado');
    
    // Focar textarea ao abrir
    modalElement.addEventListener('shown.bs.modal', function() {
        console.log('📝 Modal aberto');
        
        // Garantir z-index correto
        setTimeout(() => {
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.style.zIndex = '9998';
            }
            modalElement.style.zIndex = '9999';
            
            // Focar textarea
            replyTextarea.focus();
            replyTextarea.click();
        }, 100);
    });
    
    // Event listener para auto-resize e contador
    replyTextarea.addEventListener('input', function() {
        // Auto-resize
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
        
        // Atualizar contador
        const count = this.value.length;
        if (charCounter) {
            charCounter.textContent = `${count} / 5000`;
            if (count > 4500) {
                charCounter.classList.add('text-danger', 'fw-bold');
                charCounter.classList.remove('text-muted');
            } else {
                charCounter.classList.add('text-muted');
                charCounter.classList.remove('text-danger', 'fw-bold');
            }
        }
    });
    
    // Event listener para submit do form
    replyForm.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        handleReplySubmit();
        return false;
    });
    
    // Limpar ao abrir modal
    modalElement.addEventListener('show.bs.modal', function() {
        console.log('📝 Modal abrindo...');
        replyForm.reset();
        replyTextarea.style.height = 'auto';
        if (charCounter) charCounter.textContent = '0 / 5000';
        isSubmittingReply = false;
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="bi bi-send-fill"></i> Enviar Resposta';
    });
    
    // Limpar backdrop ao fechar
    modalElement.addEventListener('hidden.bs.modal', function() {
        setTimeout(() => {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(b => b.remove());
            document.body.classList.remove('modal-open');
            document.body.style.removeProperty('overflow');
            document.body.style.removeProperty('padding-right');
        }, 300);
    });
    
    console.log('✅ Sistema de resposta inicializado com sucesso!');
}


function handleReplySubmit() {
    console.log('📤 Tentando enviar resposta...');
    
    const replyTextarea = document.getElementById('reply_message');
    const sendButton = document.getElementById('sendReplyBtn');
    
    // Prevenir múltiplos envios
    if (isSubmittingReply) {
        console.warn('⚠️ Já está enviando, aguarde...');
        return false;
    }
    
    const message = replyTextarea.value.trim();
    
    // Validações
    if (!message) {
        showMessageNotification('Por favor, digite uma mensagem.', 'warning');
        replyTextarea.focus();
        return false;
    }
    
    if (message.length > 5000) {
        showMessageNotification('A mensagem não pode ter mais de 5000 caracteres.', 'warning');
        return false;
    }
    
    // Enviar
    isSubmittingReply = true;
    
    // Desabilitar botão e mostrar loading
    sendButton.disabled = true;
    sendButton.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Enviando...';
    
    console.log('🚀 Enviando requisição...');
    
    const formData = new FormData();
    formData.append('message', message);
    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
    
    fetch('/messages/{{ $message->id }}/reply', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => {
        console.log('📥 Resposta recebida:', response.status);
        if (!response.ok) {
            throw new Error(`Erro HTTP: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('✅ Dados:', data);
        
        if (data.success) {
            showMessageNotification('Resposta enviada com sucesso!', 'success');
            
            // Fechar modal
            if (replyModalInstance) {
                replyModalInstance.hide();
            }
            
            // Recarregar página
            setTimeout(() => {
                console.log('🔄 Recarregando página...');
                window.location.reload();
            }, 1500);
        } else {
            throw new Error(data.message || 'Erro ao enviar resposta');
        }
    })
    .catch(error => {
        console.error('❌ Erro:', error);
        showMessageNotification(error.message || 'Erro ao enviar resposta. Tente novamente.', 'danger');
        
        // Reabilitar botão
        isSubmittingReply = false;
        sendButton.disabled = false;
        sendButton.innerHTML = '<i class="bi bi-send-fill"></i> Enviar Resposta';
    });
}

function showMessageNotification(message, type = 'info') {
    console.log(`🔔 Notificação [${type}]:`, message);
    
    // Remover notificações antigas
    document.querySelectorAll('.reply-notification').forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show reply-notification`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px; max-width: 500px; box-shadow: 0 4px 12px rgba(0,0,0,0.15);';
    
    const icons = {
        success: 'check-circle-fill',
        danger: 'exclamation-triangle-fill',
        warning: 'exclamation-circle-fill',
        info: 'info-circle-fill'
    };
    
    notification.innerHTML = `
        <i class="bi bi-${icons[type] || icons.info} me-2"></i>
        <strong>${message}</strong>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remover após 5 segundos
    setTimeout(() => {
        if (notification.parentNode) {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 150);
        }
    }, 5000);
}
</script>
@endpush
