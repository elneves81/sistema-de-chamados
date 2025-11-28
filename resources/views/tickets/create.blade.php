@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-plus-circle"></i> Novo Chamado
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações do Chamado</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.store') }}" enctype="multipart/form-data">
                    @csrf

                    @if(auth()->user() && in_array(auth()->user()->role, ['admin', 'technician']))
                    <!-- Campo para abrir chamado em nome de outro usuário (somente admin/técnico) -->
                    <div class="mb-3">
                        <label for="requester_user_id" class="form-label">
                            <i class="bi bi-person-badge"></i> Abrir em nome de (opcional)
                        </label>
                        <select class="form-select @error('requester_user_id') is-invalid @enderror" 
                                id="requester_user_id" 
                                name="requester_user_id">
                            <option value="">Eu mesmo ({{ auth()->user()->name }})</option>
                            @if(isset($users))
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" 
                                            {{ old('requester_user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        @error('requester_user_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="bi bi-info-circle"></i> 
                            Use este campo quando o usuário não conseguir abrir o chamado sozinho
                        </small>
                    </div>
                    @endif

                    <div class="mb-3">
                        <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               placeholder="Descreva brevemente o problema"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}
                                            data-color="{{ $category->color }}">
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Prioridade <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" 
                                        name="priority" 
                                        required>
                                    <option value="">Selecione a prioridade</option>
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>
                                        🟢 Baixa - Não é urgente
                                    </option>
                                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>
                                        🟡 Média - Pode aguardar alguns dias
                                    </option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                                        🟠 Alta - Precisa ser resolvido em breve
                                    </option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>
                                        🔴 Urgente - Problema crítico
                                    </option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Localização -->
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="location_id" class="form-label">Localização Principal <span class="text-danger">*</span></label>
                                <select class="form-select @error('location_id') is-invalid @enderror" 
                                        id="location_id" 
                                        name="location_id"
                                        required>
                                    <option value="">Selecione uma UBS</option>
                                    @foreach($locations as $location)
                                    <option value="{{ $location->id }}" 
                                            {{ old('location_id', auth()->user()->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                        @if($location->short_name) ({{ $location->short_name }}) @endif
                                    </option>
                                    @endforeach
                                </select>
                                @error('location_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Localização do usuário: {{ auth()->user()->location ? auth()->user()->location->name : 'Não definida' }}</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="local" class="form-label">Local Específico</label>
                                <input type="text" 
                                       class="form-control @error('local') is-invalid @enderror" 
                                       id="local" 
                                       name="local" 
                                       value="{{ old('local') }}" 
                                       placeholder="Ex: Sala 101, Andar 2, Setor A">
                                @error('local')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Informação adicional sobre o local exato do problema</small>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="6" 
                                  placeholder="Descreva detalhadamente o problema, incluindo:&#10;- O que você estava fazendo quando o problema ocorreu&#10;- Mensagens de erro (se houver)&#10;- Passos para reproduzir o problema&#10;- Qualquer informação adicional relevante"
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Anexos -->
                    <div class="mb-3">
                        <label for="attachments" class="form-label">Anexos (opcional)</label>
                        <input type="file" 
                               class="form-control @error('attachments.*') is-invalid @enderror" 
                               id="attachments" 
                               name="attachments[]" 
                               multiple
                               accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx,.xls,.xlsx,.txt,.zip,.rar">
                        <small class="text-muted">Você pode selecionar múltiplos arquivos. Tamanho máximo por arquivo: 10MB.</small>
                        @error('attachments.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Tags -->
                    <div class="mb-3">
                        <label for="tags" class="form-label">Tags (opcional)</label>
                        <div class="row">
                            @foreach($tags as $tag)
                            <div class="col-md-6 col-lg-4 mb-2">
                                <div class="form-check">
                                    <input class="form-check-input" 
                                           type="checkbox" 
                                           name="tags[]" 
                                           value="{{ $tag->id }}" 
                                           id="tag{{ $tag->id }}"
                                           {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tag{{ $tag->id }}">
                                        <span class="badge" style="background-color: {{ $tag->color }}; color: white;">
                                            {{ $tag->name }}
                                        </span>
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('tags')
                            <div class="text-danger small">{{ $message }}</div>
                        @enderror
                        @if($tags->isEmpty())
                            <small class="text-muted">Nenhuma tag disponível no momento.</small>
                        @endif
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Criar Chamado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Card de Assistente Virtual IA -->
        <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="card-title mb-0">
                    <i class="bi bi-robot"></i> Assistente Virtual IA
                </h6>
                <button type="button" class="btn btn-sm btn-outline-primary" id="toggleAiChat">
                    <i class="bi bi-chat-dots"></i> Chat
                </button>
            </div>
            <div class="card-body p-0" id="aiChatContainer" style="display: none;">
                <div class="chat-messages p-3" id="chatMessages" style="height: 300px; overflow-y: auto; background: #f8f9fa;">
                    <div class="chat-message ai-message mb-3">
                        <div class="d-flex">
                            <div class="flex-shrink-0">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                    <i class="bi bi-robot" style="font-size: 14px;"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="bg-white rounded p-3 shadow-sm">
                                    <strong>Assistente IA:</strong><br>
                                    Olá! Posso ajudá-lo a classificar seu chamado e encontrar soluções. Digite sua dúvida ou descreva o problema!
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="chat-input p-3 border-top">
                    <div class="input-group">
                        <input type="text" 
                               class="form-control" 
                               id="aiChatInput" 
                               placeholder="Digite sua pergunta..."
                               maxlength="500">
                        <button class="btn btn-primary" type="button" id="sendAiMessage">
                            <i class="bi bi-send"></i>
                        </button>
                    </div>
                    <small class="text-muted">A IA pode sugerir categoria e prioridade</small>
                </div>
                <div class="ai-suggestions p-3 border-top bg-light" id="aiSuggestions" style="display: none;">
                    <h6 class="text-primary mb-2">
                        <i class="bi bi-lightbulb"></i> Sugestões da IA:
                    </h6>
                    <div id="aiSuggestionsContent"></div>
                </div>
            </div>
        </div>

        <!-- Fale Conosco -->
        <div class="card mb-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-headset"></i> Fale Conosco
                </h6>
            </div>
            <div class="card-body">
                <p class="mb-3">Precisa de ajuda imediata? Entre em contato:</p>
                <div class="d-grid gap-2">
                    <a href="tel:+554231421527" class="btn btn-outline-success">
                        <i class="bi bi-telephone"></i> (42) 3142-1527
                    </a>
                    <a href="mailto:dtisaude@guarapuava.pr.gov.br" class="btn btn-outline-primary">
                        <i class="bi bi-envelope"></i> dtisaude@guarapuava.pr.gov.br
                    </a>
                    <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#whatsappModal">
                        <i class="bi bi-whatsapp"></i> WhatsApp
                    </button>
                </div>
                <hr>
                <small class="text-muted">
                    <strong>Horário de Atendimento:</strong><br>
                    Segunda a Sexta: 8h às 12h e 13h às 15h<br>
                    Sábados e Domingos: Sobreaviso<br>
                    <small>WhatsApp Sobreaviso: (42) 99123-5068</small>
                </small>
            </div>
        </div>

        <!-- Dicas -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb"></i> Dicas para um bom chamado
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Seja específico</strong> no título
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Descreva</strong> o problema detalhadamente
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Inclua</strong> mensagens de erro
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Informe</strong> quando o problema começou
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Escolha</strong> a prioridade correta
                    </li>
                </ul>
            </div>
        </div>

        <!-- Info sobre Prioridades -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> Sobre as Prioridades
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <span class="priority-badge priority-low">Baixa</span>
                    <small class="text-muted d-block">Problemas menores, melhorias</small>
                </div>
                <div class="mb-2">
                    <span class="priority-badge priority-medium">Média</span>
                    <small class="text-muted d-block">Problemas que afetam o trabalho</small>
                </div>
                <div class="mb-2">
                    <span class="priority-badge priority-high">Alta</span>
                    <small class="text-muted d-block">Problemas importantes</small>
                </div>
                <div class="mb-0">
                    <span class="priority-badge priority-urgent">Urgente</span>
                    <small class="text-muted d-block">Sistema parado, problema crítico</small>
                </div>
            </div>
        </div>

        <!-- Categorias Disponíveis -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-tags"></i> Categorias Disponíveis
                </h6>
            </div>
            <div class="card-body">
                @foreach($categories as $category)
                <div class="mb-2">
                    <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                        {{ $category->name }}
                    </span>
                    @if($category->description)
                    <small class="text-muted d-block">{{ $category->description }}</small>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal WhatsApp -->
<div class="modal fade" id="whatsappModal" tabindex="-1" aria-labelledby="whatsappModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="whatsappModalLabel">
                    <i class="bi bi-whatsapp text-success"></i> Contato WhatsApp
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Escolha o setor para atendimento via WhatsApp:</p>
                <div class="d-grid gap-2">
                    <a href="https://wa.me/554231421527?text=Olá! Preciso de suporte técnico." 
                       class="btn btn-success" 
                       target="_blank">
                        <i class="bi bi-tools"></i> Suporte Técnico
                    </a>
                    <a href="https://wa.me/554231421527?text=Olá! Tenho uma dúvida sobre o sistema." 
                       class="btn btn-info" 
                       target="_blank">
                        <i class="bi bi-question-circle"></i> Dúvidas Gerais
                    </a>
                    <a href="https://wa.me/554231421527?text=Olá! Preciso reportar um problema urgente." 
                       class="btn btn-danger" 
                       target="_blank">
                        <i class="bi bi-exclamation-triangle"></i> Emergência
                    </a>
                    <hr>
                    <a href="https://wa.me/5542991235068?text=Olá! Preciso de atendimento em sobreaviso (fim de semana/feriado)." 
                       class="btn btn-warning" 
                       target="_blank">
                        <i class="bi bi-clock"></i> Sobreaviso (Fins de Semana)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.chat-message {
    margin-bottom: 1rem;
}

.chat-message.user-message .message-content {
    background: #007bff;
    color: white;
    margin-left: auto;
    margin-right: 0;
    text-align: right;
}

.chat-message.ai-message .message-content {
    background: #f8f9fa;
    border-left: 3px solid #007bff;
}

.chat-messages {
    max-height: 300px;
    overflow-y: auto;
}

.ai-suggestion-btn {
    margin: 2px;
    font-size: 0.875rem;
}

.chat-message .message-content {
    padding: 10px;
    border-radius: 10px;
    max-width: 85%;
    word-wrap: break-word;
}

.priority-badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
}

.priority-low { background-color: #28a745; color: white; }
.priority-medium { background-color: #ffc107; color: #212529; }
.priority-high { background-color: #fd7e14; color: white; }
.priority-urgent { background-color: #dc3545; color: white; }

#aiChatContainer {
    border-top: 1px solid #dee2e6;
}

.typing-indicator {
    display: none;
    padding: 10px;
    font-style: italic;
    color: #6c757d;
}

.typing-indicator::after {
    content: '...';
    animation: typing 1.5s infinite;
}

@keyframes typing {
    0%, 60%, 100% { opacity: 1; }
    30% { opacity: 0.5; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elementos do chat IA
    const toggleAiChat = document.getElementById('toggleAiChat');
    const aiChatContainer = document.getElementById('aiChatContainer');
    const chatMessages = document.getElementById('chatMessages');
    const aiChatInput = document.getElementById('aiChatInput');
    const sendAiMessage = document.getElementById('sendAiMessage');
    const aiSuggestions = document.getElementById('aiSuggestions');
    const aiSuggestionsContent = document.getElementById('aiSuggestionsContent');

    // Elementos do formulário
    const titleInput = document.getElementById('title');
    const descriptionInput = document.getElementById('description');
    const categorySelect = document.getElementById('category_id');
    const prioritySelect = document.getElementById('priority');

    // Toggle do chat
    toggleAiChat.addEventListener('click', function() {
        if (aiChatContainer.style.display === 'none') {
            aiChatContainer.style.display = 'block';
            toggleAiChat.innerHTML = '<i class="bi bi-x"></i> Fechar';
            toggleAiChat.classList.remove('btn-outline-primary');
            toggleAiChat.classList.add('btn-outline-danger');
        } else {
            aiChatContainer.style.display = 'none';
            toggleAiChat.innerHTML = '<i class="bi bi-chat-dots"></i> Chat';
            toggleAiChat.classList.remove('btn-outline-danger');
            toggleAiChat.classList.add('btn-outline-primary');
        }
    });

    // Enviar mensagem para IA
    function sendMessageToAI() {
        const message = aiChatInput.value.trim();
        if (!message) return;

        // Adicionar mensagem do usuário
        addChatMessage(message, 'user');
        aiChatInput.value = '';

        // Mostrar indicador de digitação
        showTypingIndicator();

        // Enviar para API da IA
        fetch('/ai/chatbot', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ message: message })
        })
        .then(async response => {
            if (!response.ok) {
                throw new Error(`Erro HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            hideTypingIndicator();
            
            console.log('Resposta da IA na criação:', data); // Debug
            console.log('Tipo da resposta:', typeof data.response); // Debug
            
            // Verificar se há uma resposta válida
            if (data.response !== undefined) {
                addChatMessage(data.response, 'ai');
            } else {
                addChatMessage('❌ Resposta inválida do servidor.', 'ai');
            }
            
            // Verificar se há sugestões
            if (Array.isArray(data.suggestions) && data.suggestions.length > 0) {
                showAISuggestions(data.suggestions);
            }

            // Auto-classificação se possível
            if (message.length > 10) {
                autoClassifyTicket(message);
            }
        })
        .catch(error => {
            hideTypingIndicator();
            console.error('Erro na IA:', error);
            addChatMessage(`❌ Erro de conexão: ${error.message}. Tente novamente.`, 'ai');
        });
    }

    // Adicionar mensagem ao chat
    function addChatMessage(message, sender) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${sender}-message mb-3`;
        
        // Verificar tipo de message e processar adequadamente
        let messageText = '';
        if (typeof message === 'object') {
            if (message && message.toString && message.toString() !== '[object Object]') {
                messageText = message.toString();
            } else {
                messageText = JSON.stringify(message, null, 2);
            }
            console.log('Objeto recebido no chat de criação:', message);
        } else if (typeof message === 'string') {
            messageText = message;
        } else {
            messageText = String(message || 'Resposta vazia');
        }
        
        if (sender === 'user') {
            messageDiv.innerHTML = `
                <div class="d-flex justify-content-end">
                    <div class="bg-primary text-white rounded p-3 shadow-sm" style="max-width: 85%;">
                        ${messageText}
                    </div>
                    <div class="flex-shrink-0 ms-3">
                        <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-person" style="font-size: 14px;"></i>
                        </div>
                    </div>
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="d-flex">
                    <div class="flex-shrink-0">
                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                            <i class="bi bi-robot" style="font-size: 14px;"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <div class="bg-white rounded p-3 shadow-sm">
                            <strong>Assistente IA:</strong><br>${messageText}
                        </div>
                    </div>
                </div>
            `;
        }
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Mostrar/esconder indicador de digitação
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.id = 'typing-indicator';
        typingDiv.className = 'typing-indicator';
        typingDiv.innerHTML = '<i class="bi bi-robot me-2"></i>IA está digitando...';
        chatMessages.appendChild(typingDiv);
        document.getElementById('typing-indicator').style.display = 'block';
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    function hideTypingIndicator() {
        const typingDiv = document.getElementById('typing-indicator');
        if (typingDiv) {
            typingDiv.remove();
        }
    }

    // Mostrar sugestões da IA
    function showAISuggestions(suggestions) {
        aiSuggestionsContent.innerHTML = '';
        
        suggestions.forEach(suggestion => {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn btn-sm btn-outline-primary ai-suggestion-btn';
            
            // Verificar se suggestion é um objeto com propriedade 'text'
            let suggestionText = '';
            let suggestionAction = '';
            
            if (typeof suggestion === 'object' && suggestion !== null) {
                if (suggestion.text) {
                    // Formato: {text: "texto", action: "acao"}
                    suggestionText = suggestion.text;
                    suggestionAction = suggestion.action || '';
                } else if (suggestion.toString && suggestion.toString() !== '[object Object]') {
                    suggestionText = suggestion.toString();
                } else {
                    suggestionText = JSON.stringify(suggestion);
                }
                console.log('Sugestão objeto recebida:', suggestion);
            } else if (typeof suggestion === 'string') {
                suggestionText = suggestion;
            } else {
                suggestionText = String(suggestion || 'Sugestão vazia');
            }
            
            btn.textContent = suggestionText;
            btn.onclick = () => applySuggestion(suggestionText);
            aiSuggestionsContent.appendChild(btn);
        });
        
        aiSuggestions.style.display = 'block';
    }

    // Aplicar sugestão
    function applySuggestion(suggestionText) {
        aiChatInput.value = suggestionText;
        sendMessageToAI();
    }

    // Auto-classificação usando IA
    function autoClassifyTicket(description) {
        const title = titleInput.value;
        
        if (!title && !description) return;

        fetch('/ai/classify', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                title: title || 'Sem título',
                description: description
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.suggested_category_id && !categorySelect.value) {
                categorySelect.value = data.suggested_category_id;
                
                // Trigger change event para atualizar visual
                categorySelect.dispatchEvent(new Event('change'));
                
                // Mostrar notificação
                showAINotification('Categoria sugerida pela IA foi aplicada!');
            }
            
            // Sugerir prioridade baseada na confiança
            if (data.confidence > 50 && !prioritySelect.value) {
                const keywords = data.keywords.join(' ').toLowerCase();
                if (keywords.includes('urgente') || keywords.includes('crítico')) {
                    prioritySelect.value = 'urgent';
                } else if (keywords.includes('problema') || keywords.includes('erro')) {
                    prioritySelect.value = 'medium';
                } else {
                    prioritySelect.value = 'low';
                }
                
                prioritySelect.dispatchEvent(new Event('change'));
                showAINotification('Prioridade sugerida pela IA foi aplicada!');
            }
        })
        .catch(error => {
            console.error('Erro na classificação:', error);
        });
    }

    // Mostrar notificação da IA
    function showAINotification(message) {
        const toast = document.createElement('div');
        toast.className = 'toast position-fixed top-0 end-0 m-3';
        toast.setAttribute('role', 'alert');
        toast.style.zIndex = '9999';
        
        toast.innerHTML = `
            <div class="toast-header bg-primary text-white">
                <i class="bi bi-robot me-2"></i>
                <strong class="me-auto">Assistente IA</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        
        document.body.appendChild(toast);
        
        const toastBootstrap = new bootstrap.Toast(toast);
        toastBootstrap.show();
        
        // Remover após 5 segundos
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    // Event listeners
    sendAiMessage.addEventListener('click', sendMessageToAI);
    
    aiChatInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessageToAI();
        }
    });

    // Auto-classificação quando o usuário digita no título ou descrição
    let classifyTimeout;
    
    [titleInput, descriptionInput].forEach(input => {
        input.addEventListener('input', function() {
            clearTimeout(classifyTimeout);
            classifyTimeout = setTimeout(() => {
                if (this.value.length > 10) {
                    autoClassifyTicket(this.value);
                }
            }, 2000); // Aguarda 2 segundos após parar de digitar
        });
    });

    // Auto-resize textarea (código existente)
    const textarea = document.getElementById('description');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Preview da categoria selecionada (código existente)
    const categorySelect_existing = document.getElementById('category_id');
    categorySelect_existing.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.dataset.color) {
            this.style.borderLeftColor = selectedOption.dataset.color;
            this.style.borderLeftWidth = '4px';
        } else {
            this.style.borderLeft = '';
        }
    });
});
</script>
@endpush
