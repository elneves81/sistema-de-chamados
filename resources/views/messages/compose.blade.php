@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-envelope-plus"></i> Compor Mensagem</h2>
                <a href="{{ route('messages.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar às Mensagens
                </a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-pencil"></i> Nova Mensagem
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('messages.send') }}" method="POST" id="composeForm">
                        @csrf
                        
                        <!-- Destinatário -->
                        <div class="mb-3">
                            <label for="to_user_id" class="form-label">
                                <i class="bi bi-person"></i> Destinatário <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('to_user_id') is-invalid @enderror" 
                                    id="to_user_id" name="to_user_id" required>
                                <option value="">Selecione um usuário...</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ (request('user') == $user->id || old('to_user_id') == $user->id) ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                        @if($user->role)
                                            - {{ ucfirst($user->role) }}
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('to_user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Assunto -->
                        <div class="mb-3">
                            <label for="subject" class="form-label">
                                <i class="bi bi-tag"></i> Assunto <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('subject') is-invalid @enderror" 
                                   id="subject" 
                                   name="subject" 
                                   value="{{ old('subject') }}" 
                                   placeholder="Digite o assunto da mensagem..."
                                   required>
                            @error('subject')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Prioridade -->
                        <div class="mb-3">
                            <label for="priority" class="form-label">
                                <i class="bi bi-exclamation-triangle"></i> Prioridade
                            </label>
                            <select class="form-select @error('priority') is-invalid @enderror" 
                                    id="priority" name="priority">
                                <option value="low" {{ old('priority') == 'low' ? 'selected' : '' }}>
                                    🟢 Baixa
                                </option>
                                <option value="medium" {{ old('priority', 'medium') == 'medium' ? 'selected' : '' }}>
                                    � Média
                                </option>
                                <option value="high" {{ old('priority') == 'high' ? 'selected' : '' }}>
                                    � Alta
                                </option>
                                <option value="urgent" {{ old('priority') == 'urgent' ? 'selected' : '' }}>
                                    🔴 Urgente
                                </option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Mensagem -->
                        <div class="mb-4">
                            <label for="message" class="form-label">
                                <i class="bi bi-chat-text"></i> Mensagem <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" 
                                      name="message" 
                                      rows="8" 
                                      placeholder="Digite sua mensagem aqui..."
                                      required>{{ old('message') }}</textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small class="text-muted">
                                    <i class="bi bi-info-circle"></i> 
                                    O usuário receberá uma notificação por email e poderá responder através do sistema.
                                </small>
                            </div>
                        </div>

                        <!-- Opções -->
                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="send_email" name="send_email" value="1" checked>
                                <label class="form-check-label" for="send_email">
                                    <i class="bi bi-envelope"></i> Enviar notificação por email
                                </label>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary" id="sendBtn">
                                <i class="bi bi-send"></i> Enviar Mensagem
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="saveDraft()">
                                <i class="bi bi-file-earmark"></i> Salvar Rascunho
                            </button>
                            <a href="{{ route('messages.index') }}" class="btn btn-outline-danger">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.priority-badge {
    display: inline-block;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    margin-right: 8px;
}

.priority-normal .priority-badge { background-color: #28a745; }
.priority-high .priority-badge { background-color: #ffc107; }
.priority-urgent .priority-badge { background-color: #dc3545; }

.form-control:focus,
.form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

#message {
    resize: vertical;
    min-height: 120px;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('composeForm');
    const sendBtn = document.getElementById('sendBtn');
    
    form.addEventListener('submit', function(e) {
        sendBtn.disabled = true;
        sendBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Enviando...';
    });
    
    // Auto-save draft functionality
    let draftTimeout;
    const inputs = ['subject', 'message', 'to_user_id', 'priority'];
    
    inputs.forEach(inputId => {
        const element = document.getElementById(inputId);
        if (element) {
            element.addEventListener('input', function() {
                clearTimeout(draftTimeout);
                draftTimeout = setTimeout(saveDraftAuto, 30000); // Auto-save após 30 segundos
            });
        }
    });
});

function saveDraft() {
    // Implementar funcionalidade de salvar rascunho
    alert('Funcionalidade de rascunho será implementada em breve!');
}

function saveDraftAuto() {
    // Auto-save silencioso
    console.log('Auto-saving draft...');
}

// Preview da prioridade
document.getElementById('priority').addEventListener('change', function() {
    const priority = this.value;
    const card = document.querySelector('.card');
    
    // Remove classes antigas
    card.classList.remove('priority-normal', 'priority-high', 'priority-urgent');
    
    // Adiciona nova classe
    card.classList.add('priority-' + priority);
});

// Validação de destinatário
document.getElementById('to_user_id').addEventListener('change', function() {
    if (this.value) {
        const selectedOption = this.options[this.selectedIndex];
        const userName = selectedOption.text.split(' (')[0];
        document.getElementById('subject').placeholder = `Mensagem para ${userName}...`;
    }
});
</script>
@endsection
