<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel TV Smart — Versão Repaginada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-a: #667eea;
            --brand-b: #764ba2;
            --urgent: #dc2626;
            --high: #f59e0b;
            --medium: #10b981;
            --low: #6b7280;
            --glass: rgba(255,255,255,0.15);
            --glass-border: rgba(255,255,255,0.2);
            --shadow: 0 8px 32px rgba(31,38,135,0.37);
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.8);
            --surface: rgba(255,255,255,0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--brand-a) 0%, var(--brand-b) 100%);
            min-height: 100vh;
            color: var(--text-primary);
            overflow: hidden;
            position: relative;
        }

        .tv-container {
            height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
            padding: 20px;
            gap: 20px;
        }

        /* Header */
        .tv-header {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 20px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: var(--shadow);
        }

        .header-title {
            font-size: 2.5rem;
            font-weight: 800;
            background: linear-gradient(45deg, #fff, rgba(255,255,255,0.7));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .header-stats {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        .stat-item {
            text-align: center;
            padding: 10px 20px;
            background: var(--surface);
            border-radius: 12px;
            border: 1px solid var(--glass-border);
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.9rem;
            opacity: 0.8;
            margin-top: 4px;
        }

        .clock {
            font-size: 1.5rem;
            font-weight: 600;
            text-align: right;
        }

        /* Main Content */
        .tv-main {
            display: grid;
            grid-template-columns: 1fr 350px;
            gap: 20px;
            height: 100%;
            overflow: hidden;
        }

        .tickets-section {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .mode-switcher {
            display: flex;
            gap: 8px;
            background: var(--surface);
            padding: 4px;
            border-radius: 12px;
            border: 1px solid var(--glass-border);
        }

        .mode-btn {
            padding: 8px 16px;
            border: none;
            background: transparent;
            color: var(--text-secondary);
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .mode-btn.active, .mode-btn:hover {
            background: var(--text-primary);
            color: var(--brand-a);
        }

        .tickets-container {
            flex: 1;
            overflow-y: auto;
            padding-right: 10px;
        }

        .tickets-container::-webkit-scrollbar {
            width: 6px;
        }

        .tickets-container::-webkit-scrollbar-track {
            background: var(--surface);
            border-radius: 3px;
        }

        .tickets-container::-webkit-scrollbar-thumb {
            background: var(--glass-border);
            border-radius: 3px;
        }

        .tickets-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 16px;
        }

        .ticket-card {
            background: var(--surface);
            border: 1px solid var(--glass-border);
            border-radius: 16px;
            padding: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .ticket-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--medium);
        }

        .ticket-card.priority-urgent::before { background: var(--urgent); }
        .ticket-card.priority-high::before { background: var(--high); }
        .ticket-card.priority-medium::before { background: var(--medium); }
        .ticket-card.priority-low::before { background: var(--low); }

        .ticket-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(31,38,135,0.5);
        }

        .ticket-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 12px;
        }

        .ticket-id {
            font-weight: 600;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .ticket-priority {
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .ticket-priority.priority-urgent { background: var(--urgent); color: white; }
        .ticket-priority.priority-high { background: var(--high); color: white; }
        .ticket-priority.priority-medium { background: var(--medium); color: white; }
        .ticket-priority.priority-low { background: var(--low); color: white; }

        .ticket-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        .ticket-info {
            display: flex;
            flex-direction: column;
            gap: 6px;
            font-size: 0.9rem;
            opacity: 0.8;
        }

        .ticket-info > div {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .ticket-info i {
            width: 16px;
            text-align: center;
        }

        /* UBS Section */
        .ubs-section {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 20px;
            box-shadow: var(--shadow);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .ubs-grid {
            display: flex;
            flex-direction: column;
            gap: 12px;
            overflow-y: auto;
            flex: 1;
        }

        .ubs-card {
            background: var(--surface);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 16px;
            transition: all 0.3s ease;
        }

        .ubs-card.has-urgent {
            border-color: var(--urgent);
            background: rgba(220, 38, 38, 0.1);
        }

        .ubs-card.has-tickets {
            border-color: var(--high);
            background: rgba(245, 158, 11, 0.1);
        }

        .ubs-name {
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.95rem;
        }

        .ubs-status {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .ubs-stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 8px;
        }

        .ubs-stat {
            text-align: center;
            padding: 8px 4px;
            background: rgba(255,255,255,0.05);
            border-radius: 6px;
        }

        .ubs-stat.urgent { background: rgba(220, 38, 38, 0.2); }
        .ubs-stat.active { background: rgba(245, 158, 11, 0.2); }
        .ubs-stat.progress { background: rgba(16, 185, 129, 0.2); }

        .ubs-stat-value {
            font-weight: 700;
            font-size: 1.2rem;
            line-height: 1;
        }

        .ubs-stat-label {
            font-size: 0.7rem;
            opacity: 0.8;
            margin-top: 2px;
        }

        /* Footer */
        .tv-footer {
            background: var(--glass);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: var(--shadow);
            font-size: 0.9rem;
        }

        /* Empty States */
        .empty-state {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 200px;
            text-align: center;
            opacity: 0.6;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        .empty-state h5 {
            margin-bottom: 8px;
            font-weight: 600;
        }

        .empty-state p {
            font-size: 0.9rem;
            opacity: 0.8;
        }

        /* Skeleton Loading */
        .skeleton {
            background: linear-gradient(90deg, rgba(255,255,255,0.1) 25%, rgba(255,255,255,0.2) 50%, rgba(255,255,255,0.1) 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }

        @keyframes skeleton-loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }

        /* Error Banner */
        .error-banner {
            position: fixed;
            top: 20px;
            right: 20px;
            background: var(--urgent);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
        }

        .error-banner.show {
            transform: translateX(0);
        }

        /* Flash Animation */
        @keyframes urgent-flash {
            0%, 100% { background: var(--glass); }
            50% { background: rgba(220, 38, 38, 0.3); }
        }

        .flash-urgent {
            animation: urgent-flash 0.5s ease-in-out 3;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .tv-main {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr auto;
            }
            
            .ubs-section {
                max-height: 300px;
            }
            
            .ubs-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: 12px;
            }
        }

        @media (max-width: 768px) {
            .tv-container {
                padding: 15px;
                gap: 15px;
            }
            
            .header-title {
                font-size: 1.8rem;
            }
            
            .header-stats {
                gap: 15px;
            }
            
            .stat-value {
                font-size: 1.5rem;
            }
            
            .tickets-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }
    </style>
</head>
<body data-endpoint="{{ route('api.tickets.realtime') ?? '/api/tickets/realtime' }}">
    <!-- Audio Element -->
    <audio id="notification-sound" preload="auto" style="display: none;">
        <source src="/sounds/notification.mp3" type="audio/mpeg">
        <source src="/sounds/notification.ogg" type="audio/ogg">
        <source src="/sounds/notification.wav" type="audio/wav">
    </audio>

    <!-- Error Banner -->
    <div id="error-banner" class="error-banner">
        <i class="bi bi-exclamation-triangle"></i>
        <span id="error-message">Erro de conexão</span>
    </div>

    <div class="tv-container">
        <!-- Header -->
        <header class="tv-header" aria-live="polite">
            <div class="header-title">
                <i class="bi bi-display"></i>
                Sistema de Chamados
            </div>
            <div class="header-stats">
                <div class="stat-item">
                    <div class="stat-value" id="total-tickets">0</div>
                    <div class="stat-label">Total</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="urgent-tickets">0</div>
                    <div class="stat-label">Urgentes</div>
                </div>
                <div class="stat-item">
                    <div class="stat-value" id="active-ubs">0</div>
                    <div class="stat-label">UBS Ativas</div>
                </div>
            </div>
            <div class="clock">
                <div id="current-time">--:--</div>
                <div id="current-date" style="font-size: 0.9rem; opacity: 0.8;">--</div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="tv-main">
            <section class="tickets-section">
                <div class="section-header">
                    <h2 class="section-title" id="panel-title">Chamados Prioritários</h2>
                    <div class="mode-switcher">
                        <button class="mode-btn active" data-mode="priority">Prioridade</button>
                        <button class="mode-btn" data-mode="recent">Recentes</button>
                        <button class="mode-btn" data-mode="ubs">Por UBS</button>
                    </div>
                </div>
                <div class="tickets-container">
                    <div class="tickets-grid" id="tickets-container">
                        <!-- Skeleton Loading -->
                        <div class="ticket-card skeleton" style="height: 140px;"></div>
                        <div class="ticket-card skeleton" style="height: 140px;"></div>
                        <div class="ticket-card skeleton" style="height: 140px;"></div>
                    </div>
                </div>
            </section>

            <aside class="ubs-section">
                <div class="section-header">
                    <h3 class="section-title">Status das UBS</h3>
                    <i class="bi bi-hospital"></i>
                </div>
                <div class="ubs-grid" id="ubs-grid">
                    <!-- Loading skeleton -->
                    <div class="ubs-card skeleton" style="height: 100px;"></div>
                    <div class="ubs-card skeleton" style="height: 100px;"></div>
                    <div class="ubs-card skeleton" style="height: 100px;"></div>
                </div>
            </aside>
        </main>

        <!-- Footer -->
        <footer class="tv-footer">
            <div>
                <i class="bi bi-activity"></i>
                Atualização automática a cada 30 segundos
            </div>
            <div id="last-update">
                Última atualização: --:--
            </div>
        </footer>
    </div>

    <script>
        // ===== Global State =====
        const CONFIG = {
            refreshInterval: 30000,
            maxTicketsDisplay: 12,
            priorityOrder: ['urgent', 'high', 'medium', 'low'],
            soundEnabled: true,
            soundCooldown: 5000
        };

        let allTickets = [];
        let ubsData = [];
        let currentMode = 'priority';
        let lastTicketCount = 0;
        let lastUrgentCount = 0;
        let lastSoundPlay = 0;
        let isFirstLoad = true;

        // ===== Utility Functions =====
        const $id = id => document.getElementById(id);
        const priorityLabel = p => ({ urgent: 'URGENTE', high: 'ALTA', medium: 'MÉDIA', low: 'BAIXA' })[p] || p.toUpperCase();
        const fmtTime = dt => {
            const d = new Date(dt);
            const now = new Date();
            const diff = Math.floor((now - d) / 60000);
            if (diff < 60) return `${diff}min`;
            if (diff < 1440) return `${Math.floor(diff / 60)}h`;
            return `${Math.floor(diff / 1440)}d`;
        };

        // ===== Clock =====
        function startClocks() {
            function updateClock() {
                const now = new Date();
                $id('current-time').textContent = now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
                $id('current-date').textContent = now.toLocaleDateString('pt-BR', { weekday: 'long', day: '2-digit', month: 'short' });
            }
            updateClock();
            setInterval(updateClock, 1000);
        }

        // ===== Error Handling =====
        function showError(message) {
            const banner = $id('error-banner');
            const msgEl = $id('error-message');
            msgEl.textContent = message;
            banner.classList.add('show');
            setTimeout(() => banner.classList.remove('show'), 5000);
        }

        function hideError() {
            $id('error-banner').classList.remove('show');
        }

        // ===== Audio & Notifications =====
        function playSound() {
            if (!CONFIG.soundEnabled) return;
            
            const now = Date.now();
            if (now - lastSoundPlay < CONFIG.soundCooldown) return;
            
            const audio = $id('notification-sound');
            if (audio) {
                audio.currentTime = 0;
                audio.play().catch(error => {
                    console.log('Audio playback failed, using fallback:', error);
                    // Fallback: speechSynthesis
                    if ('speechSynthesis' in window) {
                        const utterance = new SpeechSynthesisUtterance('Novo chamado recebido');
                        utterance.lang = 'pt-BR';
                        utterance.rate = 1.2;
                        speechSynthesis.speak(utterance);
                    }
                });
                lastSoundPlay = now;
            }
        }

        function flashHeader() {
            const header = document.querySelector('.tv-header');
            if (header) {
                header.classList.add('flash-urgent');
                setTimeout(() => header.classList.remove('flash-urgent'), 1500);
            }
        }

        function detectNewTickets(currTotal, currUrgent) {
            if (isFirstLoad) {
                lastTicketCount = currTotal;
                lastUrgentCount = currUrgent;
                isFirstLoad = false;
                return;
            }
            
            if (currUrgent > lastUrgentCount) {
                playSound();
                flashHeader();
            } else if (currTotal > lastTicketCount) {
                playSound();
            }
            
            lastTicketCount = currTotal;
            lastUrgentCount = currUrgent;
        }

        // ===== Data Loading =====
        async function loadData() {
            try {
                hideError();
                const endpoint = document.body.dataset.endpoint || '/api/tickets/realtime';
                const response = await fetch(endpoint);
                
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }
                
                const data = await response.json();
                
                if (!data.success && data.error) {
                    throw new Error(data.error);
                }
                
                // Update global state
                allTickets = data.tickets || [];
                ubsData = data.ubs_stats || [];
                
                // Detect new tickets for sound notification
                if (data.stats) {
                    detectNewTickets(data.stats.total_tickets || 0, data.stats.urgent_tickets || 0);
                }
                
                // Update UI
                updateStats(data.stats || {});
                updateUbsGrid();
                updateTicketsDisplay();
                updateLastUpdate();
                
            } catch (error) {
                console.error('Load data error:', error);
                showError(`Erro ao carregar dados: ${error.message}`);
            }
        }

        function updateStats(stats) {
            $id('total-tickets').textContent = stats.total_tickets || 0;
            $id('urgent-tickets').textContent = stats.urgent_tickets || 0;
            $id('active-ubs').textContent = stats.active_ubs || (ubsData ? ubsData.filter(u => u.open_tickets > 0).length : 0);
        }

        function updateLastUpdate() {
            const now = new Date();
            $id('last-update').textContent = `Última atualização: ${now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;
        }

        // ===== UBS =====
        function updateUbsGrid() {
            const grid = $id('ubs-grid');
            if (!ubsData.length) {
                grid.innerHTML = '<div class="empty-state"><i class="bi bi-hospital"></i><p>Aguardando dados das UBS...</p></div>';
                return;
            }
            
            const sorted = [...ubsData].sort((a, b) => {
                const au = (a.urgent_tickets || 0), bu = (b.urgent_tickets || 0);
                if (au !== bu) return bu - au;
                const ao = (a.open_tickets || 0), bo = (b.open_tickets || 0);
                if (ao !== bo) return bo - ao;
                return (a.name || '').localeCompare(b.name || '');
            });
            
            grid.innerHTML = sorted.map(ubs => {
                const hasTickets = (ubs.open_tickets || 0) > 0;
                const hasUrgent = (ubs.urgent_tickets || 0) > 0;
                const inProg = (ubs.in_progress_tickets || 0) > 0;
                const klass = hasUrgent ? 'has-urgent' : (hasTickets ? 'has-tickets' : '');
                const icon = hasUrgent ? '<i class="bi bi-exclamation-triangle-fill" style="color:#dc2626"></i>' : (hasTickets ? '<i class="bi bi-exclamation-circle-fill" style="color:#f59e0b"></i>' : '<i class="bi bi-check-circle-fill" style="color:#10b981"></i>');
                const text = hasUrgent ? 'CRÍTICO' : (hasTickets ? 'ATENÇÃO' : 'OK');
                
                return `
                    <div class="ubs-card ${klass}">
                        <div class="ubs-name">${ubs.name || 'UBS'}</div>
                        <div class="ubs-status">${icon}<span>${text}</span></div>
                        <div class="ubs-stats">
                            <div class="ubs-stat ${hasUrgent ? 'urgent' : ''}">
                                <div class="ubs-stat-value">${ubs.urgent_tickets || 0}</div>
                                <div class="ubs-stat-label">Urgentes</div>
                            </div>
                            <div class="ubs-stat ${hasTickets ? 'active' : ''}">
                                <div class="ubs-stat-value">${ubs.open_tickets || 0}</div>
                                <div class="ubs-stat-label">Abertos</div>
                            </div>
                            <div class="ubs-stat ${inProg ? 'progress' : ''}">
                                <div class="ubs-stat-value">${ubs.in_progress_tickets || 0}</div>
                                <div class="ubs-stat-label">Em Andamento</div>
                            </div>
                        </div>
                    </div>`;
            }).join('');
        }

        // ===== Tickets =====
        function updatePanelTitle() {
            $id('panel-title').textContent = ({
                priority: 'Chamados Prioritários',
                recent: 'Chamados Recentes',
                ubs: 'Chamados por UBS'
            })[currentMode];
        }

        function getTicketsByPriority() {
            return allTickets.filter(t => t.status !== 'closed' && t.status !== 'resolved')
                .sort((a, b) => {
                    const ap = CONFIG.priorityOrder.indexOf(a.priority);
                    const bp = CONFIG.priorityOrder.indexOf(b.priority);
                    if (ap !== bp) return ap - bp;
                    return new Date(b.created_at) - new Date(a.created_at);
                });
        }

        function getRecentTickets() {
            return allTickets.filter(t => t.status !== 'closed' && t.status !== 'resolved')
                .sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        }

        function getTicketsByUbs() {
            const buckets = {};
            allTickets.filter(t => t.status !== 'closed' && t.status !== 'resolved').forEach(t => {
                const k = t.ubs_name || 'Sem UBS';
                (buckets[k] ||= []).push(t);
            });
            
            return Object.entries(buckets).sort(([, a], [, b]) => {
                const ua = a.filter(t => t.priority === 'urgent').length;
                const ub = b.filter(t => t.priority === 'urgent').length;
                if (ua !== ub) return ub - ua;
                return b.length - a.length;
            }).flatMap(([, arr]) => arr.sort((a, b) => CONFIG.priorityOrder.indexOf(a.priority) - CONFIG.priorityOrder.indexOf(b.priority)));
        }

        function renderTickets(list) {
            const container = $id('tickets-container');
            if (!list.length) {
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-check-circle"></i>
                        <h5 class="mt-2">Nenhum chamado encontrado</h5>
                        <p>Não há chamados ${currentMode === 'priority' ? 'prioritários' : currentMode === 'recent' ? 'recentes' : 'nas UBS'} no momento</p>
                    </div>`;
                return;
            }
            
            container.innerHTML = list.slice(0, CONFIG.maxTicketsDisplay).map(t => `
                <div class="ticket-card priority-${t.priority}">
                    <div class="ticket-header">
                        <span class="ticket-id">#${t.id}</span>
                        <span class="ticket-priority priority-${t.priority}">${priorityLabel(t.priority)}</span>
                    </div>
                    <div class="ticket-title">${t.title || '—'}</div>
                    <div class="ticket-info">
                        <div class="ticket-user"><i class="bi bi-person"></i> ${t.user_name || 'N/A'}</div>
                        ${t.ubs_name ? `<div class="ticket-location"><i class="bi bi-geo-alt-fill"></i> ${t.ubs_name}</div>` : ''}
                        <div class="ticket-time"><i class="bi bi-clock"></i> ${fmtTime(t.created_at)}</div>
                    </div>
                </div>`).join('');
        }

        function updateTicketsDisplay() {
            let list = [];
            if (currentMode === 'priority') list = getTicketsByPriority();
            else if (currentMode === 'recent') list = getRecentTickets();
            else list = getTicketsByUbs();
            
            renderTickets(list);
            updatePanelTitle();
        }

        // ===== Events =====
        function setupEvents() {
            document.querySelectorAll('.mode-btn').forEach(btn => btn.addEventListener('click', function() {
                document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                currentMode = this.dataset.mode;
                updateTicketsDisplay();
            }));

            // Auto refresh + pause when tab is hidden
            setInterval(loadData, CONFIG.refreshInterval);
            document.addEventListener('visibilitychange', () => {
                if (!document.hidden) loadData();
            });
        }

        // ===== Initialization =====
        document.addEventListener('DOMContentLoaded', () => {
            startClocks();
            setupEvents();
            loadData();
        });
    </script>
</body>
</html>
