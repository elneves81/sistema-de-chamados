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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 25%, #16213e 50%, #0f3460 75%, #533483 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow-x: hidden;
            overflow-y: hidden;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.1) 0%, transparent 50%);
            pointer-events: none;
            z-index: 1;
        }
        
        .container-fluid {
            position: relative;
            z-index: 2;
        }
        
        .tv-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 24px;
            margin: 20px;
            padding: 20px 40px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 
                0 8px 32px rgba(0, 0, 0, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
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
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 8px 16px;
            color: #ffffff;
            font-weight: 600;
            font-size: 0.9rem;
            backdrop-filter: blur(10px);
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
            background: rgba(99, 102, 241, 0.8);
            border: 1px solid rgba(99, 102, 241, 0.6);
            color: #ffffff;
            border-radius: 16px;
            padding: 12px 20px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .fullscreen-btn:hover {
            background: rgba(99, 102, 241, 1);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
        }
        
        .tickets-container {
            padding: 0 20px 20px 20px;
        }
        
        .tickets-list {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 18px;
            min-height: 70vh;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2), inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }
        .ticket-row {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 16px;
            margin-bottom: 10px;
            padding: 14px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
            animation: slideInUp 0.6s ease-out;
        }
        
        .ticket-row::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(180deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 0 3px 3px 0;
        }
        
        .ticket-row.urgent::before {
            background: linear-gradient(180deg, #ef4444 0%, #dc2626 100%);
            box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
        }
        
        .ticket-row.urgent {
            border-color: rgba(239, 68, 68, 0.3);
            animation: urgentPulse 2s ease-in-out infinite;
        }
        
        .ticket-row.recent {
            border-color: rgba(34, 197, 94, 0.4);
            background: rgba(34, 197, 94, 0.1);
            animation: newTicketGlow 1.5s ease-out;
        }
        
        .ticket-row.recent::before {
            background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%);
            box-shadow: 0 0 20px rgba(34, 197, 94, 0.6);
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
            gap: 20px;
            margin-bottom: 16px;
        }
        
        .ticket-id {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: #ffffff;
            font-size: 1.1rem;
            font-weight: 800;
            padding: 8px 16px;
            border-radius: 12px;
            min-width: 80px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }
        
        .ticket-title {
            color: #ffffff;
            font-size: 1.6rem;
            font-weight: 700;
            flex: 1;
            display: flex;
            align-items: center;
            gap: 12px;
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
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
            line-height: 1.5;
            margin-bottom: 20px;
            padding-left: 20px;
        }
        
        .ticket-badges {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 16px;
        }
        
        .badge-modern {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 6px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .badge-status-open {
            background: rgba(34, 197, 94, 0.2);
            color: #22c55e;
            border-color: rgba(34, 197, 94, 0.3);
        }
        
        .badge-priority-urgent {
            background: rgba(239, 68, 68, 0.2);
            color: #ef4444;
            border-color: rgba(239, 68, 68, 0.3);
        }
        
        .badge-priority-high {
            background: rgba(249, 115, 22, 0.2);
            color: #f97316;
            border-color: rgba(249, 115, 22, 0.3);
        }
        
        .badge-priority-medium {
            background: rgba(234, 179, 8, 0.2);
            color: #eab308;
            border-color: rgba(234, 179, 8, 0.3);
        }
        
        .badge-priority-low {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            border-color: rgba(59, 130, 246, 0.3);
        }
        
        .badge-category {
            background: rgba(139, 92, 246, 0.2);
            color: #8b5cf6;
            border-color: rgba(139, 92, 246, 0.3);
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
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
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
        
        .toast-notification {
            position: fixed;
            top: 30px;
            right: 30px;
            background: rgba(34, 197, 94, 0.95);
            color: #ffffff;
            padding: 16px 24px;
            border-radius: 16px;
            font-weight: 600;
            z-index: 1000;
            transform: translateX(400px);
            transition: transform 0.4s ease;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }
        
        .toast-notification.show {
            transform: translateX(0);
        }
        
        .refresh-indicator {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .refresh-indicator i {
            animation: spin 2s linear infinite;
        }
        
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }
        
        @media (max-width: 1200px) {
            .header-title { font-size: 2rem; }
            .ticket-header { flex-direction: column; align-items: flex-start; gap: 12px; }
            .ticket-title { font-size: 1.4rem; }
        }
        
        @media (max-width: 768px) {
            .tv-header { 
                flex-direction: column; 
                gap: 15px; 
                padding: 20px; 
            }
            .header-left { flex-direction: column; gap: 10px; }
            .header-title { font-size: 1.8rem; }
            .tickets-list { padding: 20px; }
            .ticket-row { padding: 20px; }
            .ticket-footer { flex-direction: column; align-items: flex-start; gap: 12px; }
        }
        
        .footer-clock-bar {
            position: fixed;
            left: 0;
            bottom: 0;
            width: 100vw;
            height: 54px;
            background: linear-gradient(90deg,#6366f1 0%,#8b5cf6 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            box-shadow: 0 -2px 24px #6366f1cc;
            animation: fadeClockBar 3s infinite alternate;
        }
        @keyframes fadeClockBar {
            0% { filter: brightness(1.1); }
            100% { filter: brightness(1.5); }
        }
        @keyframes fadeClock {
            0% { opacity: 0.7; }
            100% { opacity: 1; }
        }
    </style>
</head>
<body>
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
                <button class="fullscreen-btn" id="fullscreen-btn"><i class="bi bi-arrows-fullscreen"></i> Tela cheia</button>
            </div>
        </div>
        <div class="tickets-container">
            <div class="tickets-list" id="tickets-list">
                @php
                    $openTickets = $tickets->where('status', 'open')->sortByDesc('created_at')->values();
                    $recentIds = session('recent_ticket_ids', []);
                @endphp
                @forelse($openTickets as $ticket)
                    <div class="ticket-row @if($ticket->priority=='urgent') urgent @endif @if(in_array($ticket->id, $recentIds)) recent @endif" data-ticket-id="{{ $ticket->id }}">
                        <div class="ticket-header">
                            <div class="ticket-id">#{{ $ticket->id }}</div>
                            <div class="ticket-title">
                                {{ $ticket->title }}
                                @if($ticket->priority=='urgent') <span class="urgent-icon"><i class="bi bi-exclamation-triangle"></i></span> @endif
                            </div>
                        </div>
                        <div class="ticket-description">{{ $ticket->description }}</div>
                        <div class="ticket-badges">
                            <span class="badge-modern badge-status-open"><i class="bi bi-circle"></i> Aberto</span>
                            <span class="badge-modern badge-priority-{{ strtolower($ticket->priority) }}">
                                @if($ticket->priority=='low')<i class="bi bi-arrow-down"></i>@elseif($ticket->priority=='medium')<i class="bi bi-arrow-right"></i>@elseif($ticket->priority=='high')<i class="bi bi-arrow-up"></i>@elseif($ticket->priority=='urgent')<i class="bi bi-exclamation-triangle"></i>@endif
                                {{ ucfirst($ticket->priority) }}
                            </span>
                            <span class="badge-modern badge-category">{{ $ticket->category->name ?? '-' }}</span>
                        </div>
                        <div class="ticket-footer">
                            <div class="ticket-user">
                                <span class="user-avatar">{{ isset($ticket->user->name) ? strtoupper(mb_substr($ticket->user->name,0,1)) : '?' }}</span>
                                {{ $ticket->user->name ?? '-' }}
                                <span><i class="bi bi-person-badge"></i> {{ $ticket->assignedTo->name ?? 'Não atribuído' }}</span>
                            </div>
                            <span class="ticket-date">
                                {{ $ticket->created_at ? $ticket->created_at->diffForHumans() : '-' }}
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="no-tickets"><i class="bi bi-emoji-frown"></i> Nenhum chamado aberto no momento.</div>
                @endforelse
            </div>
        </div>
        <div class="footer-clock-bar" id="footer-clock-bar"></div>
    </div>
    <audio id="sound-new" src="https://cdn.pixabay.com/audio/2022/07/26/audio_124bfae0b2.mp3" preload="auto"></audio>
    <script>
    // Remove barra de rolagem vertical
    document.body.style.overflowY = 'hidden';

    // Função tela cheia
    const btn = document.getElementById('fullscreen-btn');
    btn.onclick = function() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            btn.innerHTML = '<i class="bi bi-fullscreen-exit"></i> Sair tela cheia';
        } else {
            document.exitFullscreen();
            btn.innerHTML = '<i class="bi bi-arrows-fullscreen"></i> Tela cheia';
        }
    };
    document.addEventListener('fullscreenchange', function() {
        if (!document.fullscreenElement) {
            btn.innerHTML = '<i class="bi bi-arrows-fullscreen"></i> Tela cheia';
        }
    });

    // Som e animação para chamados recém-chegados
    const sound = document.getElementById('sound-new');
    let lastIds = JSON.parse(localStorage.getItem('lastTicketIds') || '[]');
    let currentIds = Array.from(document.querySelectorAll('.ticket-row')).map(e => e.dataset.ticketId);
    let newIds = currentIds.filter(id => !lastIds.includes(id));
    if (newIds.length > 0) {
        sound.play();
        newIds.forEach(id => {
            let el = document.querySelector('.ticket-row[data-ticket-id="'+id+'"]');
            if (el) el.classList.add('recent');
        });
    }
    localStorage.setItem('lastTicketIds', JSON.stringify(currentIds));

    // Relógio animado no rodapé
    function updateFooterClockBar() {
        const bar = document.getElementById('footer-clock-bar');
        const now = new Date();
        const dateStr = now.toLocaleDateString('pt-BR');
        const timeStr = now.toLocaleTimeString('pt-BR');
        bar.innerHTML = `<span style="font-size:1.5rem;font-weight:700;color:#fff;animation:fadeClock 3s infinite alternate;">${dateStr} - ${timeStr}</span>`;
    }
    setInterval(updateFooterClockBar, 3000);
    updateFooterClockBar();

    // Relógio no topo
    function updateLiveClock() {
        const clock = document.getElementById('live-clock');
        const now = new Date();
        clock.textContent = now.toLocaleTimeString('pt-BR');
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();
    </script>
</body>
</html>
