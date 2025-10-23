<!-- Notificação de Mensagens -->
<div class="dropdown">
    <button class="btn btn-outline-light position-relative" type="button" id="messagesDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="bi bi-envelope"></i>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" id="messagesBadge" style="display: none;">
            0
        </span>
    </button>
    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="messagesDropdown" style="width: 350px; max-height: 400px; overflow-y: auto;">
        <li class="dropdown-header d-flex justify-content-between align-items-center">
            <span>Mensagens Recentes</span>
            <a href="{{ route('messages.index') }}" class="btn btn-sm btn-outline-primary">Ver Todas</a>
        </li>
        <li><hr class="dropdown-divider"></li>
        <div id="messagesDropdownContent">
            <li class="dropdown-item-text text-center py-3">
                <div class="spinner-border spinner-border-sm text-primary" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
            </li>
        </div>
        <li><hr class="dropdown-divider"></li>
        <li>
            <a class="dropdown-item text-center" href="{{ route('messages.index') }}">
                <i class="bi bi-envelope"></i> Ver Todas as Mensagens
            </a>
        </li>
    </ul>
</div>

<style>
.dropdown-menu {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.message-item {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid #e9ecef;
    transition: background-color 0.15s ease-in-out;
}

.message-item:hover {
    background-color: #f8f9fa;
}

.message-item:last-child {
    border-bottom: none;
}

.message-item.unread {
    background-color: #e7f3ff;
    border-left: 3px solid #007bff;
}

.message-avatar-small {
    width: 32px;
    height: 32px;
    background: #007bff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 14px;
    flex-shrink: 0;
}

.message-content-small {
    flex: 1;
    min-width: 0;
}

.message-subject {
    font-weight: 600;
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.message-preview {
    font-size: 0.8rem;
    color: #6c757d;
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.message-time {
    font-size: 0.75rem;
    color: #adb5bd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Carregar mensagens iniciais
    loadRecentMessages();
    
    // Atualizar a cada 30 segundos
    setInterval(loadRecentMessages, 30000);
    
    // Recarregar quando o dropdown for aberto
    document.getElementById('messagesDropdown').addEventListener('click', loadRecentMessages);
});

async function loadRecentMessages() {
    try {
        const response = await fetch('/ajax/messages/recent', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                'X-Requested-With': 'XMLHttpRequest'
            },
            credentials: 'same-origin'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        updateMessagesBadge(data.unread_count);
        updateMessagesDropdown(data.messages);
        
    } catch (error) {
        console.error('Erro ao carregar mensagens:', error);
    }
}

function updateMessagesBadge(count) {
    const badge = document.getElementById('messagesBadge');
    if (count > 0) {
        badge.textContent = count > 99 ? '99+' : count;
        badge.style.display = 'inline';
    } else {
        badge.style.display = 'none';
    }
}

function updateMessagesDropdown(messages) {
    const content = document.getElementById('messagesDropdownContent');
    
    if (messages.length === 0) {
        content.innerHTML = `
            <li class="dropdown-item-text text-center py-4 text-muted">
                <i class="bi bi-envelope display-6"></i><br>
                Nenhuma mensagem recente
            </li>
        `;
        return;
    }
    
    content.innerHTML = messages.map(message => `
        <li class="message-item ${!message.is_read ? 'unread' : ''}" onclick="openMessage(${message.id})" style="cursor: pointer;">
            <div class="d-flex align-items-start">
                <div class="message-avatar-small me-2">
                    <i class="bi bi-person"></i>
                </div>
                <div class="message-content-small">
                    <div class="message-subject">${escapeHtml(message.subject)}</div>
                    <div class="message-preview">De: ${escapeHtml(message.from_user.name)}</div>
                    <div class="message-preview">${escapeHtml(message.message.substring(0, 50))}${message.message.length > 50 ? '...' : ''}</div>
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="message-time">${message.time_ago}</div>
                        ${!message.is_read ? '<span class="badge bg-primary" style="font-size: 0.7rem;">Nova</span>' : ''}
                    </div>
                </div>
            </div>
        </li>
    `).join('');
}

function openMessage(messageId) {
    window.location.href = `/messages/${messageId}`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
</script>
