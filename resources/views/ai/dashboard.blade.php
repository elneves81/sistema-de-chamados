<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🤖 IA Assistant - Sistema de Chamados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        :root {
            --ai-primary: #667eea;
            --ai-secondary: #764ba2;
            --ai-accent: #f093fb;
            --ai-success: #4facfe;
            --ai-warning: #ffeaa7;
            --ai-danger: #fd79a8;
            --ai-dark: #2d3436;
            --ai-light: #f8f9fa;
        }

        .ai-gradient {
            background: linear-gradient(135deg, var(--ai-primary) 0%, var(--ai-secondary) 100%);
        }

        .ai-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .ai-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.15);
        }

        .chat-container {
            height: 500px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 20px;
            overflow: hidden;
        }

        .chat-messages {
            height: 400px;
            overflow-y: auto;
            padding: 20px;
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .message {
            max-width: 80%;
            padding: 12px 18px;
            border-radius: 20px;
            animation: slideIn 0.3s ease;
            position: relative;
        }

        .message.user {
            background: var(--ai-primary);
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 5px;
        }

        .message.ai {
            background: white;
            color: var(--ai-dark);
            align-self: flex-start;
            border-bottom-left-radius: 5px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .message.ai::before {
            content: '🤖';
            position: absolute;
            left: -30px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 20px;
        }

        .chat-input {
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-top: 1px solid rgba(255, 255, 255, 0.3);
        }

        .suggestions {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 10px;
        }

        .suggestion-btn {
            background: var(--ai-accent);
            border: none;
            color: white;
            padding: 5px 12px;
            border-radius: 15px;
            font-size: 12px;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .suggestion-btn:hover {
            background: var(--ai-primary);
            transform: scale(1.05);
        }

        /* Estilos para botões especiais */
        .suggestions .btn-success {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            color: white;
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 15px;
        }

        .suggestions .btn-success:hover {
            background: linear-gradient(135deg, #20c997, #28a745);
            transform: scale(1.05);
        }

        .suggestions .btn-info {
            background: linear-gradient(135deg, #17a2b8, #007bff);
            border: none;
            color: white;
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 15px;
        }

        .suggestions .btn-info:hover {
            background: linear-gradient(135deg, #007bff, #17a2b8);
            transform: scale(1.05);
        }

        .ai-insight {
            background: linear-gradient(135deg, var(--ai-success) 0%, var(--ai-accent) 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            border: none;
            animation: pulse 2s infinite;
        }

        .ai-metric {
            text-align: center;
            padding: 25px;
            border-radius: 15px;
            background: white;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .ai-metric:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
        }

        .ai-metric .metric-value {
            font-size: 2.5rem;
            font-weight: bold;
            background: linear-gradient(135deg, var(--ai-primary), var(--ai-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .prediction-chart {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .typing-indicator {
            display: flex;
            align-items: center;
            gap: 5px;
            padding: 10px 15px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 20px;
            align-self: flex-start;
        }

        .typing-dot {
            width: 8px;
            height: 8px;
            background: var(--ai-primary);
            border-radius: 50%;
            animation: typing 1.4s infinite;
        }

        .typing-dot:nth-child(2) { animation-delay: 0.2s; }
        .typing-dot:nth-child(3) { animation-delay: 0.4s; }

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

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-10px); }
        }

        .ai-feature {
            padding: 30px;
            border-radius: 20px;
            background: white;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .ai-feature:hover {
            border-color: var(--ai-primary);
            transform: scale(1.02);
        }

        .ai-feature .feature-icon {
            font-size: 3rem;
            background: linear-gradient(135deg, var(--ai-primary), var(--ai-accent));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
        }

        .status-online {
            display: inline-block;
            width: 10px;
            height: 10px;
            background: #2ecc71;
            border-radius: 50%;
            margin-right: 8px;
            animation: pulse 2s infinite;
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
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="ai-card p-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h1 class="mb-0">🤖 Assistente IA - DITIS</h1>
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
                            <div>
                                <span class="status-online"></span>
                                <span class="text-success fw-bold">Online</span>
                                <br>
                                <small class="text-muted">Última atualização: <span id="lastUpdate">{{ now()->format('H:i:s') }}</span></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Métricas Principais -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="ai-metric">
                    <div class="metric-value" id="totalTickets">0</div>
                    <div class="text-muted">Total de Chamados</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ai-metric">
                    <div class="metric-value" id="ticketsToday">0</div>
                    <div class="text-muted">Hoje</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ai-metric">
                    <div class="metric-value" id="ticketsWeek">0</div>
                    <div class="text-muted">Esta Semana</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ai-metric">
                    <div class="metric-value" id="growthRate">0%</div>
                    <div class="text-muted">Crescimento</div>
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
            <!-- Chatbot -->
            <div class="col-lg-6">
                <div class="ai-card h-100">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h3 class="mb-0">💬 Chat Inteligente</h3>
                        <p class="text-muted mb-0">Converse com o assistente IA</p>
                    </div>
                    <div class="chat-container">
                        <div class="chat-messages" id="chatMessages">
                            <div class="message ai">
                                Olá! Sou o assistente IA do sistema de chamados. Como posso ajudá-lo hoje?
                            </div>
                        </div>
                        <div class="chat-input">
                            <div class="input-group">
                                <input type="text" class="form-control" id="chatInput" placeholder="Digite sua mensagem...">
                                <button class="btn btn-primary" id="sendMessage">
                                    <i class="bi bi-send"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Classificador Inteligente -->
            <div class="col-lg-6">
                <div class="ai-card h-100">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h3 class="mb-0">🎯 Classificador IA</h3>
                        <p class="text-muted mb-0">Análise automática de chamados</p>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Título do Chamado</label>
                            <input type="text" class="form-control" id="classifyTitle" placeholder="Ex: Computador não liga">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea class="form-control" id="classifyDescription" rows="3" placeholder="Descreva o problema..."></textarea>
                        </div>
                        <button class="btn btn-primary w-100" id="classifyBtn">
                            <i class="bi bi-cpu"></i> Analisar com IA
                        </button>
                        <div id="classificationResult"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Previsões e Análises -->
        <div class="row mb-4">
            <!-- Previsão de Demanda -->
            <div class="col-lg-8">
                <div class="prediction-chart">
                    <h4 class="mb-3">📊 Previsão de Demanda (7 dias)</h4>
                    <canvas id="predictionChart" width="400" height="200"></canvas>
                </div>
            </div>

            <!-- Top Categorias -->
            <div class="col-lg-4">
                <div class="ai-card h-100">
                    <div class="card-header bg-transparent border-0 p-4">
                        <h4 class="mb-0">📋 Categorias Populares</h4>
                    </div>
                    <div class="card-body">
                        <div id="topCategories"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Funcionalidades IA -->
        <div class="row">
            <div class="col-md-3">
                <div class="ai-feature">
                    <div class="feature-icon">🤖</div>
                    <h5>Chatbot Inteligente</h5>
                    <p class="text-muted mb-0">Respostas automáticas e suporte 24/7</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ai-feature">
                    <div class="feature-icon">🎯</div>
                    <h5>Classificação Auto</h5>
                    <p class="text-muted mb-0">Categorização inteligente de chamados</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ai-feature">
                    <div class="feature-icon">📈</div>
                    <h5>Análise Preditiva</h5>
                    <p class="text-muted mb-0">Previsão de demanda e tendências</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="ai-feature">
                    <div class="feature-icon">💡</div>
                    <h5>Sugestões Smart</h5>
                    <p class="text-muted mb-0">Soluções automáticas da base de conhecimento</p>
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

                messageDiv.textContent = displayText;
                messagesContainer.appendChild(messageDiv);

                // Processar sugestões
                if (Array.isArray(suggestions) && suggestions.length > 0) {
                    const suggestionsDiv = document.createElement('div');
                    suggestionsDiv.className = 'suggestions';
                    
                    suggestions.forEach(suggestion => {
                        const btn = document.createElement('button');
                        btn.className = 'suggestion-btn';
                        
                        let suggestionText = '';
                        let isSpecialAction = false;
                        
                        if (typeof suggestion === 'object' && suggestion !== null) {
                            suggestionText = suggestion.text || suggestion.label || JSON.stringify(suggestion);
                            
                            // Verificar se é uma ação especial
                            if (suggestion.action === 'create_ticket') {
                                btn.className = 'btn btn-success btn-sm';
                                btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>' + suggestionText;
                                isSpecialAction = true;
                            } else if (suggestion.action === 'redirect_contact') {
                                btn.className = 'btn btn-info btn-sm';
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
                        suggestionsDiv.appendChild(btn);
                    });
                    
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
