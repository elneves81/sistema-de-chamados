@extends('layouts.app')

@section('title', 'Preferências de Notificação')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="bi bi-bell"></i>
                    Preferências de Notificação
                </h2>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Configuração de Canais -->
                <div class="col-lg-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-gear"></i>
                                Canais de Notificação
                            </h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('notifications.preferences.update') }}" method="POST">
                                @csrf
                                @method('PUT')

                                <!-- Email -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-envelope"></i>
                                        Email
                                    </label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->email }}" disabled>
                                    <small class="text-muted">Email cadastrado no sistema</small>
                                </div>

                                <!-- Telefone -->
                                <div class="mb-4">
                                    <label class="form-label fw-bold">
                                        <i class="bi bi-phone"></i>
                                        Telefone (SMS)
                                    </label>
                                    <input type="text" class="form-control" value="{{ auth()->user()->phone }}" disabled>
                                    <small class="text-muted">Atualizar no perfil</small>
                                </div>

                                <!-- WhatsApp -->
                                <div class="mb-4">
                                    <label for="whatsapp" class="form-label fw-bold">
                                        <i class="bi bi-whatsapp"></i>
                                        WhatsApp
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="whatsapp" 
                                           name="whatsapp" 
                                           value="{{ old('whatsapp', auth()->user()->whatsapp) }}"
                                           placeholder="+55 11 98765-4321">
                                    <small class="text-muted">Formato: +55 11 98765-4321</small>
                                </div>

                                <!-- Telegram -->
                                <div class="mb-4">
                                    <label for="telegram_id" class="form-label fw-bold">
                                        <i class="bi bi-telegram"></i>
                                        Telegram Chat ID
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="telegram_id" 
                                           name="telegram_id" 
                                           value="{{ old('telegram_id', auth()->user()->telegram_id) }}"
                                           placeholder="123456789">
                                    <small class="text-muted">
                                        <a href="#" id="telegram-help" data-bs-toggle="modal" data-bs-target="#telegramModal">
                                            Como obter meu Chat ID?
                                        </a>
                                    </small>
                                </div>

                                <!-- Teste de Notificações -->
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="card-title">Testar Notificações</h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="testNotification('email')">
                                                <i class="bi bi-envelope"></i> Email
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="testNotification('sms')">
                                                <i class="bi bi-phone"></i> SMS
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-info" onclick="testNotification('telegram')">
                                                <i class="bi bi-telegram"></i> Telegram
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success" onclick="testNotification('whatsapp')">
                                                <i class="bi bi-whatsapp"></i> WhatsApp
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-save"></i>
                                    Salvar Configurações
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Configuração de Eventos -->
                <div class="col-lg-8">
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">
                                <i class="bi bi-calendar-event"></i>
                                Tipos de Notificação
                            </h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-4">
                                Configure quais eventos você deseja ser notificado e por quais canais:
                            </p>

                            <form action="{{ route('notifications.preferences.update') }}" method="POST" id="eventsForm">
                                @csrf
                                @method('PUT')

                                <!-- Manter campos de contato sincronizados -->
                                <input type="hidden" name="telegram_id" :value="document.getElementById('telegram_id')?.value">
                                <input type="hidden" name="whatsapp" :value="document.getElementById('whatsapp')?.value">

                                @php
                                    $events = [
                                        'ticket.created' => [
                                            'title' => 'Novo Chamado Criado',
                                            'description' => 'Quando um novo chamado for criado',
                                            'icon' => 'bi-plus-circle',
                                            'color' => 'primary'
                                        ],
                                        'ticket.assigned' => [
                                            'title' => 'Chamado Atribuído',
                                            'description' => 'Quando um chamado for atribuído a você',
                                            'icon' => 'bi-person-check',
                                            'color' => 'success'
                                        ],
                                        'ticket.status_changed' => [
                                            'title' => 'Status Alterado',
                                            'description' => 'Quando o status de um chamado mudar',
                                            'icon' => 'bi-arrow-repeat',
                                            'color' => 'warning'
                                        ],
                                        'ticket.commented' => [
                                            'title' => 'Novo Comentário',
                                            'description' => 'Quando houver um novo comentário',
                                            'icon' => 'bi-chat-dots',
                                            'color' => 'info'
                                        ],
                                        'ticket.sla_warning' => [
                                            'title' => 'Alerta de SLA',
                                            'description' => 'Quando o SLA estiver próximo do vencimento',
                                            'icon' => 'bi-exclamation-triangle',
                                            'color' => 'danger'
                                        ],
                                    ];
                                @endphp

                                @foreach($events as $eventType => $eventInfo)
                                    @php
                                        $eventPrefs = $preferences['events'][$eventType] ?? ['enabled' => true, 'channels' => ['email']];
                                    @endphp

                                    <div class="card mb-3 border-{{ $eventInfo['color'] }}">
                                        <div class="card-body">
                                            <div class="d-flex align-items-start">
                                                <div class="flex-shrink-0 me-3">
                                                    <i class="bi {{ $eventInfo['icon'] }} fs-2 text-{{ $eventInfo['color'] }}"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <div>
                                                            <h6 class="mb-1">{{ $eventInfo['title'] }}</h6>
                                                            <small class="text-muted">{{ $eventInfo['description'] }}</small>
                                                        </div>
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" 
                                                                   type="checkbox" 
                                                                   name="events[{{ $eventType }}][enabled]" 
                                                                   value="1"
                                                                   id="event_{{ $eventType }}"
                                                                   {{ $eventPrefs['enabled'] ? 'checked' : '' }}
                                                                   onchange="toggleChannels('{{ $eventType }}')">
                                                            <label class="form-check-label" for="event_{{ $eventType }}">
                                                                Ativo
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div id="channels_{{ $eventType }}" class="{{ $eventPrefs['enabled'] ? '' : 'd-none' }}">
                                                        <div class="d-flex flex-wrap gap-2 mt-2">
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="checkbox" 
                                                                       name="events[{{ $eventType }}][channels][]" 
                                                                       value="email"
                                                                       id="channel_{{ $eventType }}_email"
                                                                       {{ in_array('email', $eventPrefs['channels']) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="channel_{{ $eventType }}_email">
                                                                    <i class="bi bi-envelope"></i> Email
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="checkbox" 
                                                                       name="events[{{ $eventType }}][channels][]" 
                                                                       value="sms"
                                                                       id="channel_{{ $eventType }}_sms"
                                                                       {{ in_array('sms', $eventPrefs['channels']) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="channel_{{ $eventType }}_sms">
                                                                    <i class="bi bi-phone"></i> SMS
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="checkbox" 
                                                                       name="events[{{ $eventType }}][channels][]" 
                                                                       value="telegram"
                                                                       id="channel_{{ $eventType }}_telegram"
                                                                       {{ in_array('telegram', $eventPrefs['channels']) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="channel_{{ $eventType }}_telegram">
                                                                    <i class="bi bi-telegram"></i> Telegram
                                                                </label>
                                                            </div>
                                                            <div class="form-check">
                                                                <input class="form-check-input" 
                                                                       type="checkbox" 
                                                                       name="events[{{ $eventType }}][channels][]" 
                                                                       value="whatsapp"
                                                                       id="channel_{{ $eventType }}_whatsapp"
                                                                       {{ in_array('whatsapp', $eventPrefs['channels']) ? 'checked' : '' }}>
                                                                <label class="form-check-label" for="channel_{{ $eventType }}_whatsapp">
                                                                    <i class="bi bi-whatsapp"></i> WhatsApp
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-save"></i>
                                        Salvar Preferências de Eventos
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Telegram -->
<div class="modal fade" id="telegramModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-telegram"></i>
                    Como Configurar o Telegram
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <ol id="telegram-instructions">
                    <li class="mb-2">Carregando instruções...</li>
                </ol>
                <div class="alert alert-info mt-3">
                    <i class="bi bi-info-circle"></i>
                    <strong>Dica:</strong> O Chat ID é um número único que identifica sua conversa com o bot.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function toggleChannels(eventType) {
    const checkbox = document.getElementById(`event_${eventType}`);
    const channelsDiv = document.getElementById(`channels_${eventType}`);
    
    if (checkbox.checked) {
        channelsDiv.classList.remove('d-none');
    } else {
        channelsDiv.classList.add('d-none');
    }
}

function testNotification(channel) {
    if (confirm(`Deseja enviar uma notificação de teste via ${channel}?`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("notifications.preferences.test") }}';
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        
        const channelInput = document.createElement('input');
        channelInput.type = 'hidden';
        channelInput.name = 'channel';
        channelInput.value = channel;
        
        form.appendChild(csrfInput);
        form.appendChild(channelInput);
        document.body.appendChild(form);
        form.submit();
    }
}

// Carregar instruções do Telegram
document.addEventListener('DOMContentLoaded', function() {
    fetch('{{ route("notifications.telegram.instructions") }}')
        .then(response => response.json())
        .then(data => {
            const list = document.getElementById('telegram-instructions');
            list.innerHTML = '';
            
            data.instructions.forEach(instruction => {
                const li = document.createElement('li');
                li.className = 'mb-2';
                li.textContent = instruction;
                list.appendChild(li);
            });
            
            if (data.bot_url) {
                const link = document.createElement('a');
                link.href = data.bot_url;
                link.target = '_blank';
                link.className = 'btn btn-info w-100 mt-3';
                link.innerHTML = '<i class="bi bi-telegram"></i> Abrir Bot no Telegram';
                list.parentElement.appendChild(link);
            }
        })
        .catch(error => {
            console.error('Erro ao carregar instruções:', error);
        });
});
</script>
@endpush

@push('styles')
<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
    font-size: 0.875rem;
}
</style>
@endpush
