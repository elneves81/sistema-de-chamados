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
        @if($message->to_user_id === auth()->id())
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
@if($message->to_user_id === auth()->id())
<div class="modal fade" id="replyModal" tabindex="-1" aria-labelledby="replyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="replyModalLabel">
                    <i class="bi bi-reply"></i> Responder Mensagem
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <strong>Para:</strong> {{ $message->fromUser->name }} ({{ $message->fromUser->email }})
                </div>
                <div class="mb-3">
                    <strong>Assunto:</strong> Re: {{ $message->subject }}
                </div>
                
                <form id="replyForm">
                    @csrf
                    <div class="mb-3">
                        <label for="reply_message" class="form-label">Sua Resposta <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reply_message" name="message" rows="6" required maxlength="5000"
                                  placeholder="Digite sua resposta aqui..."></textarea>
                        <div class="form-text">Máximo 5000 caracteres.</div>
                    </div>
                </form>
                
                <!-- Mensagem original -->
                <div class="mt-4">
                    <h6>Mensagem Original:</h6>
                    <div class="border rounded p-3 bg-light">
                        <div class="text-muted small mb-2">
                            De: {{ $message->fromUser->name }} em {{ $message->created_at->format('d/m/Y H:i') }}
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
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="sendReplyBtn">
                    <i class="bi bi-send"></i> Enviar Resposta
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
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Event listener para enviar resposta
    document.getElementById('sendReplyBtn')?.addEventListener('click', sendReply);
    
    // Auto-resize do textarea
    const textarea = document.getElementById('reply_message');
    if (textarea) {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    }
});

// Enviar resposta
function sendReply() {
    const form = document.getElementById('replyForm');
    const btn = document.getElementById('sendReplyBtn');
    const formData = new FormData(form);
    
    btn.disabled = true;
    btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
    
    fetch('/messages/{{ $message->id }}/reply', {
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
            showAlert('Resposta enviada com sucesso!', 'success');
            form.reset();
            
            // Fechar modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('replyModal'));
            modal.hide();
            
            // Recarregar página após um tempo
            setTimeout(() => location.reload(), 1500);
        } else {
            showAlert(data.message || 'Erro ao enviar resposta.', 'danger');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        showAlert('Erro ao enviar resposta. Tente novamente.', 'danger');
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-send"></i> Enviar Resposta';
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
