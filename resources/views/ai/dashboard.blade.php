<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🤖 Assistente IA - Suporte+ Saúde</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --ai-primary: #667eea;
            --ai-secondary: #764ba2;
            --ai-accent: #10b981;
            --ai-warning: #f59e0b;
            --ai-danger: #ef4444;
            --ai-dark: #1e293b;
            --ai-light: #f8fafc;
            --ai-border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/></svg>');
            pointer-events: none;
        }

        .container-fluid {
            position: relative;
            z-index: 1;
        }

        /* Header Moderno */
        .ai-header {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2rem;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            margin-bottom: 2rem;
        }

        .ai-header h1 {
            font-size: 2rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--ai-primary), var(--ai-secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .ai-header h1 .ai-icon {
            font-size: 2.5rem;
            -webkit-text-fill-color: initial;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(16, 185, 129, 0.1);
            border-radius: 100px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--ai-accent);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            background: var(--ai-accent);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        /* Cards Melhorados */
        .ai-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .ai-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.15);
        }

        .ai-card-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.05), rgba(118, 75, 162, 0.05));
            border-bottom: 1px solid var(--ai-border);
            padding: 1.5rem;
        }

        .ai-card-header h3 {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--ai-dark);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .ai-card-body {
            padding: 1.5rem;
        }

        /* Métricas Modernas */
        .metric-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid var(--ai-border);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .metric-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--ai-primary), var(--ai-secondary));
        }

        .metric-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 24px rgba(0, 0, 0, 0.1);
        }

        .metric-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: var(--ai-primary);
            margin-bottom: 0.5rem;
            line-height: 1;
        }

        .metric-label {
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .metric-trend {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.875rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .metric-trend.up {
            color: var(--ai-accent);
        }

        .metric-trend.down {
            color: var(--ai-danger);
        }

        /* Chat Melhorado */
        .chat-container {
            height: 600px;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 20px;
            overflow: hidden;
        }

        .chat-messages {
            flex: 1;
            overflow-y: auto;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 1rem;
            scroll-behavior: smooth;
        }

        .chat-messages::-webkit-scrollbar {
            width: 8px;
        }

        .chat-messages::-webkit-scrollbar-track {
            background: transparent;
        }

        .chat-messages::-webkit-scrollbar-thumb {
            background: rgba(102, 126, 234, 0.3);
            border-radius: 4px;
        }

        .message {
            max-width: 75%;
            padding: 1rem 1.25rem;
            border-radius: 18px;
            animation: messageSlide 0.3s ease-out;
            position: relative;
            word-wrap: break-word;
        }

        @keyframes messageSlide {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .message.user {
            background: linear-gradient(135deg, var(--ai-primary), var(--ai-secondary));
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .message.ai {
            background: white;
            color: var(--ai-dark);
            align-self: flex-start;
            border-bottom-left-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border: 1px solid var(--ai-border);
        }

        .message.ai::before {
            content: '🤖';
            position: absolute;
            left: -40px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 24px;
        }

        .message-time {
            font-size: 0.75rem;
            opacity: 0.7;
            margin-top: 0.5rem;
        }

        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 1rem;
            background: white;
            border-radius: 18px;
            width: fit-content;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: var(--ai-primary);
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-dot:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 60%, 100% {
                opacity: 0.3;
                transform: translateY(0);
            }
            30% {
                opacity: 1;
                transform: translateY(-8px);
            }
        }

        .chat-input-area {
            padding: 1.5rem;
            background: rgba(255, 255, 255, 0.95);
            border-top: 1px solid var(--ai-border);
        }

        .chat-input-group {
            display: flex;
            gap: 0.75rem;
            align-items: center;
        }

        .chat-input-group input {
            flex: 1;
            border: 2px solid var(--ai-border);
            border-radius: 12px;
            padding: 0.875rem 1.25rem;
            font-size: 0.9375rem;
            transition: all 0.3s ease;
        }

        .chat-input-group input:focus {
            outline: none;
            border-color: var(--ai-primary);
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .chat-send-btn {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--ai-primary), var(--ai-secondary));
            border: none;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.25rem;
        }

        .chat-send-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        }

        .chat-send-btn:active {
            transform: scale(0.95);
        }

        /* Sugestões Rápidas */
        .quick-suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.75rem;
        }

        .suggestion-chip {
            background: rgba(102, 126, 234, 0.1);
            border: 1px solid rgba(102, 126, 234, 0.2);
            color: var(--ai-primary);
            padding: 0.5rem 1rem;
            border-radius: 100px;
            font-size: 0.8125rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .suggestion-chip:hover {
            background: var(--ai-primary);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        /* Insights IA */
        .ai-insight {
            background: linear-gradient(135deg, var(--ai-accent) 0%, #059669 100%);
            color: white;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: 0 8px 24px rgba(16, 185, 129, 0.3);
        }

        .ai-insight-icon {
            font-size: 2rem;
            margin-bottom: 0.5rem;
        }

        .ai-insight-title {
            font-size: 1.125rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .ai-insight-text {
            font-size: 0.9375rem;
            opacity: 0.95;
        }

        /* Features Grid */
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .feature-card {
            background: rgba(255, 255, 255, 0.98);
            border-radius: 16px;
            padding: 2rem;
            text-align: center;
            border: 1px solid var(--ai-border);
            transition: all 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.12);
        }

        .feature-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feature-card h5 {
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--ai-dark);
            margin-bottom: 0.75rem;
        }

        .feature-card p {
            font-size: 0.875rem;
            color: #64748b;
            margin: 0;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .ai-header h1 {
                font-size: 1.5rem;
            }

            .chat-container {
                height: 500px;
            }

            .message {
                max-width: 85%;
            }

            .metric-value {
                font-size: 2rem;
            }
        }

        /* Loading State */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s ease-in-out infinite;
            border-radius: 8px;
        }

        @keyframes loading {
            0% {
                background-position: 200% 0;
            }
            100% {
                background-position: -200% 0;
            }
        }

        /* Additional Charts */
        .prediction-chart {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .classification-result {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin: 15px 0;
        }

        .confidence-bar {
            width: 100%;
            height: 8px;
            background: rgba(255, 255, 255, 0.3);
            border-radius: 4px;
            overflow: hidden;
            margin-top: 10px;
        }

        .confidence-fill {
            height: 100%;
            background: linear-gradient(90deg, #f093fb, #f5576c);
            transition: width 0.5s ease;
            border-radius: 4px;
        }
    </style>
</head>
<body class="ai-gradient">
    <div class="container-fluid py-4">
        <!-- Header Modernizado -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="ai-header">
                    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
                        <div>
                            <h1 class="mb-1">
                                <span class="ai-icon">🤖</span>
                                Assistente IA - DITIS
                            </h1>
                            <p class="text-muted mb-0">Sistema Inteligente de Suporte Técnico</p>
                        </div>
                        <div class="text-end">
                            <div class="mb-2">
                                <a href="/tickets/create" class="btn btn-primary btn-sm me-2">
                                    <i class="bi bi-plus-circle"></i> Criar Chamado
                                </a>
                                <a href="/tickets" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-list-ul"></i> Meus Chamados
                                </a>
                            </div>
                            <div class="status-badge">
                                <span class="status-dot"></span>
                                <span class="fw-semibold">Sistema Online</span>
                                <span class="text-muted small ms-2">{{ now()->format('H:i:s') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas Principais Modernizadas -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="metric-card">
                    <div class="metric-icon">📊</div>
                    <div class="metric-value" id="totalTickets">0</div>
                    <div class="metric-label">Total de Chamados</div>
                    <div class="metric-trend trend-up">
                        <i class="bi bi-arrow-up"></i> 12%
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="metric-card">
                    <div class="metric-icon">📅</div>
                    <div class="metric-value" id="ticketsToday">0</div>
                    <div class="metric-label">Chamados Hoje</div>
                    <div class="metric-trend trend-up">
                        <i class="bi bi-arrow-up"></i> 5%
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3 mb-md-0">
                <div class="metric-card">
                    <div class="metric-icon">📆</div>
                    <div class="metric-value" id="ticketsWeek">0</div>
                    <div class="metric-label">Esta Semana</div>
                    <div class="metric-trend trend-neutral">
                        <i class="bi bi-dash"></i> 0%
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="metric-card">
                    <div class="metric-icon">📈</div>
                    <div class="metric-value" id="growthRate">0%</div>
                    <div class="metric-label">Crescimento</div>
                    <div class="metric-trend trend-up">
                        <i class="bi bi-arrow-up"></i> Positivo
                    </div>
                </div>
            </div>
        </div>

        <!-- Insights IA -->
        <div class="row mb-4">
            <div class="col-12">
                <div id="aiInsights"></div>
            </div>
        </div>

        <!-- Funcionalidades Principais -->
        <div class="row mb-4">
            <!-- Chatbot Modernizado -->
            <div class="col-lg-6 mb-4 mb-lg-0">
                <div class="ai-card h-100">
                    <div class="ai-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="header-icon">💬</span>
                            <div>
                                <h3 class="mb-0">Chat Inteligente</h3>
                                <p class="text-muted small mb-0">Converse com o assistente IA</p>
                            </div>
                        </div>
                    </div>
                    <div class="chat-container">
                        <div class="chat-messages" id="chatMessages">
                            <div class="message ai">
                                <div class="message-avatar">🤖</div>
                                <div class="message-content">
                                    Olá! Sou o assistente IA do sistema de chamados. Como posso ajudá-lo hoje?
                                </div>
                            </div>
                        </div>
                        <div class="quick-suggestions">
                            <button class="suggestion-chip">Como abrir um chamado?</button>
                            <button class="suggestion-chip">Problemas comuns</button>
                            <button class="suggestion-chip">Status do sistema</button>
                        </div>
                        <div class="chat-input-area">
                            <div class="chat-input-group">
                                <input type="text" class="chat-input" id="chatInput" placeholder="Digite sua mensagem...">
                                <button class="chat-send-btn" id="sendMessage">
                                    <i class="bi bi-send-fill"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classificador Inteligente -->
            <div class="col-lg-6">
                <div class="ai-card h-100">
                    <div class="ai-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="header-icon">🎯</span>
                            <div>
                                <h3 class="mb-0">Classificador IA</h3>
                                <p class="text-muted small mb-0">Análise automática de chamados</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Título do Chamado</label>
                            <input type="text" class="form-control" id="classifyTitle" placeholder="Ex: Computador não liga">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Descrição</label>
                            <textarea class="form-control" id="classifyDescription" rows="4" placeholder="Descreva o problema em detalhes..."></textarea>
                        </div>
                        <button class="btn btn-primary w-100 py-2" id="classifyBtn">
                            <i class="bi bi-cpu me-1"></i> Analisar com IA
                        </button>
                        <div id="classificationResult"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previsões e Análises -->
        <div class="row mb-4">
            <!-- Previsão de Demanda -->
            <div class="col-lg-8 mb-4 mb-lg-0">
                <div class="ai-card">
                    <div class="ai-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="header-icon">📊</span>
                            <div>
                                <h4 class="mb-0">Previsão de Demanda</h4>
                                <p class="text-muted small mb-0">Próximos 7 dias</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <canvas id="predictionChart" height="80"></canvas>
                    </div>
                </div>
            </div>

            <!-- Top Categorias -->
            <div class="col-lg-4">
                <div class="ai-card h-100">
                    <div class="ai-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <span class="header-icon">📋</span>
                            <div>
                                <h4 class="mb-0">Categorias Populares</h4>
                                <p class="text-muted small mb-0">Mais reportadas</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div id="topCategories"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Funcionalidades IA -->
        <div class="row">
            <div class="col-12">
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🤖</div>
                        <h5 class="mb-2">Chatbot Inteligente</h5>
                        <p class="text-muted small mb-0">Respostas automáticas e suporte 24/7</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🎯</div>
                        <h5 class="mb-2">Classificação Auto</h5>
                        <p class="text-muted small mb-0">Categorização inteligente de chamados</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📈</div>
                        <h5 class="mb-2">Análise Preditiva</h5>
                        <p class="text-muted small mb-0">Previsão de demanda e tendências</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">💡</div>
                        <h5 class="mb-2">Sugestões Smart</h5>
                        <p class="text-muted small mb-0">Soluções automáticas da base de conhecimento</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        class AIAssistant {
            constructor() {
                this.initEventListeners();
                this.loadDashboardData();
                this.loadPredictions();
            }

            initEventListeners() {
                // Chat
                document.getElementById('sendMessage').addEventListener('click', () => this.sendMessage());
                document.getElementById('chatInput').addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') this.sendMessage();
                });

                // Classificador
                document.getElementById('classifyBtn').addEventListener('click', () => this.classifyTicket());

                // Suggestion Chips
                document.querySelectorAll('.suggestion-chip').forEach(chip => {
                    chip.addEventListener('click', (e) => {
                        const input = document.getElementById('chatInput');
                        input.value = e.target.textContent;
                        input.focus();
                    });
                });
            }

            async sendMessage() {
                const input = document.getElementById('chatInput');
                const message = input.value.trim();
                
                if (!message) return;

                this.addMessage(message, 'user');
                input.value = '';
                
                this.showTypingIndicator();

                try {
                    const response = await fetch('/api/ai/chatbot', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ message })
                    });

                    if (!response.ok) {
                        throw new Error(`Erro HTTP: ${response.status}`);
                    }

                    const data = await response.json();
                    
                    console.log('Resposta completa da IA:', data); // Debug
                    console.log('Tipo da resposta:', typeof data.response); // Debug
                    
                    this.hideTypingIndicator();
                    
                    // Verificar se há uma resposta válida
                    if (data.response !== undefined) {
                        this.addMessage(data.response, 'ai', data.suggestions || []);
                    } else {
                        this.addMessage('❌ Resposta inválida do servidor.', 'ai');
                    }

                } catch (error) {
                    console.error('Erro ao enviar mensagem:', error);
                    this.hideTypingIndicator();
                    this.addMessage(`❌ Erro de conexão: ${error.message}. Tente novamente.`, 'ai');
                }
            }

            addMessage(text, sender, suggestions = []) {
                const messagesContainer = document.getElementById('chatMessages');
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${sender}`;
                
                // Avatar
                const avatar = document.createElement('div');
                avatar.className = 'message-avatar';
                avatar.textContent = sender === 'user' ? '👤' : '🤖';
                
                // Content
                const content = document.createElement('div');
                content.className = 'message-content';
                
                // Verificar tipo de text e processar adequadamente
                let displayText = '';
                if (typeof text === 'object') {
                    if (text && text.toString && text.toString() !== '[object Object]') {
                        displayText = text.toString();
                    } else {
                        displayText = JSON.stringify(text, null, 2);
                    }
                    console.log('Objeto recebido no dashboard:', text);
                } else if (typeof text === 'string') {
                    displayText = text;
                } else {
                    displayText = String(text || 'Resposta vazia');
                }

                content.textContent = displayText;
                
                messageDiv.appendChild(avatar);
                messageDiv.appendChild(content);
                messagesContainer.appendChild(messageDiv);

                // Processar sugestões
                if (Array.isArray(suggestions) && suggestions.length > 0) {
                    const suggestionsDiv = document.createElement('div');
                    suggestionsDiv.className = 'message ai';
                    
                    const suggestionsContent = document.createElement('div');
                    suggestionsContent.className = 'quick-suggestions mt-2';
                    
                    suggestions.forEach(suggestion => {
                        const btn = document.createElement('button');
                        btn.className = 'suggestion-chip';
                        
                        let suggestionText = '';
                        let isSpecialAction = false;
                        
                        if (typeof suggestion === 'object' && suggestion !== null) {
                            suggestionText = suggestion.text || suggestion.label || JSON.stringify(suggestion);
                            
                            // Verificar se é uma ação especial
                            if (suggestion.action === 'create_ticket') {
                                btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>' + suggestionText;
                                isSpecialAction = true;
                            } else if (suggestion.action === 'redirect_contact') {
                                btn.innerHTML = '<i class="bi bi-envelope me-1"></i>' + suggestionText;
                                isSpecialAction = true;
                            }
                        } else {
                            suggestionText = String(suggestion || 'Sugestão');
                        }
                        
                        if (!isSpecialAction) {
                            btn.textContent = suggestionText;
                        }
                        
                        btn.addEventListener('click', () => {
                            if (suggestion.action === 'create_ticket') {
                                window.location.href = '/tickets/create';
                            } else if (suggestion.action === 'redirect_contact') {
                                window.location.href = '/fale-conosco';
                            } else {
                                document.getElementById('chatInput').value = suggestionText;
                            }
                        });
                        suggestionsContent.appendChild(btn);
                    });
                    
                    suggestionsDiv.appendChild(suggestionsContent);
                    messagesContainer.appendChild(suggestionsDiv);
                }

                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            showTypingIndicator() {
                const messagesContainer = document.getElementById('chatMessages');
                const indicator = document.createElement('div');
                indicator.className = 'typing-indicator';
                indicator.id = 'typingIndicator';
                indicator.innerHTML = '<div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div>';
                messagesContainer.appendChild(indicator);
                messagesContainer.scrollTop = messagesContainer.scrollHeight;
            }

            hideTypingIndicator() {
                const indicator = document.getElementById('typingIndicator');
                if (indicator) indicator.remove();
            }

            async classifyTicket() {
                const title = document.getElementById('classifyTitle').value;
                const description = document.getElementById('classifyDescription').value;
                
                if (!title && !description) {
                    alert('Preencha pelo menos o título ou descrição');
                    return;
                }

                const resultDiv = document.getElementById('classificationResult');
                resultDiv.innerHTML = '<div class="text-center"><div class="spinner-border text-primary" role="status"></div></div>';

                try {
                    const response = await fetch('/api/ai/classify', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ title, description })
                    });

                    const data = await response.json();
                    this.displayClassificationResult(data);

                } catch (error) {
                    resultDiv.innerHTML = '<div class="alert alert-danger">Erro na classificação</div>';
                }
            }

            displayClassificationResult(data) {
                const resultDiv = document.getElementById('classificationResult');
                
                let html = '<div class="classification-result mt-3">';
                
                if (data.classification) {
                    html += `
                        <h6><i class="bi bi-tag"></i> Categoria Sugerida</h6>
                        <p>Confiança: ${Math.round(data.classification.confidence)}%</p>
                        <div class="confidence-bar">
                            <div class="confidence-fill" style="width: ${data.classification.confidence}%"></div>
                        </div>
                    `;
                }
                
                if (data.urgency) {
                    html += `
                        <h6 class="mt-3"><i class="bi bi-exclamation-triangle"></i> Prioridade</h6>
                        <p>Nível: <strong>${data.urgency.priority.toUpperCase()}</strong></p>
                        <p><small>${data.urgency.reason}</small></p>
                    `;
                }
                
                html += '</div>';
                
                if (data.suggestions && data.suggestions.length > 0) {
                    html += '<div class="mt-3"><h6>💡 Soluções Sugeridas:</h6><ul>';
                    data.suggestions.forEach(suggestion => {
                        html += `<li><a href="${suggestion.url}" target="_blank">${suggestion.title}</a></li>`;
                    });
                    html += '</ul></div>';
                }
                
                resultDiv.innerHTML = html;
            }

            async loadDashboardData() {
                try {
                    const response = await fetch('/api/ai/dashboard');
                    const data = await response.json();
                    
                    document.getElementById('totalTickets').textContent = data.stats.total_tickets;
                    document.getElementById('ticketsToday').textContent = data.stats.tickets_today;
                    document.getElementById('ticketsWeek').textContent = data.stats.tickets_this_week;
                    document.getElementById('growthRate').textContent = `${data.stats.growth_rate}%`;
                    
                    this.displayInsights(data.insights);
                    this.displayTopCategories(data.top_categories);
                    
                    document.getElementById('lastUpdate').textContent = new Date().toLocaleTimeString();
                    
                } catch (error) {
                    console.error('Erro ao carregar dados:', error);
                }
            }

            displayInsights(insights) {
                const container = document.getElementById('aiInsights');
                container.innerHTML = '';
                
                insights.forEach(insight => {
                    const priorityClass = insight.priority === 'high' ? 'danger' : 
                                        insight.priority === 'medium' ? 'warning' : 'info';
                    
                    const html = `
                        <div class="ai-insight alert alert-${priorityClass}">
                            <h5><i class="bi bi-lightbulb"></i> ${insight.title}</h5>
                            <p class="mb-2">${insight.message}</p>
                            <small><strong>Ação recomendada:</strong> ${insight.action}</small>
                        </div>
                    `;
                    container.innerHTML += html;
                });
            }

            displayTopCategories(categories) {
                const container = document.getElementById('topCategories');
                container.innerHTML = '';
                
                categories.forEach((category, index) => {
                    const percentage = Math.round((category.count / categories[0].count) * 100);
                    
                    const html = `
                        <div class="mb-3">
                            <div class="d-flex justify-content-between">
                                <span>${category.name}</span>
                                <span class="badge bg-primary">${category.count}</span>
                            </div>
                            <div class="progress mt-1" style="height: 8px;">
                                <div class="progress-bar" style="width: ${percentage}%"></div>
                            </div>
                        </div>
                    `;
                    container.innerHTML += html;
                });
            }

            async loadPredictions() {
                try {
                    const response = await fetch('/api/ai/predict?days=7');
                    const data = await response.json();
                    
                    this.createPredictionChart(data.predictions);
                    
                } catch (error) {
                    console.error('Erro ao carregar previsões:', error);
                }
            }

            createPredictionChart(predictions) {
                const ctx = document.getElementById('predictionChart').getContext('2d');
                // Normaliza dados e evita erros quando propriedades não existem
                const safe = Array.isArray(predictions) ? predictions.filter(p => p) : [];
                const labels = safe.map(p => {
                    const label = p.day_name ?? p.day_of_week ?? p.date ?? '';
                    return String(label).substring(0, 3) || 'N/D';
                });
                const series = safe.map(p => Number(p.predicted_tickets ?? p.value ?? p.count ?? 0));

                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels,
                        datasets: [{
                            label: 'Chamados Previstos',
                            data: series,
                            borderColor: '#667eea',
                            backgroundColor: 'rgba(102, 126, 234, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: '#667eea',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        },
                        elements: {
                            point: {
                                hoverRadius: 8
                            }
                        }
                    }
                });
            }
        }

        // Inicializar quando a página carregar
        document.addEventListener('DOMContentLoaded', () => {
            new AIAssistant();
        });

        // Atualizar dados a cada 5 minutos
        setInterval(() => {
            const ai = new AIAssistant();
            ai.loadDashboardData();
        }, 300000);
    </script>
</body>
</html>
