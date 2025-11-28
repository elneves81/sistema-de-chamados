<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#667eea">
    <link rel="manifest" href="/manifest.json">
    <title>Painel TV Smart — Versão Repaginada</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Cores principais - Roxo (padrão anterior) */
            --brand-a: #667eea;
            --brand-b: #764ba2;
            --brand-c: #6b6ee8;
            
            /* Cores de prioridade */
            --urgent: #ef4444;
            --high: #f59e0b;
            --medium: #10b981;
            --low: #6b7280;
            
            /* Glass morphism */
            --glass: rgba(255,255,255,0.12);
            --glass-border: rgba(255,255,255,0.18);
            --shadow: 0 8px 32px rgba(118, 75, 162, 0.25);
            
            /* Textos */
            --text-primary: #ffffff;
            --text-secondary: rgba(255,255,255,0.85);
            --surface: rgba(255,255,255,0.08);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, var(--brand-a) 0%, var(--brand-b) 50%, var(--brand-c) 100%);
            min-height: 100vh;
            color: var(--text-primary);
            overflow-x: hidden;
            position: relative;
        }
        
        /* Adicionar pattern sutil ao fundo */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(255,255,255,0.05) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
            pointer-events: none;
            z-index: 0;
        }

        .tv-container {
            height: 100vh;
            display: grid;
            grid-template-rows: auto 1fr auto;
            padding: 20px;
            gap: 20px;
            width: 100%;
            max-width: 100vw;
            overflow-x: hidden;
            position: relative;
            z-index: 1;
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
            background: linear-gradient(135deg, #fff 0%, rgba(255,255,255,0.8) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .header-title i {
            background: linear-gradient(135deg, #fff 0%, #f0f9ff 100%);
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
            grid-template-columns: 1fr;
            gap: 20px;
            height: 100%;
            overflow: hidden;
            width: 100%;
            max-width: 100%;
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
            max-width: 100%;
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
            background: linear-gradient(135deg, var(--text-primary) 0%, #f4f0ff 100%);
            color: var(--brand-a);
            box-shadow: 0 2px 8px rgba(118, 75, 162, 0.3);
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
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 280px), 1fr));
            gap: 16px;
            width: 100%;
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

        /* A cor base do chamado será injetada via inline style (CSS variable --ticket-base) */
        .ticket-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: var(--ticket-base, var(--medium));
        }

        /* Mantém compatibilidade caso não venha categoria_color */
        .ticket-card.priority-urgent:not([style*="--ticket-base"])::before { background: var(--urgent); }
        .ticket-card.priority-high:not([style*="--ticket-base"])::before { background: var(--high); }
        .ticket-card.priority-medium:not([style*="--ticket-base"])::before { background: var(--medium); }
        .ticket-card.priority-low:not([style*="--ticket-base"])::before { background: var(--low); }

        .ticket-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px rgba(118, 75, 162, 0.4);
            background: rgba(255,255,255,0.12);
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

        .ticket-assigned {
            color: #10b981;
            font-weight: 600;
            opacity: 1 !important;
        }

        .ticket-assigned i {
            color: #10b981;
        }

        .ticket-support-team {
            color: #3b82f6;
            font-size: 0.95em;
            margin-top: 4px;
            opacity: 0.95;
            font-weight: 500;
        }

        .ticket-support-team i {
            color: #3b82f6;
            font-size: 1em;
        }

        .support-badge {
            display: inline-block;
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 0.9em;
            margin-right: 6px;
            margin-top: 3px;
            font-weight: 600;
            border: 1px solid rgba(59, 130, 246, 0.3);
        }

        .support-badge i {
            margin-right: 3px;
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
            background: rgba(239, 68, 68, 0.12);
            box-shadow: 0 4px 12px rgba(239,68,68,0.2);
        }

        .ubs-card.has-tickets {
            border-color: var(--high);
            background: rgba(245, 158, 11, 0.12);
            box-shadow: 0 4px 12px rgba(245,158,11,0.2);
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
            grid-template-columns: repeat(auto-fit, minmax(70px, 1fr));
            gap: 8px;
            width: 100%;
        }

        .ubs-stat {
            text-align: center;
            padding: 8px 4px;
            background: rgba(255,255,255,0.05);
            border-radius: 6px;
        }

        .ubs-stat.urgent { 
            background: rgba(239, 68, 68, 0.25);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }
        .ubs-stat.active { 
            background: rgba(245, 158, 11, 0.25);
            border: 1px solid rgba(245, 158, 11, 0.3);
        }
        .ubs-stat.progress { 
            background: rgba(16, 185, 129, 0.25);
            border: 1px solid rgba(16, 185, 129, 0.3);
        }

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

        .footer-watermark {
            font-size: 0.75rem;
            font-weight: 600;
            opacity: 0.6;
            letter-spacing: 0.5px;
        }

        .footer-watermark .hubi {
            color: #10b981;
            font-weight: 700;
        }

        .footer-watermark .software {
            color: #f97316;
            font-weight: 700;
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
            0%, 100% { 
                background: var(--glass); 
                box-shadow: var(--shadow);
            }
            50% { 
                background: rgba(239, 68, 68, 0.25);
                box-shadow: 0 8px 32px rgba(239,68,68,0.5);
            }
        }

        .flash-urgent {
            animation: urgent-flash 0.5s ease-in-out 3;
        }

        /* Live Indicator Animation */
        @keyframes pulse-live {
            0%, 100% {
                opacity: 1;
                transform: scale(1);
            }
            50% {
                opacity: 0.6;
                transform: scale(1.1);
            }
        }

        /* Test Sound Button Hover */
        #test-sound:hover {
            opacity: 1 !important;
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.35) !important;
        }

        #test-sound:active {
            transform: translateY(0px);
        }

        /* Responsive */
        @media (max-width: 1400px) {
            .header-title {
                font-size: 2rem;
            }
            
            .stat-value {
                font-size: 1.8rem;
            }
        }

        @media (max-width: 1200px) {
            .tv-main {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr auto;
            }
            
            .ubs-section {
                max-height: 280px;
            }
            
            .ubs-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 12px;
                width: 100%;
            }
            
            .tickets-grid {
                grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
            }
        }

        @media (max-width: 768px) {
            .tv-container {
                padding: 12px;
                gap: 12px;
                grid-template-rows: auto 1fr auto;
            }
            
            .tv-header {
                padding: 16px 20px;
                flex-direction: column;
                gap: 16px;
                border-radius: 16px;
            }
            
            .header-title {
                font-size: 1.5rem;
                width: 100%;
                justify-content: center;
                gap: 10px;
            }
            
            .header-stats {
                width: 100%;
                gap: 10px;
                flex-wrap: nowrap;
                justify-content: space-between;
            }
            
            .stat-item {
                flex: 1;
                min-width: 0;
                padding: 8px 12px;
            }
            
            .stat-value {
                font-size: 1.4rem;
            }
            
            .stat-label {
                font-size: 0.75rem;
            }
            
            .clock {
                font-size: 1.1rem;
                text-align: center;
            }
            
            #live-indicator {
                font-size: 0.65rem;
            }
            
            #live-indicator span {
                width: 6px;
                height: 6px;
            }
            
            .tv-main {
                grid-template-columns: 1fr;
                grid-template-rows: 1fr auto;
                gap: 12px;
            }
            
            .tickets-section,
            .ubs-section {
                padding: 16px;
                border-radius: 16px;
            }
            
            .section-title {
                font-size: 1.2rem;
            }
            
            .mode-switcher {
                flex-wrap: wrap;
            }
            
            .mode-btn {
                padding: 6px 12px;
                font-size: 0.85rem;
            }
            
            .tickets-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }
            
            .ticket-card {
                padding: 16px;
            }
            
            .ticket-title {
                font-size: 1rem;
            }
            
            .ubs-section {
                max-height: 320px;
            }
            
            .ubs-grid {
                grid-template-columns: 1fr;
            }
            
            .ubs-card {
                padding: 14px;
            }
            
            .ubs-stats {
                grid-template-columns: repeat(3, 1fr);
                gap: 6px;
            }
            
            .tv-footer {
                padding: 12px 16px;
                flex-direction: column;
                gap: 8px;
                text-align: center;
                font-size: 0.8rem;
            }
        }

        @media (max-width: 480px) {
            .tv-container {
                padding: 8px;
                gap: 8px;
            }
            
            .tv-header {
                padding: 12px 16px;
            }
            
            .header-title {
                font-size: 1.3rem;
            }
            
            .stat-item {
                padding: 6px 8px;
            }
            
            .stat-value {
                font-size: 1.2rem;
            }
            
            .stat-label {
                font-size: 0.7rem;
            }
            
            .tickets-section,
            .ubs-section {
                padding: 12px;
            }
            
            .section-title {
                font-size: 1.1rem;
            }
            
            .mode-btn {
                padding: 5px 10px;
                font-size: 0.8rem;
            }
            
            .ticket-card {
                padding: 12px;
            }
            
            .ubs-section {
                max-height: 280px;
            }
            
            .footer-watermark {
                font-size: 0.7rem;
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
    <audio id="notification-sound" preload="auto" playsinline style="display: none;">
        <source src="/sounds/notification.mp3" type="audio/mpeg">
        <source src="/sounds/notification.ogg" type="audio/ogg">
        <source src="/sounds/notification.wav" type="audio/wav">
        <!-- Base64 fallback curto (beep) para ambientes restritos -->
        <source src="data:audio/wav;base64,UklGRhQAAABXQVZFZm10IBAAAAABAAEAESsAACJWAAACABYAZGF0YQwAAAABAQEBAQEBAQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA==" type="audio/wav">
    </audio>

    <!-- Botão para habilitar som (aparece apenas se o navegador bloquear autoplay) -->
    <button id="enable-sound" onclick="enableSoundClick()" style="position: fixed; right: 16px; top: 16px; z-index: 2000; display:none; align-items:center; gap:6px; border: none; padding: 9px 13px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; color: #0b1220; background: linear-gradient(135deg, #f59e0b, #10b981); box-shadow: 0 8px 20px rgba(0,0,0,0.28); cursor: pointer;">
        <i class="bi bi-volume-up"></i>
        Ativar Som
    </button>

    <!-- Botão para testar som manualmente (sempre visível, pequeno) -->
    <button id="test-sound" onclick="testSoundClick()" title="Testar som" style="position: fixed; right: 20px; bottom: 20px; z-index: 2000; display:inline-flex; align-items:center; gap:6px; border: none; padding: 8px 12px; border-radius: 12px; font-weight: 700; font-size: 0.85rem; color: #0b1220; background: linear-gradient(135deg, #a7f3d0, #93c5fd); box-shadow: 0 6px 20px rgba(0,0,0,0.25); cursor: pointer; opacity:0.92; transition: all 0.3s ease;">
        <i class="bi bi-music-note-beamed"></i>
        Testar Som
    </button>

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
                <span style="color:#10b981; font-weight:800; letter-spacing:0.2px;">Suporte+</span>
                <span style="color:#f59e0b; font-weight:800; letter-spacing:0.2px;">Saúde</span>
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
            </div>
            <div class="clock">
                <div style="display: flex; align-items: center; gap: 8px; justify-content: flex-end; margin-bottom: 4px;">
                    <span id="live-indicator" style="display: inline-flex; align-items: center; gap: 6px; font-size: 0.75rem; font-weight: 600; opacity: 0.9;">
                        <span style="width: 8px; height: 8px; border-radius: 50%; background: #10b981; box-shadow: 0 0 8px rgba(16,185,129,0.6); animation: pulse-live 2s ease-in-out infinite;"></span>
                        AO VIVO
                    </span>
                </div>
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
        </main>

        <!-- Footer -->
        <footer class="tv-footer">
            <div>
                <i class="bi bi-activity"></i>
                Atualização automática a cada 10 segundos
            </div>
            <div class="footer-watermark">
                <span class="hubi">HUBI</span> <span class="software">SOFTWARE</span>
            </div>
            <div id="last-update">
                Última atualização: --:--
            </div>
        </footer>
    </div>

    <button id="pwa-install" style="position: fixed; right: max(12px, env(safe-area-inset-right, 12px)); bottom: max(16px, env(safe-area-inset-bottom, 16px)); z-index: 2000; display:inline-flex; align-items:center; gap:6px; border: none; padding: 9px 13px; border-radius: 10px; font-weight: 700; font-size: 0.9rem; color: #0b1220; background: linear-gradient(135deg, #10b981, #f59e0b); box-shadow: 0 8px 20px rgba(0,0,0,0.28); cursor: pointer; transition: all 0.3s ease;">
        <i class="bi bi-download"></i>
        <span class="install-text">Instalar App</span>
    </button>

    <!-- Modal de instruções -->
    <div id="install-modal" style="display:none; position:fixed; inset:0; z-index:3000; background:rgba(0,0,0,0.7); align-items:center; justify-content:center; padding: 20px;">
        <div style="background:#122134; border:1px solid rgba(255,255,255,0.18); border-radius:20px; padding:24px; max-width:min(480px, 90vw); width: 100%; box-shadow:0 16px 40px rgba(0,0,0,0.4);">
            <div style="display:flex; align-items:center; gap:12px; margin-bottom:14px;">
                <i class="bi bi-info-circle-fill" style="font-size:24px; color:#667eea;"></i>
                <h3 style="margin:0; color:#e5f2ff; font-size:18px; word-break: break-word;">suportesaude.guarapuava.pr.gov.br:8083</h3>
            </div>
            <p style="color:rgba(229,242,255,0.85); line-height:1.6; margin:0 0 10px; font-size: 15px;">Para instalar este painel como app:</p>
            <ul style="color:rgba(229,242,255,0.75); line-height:1.7; margin:0 0 18px; padding-left:20px; font-size: 14px;">
                <li>Use Chrome/Edge em HTTPS (recomendado)</li>
                <li>Ou no celular: abra o menu do navegador e toque em "Adicionar à tela inicial"</li>
            </ul>
            <button onclick="document.getElementById('install-modal').style.display='none'" style="width:100%; padding:11px; background:#667eea; color:#fff; border:none; border-radius:10px; font-weight:600; cursor:pointer; font-size:15px;">OK</button>
        </div>
    </div>

    <style>
        @media (max-width: 768px) {
            #pwa-install {
                padding: 8px 11px;
                font-size: 0.85rem;
                bottom: max(12px, env(safe-area-inset-bottom, 12px));
            }
            
            #pwa-install .install-text {
                display: none;
            }
            
            #pwa-install i {
                margin: 0;
            }
            
            #install-modal > div {
                padding: 20px;
            }
            
            #install-modal h3 {
                font-size: 16px;
            }
            
            #install-modal p {
                font-size: 14px;
            }
            
            #install-modal ul {
                font-size: 13px;
            }
        }
        
        @media (max-width: 480px) {
            #pwa-install {
                padding: 7px 10px;
                font-size: 0.8rem;
            }
            
            #install-modal > div {
                padding: 18px;
                border-radius: 16px;
            }
            
            #install-modal h3 {
                font-size: 14px;
            }
        }
    </style>

    <script>
        // ===== Global State =====
        const CONFIG = {
            refreshInterval: 10000,
            maxTicketsDisplay: 12,
            priorityOrder: ['urgent', 'high', 'medium', 'low'],
            soundEnabled: true,
            soundCooldown: 5000,
            autoFit: true,
            keepResolvedMinutes: 0 // 0 = remover imediatamente do painel
        };

        let allTickets = [];
        let ubsData = [];
        let currentMode = 'priority';
        let lastTicketCount = 0;
        let lastUrgentCount = 0;
        let lastSoundPlay = 0;
    let isFirstLoad = true;
    let audioReady = false;
    let lastTicketIds = new Set();

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
            
            // Muda indicador para vermelho
            const indicator = document.querySelector('#live-indicator span');
            if (indicator) {
                indicator.style.background = '#ef4444';
                indicator.style.boxShadow = '0 0 8px rgba(239,68,68,0.6)';
            }
            
            setTimeout(() => banner.classList.remove('show'), 5000);
        }

        function hideError() {
            $id('error-banner').classList.remove('show');
            
            // Volta indicador para verde
            const indicator = document.querySelector('#live-indicator span');
            if (indicator) {
                indicator.style.background = '#10b981';
                indicator.style.boxShadow = '0 0 8px rgba(16,185,129,0.6)';
            }
        }

        // ===== Audio & Notifications =====
        function showEnableSoundButton() {
            const btn = $id('enable-sound');
            if (btn) btn.style.display = 'inline-flex';
        }

        function hideEnableSoundButton() {
            const btn = $id('enable-sound');
            if (btn) btn.style.display = 'none';
        }

        function tryInitAudio(showPromptOnFail = false) {
            const audio = $id('notification-sound');
            if (!audio) return false;
            // Aplica volume salvo (0.0 a 1.0)
            const savedVol = parseFloat(localStorage.getItem('tv_sound_volume') || '0.85');
            audio.volume = isNaN(savedVol) ? 0.85 : Math.min(1, Math.max(0, savedVol));
            audio.muted = true;
            const p = audio.play();
            if (!p || typeof p.then !== 'function') return false;
            return p.then(() => {
                audio.pause();
                audio.currentTime = 0;
                audio.muted = false;
                audioReady = true;
                hideEnableSoundButton();
                return true;
            }).catch(err => {
                audioReady = false;
                if (showPromptOnFail) showEnableSoundButton();
                return false;
            });
        }
        function webAudioBeep(duration=300, freq=880, vol=0.2) {
            try {
                const AudioCtx = window.AudioContext || window.webkitAudioContext;
                if (!AudioCtx) return false;
                const ctx = new AudioCtx();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();
                osc.type = 'sine';
                osc.frequency.value = freq;
                gain.gain.value = vol;
                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                setTimeout(() => { osc.stop(); ctx.close(); }, duration);
                return true;
            } catch (e) { return false; }
        }

        function playSound() {
            if (!CONFIG.soundEnabled) return;
            
            const now = Date.now();
            if (now - lastSoundPlay < CONFIG.soundCooldown) return;
            
            const audio = $id('notification-sound');
            if (audio) {
                if (!audioReady) {
                    // Tenta inicializar silenciosamente; se falhar, cai no fallback abaixo
                    tryInitAudio();
                }
                audio.currentTime = 0;
                audio.play().catch(error => {
                    console.log('Audio playback failed, using fallback:', error);
                    // Fallback 1: WebAudio beep
                    const beeped = webAudioBeep(250, 940, 0.25);
                    // Fallback 2: speechSynthesis
                    if (!beeped && 'speechSynthesis' in window) {
                        const utterance = new SpeechSynthesisUtterance('Novo chamado recebido');
                        utterance.lang = 'pt-BR';
                        utterance.rate = 1.2;
                        speechSynthesis.speak(utterance);
                    }
                    // Exibe botão para ativar o som manualmente
                    showEnableSoundButton();
                });
                lastSoundPlay = now;
            }
        }

        // Handlers globais para fallback em onclick inline
        async function enableSoundClick() {
            try {
                await tryInitAudio(false);
            } catch (e) {}
            CONFIG.soundEnabled = true;
            localStorage.setItem('tv_sound_enabled', 'true');
            lastSoundPlay = 0; // permite tocar imediatamente
            playSound();
        }

        async function testSoundClick() {
            try {
                await tryInitAudio(false);
            } catch (e) {}
            CONFIG.soundEnabled = true;
            localStorage.setItem('tv_sound_enabled', 'true');
            lastSoundPlay = 0; // permite tocar imediatamente
            playSound();
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

        // Detecta novos IDs de chamados, independente de prioridade/contadores
        function detectNewTicketIds(tickets) {
            const ids = new Set((tickets || []).map(t => t.id));
            if (lastTicketIds.size === 0) {
                lastTicketIds = ids;
                return;
            }
            let hasNew = false;
            for (const id of ids) {
                if (!lastTicketIds.has(id)) { hasNew = true; break; }
            }
            if (hasNew) {
                playSound();
                flashHeader();
            }
            lastTicketIds = ids;
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
                // Robust: também detecta por novos IDs (toca som para qualquer novo chamado)
                detectNewTicketIds(allTickets);
                
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
        }

        function updateLastUpdate() {
            const now = new Date();
            $id('last-update').textContent = `Última atualização: ${now.toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' })}`;
        }

        // ===== Layout Auto-Fit =====
        function calcMaxTicketsDisplay() {
            if (!CONFIG.autoFit) return;
            const container = $id('tickets-container');
            if (!container) return;
            const gap = 16; // manter em sincronia com .tickets-grid gap
            const cardMinW = 280; // largura mínima aproximada do card
            const cardMinH = 170; // altura mínima aproximada do card
            const cw = container.clientWidth || container.offsetWidth || 0;
            const ch = container.clientHeight || container.offsetHeight || 0;
            if (!cw || !ch) return;
            const cols = Math.max(1, Math.floor((cw + gap) / (cardMinW + gap)));
            const rows = Math.max(1, Math.floor((ch + gap) / (cardMinH + gap)));
            const max = Math.max(8, cols * rows);
            CONFIG.maxTicketsDisplay = max;
        }

        // ===== UBS =====
        function updateUbsGrid() {
            const grid = $id('ubs-grid');
            if (!grid) return; // Se a coluna de status não existir, apenas ignore
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
                const icon = hasUrgent ? '<i class="bi bi-exclamation-triangle-fill" style="color:#ef4444"></i>' : (hasTickets ? '<i class="bi bi-exclamation-circle-fill" style="color:#f59e0b"></i>' : '<i class="bi bi-check-circle-fill" style="color:#10b981"></i>');
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

        // Tickets visibility rules: mostrar abertos/andamento/espera; remover resolvidos/fechados imediatamente (configurável)
        function withinMinutes(dateStr, minutes) {
            if (!dateStr) return false;
            const dt = new Date(dateStr);
            if (isNaN(dt)) return false;
            return (Date.now() - dt.getTime()) <= minutes * 60 * 1000;
        }

        function isTicketVisible(t) {
            if (!t || !t.status) return false;
            if (t.status === 'resolved' || t.status === 'closed') {
                const keep = Number(CONFIG.keepResolvedMinutes || 0);
                if (keep <= 0) return false;
                return withinMinutes(t.updated_at, keep) || withinMinutes(t.resolved_at, keep) || withinMinutes(t.closed_at, keep);
            }
            return true;
        }

        function getTicketsByPriority() {
            const base = allTickets.filter(isTicketVisible);
            return base
                .sort((a, b) => {
                    const ap = CONFIG.priorityOrder.indexOf(a.priority);
                    const bp = CONFIG.priorityOrder.indexOf(b.priority);
                    if (ap !== bp) return ap - bp;
                    return new Date(b.created_at) - new Date(a.created_at);
                });
        }

        function getRecentTickets() {
            const base = allTickets.filter(isTicketVisible);
            return base.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
        }

        function getTicketsByUbs() {
            const buckets = {};
            allTickets.filter(isTicketVisible).forEach(function(t) {
                const k = t.ubs_name || 'Sem UBS';
                if (!buckets[k]) buckets[k] = [];
                buckets[k].push(t);
            });

            const entries = Object.entries(buckets).sort(function(a, b) {
                const av = a[1], bv = b[1];
                const ua = av.filter(function(t){ return t.priority === 'urgent'; }).length;
                const ub = bv.filter(function(t){ return t.priority === 'urgent'; }).length;
                if (ua !== ub) return ub - ua;
                return bv.length - av.length;
            });

            const result = [];
            entries.forEach(function(pair){
                const arr = pair[1];
                arr.sort(function(a,b){
                    return CONFIG.priorityOrder.indexOf(a.priority) - CONFIG.priorityOrder.indexOf(b.priority);
                });
                arr.forEach(function(t){ result.push(t); });
            });
            return result;
        }

        /**
         * Converte cor hex (#RRGGBB) para rgba(r,g,b,a)
         */
        function hexToRgba(hex, alpha = 1) {
            if (!hex) return `rgba(255,255,255,${alpha})`;
            hex = hex.replace('#','');
            if (hex.length === 3) {
                hex = hex.split('').map(c => c + c).join('');
            }
            const bigint = parseInt(hex, 16);
            const r = (bigint >> 16) & 255;
            const g = (bigint >> 8) & 255;
            const b = bigint & 255;
            return `rgba(${r},${g},${b},${alpha})`;
        }

        function priorityColor(p) {
            switch(p) {
                case 'urgent': return '#ef4444';  // Vermelho forte (URGENTE)
                case 'high': return '#f59e0b';    // Amarelo/Laranja (ALTA)
                case 'medium': return '#10b981';  // Verde (MÉDIA)
                default: return '#6b7280';        // Cinza (BAIXA)
            }
        }

        function buildTicketStyle(t) {
            // Usa sempre a cor da prioridade (urgent=vermelho, high=amarelo, medium=verde, low=cinza)
            const base = priorityColor(t.priority);
            const rgbaStrong = hexToRgba(base, 0.50);  // Aumentado para cor mais visível
            const rgbaSoft = hexToRgba(base, 0.30);     // Aumentado para melhor contraste
            const rgbaBorder = hexToRgba(base, 0.70);   // Borda mais forte
            const shadow = hexToRgba(base, 0.45);       // Sombra mais evidente
            return `--ticket-base:${base};background:linear-gradient(135deg, ${rgbaStrong} 0%, ${rgbaSoft} 100%);border:2px solid ${rgbaBorder};box-shadow:0 8px 28px ${shadow};`;
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
            
            container.innerHTML = list.slice(0, CONFIG.maxTicketsDisplay).map(t => {
                let supportTeamHtml = '';
                if (t.support_technicians && t.support_technicians.length > 0) {
                    const supportNames = t.support_technicians.map(st => 
                        `<span class="support-badge"><i class="bi bi-person-plus"></i> ${st.name}</span>`
                    ).join('');
                    supportTeamHtml = `<div class="ticket-support-team">${supportNames}</div>`;
                }
                
                return `
                <div class="ticket-card priority-${t.priority}" style="${buildTicketStyle(t)}">
                    <div class="ticket-header">
                        <span class="ticket-id">#${t.id}</span>
                        <span class="ticket-priority priority-${t.priority}">${priorityLabel(t.priority)}</span>
                    </div>
                    <div class="ticket-title">${t.title || '—'}</div>
                    <div class="ticket-info">
                        <div class="ticket-user"><i class="bi bi-person"></i> ${t.user_name || 'N/A'}</div>
                        ${t.ubs_name ? `<div class="ticket-location"><i class="bi bi-geo-alt-fill"></i> ${t.ubs_name}</div>` : ''}
                        ${t.assigned_to ? `<div class="ticket-assigned"><i class="bi bi-person-check-fill"></i> ${t.assigned_to}</div>` : ''}
                        ${supportTeamHtml}
                        <div class="ticket-time"><i class="bi bi-clock"></i> ${fmtTime(t.created_at)}</div>
                    </div>
                </div>`;
            }).join('');
        }

        function updateTicketsDisplay() {
            calcMaxTicketsDisplay();
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
            calcMaxTicketsDisplay();
            loadData();

            // Recalcular ao redimensionar (debounce)
            let resizeTimer;
            window.addEventListener('resize', () => {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(() => {
                    const prev = CONFIG.maxTicketsDisplay;
                    calcMaxTicketsDisplay();
                    if (CONFIG.maxTicketsDisplay !== prev) {
                        updateTicketsDisplay();
                    }
                }, 150);
            });

            // ===== Service Worker Registration =====
            if ('serviceWorker' in navigator) {
                navigator.serviceWorker.register('/sw.js')
                    .then(reg => {
                        console.log('ServiceWorker registrado:', reg.scope);
                    })
                    .catch(err => console.warn('SW falhou:', err));
            }

            // ===== Install Prompt (A2HS) =====
            let deferredPrompt;
            const installBtn = document.getElementById('pwa-install');

            // Esconde se já estiver em modo standalone (app instalado)
            const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
            if (isStandalone) {
                installBtn.style.display = 'none';
            }

            window.addEventListener('beforeinstallprompt', (e) => {
                e.preventDefault();
                deferredPrompt = e;
                installBtn.style.display = 'inline-flex';
            });

            installBtn.addEventListener('click', async () => {
                // Se o prompt não estiver disponível (ex.: HTTP), mostra modal com instruções
                if (!deferredPrompt) {
                    const modal = document.getElementById('install-modal');
                    modal.style.display = 'flex';
                    return;
                }

                try {
                    deferredPrompt.prompt();
                    const { outcome } = await deferredPrompt.userChoice;
                    if (outcome === 'accepted') {
                        console.log('PWA: instalado');
                        installBtn.style.display = 'none';
                    }
                } finally {
                    deferredPrompt = null;
                }
            });

            window.addEventListener('appinstalled', () => {
                console.log('PWA: app instalado');
                installBtn.style.display = 'none';
            });

            // Carrega preferências
            const savedEnabled = localStorage.getItem('tv_sound_enabled');
            if (savedEnabled !== null) {
                CONFIG.soundEnabled = savedEnabled === 'true';
            }

            // Tentativa de habilitar áudio no carregamento (pode ser bloqueado)
            tryInitAudio(true);

            // Desbloqueio global no primeiro gesto do usuário (Chrome/Safari)
            let unlockTried = false;
            const unlockAudioOnce = async () => {
                if (unlockTried || audioReady) return;
                unlockTried = true;
                try { await tryInitAudio(false); } catch(e) {}
            };
            ['click','touchstart','keydown'].forEach(evt => {
                window.addEventListener(evt, unlockAudioOnce, { once: true, capture: true });
            });

            // Clique manual para habilitar som
            const enableBtn = $id('enable-sound');
            if (enableBtn) {
                enableBtn.addEventListener('click', async () => {
                    await tryInitAudio(false);
                    // Marca som como habilitado e testa
                    CONFIG.soundEnabled = true;
                    localStorage.setItem('tv_sound_enabled', 'true');
                    playSound();
                });
            }

            // Botão de Teste de Som
            const testBtn = $id('test-sound');
            if (testBtn) {
                testBtn.addEventListener('click', async () => {
                    // Gesto de usuário: re-tenta inicializar e tocar
                    await tryInitAudio(false);
                    CONFIG.soundEnabled = true;
                    localStorage.setItem('tv_sound_enabled', 'true');
                    // Ignora cooldown para teste imediato
                    lastSoundPlay = 0;
                    playSound();
                });
            }
        });
    </script>
</body>
</html>
