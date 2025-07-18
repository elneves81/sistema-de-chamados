<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Painel de Chamados - TV</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Pusher e Laravel Echo para integração em tempo real -->
    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.15.0/dist/echo.iife.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            min-height: 100vh;
            background: #181824;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow-x: hidden;
            overflow-y: hidden;
            position: relative;
            transition: all 0.5s ease;
        }
        
        body.urgent-alert {
            background: #dc2626;
            animation: urgentScreenPulse 2s ease-in-out infinite alternate;
        }
        
        body.has-tickets {
            background: #2563eb;
        }
        
        @keyframes urgentScreenPulse {
            0% {
                filter: brightness(1);
                box-shadow: inset 0 0 0 0 rgba(239, 68, 68, 0.2);
            }
            100% {
                filter: brightness(1.1);
                box-shadow: inset 0 0 100px 20px rgba(239, 68, 68, 0.3);
            }
        }
        
        body::before {
            display: none;
        }
        
        .container-fluid {
            position: relative;
            z-index: 2;
        }
        
        .tv-header {
            background: #232336;
            border-radius: 24px;
            margin: 20px;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .header-title {
            color: #ffffff;
            font-size: 2.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            text-shadow: 0 2px 20px rgba(0, 0, 0, 0.5);
        }
        
        .header-stats {
            display: flex;
            gap: 15px;
        }
        
        .stat-badge {
            background: #232336;
            border-radius: 12px;
            padding: 8px 16px;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9rem;
        }
        
        .header-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }
        
        .live-clock {
            color: #ffffff;
            font-size: 1.8rem;
            font-weight: 700;
            font-family: 'Inter', monospace;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }
        
        .fullscreen-btn {
            background: #6ee7b7;
            color: #222;
            border: 2px solid #fff;
            border-radius: 12px;
            padding: 10px 18px;
            font-size: 1.1rem;
            font-weight: 700;
            box-shadow: 0 2px 8px #6ee7b799;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            z-index: 2000;
            position: relative;
        }
        .fullscreen-btn:hover {
            background: #34d399;
            color: #fff;
            box-shadow: 0 4px 16px #34d399cc;
        }
        
        .tickets-container {
            padding: 0 20px 20px 20px;
        }
        
        .tickets-list {
            background: #232336;
            border-radius: 24px;
            padding: 20px;
            min-height: calc(100vh - 200px);
            max-height: calc(100vh - 200px);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
        }
        .ticket-row {
            background: #232336;
            border-left: 5px solid #6366f1;
            border-radius: 8px;
            margin-bottom: 8px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s ease;
            position: relative;
            animation: slideInLeft 0.5s ease-out;
            min-height: 60px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        /* Barra individual para cada ticket */
        .ticket-individual-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            border-radius: 0 0 8px 8px;
            transition: all 0.3s ease;
        }
        
        .ticket-individual-bar.priority-low {
            background: #2563eb;
        }
        
        .ticket-individual-bar.priority-normal,
        .ticket-individual-bar.priority-medium {
            background: #eab308;
        }
        
        .ticket-individual-bar.priority-high,
        .ticket-individual-bar.priority-urgent,
        .ticket-individual-bar.priority-critical {
            background: #ef4444;
        }
        @media (max-width: 600px) {
            .tickets-list {
                grid-template-columns: 1fr;
                padding: 10px 10px;
                max-height: none;
                overflow-y: auto;
            }
            .ticket-row {
                height: auto;
            }
        }
        
        @keyframes slideInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes urgentPulse {
            0%, 100% {
                box-shadow: 0 4px 20px rgba(239, 68, 68, 0.2);
            }
            50% {
                box-shadow: 0 8px 40px rgba(239, 68, 68, 0.4);
            }
        }
        
        @keyframes newTicketGlow {
            0% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            }
            70% {
                box-shadow: 0 0 0 20px rgba(34, 197, 94, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }
        
        .ticket-header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 20px;
        }
        
        .ticket-id {
            background: #6366f1;
            color: white;
            font-weight: 800;
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 6px;
            min-width: 50px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }
        
        .ticket-title {
            color: #eaeaea;
            font-size: 1.55rem;
            font-weight: 700;
            flex: 1;
            display: flex;
            align-items: center;
            gap: 12px;
            text-shadow: 0 2px 8px rgba(0, 0, 0, 0.75);
            letter-spacing: 0.25px;
        }
        
        .urgent-icon {
            color: #ef4444;
            font-size: 1.4rem;
            animation: urgentBlink 1s ease-in-out infinite alternate;
        }
        
        @keyframes urgentBlink {
            from { opacity: 1; }
            to { opacity: 0.5; }
        }
        
        .ticket-description {
            color: rgba(245, 245, 245, 0.95);
            font-size: 1.05rem;
            line-height: 1.6;
            margin-bottom: 22px;
            padding-left: 18px;
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
            font-weight: 500;
        }
        
        .ticket-badges {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 24px;
        }
        
        .badge-modern {
            padding: 8px 18px;
            border-radius: 22px;
            font-size: 1rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(14px);
            border: 2px solid rgba(255, 255, 255, 0.4);
            text-shadow: 0 1px 4px rgba(0, 0, 0, 0.6);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .badge-status-open {
            background: rgba(34, 197, 94, 0.3);
            color: #ffffff;
            border-color: rgba(34, 197, 94, 0.5);
            box-shadow: 0 2px 8px rgba(34, 197, 94, 0.3);
        }

        .badge-priority-urgent {
            background: rgba(239, 68, 68, 0.3);
            color: #ffffff;
            border-color: rgba(239, 68, 68, 0.5);
            box-shadow: 0 2px 8px rgba(239, 68, 68, 0.3);
        }

        .badge-priority-high {
            background: rgba(249, 115, 22, 0.3);
            color: #ffffff;
            border-color: rgba(249, 115, 22, 0.5);
            box-shadow: 0 2px 8px rgba(249, 115, 22, 0.3);
        }

        .badge-priority-medium {
            background: rgba(234, 179, 8, 0.3);
            color: #ffffff;
            border-color: rgba(234, 179, 8, 0.5);
            box-shadow: 0 2px 8px rgba(234, 179, 8, 0.3);
        }

        .badge-priority-low {
            background: rgba(59, 130, 246, 0.3);
            color: #ffffff;
            border-color: rgba(59, 130, 246, 0.5);
            box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
        }

        .badge-category {
            background: rgba(139, 92, 246, 0.3);
            color: #ffffff;
            border-color: rgba(139, 92, 246, 0.5);
            box-shadow: 0 2px 8px rgba(139, 92, 246, 0.3);
        }
        
        .ticket-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-top: 16px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .ticket-user {
            display: flex;
            align-items: center;
            gap: 12px;
            color: rgba(255, 255, 255, 0.9);
            font-weight: 500;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #6366f1;
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        
        .ticket-date {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.95rem;
            font-weight: 500;
        }
        
        .no-tickets {
            text-align: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 1.5rem;
            font-weight: 500;
            padding: 60px 20px;
        }
        
        .no-tickets i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        /* Indicadores de status */
        .connection-status {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1000;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 14px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .connection-status.connected {
            background: rgba(34, 197, 94, 0.8);
            color: #fff;
        }

        .connection-status.disconnected {
            background: rgba(239, 68, 68, 0.8);
            color: #fff;
        }

        /* Notificação de som */
        .sound-notice {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(0, 0, 0, 0.9);
            color: white;
            padding: 20px 30px;
            border-radius: 15px;
            text-align: center;
            z-index: 10000;
            font-size: 1.1rem;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.2);
            opacity: 1;
        }
        
        .sound-notice.show {
            opacity: 1;
        }
            right: 20px;
            z-index: 4000;
            display: flex;
            gap: 8px;
        }

        .settings-btn {
            background: rgba(255, 255, 255, 0.15);
            color: #fff;
            border: none;
            border-radius: 12px;
            padding: 8px 12px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            transition: all 0.3s ease;
        }

        .settings-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: translateY(-2px);
        }

        .settings-btn.active {
            background: rgba(34, 197, 94, 0.8);
        }

        /* Notificações */
        .notification {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1001;
            background: rgba(34, 197, 94, 0.95);
            color: #fff;
            padding: 16px 24px;
            border-radius: 16px;
            font-weight: 600;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transform: translateX(400px);
            transition: transform 0.4s ease;
        }

        .notification.show {
            transform: translateX(0);
        }

        .notification.error {
            background: rgba(239, 68, 68, 0.95);
        }

        /* Animações para notificações */
        @keyframes slideInRight {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }

        @keyframes slideOutRight {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }

        .footer-cycling, .footer-bar, .footer {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100vw;
            min-height: 44px;
            background: linear-gradient(90deg,#6366f1 0%,#8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 3000;
            box-shadow: 0 -2px 24px #6366f1cc;
            animation: fadeClockBar 3s infinite alternate;
        }
        .footer-cycling span, .footer-bar span, .footer span {
            z-index: 4000;
            position: relative;
        }
        .horizontal-bar {
            border: none;
            height: 8px;
            width: 100%;
            margin: 10px 0 20px 0;
            border-radius: 8px;
            transition: background 0.5s;
        }
        .horizontal-bar.low {
            background: #2563eb;
        }
        .horizontal-bar.medium {
            background: #eab308;
        }
        .horizontal-bar.high, .horizontal-bar.urgent, .horizontal-bar.critical {
            background: #ef4444;
        }

        .ticket-row.urgent {
            border-left-color: #ef4444;
            background: rgba(239, 68, 68, 0.12);
            animation: urgentPulse 2s ease-in-out infinite;
        }
        .ticket-row.recent {
            border-left-color: #22c55e;
            background: rgba(34, 197, 94, 0.12);
            animation: newTicketGlow 1.5s ease-out;
        }
        .ticket-row:hover {
            background: rgba(255, 255, 255, 0.15);
            transform: translateX(5px);
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.2);
        }
        
        .ticket-left {
            display: flex;
            align-items: center;
            gap: 15px;
            flex: 1;
        }
        
        .ticket-id-badge {
            background: #6366f1;
            color: white;
            font-weight: 800;
            font-size: 0.85rem;
            padding: 6px 12px;
            border-radius: 6px;
            min-width: 50px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(99, 102, 241, 0.3);
        }
        
        .ticket-content {
            flex: 1;
        }
        
        .ticket-title-compact {
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 4px;
            line-height: 1.2;
        }
        
        .ticket-meta {
            display: flex;
            gap: 15px;
            align-items: center;
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.7);
        }
        
        .ticket-right {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .priority-badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }
        
        .priority-urgent {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        
        .priority-high {
            background: rgba(249, 115, 22, 0.2);
            color: #f97316;
            border: 1px solid rgba(249, 115, 22, 0.3);
        }
        
        .priority-medium {
            background: rgba(234, 179, 8, 0.2);
            color: #eab308;
            border: 1px solid rgba(234, 179, 8, 0.3);
        }
        
        .priority-low {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }
        
        .ticket-time {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
            font-weight: 500;
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
    </style>
    </style>
    <style>
        .horizontal-bar {
            border: none;
            height: 4px;
            background: linear-gradient(90deg, #6366f1 0%, #8b5cf6 100%);
            margin: 10px 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <!-- Script para abrir automaticamente em nova aba se não estiver em window.top -->
    <script>
    // Configuração do Laravel Echo com Pusher
    window.Echo = new Echo({
        broadcaster: 'pusher',
        key: 'abcd1234efgh5678', // Chave real do Pusher
        cluster: 'sa1', // Cluster real do Pusher
        forceTLS: true
    });
    if (window.top === window.self && !window.location.search.includes('noauto')) {
        // Só abre em nova aba se não estiver em iframe ou popup
        window.open(window.location.href + '?noauto=1', '_blank');
        window.location.href = '/'; // Redireciona para home para evitar duplicidade
    }
    </script>
    <!-- Indicador de conexão -->
    <div id="connection-status" class="connection-status connected">
        <i class="bi bi-wifi"></i> Conectado
    </div>

    <!-- Configurações do painel -->
    <div class="panel-settings" style="z-index:10001;">
        <button class="settings-btn" id="auto-refresh-btn" onclick="toggleAutoRefresh()">
            <i class="bi bi-arrow-clockwise"></i> Auto: ON
        </button>
        <button class="settings-btn" id="sound-btn" onclick="toggleSound()">
            <i class="bi bi-volume-up"></i> Som: ON
        </button>
        <button class="settings-btn" onclick="refreshNow()">
            <i class="bi bi-arrow-clockwise"></i> Atualizar
        </button>
    </div>

    <div class="container-fluid">
        <div class="tv-header">
            <div class="header-left">
                <div class="header-title">
                    <i class="bi bi-display"></i> PAINEL DE CHAMADOS
                </div>
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="bi bi-ticket-perforated"></i>
                        <span id="total-tickets">{{ $tickets->where('status', 'open')->count() }}</span> Abertos
                    </div>
                    <div class="stat-badge">
                        <i class="bi bi-exclamation-triangle"></i>
                        <span id="urgent-tickets">{{ $tickets->where('status', 'open')->where('priority', 'urgent')->count() }}</span> Urgentes
                    </div>
                </div>
            </div>
            <div class="header-right">
                <div class="live-clock" id="live-clock"></div>
                <button class="fullscreen-btn" id="fullscreen-btn">
                    <i class="bi bi-arrows-fullscreen"></i> Tela Cheia
                </button>
            </div>
        </div>

        <hr class="horizontal-bar" id="priority-bar" style="z-index: 1000; position: relative;" />

        <div class="tickets-container">
            <div class="tickets-list" id="tickets-list">
                @php
                    $openTickets = $tickets->where('status', 'open')->sortByDesc('created_at')->values();
                @endphp
                @forelse($openTickets as $ticket)
                    <div class="ticket-row @if($ticket->priority=='urgent' || $ticket->priority=='high' || $ticket->priority=='critical') urgent @endif" 
                         data-ticket-id="{{ $ticket->id }}" 
                         data-priority="{{ $ticket->priority }}">
                        <div class="ticket-left">
                            <div class="ticket-id-badge">#{{ $ticket->id }}</div>
                            <div class="ticket-content">
                                <div class="ticket-title-compact">
                                    {{ $ticket->title }}
                                    @if($ticket->priority=='urgent' || $ticket->priority=='high' || $ticket->priority=='critical') 
                                        <i class="bi bi-exclamation-triangle urgent-icon" style="color: #ef4444; margin-left: 8px;"></i>
                                    @endif
                                </div>
                                <div class="ticket-meta">
                                    <span><i class="bi bi-person"></i> {{ $ticket->user->name ?? 'N/A' }}</span>
                                    <span><i class="bi bi-geo-alt"></i> {{ $ticket->local ?? 'Local não informado' }}</span>
                                    <span><i class="bi bi-tag"></i> {{ $ticket->category->name ?? 'Sem categoria' }}</span>
                                    <span><i class="bi bi-person-badge"></i> {{ $ticket->assignedTo->name ?? 'Não atribuído' }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="ticket-right">
                            <div class="priority-badge priority-{{ strtolower($ticket->priority) }} badge-priority-{{ strtolower($ticket->priority) }}">
                                @php
                                    $priorityLabels = [
                                        'low' => 'Baixa',
                                        'normal' => 'Normal',
                                        'high' => 'Alta',
                                        'critical' => 'Crítica',
                                        'urgent' => 'Urgente',
                                    ];
                                @endphp
                                {{ $priorityLabels[strtolower($ticket->priority)] ?? ucfirst($ticket->priority) }}
                            </div>
                            <div class="ticket-time">
                                {{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '-' }}
                            </div>
                        </div>
                        <!-- Barra individual colorida baseada na prioridade -->
                        <div class="ticket-individual-bar priority-{{ strtolower($ticket->priority) }}"></div>
                    </div>
                @empty
                    <div class="no-tickets">
                        <i class="bi bi-check-circle"></i>
                        <div>Nenhum chamado aberto no momento</div>
                        <small>Todos os chamados foram resolvidos!</small>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Som para notificações -->
    <audio id="notification-sound" preload="auto" style="display: none;">
        <!-- Som será gerado via Web Audio API -->
    </audio>

    <div class="footer-cycling" id="footer-cycling">DITIS - BY ELN</div>

    <script>
    // Variáveis globais
    let soundEnabled = localStorage.getItem('tv_sound') !== 'false';
    let autoRefreshEnabled = localStorage.getItem('tv_auto_refresh') !== 'false';
    let refreshInterval = 15000; // 15 segundos
    let refreshTimer;
    let lastTicketIds = JSON.parse(localStorage.getItem('lastTicketIds') || '[]');
    let userHasInteracted = false;
    let audioContext = null;
    let soundAlertCount = 0;
    const maxSoundAlerts = 3;

    // Elementos DOM
    const connectionStatus = document.getElementById('connection-status');
    const autoRefreshBtn = document.getElementById('auto-refresh-btn');
    const soundBtn = document.getElementById('sound-btn');
    const fullscreenBtn = document.getElementById('fullscreen-btn');
    const liveClock = document.getElementById('live-clock');
    const ticketsList = document.getElementById('tickets-list');

    // Função para gerar som de alerta via Web Audio API
    function createAlertSound() {
        if (!audioContext) {
            try {
                audioContext = new (window.AudioContext || window.webkitAudioContext)();
            } catch (e) {
                console.log('Web Audio API não suportado');
                return;
            }
        }

        if (audioContext.state === 'suspended') {
            audioContext.resume();
        }

        // Cria um som de alerta (bip duplo)
        const oscillator = audioContext.createOscillator();
        const gainNode = audioContext.createGain();
        
        oscillator.connect(gainNode);
        gainNode.connect(audioContext.destination);
        
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime);
        oscillator.frequency.setValueAtTime(1000, audioContext.currentTime + 0.1);
        oscillator.frequency.setValueAtTime(800, audioContext.currentTime + 0.2);
        
        gainNode.gain.setValueAtTime(0, audioContext.currentTime);
        gainNode.gain.linearRampToValueAtTime(0.3, audioContext.currentTime + 0.05);
        gainNode.gain.linearRampToValueAtTime(0, audioContext.currentTime + 0.15);
        gainNode.gain.linearRampToValueAtTime(0.3, audioContext.currentTime + 0.2);
        gainNode.gain.linearRampToValueAtTime(0, audioContext.currentTime + 0.35);
        
        oscillator.start(audioContext.currentTime);
        oscillator.stop(audioContext.currentTime + 0.4);
    }

    // Função para tocar som que funciona sem interação prévia
    function playNotificationSound() {
        if (!soundEnabled) return;
        if (soundAlertCount >= maxSoundAlerts) return;
        soundAlertCount++;
        try {
            createAlertSound();
        } catch (e) {
            console.log('Erro ao tocar som:', e.message);
        }
    }

    // Mostra notificação para interação de som (fixa até interação)
    function showSoundNotice() {
        if (soundEnabled && !userHasInteracted && !document.querySelector('.sound-notice')) {
            const notice = document.createElement('div');
            notice.className = 'sound-notice';
            notice.innerHTML = `
                <i class="bi bi-volume-up" style="font-size: 2rem; margin-bottom: 10px;"></i><br>
                <strong>Clique em qualquer lugar para ativar som</strong><br>
                <small>Necessário para alertas de tickets urgentes</small>
            `;
            document.body.appendChild(notice);
        }
    }

    // Detecta primeira interação do usuário para habilitar áudio
    function enableAudioOnInteraction() {
        if (!userHasInteracted) {
            userHasInteracted = true;
            if (audioContext && audioContext.state === 'suspended') {
                audioContext.resume();
            }
            // Remove notificação de som se existir
            const soundNotice = document.querySelector('.sound-notice');
            if (soundNotice) {
                soundNotice.remove();
            }
            document.removeEventListener('click', enableAudioOnInteraction);
            document.removeEventListener('keydown', enableAudioOnInteraction);
            document.removeEventListener('touchstart', enableAudioOnInteraction);
        }
    }

    // Adiciona listeners para primeira interação
    document.addEventListener('click', enableAudioOnInteraction);
    document.addEventListener('keydown', enableAudioOnInteraction);
    document.addEventListener('touchstart', enableAudioOnInteraction);

    // Inicialização
    document.addEventListener('DOMContentLoaded', function() {
        updateUI();
        updateClock();
        setInterval(updateClock, 1000);
        setInterval(updateFooterCycling, 3000);
        updateFooterCycling();
        if (autoRefreshEnabled) {
            startAutoRefresh();
        }
        // Verifica novos tickets na primeira carga
        checkForNewTickets();
        // Verifica alerta urgente na primeira carga
        checkUrgentAlert();
        // Mostra notificação de som se necessário
        setTimeout(showSoundNotice, 2000);
    });

    // Atualiza interface baseada nas configurações
    function updateUI() {
        autoRefreshBtn.textContent = autoRefreshEnabled ? 'Auto: ON' : 'Auto: OFF';
        autoRefreshBtn.className = autoRefreshEnabled ? 'settings-btn active' : 'settings-btn';
        soundBtn.innerHTML = soundEnabled ? '<i class="bi bi-volume-up"></i> Som: ON' : '<i class="bi bi-volume-mute"></i> Som: OFF';
        soundBtn.className = soundEnabled ? 'settings-btn active' : 'settings-btn';
    }

    // Atualiza relógio
    function updateClock() {
        const now = new Date();
        liveClock.textContent = now.toLocaleTimeString('pt-BR');
    }

    // Atualiza status de conexão
    function updateConnectionStatus(connected) {
        if (connected) {
            connectionStatus.innerHTML = '<i class="bi bi-wifi"></i> Conectado';
            connectionStatus.className = 'connection-status connected';
        } else {
            connectionStatus.innerHTML = '<i class="bi bi-wifi-off"></i> Desconectado';
            connectionStatus.className = 'connection-status disconnected';
        }
    }

    // Carrega tickets via AJAX
    async function loadTickets() {
        try {
            updateConnectionStatus(true);
            // WebSocket para atualização em tempo real (exemplo com Laravel Echo/Pusher)
            if (!window.tvSocketConnected) {
                window.tvSocketConnected = true;
                if (window.Echo) {
                    Echo.channel('tickets')
                        .listen('TicketCreated', (e) => {
                            loadTickets();
                            showNotification('Novo chamado recebido!', 'success');
                        });
                }
            }
            const response = await fetch('{{ route("tickets.boardTvEnhanced") }}', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            if (!response.ok) throw new Error('Erro na requisição');
            const html = await response.text();
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newTicketsList = doc.querySelector('#tickets-list');
            if (newTicketsList) {
                const oldIds = getCurrentTicketIds();
                ticketsList.innerHTML = newTicketsList.innerHTML;
                updateHeaderStats();
                checkForNewTickets(oldIds);
            }
        } catch (error) {
            console.error('Erro ao carregar tickets:', error);
            updateConnectionStatus(false);
            showNotification('Erro ao carregar tickets', 'error');
        }
    }

    // Obtém IDs dos tickets atuais
    function getCurrentTicketIds() {
        return Array.from(document.querySelectorAll('.ticket-row')).map(e => e.dataset.ticketId);
    }

    // Atualiza estatísticas no header
    function updateHeaderStats() {
        const totalTickets = document.querySelectorAll('.ticket-row').length;
        const urgentTickets = document.querySelectorAll('.ticket-row.urgent').length;
        
        document.getElementById('total-tickets').textContent = totalTickets;
        document.getElementById('urgent-tickets').textContent = urgentTickets;
        
        // Aplica alerta vermelho na tela se houver tickets urgentes ou críticos
        checkUrgentAlert();
        // Aplica cor de fundo se houver qualquer chamado aberto
        checkAnyTicketAlert();
        updatePriorityBar();
    }
    
    // Verifica tickets urgentes e aplica alerta visual na tela
    function checkUrgentAlert() {
        // Verifica por tickets com prioridade urgent, high ou critical
        const urgentTickets = document.querySelectorAll('[data-priority="urgent"], [data-priority="high"], [data-priority="critical"]');
        const hasUrgentTickets = urgentTickets.length > 0;
        
        if (hasUrgentTickets) {
            document.body.classList.add('urgent-alert');
            
            // Toca som de alerta se habilitado (apenas uma vez por verificação)
            if (soundEnabled && !document.body.classList.contains('urgent-sound-played')) {
                document.body.classList.add('urgent-sound-played');
                playNotificationSound();
                
                // Remove a flag após 10 segundos para permitir novo som
                setTimeout(() => {
                    document.body.classList.remove('urgent-sound-played');
                }, 10000);
            }
        } else {
            document.body.classList.remove('urgent-alert');
            document.body.classList.remove('urgent-sound-played');
        }
    }

    // Aplica cor de fundo se houver qualquer chamado aberto
    function checkAnyTicketAlert() {
        const totalTickets = document.querySelectorAll('.ticket-row').length;
        if (totalTickets > 0) {
            document.body.classList.add('has-tickets');
        } else {
            document.body.classList.remove('has-tickets');
        }
    }

    // Verifica novos tickets
    function checkForNewTickets(oldIds = []) {
        const currentIds = getCurrentTicketIds();
        const newIds = currentIds.filter(id => !oldIds.includes(id));
        
        if (newIds.length > 0) {
            // Toca som se habilitado
            if (soundEnabled) {
                playNotificationSound();
            }
            
            // Adiciona animação aos novos tickets
            newIds.forEach(id => {
                const el = document.querySelector('.ticket-row[data-ticket-id="'+id+'"]');
                if (el) {
                    el.classList.add('recent');
                    // Remove a classe após a animação
                    setTimeout(() => el.classList.remove('recent'), 3000);
                }
            });
            
            // Mostra notificação
            showNotification(`${newIds.length} novo(s) chamado(s) recebido(s)!`);
        }
        
        // Atualiza localStorage
        localStorage.setItem('lastTicketIds', JSON.stringify(currentIds));
        lastTicketIds = currentIds;
    }

    // Mostra notificação
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `<i class="bi bi-bell"></i> ${message}`;
        document.body.appendChild(notification);
        
        // Mostra notificação
        setTimeout(() => notification.classList.add('show'), 100);
        
        // Remove após 5 segundos
        setTimeout(() => {
            notification.classList.remove('show');
            setTimeout(() => notification.remove(), 400);
        }, 5000);
    }

    // Toggle auto-refresh
    function toggleAutoRefresh() {
        autoRefreshEnabled = !autoRefreshEnabled;
        localStorage.setItem('tv_auto_refresh', autoRefreshEnabled);
        
        if (autoRefreshEnabled) {
            startAutoRefresh();
        } else {
            stopAutoRefresh();
        }
        
        updateUI();
    }

    // Toggle som
    function toggleSound() {
        soundEnabled = !soundEnabled;
        localStorage.setItem('tv_sound', soundEnabled);
        updateUI();
    }

    // Atualizar agora
    function refreshNow() {
        loadTickets();
        showNotification('Painel atualizado!');
    }

    // Inicia auto-refresh
    function startAutoRefresh() {
        if (refreshTimer) clearInterval(refreshTimer);
        refreshTimer = setInterval(loadTickets, refreshInterval);
    }

    // Para auto-refresh
    function stopAutoRefresh() {
        if (refreshTimer) {
            clearInterval(refreshTimer);
            refreshTimer = null;
        }
    }

    // Corrige botão tela cheia para funcionar em todos navegadores
    fullscreenBtn.addEventListener('click', function() {
        const docElm = document.documentElement;
        if (!document.fullscreenElement && docElm.requestFullscreen) {
            docElm.requestFullscreen();
            fullscreenBtn.innerHTML = '<i class="bi bi-fullscreen-exit"></i> Sair Tela Cheia';
        } else if (document.exitFullscreen) {
            document.exitFullscreen();
            fullscreenBtn.innerHTML = '<i class="bi bi-arrows-fullscreen"></i> Tela Cheia';
        }
    });
    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement) {
            fullscreenBtn.innerHTML = '<i class="bi bi-arrows-fullscreen"></i> Tela Cheia';
        }
    });

    // Para o auto-refresh quando a página não está visível (otimização)
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            stopAutoRefresh();
        } else if (autoRefreshEnabled) {
            startAutoRefresh();
        }
    });

    // Atalhos de teclado
    document.addEventListener('keydown', function(e) {
        switch(e.key) {
            case 'F11':
                e.preventDefault();
                fullscreenBtn.click();
                break;
            case 'r':
            case 'R':
                if (e.ctrlKey) {
                    e.preventDefault();
                    refreshNow();
                }
                break;
            case 'a':
            case 'A':
                if (e.ctrlKey) {
                    e.preventDefault();
                    toggleAutoRefresh();
                }
                break;
            case 's':
            case 'S':
                if (e.ctrlKey) {
                    e.preventDefault();
                    toggleSound();
                }
                break;
        }
    });

    // Detecta inatividade e pausa refresh para economizar recursos
    let inactivityTimer;
    let isInactive = false;

    function resetInactivityTimer() {
        clearTimeout(inactivityTimer);
        if (isInactive) {
            isInactive = false;
            if (autoRefreshEnabled) {
                startAutoRefresh();
            }
            showNotification('Painel reativado');
        }
        
        inactivityTimer = setTimeout(() => {
            isInactive = true;
            stopAutoRefresh();
            showNotification('Painel pausado por inatividade');
        }, 300000); // 5 minutos
    }

    // Eventos para detectar atividade
    ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(event => {
        document.addEventListener(event, resetInactivityTimer, true);
    });

    // Inicia timer de inatividade
    resetInactivityTimer();

    function updateFooterCycling() {
        const bar = document.getElementById('footer-cycling');
        const now = new Date();
        const dateStr = now.toLocaleDateString('pt-BR');
        const timeStr = now.toLocaleTimeString('pt-BR');
        if (bar) {
            bar.innerHTML = `<span style="font-size:1.3rem;font-weight:700;color:#fff;animation:fadeClock 3s infinite alternate;">${dateStr} - ${timeStr}</span>`;
        }
    }

    function updatePriorityBar() {
        const bar = document.getElementById('priority-bar');
        if (!bar) return;
        // Só define a cor se ainda não foi definida
        if (!bar.dataset.locked) {
            // Aguarda o DOM estar pronto e os tickets renderizados
            setTimeout(() => {
                const firstTicket = document.querySelector('.ticket-row');
                if (firstTicket) {
                    const p = firstTicket.getAttribute('data-priority');
                    bar.className = 'horizontal-bar';
                    if (p === 'critical' || p === 'urgent' || p === 'high') {
                        bar.classList.add('high');
                    } else if (p === 'medium') {
                        bar.classList.add('medium');
                    } else if (p === 'low') {
                        bar.classList.add('low');
                    }
                    // Trava a cor para não mudar mais
                    bar.dataset.locked = 'true';
                }
            }, 200); // Pequeno delay para garantir renderização
        }
    }
    </script>
</body>
</html>
