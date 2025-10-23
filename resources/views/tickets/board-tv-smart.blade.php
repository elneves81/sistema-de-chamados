<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Painel de Chamados - TV Smart</title>
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            overflow: hidden;
            position: relative;
        }
        
        .tv-container {
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 15px;
        }
        
        /* Header */
        .tv-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .header-title {
            color: white;
            font-size: 2rem;
            font-weight: 800;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-stats {
            display: flex;
            gap: 20px;
            align-items: center;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 10px 20px;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stat-value {
            font-size: 1.8rem;
            font-weight: 800;
            color: white;
            line-height: 1;
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 500;
        }
        
        .clock {
            color: white;
            font-size: 1.5rem;
            font-weight: 600;
            font-variant-numeric: tabular-nums;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 15px;
        }
        
        /* Painel Principal */
        .tickets-panel {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        
        /* Resumo UBS */
        .ubs-summary {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            height: fit-content;
        }
        
        .ubs-title {
            color: white;
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .ubs-subtitle {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            margin-bottom: 20px;
            line-height: 1.4;
        }
        
        /* UBS Cards */
        .ubs-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 10px;
            max-height: 500px;
            overflow-y: auto;
        }
        
        .ubs-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 15px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
        }
        
        .ubs-card.has-tickets {
            background: rgba(251, 191, 36, 0.2);
            border-color: rgba(251, 191, 36, 0.4);
        }
        
        .ubs-card.has-urgent {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.4);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }
        
        .ubs-header {
            margin-bottom: 12px;
        }
        
        .ubs-name {
            color: white;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 8px;
            line-height: 1.2;
        }
        
        .ubs-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .ubs-stats {
            display: flex;
            justify-content: space-between;
            font-size: 0.7rem;
        }
        
        .ubs-stat {
            text-align: center;
            padding: 4px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .ubs-stat.urgent {
            background: rgba(239, 68, 68, 0.3);
        }
        
        .ubs-stat.active {
            background: rgba(251, 191, 36, 0.3);
        }
        
        .ubs-stat.progress {
            background: rgba(59, 130, 246, 0.3);
        }
        
        .ubs-stat-value {
            color: white;
            font-weight: 700;
            font-size: 1rem;
        }
        
        .ubs-stat-label {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.65rem;
        }
        
        /* Tickets Display */
        .tickets-header {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .tickets-mode-switch {
            margin-left: auto;
            display: flex;
            gap: 10px;
        }
        
        .mode-btn {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 10px;
            padding: 8px 15px;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .mode-btn.active {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.4);
        }
        
        /* Ticket Cards */
        .tickets-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            max-height: 600px;
            overflow-y: auto;
        }
        
        .ticket-card {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 20px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            min-height: 140px;
        }
        
        .ticket-card.priority-urgent {
            background: rgba(239, 68, 68, 0.2);
            border-color: rgba(239, 68, 68, 0.4);
            animation: urgent-pulse 2s infinite;
        }
        
        .ticket-card.priority-high {
            background: rgba(251, 191, 36, 0.2);
            border-color: rgba(251, 191, 36, 0.4);
        }
        
        @keyframes urgent-pulse {
            0%, 100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.5); }
            50% { box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
        }
        
        @keyframes urgent-flash {
            0% { 
                background: rgba(255, 255, 255, 0.1);
                border-color: rgba(255, 255, 255, 0.2);
            }
            25% { 
                background: rgba(239, 68, 68, 0.3);
                border-color: rgba(239, 68, 68, 0.6);
                box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
            }
            50% { 
                background: rgba(255, 255, 255, 0.1);
                border-color: rgba(255, 255, 255, 0.2);
            }
            75% { 
                background: rgba(239, 68, 68, 0.3);
                border-color: rgba(239, 68, 68, 0.6);
                box-shadow: 0 0 20px rgba(239, 68, 68, 0.5);
            }
            100% { 
                background: rgba(255, 255, 255, 0.1);
                border-color: rgba(255, 255, 255, 0.2);
            }
        }
        
        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 10px;
        }
        
        .ticket-id {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        .ticket-priority {
            padding: 4px 8px;
            border-radius: 8px;
            font-size: 0.7rem;
            font-weight: 600;
        }
        
        .priority-urgent { background: #dc2626; color: white; }
        .priority-high { background: #f59e0b; color: white; }
        .priority-medium { background: #3b82f6; color: white; }
        .priority-low { background: #10b981; color: white; }
        
        .ticket-title {
            color: white;
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 12px;
            line-height: 1.4;
        }
        
        .ticket-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 15px;
        }
        
        .ticket-user {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .ticket-location {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .ticket-time {
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            margin-top: 5px;
        }
        
        /* Empty State */
        .empty-state {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            padding: 50px 20px;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.3);
            border-radius: 3px;
        }
        
        /* Responsive */
        @media (max-width: 1200px) {
            .main-content {
                grid-template-columns: 1fr;
                grid-template-rows: 2fr 1fr;
            }
            
            .ubs-grid {
                grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
                max-height: 200px;
            }
        }
        
        /* Animations */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .ticket-card {
            animation: slideIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <!-- Áudio para notificação de novos chamados -->
    <audio id="notification-sound" preload="auto">
        <source src="/sounds/notification.mp3" type="audio/mpeg">
        <source src="/sounds/notification.ogg" type="audio/ogg">
        <source src="/sounds/notification.wav" type="audio/wav">
    </audio>
    
    <div class="tv-container">
        <!-- Header -->
        <div class="tv-header">
            <div class="header-title">
                <i class="bi bi-tv"></i>
                PAINEL DE CHAMADOS
            </div>
            <div class="header-stats">
                <div class="stat-card">
                    <div class="stat-value" id="total-tickets">0</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="urgent-tickets">0</div>
                    <div class="stat-label">Urgentes</div>
                </div>
                <div class="stat-card">
                    <div class="stat-value" id="active-ubs">0</div>
                    <div class="stat-label">UBS Ativas</div>
                </div>
                <div class="clock" id="clock"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <!-- Painel de Tickets -->
            <div class="tickets-panel">
                <div class="tickets-header">
                    <i class="bi bi-exclamation-triangle"></i>
                    <span id="panel-title">Chamados Prioritários</span>
                    <div class="tickets-mode-switch">
                        <button class="mode-btn active" data-mode="priority">Prioritários</button>
                        <button class="mode-btn" data-mode="recent">Recentes</button>
                        <button class="mode-btn" data-mode="ubs">Por UBS</button>
                    </div>
                </div>
                <div class="tickets-container" id="tickets-container">
                    <!-- Tickets serão carregados aqui -->
                </div>
            </div>

            <!-- Resumo UBS -->
            <div class="ubs-summary">
                <div class="ubs-title">
                    <i class="bi bi-hospital"></i>
                    Monitor UBS em Tempo Real
                </div>
                <div class="ubs-subtitle">
                    Situação atual das Unidades Básicas de Saúde
                </div>
                <div class="ubs-grid" id="ubs-grid">
                    <!-- UBS cards serão carregados aqui -->
                </div>
            </div>
        </div>
    </div>

    <!-- Audio para notificação de novos chamados -->
    <audio id="notification-sound" preload="auto">
        <source src="/sounds/notification.mp3" type="audio/mpeg">
        <source src="/sounds/notification.ogg" type="audio/ogg">
        <source src="/sounds/notification.wav" type="audio/wav">
    </audio>

    <script>
        // Configurações
        const CONFIG = {
            refreshInterval: 30000, // 30 segundos
            maxTicketsDisplay: 12,
            priorityOrder: ['urgent', 'high', 'medium', 'low'],
            soundEnabled: true, // Ativar som de notificação
            soundCooldown: 5000 // Cooldown de 5 segundos entre sons
        };

        // Estado global
        let allTickets = [];
        let ubsData = [];
        let currentMode = 'priority';
        let lastTicketCount = 0;
        let lastUrgentCount = 0;
        let lastSoundPlay = 0;
        let isFirstLoad = true;

        // Função para tocar som de notificação
        function playNotificationSound() {
            if (!CONFIG.soundEnabled) return;
            
            const now = Date.now();
            if (now - lastSoundPlay < CONFIG.soundCooldown) return;
            
            const audio = document.getElementById('notification-sound');
            if (audio) {
                audio.currentTime = 0; // Reinicia o áudio
                audio.play().catch(error => {
                    console.log('Erro ao reproduzir som:', error);
                    // Fallback: beep do sistema
                    if (window.speechSynthesis) {
                        const utterance = new SpeechSynthesisUtterance('beep');
                        utterance.volume = 0.1;
                        utterance.rate = 10;
                        window.speechSynthesis.speak(utterance);
                    }
                });
                lastSoundPlay = now;
            }
        }

        // Função para detectar novos chamados
        function detectNewTickets(currentTickets, currentUrgent) {
            if (isFirstLoad) {
                lastTicketCount = currentTickets;
                lastUrgentCount = currentUrgent;
                isFirstLoad = false;
                return;
            }

            // Detecta novos chamados urgentes (prioridade máxima)
            if (currentUrgent > lastUrgentCount) {
                console.log(`🚨 NOVO CHAMADO URGENTE! Urgentes: ${lastUrgentCount} → ${currentUrgent}`);
                playNotificationSound();
                // Efeito visual adicional para urgentes
                showUrgentAlert();
            }
            // Detecta novos chamados em geral
            else if (currentTickets > lastTicketCount) {
                console.log(`📢 Novo chamado recebido! Total: ${lastTicketCount} → ${currentTickets}`);
                playNotificationSound();
            }

            lastTicketCount = currentTickets;
            lastUrgentCount = currentUrgent;
        }

        // Efeito visual para chamados urgentes
        function showUrgentAlert() {
            const header = document.querySelector('.tv-header');
            if (header) {
                header.style.animation = 'urgent-flash 3s ease-in-out';
                setTimeout(() => {
                    header.style.animation = '';
                }, 3000);
            }
        }

        // Inicialização
        document.addEventListener('DOMContentLoaded', function() {
            initializeClock();
            loadData();
            setupEventListeners();
            
            // Auto-refresh
            setInterval(loadData, CONFIG.refreshInterval);
        });

        // Relógio
        function initializeClock() {
            function updateClock() {
                const now = new Date();
                const timeString = now.toLocaleTimeString('pt-BR', {
                    hour: '2-digit',
                    minute: '2-digit',
                    second: '2-digit'
                });
                document.getElementById('clock').textContent = timeString;
            }
            
            updateClock();
            setInterval(updateClock, 1000);
        }

        // Event Listeners
        function setupEventListeners() {
            // Mode switch buttons
            document.querySelectorAll('.mode-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    currentMode = this.dataset.mode;
                    updateTicketsDisplay();
                    updatePanelTitle();
                });
            });
        }

        // Carregar dados
        async function loadData() {
            try {
                const response = await fetch('/api/tickets/realtime');
                const data = await response.json();
                
                // Detectar novos chamados para campainha
                if (data.stats) {
                    detectNewTickets(data.stats.total_tickets || 0, data.stats.urgent_tickets || 0);
                }
                
                allTickets = data.tickets || [];
                ubsData = data.ubs_stats || [];
                
                updateStats(data.stats || {});
                updateUbsGrid();
                updateTicketsDisplay();
                
            } catch (error) {
                console.error('❌ Erro ao carregar dados:', error);
            }
        }

        // Atualizar estatísticas do header
        function updateStats(stats) {
            document.getElementById('total-tickets').textContent = stats.total_tickets || 0;
            document.getElementById('urgent-tickets').textContent = stats.urgent_tickets || 0;
            document.getElementById('active-ubs').textContent = stats.active_ubs || 0;
        }

        // Atualizar grid das UBS
        function updateUbsGrid() {
            const grid = document.getElementById('ubs-grid');
            
            if (!ubsData.length) {
                grid.innerHTML = '<div class="empty-state"><i class="bi bi-hospital"></i><p>Aguardando dados das UBS...</p></div>';
                return;
            }

            // Ordenar UBS por prioridade (urgentes primeiro, depois com tickets, depois sem tickets)
            const sortedUbs = ubsData.sort((a, b) => {
                if (a.urgent_tickets > 0 && b.urgent_tickets === 0) return -1;
                if (a.urgent_tickets === 0 && b.urgent_tickets > 0) return 1;
                if (a.open_tickets > 0 && b.open_tickets === 0) return -1;
                if (a.open_tickets === 0 && b.open_tickets > 0) return 1;
                return a.name.localeCompare(b.name);
            });

            grid.innerHTML = sortedUbs.map(ubs => {
                const hasTickets = ubs.open_tickets > 0;
                const hasUrgent = ubs.urgent_tickets > 0;
                const inProgress = ubs.in_progress_tickets > 0;
                
                let cardClass = '';
                let statusIcon = '';
                let statusText = '';
                
                if (hasUrgent) {
                    cardClass = 'has-urgent';
                    statusIcon = '<i class="bi bi-exclamation-triangle-fill" style="color: #dc2626;"></i>';
                    statusText = 'CRÍTICO';
                } else if (hasTickets) {
                    cardClass = 'has-tickets';
                    statusIcon = '<i class="bi bi-exclamation-circle-fill" style="color: #f59e0b;"></i>';
                    statusText = 'ATENÇÃO';
                } else {
                    statusIcon = '<i class="bi bi-check-circle-fill" style="color: #10b981;"></i>';
                    statusText = 'OK';
                }
                
                return `
                    <div class="ubs-card ${cardClass}">
                        <div class="ubs-header">
                            <div class="ubs-name">${ubs.name}</div>
                            <div class="ubs-status">
                                ${statusIcon}
                                <span>${statusText}</span>
                            </div>
                        </div>
                        <div class="ubs-stats">
                            <div class="ubs-stat ${ubs.urgent_tickets > 0 ? 'urgent' : ''}">
                                <div class="ubs-stat-value">${ubs.urgent_tickets || 0}</div>
                                <div class="ubs-stat-label">Urgentes</div>
                            </div>
                            <div class="ubs-stat ${ubs.open_tickets > 0 ? 'active' : ''}">
                                <div class="ubs-stat-value">${ubs.open_tickets || 0}</div>
                                <div class="ubs-stat-label">Abertos</div>
                            </div>
                            <div class="ubs-stat ${inProgress ? 'progress' : ''}">
                                <div class="ubs-stat-value">${ubs.in_progress_tickets || 0}</div>
                                <div class="ubs-stat-label">Em Andamento</div>
                            </div>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Atualizar título do painel
        function updatePanelTitle() {
            const titles = {
                'priority': 'Chamados Prioritários',
                'recent': 'Chamados Recentes',
                'ubs': 'Chamados por UBS'
            };
            document.getElementById('panel-title').textContent = titles[currentMode];
        }

        // Atualizar display dos tickets
        function updateTicketsDisplay() {
            const container = document.getElementById('tickets-container');
            let ticketsToShow = [];

            switch (currentMode) {
                case 'priority':
                    ticketsToShow = getTicketsByPriority();
                    break;
                case 'recent':
                    ticketsToShow = getRecentTickets();
                    break;
                case 'ubs':
                    ticketsToShow = getTicketsByUbs();
                    break;
            }

            if (!ticketsToShow.length) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <h3>Nenhum chamado encontrado</h3>
                        <p>Não há chamados ${currentMode === 'priority' ? 'prioritários' : currentMode === 'recent' ? 'recentes' : 'nas UBS'} no momento</p>
                    </div>
                `;
                return;
            }

            container.innerHTML = ticketsToShow.slice(0, CONFIG.maxTicketsDisplay).map(ticket => `
                <div class="ticket-card priority-${ticket.priority}">
                    <div class="ticket-header">
                        <span class="ticket-id">#${ticket.id}</span>
                        <span class="ticket-priority priority-${ticket.priority}">${getPriorityLabel(ticket.priority)}</span>
                    </div>
                    <div class="ticket-title">${ticket.title}</div>
                    <div class="ticket-info">
                        <div class="ticket-user">
                            <i class="bi bi-person"></i> ${ticket.user_name}
                        </div>
                        ${ticket.ubs_name ? `
                        <div class="ticket-location">
                            <i class="bi bi-geo-alt-fill"></i> ${ticket.ubs_name}
                        </div>
                        ` : ''}
                        <div class="ticket-time">
                            <i class="bi bi-clock"></i> ${formatTime(ticket.created_at)}
                        </div>
                    </div>
                </div>
            `).join('');
        }

        // Obter tickets por prioridade
        function getTicketsByPriority() {
            return allTickets
                .filter(ticket => ticket.status !== 'closed' && ticket.status !== 'resolved')
                .sort((a, b) => {
                    const aPriority = CONFIG.priorityOrder.indexOf(a.priority);
                    const bPriority = CONFIG.priorityOrder.indexOf(b.priority);
                    if (aPriority !== bPriority) return aPriority - bPriority;
                    return new Date(b.created_at) - new Date(a.created_at);
                });
        }

        // Obter tickets recentes
        function getRecentTickets() {
            return allTickets
                .filter(ticket => ticket.status !== 'closed' && ticket.status !== 'resolved')
                .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        }

        // Obter tickets por UBS
        function getTicketsByUbs() {
            const ticketsByUbs = {};
            allTickets
                .filter(ticket => ticket.status !== 'closed' && ticket.status !== 'resolved')
                .forEach(ticket => {
                    const ubsName = ticket.ubs_name || 'Sem UBS';
                    if (!ticketsByUbs[ubsName]) {
                        ticketsByUbs[ubsName] = [];
                    }
                    ticketsByUbs[ubsName].push(ticket);
                });

            // Ordenar UBS por quantidade de tickets urgentes, depois por total
            return Object.entries(ticketsByUbs)
                .sort(([,a], [,b]) => {
                    const aUrgent = a.filter(t => t.priority === 'urgent').length;
                    const bUrgent = b.filter(t => t.priority === 'urgent').length;
                    if (aUrgent !== bUrgent) return bUrgent - aUrgent;
                    return b.length - a.length;
                })
                .flatMap(([, tickets]) => tickets.sort((a, b) => {
                    const aPriority = CONFIG.priorityOrder.indexOf(a.priority);
                    const bPriority = CONFIG.priorityOrder.indexOf(b.priority);
                    return aPriority - bPriority;
                }));
        }

        // Utilitários
        function getPriorityLabel(priority) {
            const labels = {
                'urgent': 'URGENTE',
                'high': 'ALTA',
                'medium': 'MÉDIA',
                'low': 'BAIXA'
            };
            return labels[priority] || priority.toUpperCase();
        }

        function formatTime(dateString) {
            const date = new Date(dateString);
            const now = new Date();
            const diffMs = now - date;
            const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
            const diffMinutes = Math.floor((diffMs % (1000 * 60 * 60)) / (1000 * 60));

            if (diffHours > 24) {
                return `${Math.floor(diffHours / 24)}d`;
            } else if (diffHours > 0) {
                return `${diffHours}h`;
            } else {
                return `${diffMinutes}min`;
            }
        }

        // Auto-switch de modo (opcional)
        let autoSwitchInterval;
        function startAutoSwitch() {
            autoSwitchInterval = setInterval(() => {
                const modes = ['priority', 'recent', 'ubs'];
                const currentIndex = modes.indexOf(currentMode);
                const nextIndex = (currentIndex + 1) % modes.length;
                
                document.querySelector(`[data-mode="${modes[nextIndex]}"]`).click();
            }, 60000); // Muda a cada 1 minuto
        }

        // Descomente a linha abaixo para ativar o auto-switch
        // startAutoSwitch();
    </script>
</body>
</html>
