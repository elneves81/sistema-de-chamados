<!-- Widget Chat IA Flutuante — versão aprimorada com modal inteligente
Compatível com Blade/Laravel (usa csrf-token). Foco em segurança (anti‑XSS), acessibilidade e UX. 
Coloque este bloco próximo ao </body>. Requer Bootstrap Icons.
-->

<div class="ai-float-chat" id="aiFloatChat" aria-live="polite">
  <!-- Botão para abrir/fechar o chat -->
  <button class="ai-chat-toggle" id="aiChatToggle" type="button" aria-label="Abrir chat de suporte">
    <i class="bi bi-robot" aria-hidden="true"></i>
    <span class="badge bg-danger ai-notification-badge" id="aiNotificationBadge" style="display:none;">0</span>
  </button>

  <!-- Container do chat -->
  <section class="ai-chat-container" id="aiChatContainer" style="display:none;" role="dialog" aria-modal="true" aria-labelledby="aiChatTitle">
    <!-- Header do chat -->
    <header class="ai-chat-header">
      <div class="d-flex align-items-center">
        <div class="ai-avatar me-2" aria-hidden="true">
          <i class="bi bi-robot"></i>
        </div>
        <div>
          <h6 class="mb-0" id="aiChatTitle">Assistente IA - DITIS</h6>
          <small class="text-muted">Online • Guarapuava</small>
        </div>
      </div>
      <button type="button" class="btn-close btn-close-white" id="aiChatClose" aria-label="Fechar chat"></button>
    </header>

    <!-- Mensagens do chat -->
    <main class="ai-chat-messages" id="aiChatMessages" aria-live="polite" aria-relevant="additions" tabindex="0">
      <div class="ai-message">
        <div class="ai-avatar-small" aria-hidden="true">
          <i class="bi bi-robot"></i>
        </div>
        <div class="message-bubble ai-bubble">
          <p>Olá, {{ auth()->user()->name ?? 'usuário' }}! 👋</p>
          <p>Sou seu assistente virtual do DITIS (Departamento de Informação, Tecnologia e Inovação em Saúde).</p>
          <p>Como posso ajudá-lo hoje?</p>
          <div class="message-time">{{ now()->format('H:i') }}</div>
        </div>
      </div>
    </main>

    <!-- Sugestões rápidas -->
    <div class="ai-quick-suggestions" id="aiQuickSuggestions">
      <div class="suggestion-buttons">
        <button type="button" class="btn btn-sm btn-outline-primary suggestion-btn" data-message="Como criar um chamado?">
          🎫 Criar chamado
        </button>
        <button type="button" class="btn btn-sm btn-outline-primary suggestion-btn" data-message="Meu computador não liga">
          💻 Hardware
        </button>
        <button type="button" class="btn btn-sm btn-outline-primary suggestion-btn" data-message="Sistema travou">
          🖥️ Software
        </button>
        <button type="button" class="btn btn-sm btn-outline-primary suggestion-btn" data-message="Internet está lenta">
          🌐 Rede
        </button>
      </div>
    </div>

    <!-- Input de mensagem -->
    <footer class="ai-chat-input">
      <form class="input-group" id="aiFloatForm" autocomplete="off">
        <input type="text" class="form-control" id="aiFloatChatInput" placeholder="Digite sua mensagem..." maxlength="500" aria-label="Campo de mensagem do chat">
        <button class="btn btn-primary" type="submit" id="aiFloatSendMessage" aria-label="Enviar mensagem">
          <i class="bi bi-send" aria-hidden="true"></i>
        </button>
      </form>
    </footer>
  </section>
</div>

<!-- Modal Inteligente para Criação de Chamado via IA -->
<div class="modal fade" id="aiTicketModal" tabindex="-1" aria-labelledby="aiTicketModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content border-0 shadow-lg">
      
      <!-- Estado Normal: Formulário -->
      <div id="aiTicketFormState">
        <div class="modal-header bg-gradient-primary text-white">
          <h5 class="modal-title" id="aiTicketModalLabel">
            <i class="bi bi-robot me-2"></i>Criar Chamado com Assistência IA
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-info border-0">
            <i class="bi bi-lightbulb me-2"></i>
            <strong>Dica:</strong> A IA analisará automaticamente sua solicitação e sugerirá a melhor categoria e prioridade.
          </div>
          
          <form id="createTicketForm">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="ticketTitle" class="form-label">
                  <i class="bi bi-card-heading me-1"></i>Título do Chamado *
                </label>
                <input type="text" class="form-control" id="ticketTitle" name="title" required maxlength="255">
                <div class="form-text">Resuma o problema em poucas palavras</div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="ticketLocal" class="form-label">
                  <i class="bi bi-geo-alt me-1"></i>Local/Setor *
                </label>
                <input type="text" class="form-control" id="ticketLocal" name="local" required maxlength="255" placeholder="Ex: UBS Centro, Secretaria...">
                <div class="form-text">Onde está localizado o problema?</div>
              </div>
            </div>
            
            <div class="mb-3">
              <label for="ticketDescription" class="form-label">
                <i class="bi bi-card-text me-1"></i>Descrição Detalhada *
              </label>
              <textarea class="form-control" id="ticketDescription" name="description" rows="4" required placeholder="Descreva o problema detalhadamente. Inclua quando começou, quais ações foram tentadas, mensagens de erro, etc."></textarea>
              <div class="form-text">Quanto mais detalhes, melhor poderemos ajudar</div>
            </div>
            
            <div class="mb-3">
              <label for="ticketUserName" class="form-label">
                <i class="bi bi-person me-1"></i>Seu Nome Completo *
              </label>
              <input type="text" class="form-control" id="ticketUserName" name="user_name" required maxlength="255" value="{{ auth()->user()->name ?? '' }}">
            </div>

            <!-- Preview da Análise IA -->
            <div id="aiAnalysisPreview" class="mt-4" style="display:none;">
              <div class="card border-primary">
                <div class="card-header bg-light">
                  <h6 class="mb-0 text-primary">
                    <i class="bi bi-cpu me-2"></i>Análise Inteligente da IA
                  </h6>
                </div>
                <div class="card-body">
                  <div class="row">
                    <div class="col-md-4">
                      <small class="text-muted d-block">Categoria Sugerida:</small>
                      <span id="aiSuggestedCategory" class="badge bg-info fs-6"></span>
                    </div>
                    <div class="col-md-4">
                      <small class="text-muted d-block">Prioridade:</small>
                      <span id="aiSuggestedPriority" class="badge bg-warning fs-6"></span>
                    </div>
                    <div class="col-md-4">
                      <small class="text-muted d-block">Confiança:</small>
                      <div class="progress mt-1" style="height: 20px;">
                        <div id="aiConfidenceBar" class="progress-bar bg-success" role="progressbar" style="font-size: 12px;"></div>
                      </div>
                    </div>
                  </div>
                  <div class="mt-3">
                    <small class="text-muted d-block">Sugestões da IA:</small>
                    <div id="aiSuggestions" class="text-dark"></div>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
            <i class="bi bi-x-circle me-1"></i>Cancelar
          </button>
          <button type="button" class="btn btn-info" id="analyzeWithAiBtn">
            <i class="bi bi-search me-1"></i>Analisar com IA
          </button>
          <button type="button" class="btn btn-success" id="createTicketBtn">
            <i class="bi bi-plus-circle me-1"></i>Criar Chamado
          </button>
        </div>
      </div>

      <!-- Estado Loading: Criando Chamado -->
      <div id="aiTicketLoadingState" style="display:none;">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title">
            <i class="bi bi-hourglass-split me-2"></i>Processando Chamado
          </h5>
        </div>
        <div class="modal-body text-center py-5">
          <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
            <span class="visually-hidden">Criando chamado...</span>
          </div>
          <h5 class="text-primary">A IA está analisando e criando seu chamado...</h5>
          <p class="text-muted">
            <i class="bi bi-cpu me-1"></i>Classificando automaticamente<br>
            <i class="bi bi-tags me-1"></i>Definindo prioridade<br>
            <i class="bi bi-check2-square me-1"></i>Salvando no sistema
          </p>
          <div class="progress mt-3">
            <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 75%"></div>
          </div>
        </div>
      </div>

      <!-- Estado Success: Chamado Criado -->
      <div id="aiTicketSuccessState" style="display:none;">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title">
            <i class="bi bi-check-circle-fill me-2"></i>Chamado Criado com Sucesso!
          </h5>
        </div>
        <div class="modal-body text-center py-4">
          <div class="text-success mb-4">
            <i class="bi bi-check-circle-fill" style="font-size: 4rem;"></i>
          </div>
          <div class="card">
            <div class="card-body">
              <h5 class="card-title text-success">Chamado Registrado</h5>
              <div class="row text-start">
                <div class="col-md-6">
                  <strong>Número:</strong> <span id="successTicketNumber" class="text-primary">#0000</span><br>
                  <strong>Título:</strong> <span id="successTicketTitle"></span><br>
                  <strong>Local:</strong> <span id="successTicketLocal"></span>
                </div>
                <div class="col-md-6">
                  <strong>Prioridade:</strong> <span id="successTicketPriority" class="badge bg-warning"></span><br>
                  <strong>Status:</strong> <span class="badge bg-info">Aberto</span><br>
                  <strong>Criado em:</strong> <span id="successTicketDate"></span>
                </div>
              </div>
            </div>
          </div>
          <div class="alert alert-info mt-3">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Próximos passos:</strong> Nossa equipe técnica analisará seu chamado e entrará em contato em breve.
          </div>
        </div>
        <div class="modal-footer bg-light">
          <button type="button" class="btn btn-outline-primary" id="viewTicketBtn">
            <i class="bi bi-eye me-1"></i>Ver Chamado
          </button>
          <button type="button" class="btn btn-outline-secondary" id="createAnotherBtn">
            <i class="bi bi-plus me-1"></i>Criar Outro
          </button>
          <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
            <i class="bi bi-check me-1"></i>Concluir
          </button>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .ai-float-chat{position:fixed;bottom:20px;right:20px;z-index:1050;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif}
  .ai-chat-toggle{width:60px;height:60px;background:linear-gradient(135deg,#007bff,#0056b3);border:none;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;cursor:pointer;box-shadow:0 4px 20px rgba(0,123,255,.3);transition:all .3s ease;position:relative}
  .ai-chat-toggle:hover{transform:scale(1.08);box-shadow:0 6px 25px rgba(0,123,255,.4)}
  .ai-chat-toggle i{font-size:24px}
  .ai-notification-badge{position:absolute;top:-5px;right:-5px;width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;animation:pulse 2s infinite}
  .ai-chat-container{width:350px;height:500px;background:#fff;border-radius:20px;box-shadow:0 10px 30px rgba(0,0,0,.2);position:absolute;bottom:80px;right:0;display:flex;flex-direction:column;overflow:hidden;border:1px solid #e9ecef}
  .ai-chat-header{background:linear-gradient(135deg,#007bff,#0056b3);color:#fff;padding:15px;display:flex;align-items:center;justify-content:space-between}
  .ai-avatar{width:35px;height:35px;background:rgba(255,255,255,.2);border-radius:50%;display:flex;align-items:center;justify-content:center}
  .ai-avatar-small{width:28px;height:28px;background:#007bff;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;flex-shrink:0}
  .ai-chat-messages{flex:1;padding:15px;overflow-y:auto;background:#f8f9fa;background-image:radial-gradient(circle at 25% 25%,#007bff08 0,transparent 50%),radial-gradient(circle at 75% 75%,#007bff08 0,transparent 50%)}
  .ai-message,.user-message{display:flex;margin-bottom:15px;animation:slideIn .3s ease}
  .user-message{flex-direction:row-reverse}
  .user-message .ai-avatar-small{background:#28a745;margin-left:10px;margin-right:0}
  .ai-message .ai-avatar-small{margin-right:10px}
  .message-bubble{max-width:70%;padding:12px 16px;border-radius:18px;position:relative;word-wrap:break-word}
  .ai-bubble{background:#fff;border:1px solid #e9ecef;color:#333;margin-left:5px}
  .user-bubble{background:linear-gradient(135deg,#007bff,#0056b3);color:#fff;margin-right:5px}
  .message-bubble p{margin:0 0 5px 0}
  .message-bubble p:last-of-type{margin-bottom:8px}
  .message-time{font-size:10px;opacity:.7;text-align:right}
  .ai-quick-suggestions{padding:10px 15px;border-top:1px solid #e9ecef;background:#fff}
  .suggestion-buttons{display:flex;flex-wrap:wrap;gap:5px}
  .suggestion-btn{font-size:11px;padding:4px 8px;border-radius:12px;white-space:nowrap}
  .ai-chat-input{padding:15px;background:#fff;border-top:1px solid #e9ecef}
  .ai-chat-input .form-control{border-radius:25px;border:1px solid #dee2e6;padding:10px 15px}
  .ai-chat-input .btn{border-radius:25px;padding:10px 15px}
  .typing-indicator{display:flex;align-items:center;margin-bottom:15px}
  .typing-dots{display:flex;margin-left:10px}
  .typing-dots span{width:8px;height:8px;background:#007bff;border-radius:50%;margin:0 2px;animation:typing 1.4s infinite ease-in-out}
  .typing-dots span:nth-child(1){animation-delay:-.32s}
  .typing-dots span:nth-child(2){animation-delay:-.16s}
  @keyframes typing{0%,80%,100%{transform:scale(0);opacity:.5}40%{transform:scale(1);opacity:1}}
  @keyframes slideIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
  @keyframes pulse{0%{transform:scale(1)}50%{transform:scale(1.1)}100%{transform:scale(1)}}
  .ai-suggestions{border-top:1px solid rgba(255,255,255,.1);padding-top:8px;margin-top:8px}
  .ai-suggestions .btn{margin:2px;font-size:.8rem}
  .bg-gradient-primary{background:linear-gradient(135deg,#007bff,#0056b3)!important}
  @media (max-width:768px){.ai-chat-container{width:300px;height:450px;bottom:70px;right:-10px}.ai-float-chat{bottom:15px;right:15px}}
  .ai-chat-messages::-webkit-scrollbar{width:6px}
  .ai-chat-messages::-webkit-scrollbar-track{background:#f1f1f1;border-radius:10px}
  .ai-chat-messages::-webkit-scrollbar-thumb{background:#c1c1c1;border-radius:10px}
  .ai-chat-messages::-webkit-scrollbar-thumb:hover{background:#a8a8a8}
</style>

<script>
(function(){
  // ====== Configurações ======
  const API_ENDPOINT = '/api/ai/chatbot';
  const CREATE_TICKET_ENDPOINT = '/api/ai/create-ticket';
  const CLASSIFY_ENDPOINT = '/api/ai/classify';
  const MAX_MESSAGE_LEN = 500;
  const STORAGE_KEY = 'aiFloatChat.history.v2';

  // ====== Elementos ======
  const aiFloatChat = document.getElementById('aiFloatChat');
  const aiChatToggle = document.getElementById('aiChatToggle');
  const aiChatContainer = document.getElementById('aiChatContainer');
  const aiChatClose = document.getElementById('aiChatClose');
  const aiChatMessages = document.getElementById('aiChatMessages');
  const aiFloatChatInput = document.getElementById('aiFloatChatInput');
  const aiFloatForm = document.getElementById('aiFloatForm');
  const aiQuickSuggestions = document.getElementById('aiQuickSuggestions');
  const aiNotificationBadge = document.getElementById('aiNotificationBadge');

  // ====== Estado ======
  let isOpen = false;
  let sentFirstMessage = false;
  let unreadCount = 0;
  let aiTicketModalInstance = null;

  // ====== Funções Utilitárias ======
  function nowTime(){
    const d = new Date();
    return d.toLocaleTimeString('pt-BR',{hour:'2-digit',minute:'2-digit'});
  }
  
  function clampText(text){
    if(!text) return '';
    text = String(text).slice(0, MAX_MESSAGE_LEN);
    return text;
  }
  
  function getCsrf(){
    const m = document.querySelector('meta[name="csrf-token"]');
    return m ? m.getAttribute('content') : undefined;
  }

  function generateSmartTitle(message) {
    const patterns = [
      {regex: /(não liga|não funciona|não inicia)/i, title: 'Equipamento não funciona'},
      {regex: /(internet|rede|wifi|conexão)/i, title: 'Problema de internet/rede'},
      {regex: /(sistema|programa|software)/i, title: 'Problema no sistema'},
      {regex: /(impressora|imprimir)/i, title: 'Problema na impressora'},
      {regex: /(lento|travando|trava)/i, title: 'Computador lento ou travando'},
      {regex: /(senha|login|acesso)/i, title: 'Problema de acesso/login'},
      {regex: /(email|e-mail|outlook)/i, title: 'Problema no email'},
      {regex: /(backup|arquivos|dados)/i, title: 'Problema com arquivos/backup'}
    ];
    
    for(const pattern of patterns) {
      if(pattern.regex.test(message)) {
        return pattern.title;
      }
    }
    
    return message.substring(0, 50).trim() + (message.length > 50 ? '...' : '');
  }

  function getPriorityColor(priority) {
    switch(priority) {
      case 'high': return 'danger';
      case 'medium': return 'warning';
      case 'low': return 'success';
      default: return 'secondary';
    }
  }

  function getPriorityText(priority) {
    switch(priority) {
      case 'high': return 'Alta';
      case 'medium': return 'Média';
      case 'low': return 'Baixa';
      default: return 'Normal';
    }
  }

  // ====== Chat Básico ======
  aiChatToggle.addEventListener('click', toggleChat);
  aiChatClose.addEventListener('click', closeChat);

  function toggleChat(){ isOpen ? closeChat() : openChat(); }
  
  function openChat(){
    aiChatContainer.style.display='flex';
    isOpen = true;
    aiNotificationBadge.style.display='none';
    unreadCount=0;
    updateBadge();
    setTimeout(()=>aiFloatChatInput.focus(),0);
  }
  
  function closeChat(){
    aiChatContainer.style.display='none';
    isOpen = false;
  }

  function updateBadge(){
    if(unreadCount>0){
      aiNotificationBadge.style.display='flex';
      aiNotificationBadge.textContent = String(unreadCount);
    }else{
      aiNotificationBadge.style.display='none';
      aiNotificationBadge.textContent = '0';
    }
  }

  // ====== Mensagens ======
  aiFloatForm.addEventListener('submit', (e)=>{
    e.preventDefault();
    sendMessage();
  });

  function sendMessage(){
    const message = clampText(aiFloatChatInput.value.trim());
    if(!message) return;

    addMessage({text: message, sender:'user'});
    aiFloatChatInput.value='';

    if(!sentFirstMessage){
      aiQuickSuggestions.style.display='none';
      sentFirstMessage = true;
    }

    const hideTyping = showTyping();

    fetch(API_ENDPOINT, {
      method:'POST',
      headers:{
        'Content-Type':'application/json',
        ...(getCsrf()? {'X-CSRF-TOKEN':getCsrf()} : {})
      },
      body: JSON.stringify({message})
    })
    .then(async (response)=>{
      const data = await response.json().catch(()=>({response:'Desculpe, resposta inválida do servidor.'}));
      hideTyping();
      handleAIResponse(data);
    })
    .catch((error)=>{
      console.error('Erro na IA:', error);
      hideTyping();
      addMessage({text:'❌ Desculpe, ocorreu um erro. Tente novamente ou use o formulário manual.', sender:'ai'});
    });
  }

  function handleAIResponse(data){
    const aiText = (typeof data?.response === 'string') ? data.response : JSON.stringify(data?.response || '');
    addMessage({text: aiText, sender:'ai'});

    // Verificar se precisa criar chamado
    if(data?.needs_ticket_info || (data?.action === 'create_ticket')) {
      setTimeout(() => {
        addTicketCreationSuggestion(data);
      }, 500);
    }

    // Outras sugestões
    if(Array.isArray(data?.suggestions) && data.suggestions.length){
      setTimeout(()=> addActionSuggestions(data.suggestions), 800);
    }

    if(!isOpen){
      unreadCount += 1;
      updateBadge();
    }
  }

  function addTicketCreationSuggestion(data) {
    const suggestionDiv = document.createElement('div');
    suggestionDiv.className = 'ai-suggestions mt-3 p-3 bg-primary text-white rounded';
    suggestionDiv.innerHTML = `
      <div class="d-flex align-items-center justify-content-between">
        <div>
          <h6 class="mb-1"><i class="bi bi-lightbulb me-2"></i>Precisa de Ajuda Técnica?</h6>
          <small>Vou ajudar você a criar um chamado de forma inteligente</small>
        </div>
        <button type="button" class="btn btn-light btn-sm" onclick="openSmartTicketModal()">
          <i class="bi bi-plus-circle me-1"></i>Criar Chamado
        </button>
      </div>
    `;

    const lastMessage = aiChatMessages.querySelector('.ai-message:last-of-type .message-bubble');
    if(lastMessage){ lastMessage.appendChild(suggestionDiv); }
  }

  function addActionSuggestions(suggestions){
    if(!suggestions.length) return;

    const suggestionDiv = document.createElement('div');
    suggestionDiv.className = 'ai-suggestions mt-2';
    const wrap = document.createElement('div');
    wrap.className = 'd-flex gap-1 flex-wrap';

    suggestions.forEach(s =>{
      let btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'btn btn-outline-secondary btn-sm';
      btn.textContent = s.text || 'Ação';

      if(s.action === 'create_ticket'){
        btn.className = 'btn btn-primary btn-sm';
        btn.innerHTML = '<i class="bi bi-plus-circle me-1"></i>' + (s.text || 'Criar Chamado');
        btn.addEventListener('click', ()=>{ openSmartTicketModal(); });
      } else if(s.action === 'redirect_contact'){
        btn.className = 'btn btn-info btn-sm';
        btn.innerHTML = '<i class="bi bi-envelope me-1"></i>' + (s.text || 'Contato');
        btn.addEventListener('click', ()=>{ window.location.href = '/fale-conosco'; });
      } else {
        btn.addEventListener('click', ()=>{
          aiFloatChatInput.value = s.text || '';
          aiFloatChatInput.focus();
        });
      }
      wrap.appendChild(btn);
    });

    suggestionDiv.appendChild(wrap);
    const lastMessage = aiChatMessages.querySelector('.ai-message:last-of-type .message-bubble');
    if(lastMessage){ lastMessage.appendChild(suggestionDiv); }
  }

  function showTyping(){
    const typingDiv = document.createElement('div');
    typingDiv.id = 'float-typing-indicator';
    typingDiv.className = 'typing-indicator';
    typingDiv.innerHTML = `
      <div class="ai-avatar-small">
        <i class="bi bi-robot"></i>
      </div>
      <div class="typing-dots">
        <span></span><span></span><span></span>
      </div>`;
    aiChatMessages.appendChild(typingDiv);
    aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
    return function hide(){ 
      const el = document.getElementById('float-typing-indicator'); 
      if(el) el.remove(); 
    };
  }

  function addMessage({text, sender, isTrustedHTML=false}){
    const messageDiv = document.createElement('div');
    messageDiv.className = `${sender==='user'?'user-message':'ai-message'}`;

    const avatar = document.createElement('div');
    avatar.className = 'ai-avatar-small';
    avatar.innerHTML = `<i class="bi bi-${sender==='user' ? 'person' : 'robot'}"></i>`;

    const bubble = document.createElement('div');
    bubble.className = `message-bubble ${sender==='user'?'user-bubble':'ai-bubble'}`;

    if(isTrustedHTML){
      bubble.innerHTML = text;
    } else {
      const p = document.createElement('p');
      p.textContent = text;
      bubble.appendChild(p);
    }

    const time = document.createElement('div');
    time.className = 'message-time';
    time.textContent = nowTime();
    bubble.appendChild(time);

    messageDiv.appendChild(avatar);
    messageDiv.appendChild(bubble);
    aiChatMessages.appendChild(messageDiv);
    aiChatMessages.scrollTop = aiChatMessages.scrollHeight;
  }

  // ====== Modal Inteligente ======
  window.openSmartTicketModal = function() {
    if(!aiTicketModalInstance) {
      aiTicketModalInstance = new bootstrap.Modal(document.getElementById('aiTicketModal'));
    }
    
    // Reset states
    showFormState();
    
    // Pre-preencher com contexto da conversa
    const lastUserMessage = getLastUserMessage();
    if (lastUserMessage) {
      const title = generateSmartTitle(lastUserMessage);
      document.getElementById('ticketTitle').value = title;
      document.getElementById('ticketDescription').value = lastUserMessage;
    }
    
    aiTicketModalInstance.show();
  };

  function getLastUserMessage() {
    const userMessages = aiChatMessages.querySelectorAll('.user-message .message-bubble p');
    return userMessages.length > 0 ? userMessages[userMessages.length - 1].textContent : '';
  }

  function showFormState() {
    document.getElementById('aiTicketFormState').style.display = 'block';
    document.getElementById('aiTicketLoadingState').style.display = 'none';
    document.getElementById('aiTicketSuccessState').style.display = 'none';
  }

  function showLoadingState() {
    document.getElementById('aiTicketFormState').style.display = 'none';
    document.getElementById('aiTicketLoadingState').style.display = 'block';
    document.getElementById('aiTicketSuccessState').style.display = 'none';
  }

  function showSuccessState(ticketData) {
    document.getElementById('aiTicketFormState').style.display = 'none';
    document.getElementById('aiTicketLoadingState').style.display = 'none';
    document.getElementById('aiTicketSuccessState').style.display = 'block';
    
    // Preencher dados do sucesso
    document.getElementById('successTicketNumber').textContent = `#${ticketData.id}`;
    document.getElementById('successTicketTitle').textContent = ticketData.title;
    document.getElementById('successTicketLocal').textContent = ticketData.local;
    document.getElementById('successTicketPriority').textContent = getPriorityText(ticketData.priority);
    document.getElementById('successTicketPriority').className = `badge bg-${getPriorityColor(ticketData.priority)}`;
    document.getElementById('successTicketDate').textContent = new Date().toLocaleString('pt-BR');
  }

  // ====== Event Listeners do Modal ======
  document.getElementById('analyzeWithAiBtn')?.addEventListener('click', function() {
    const title = document.getElementById('ticketTitle').value.trim();
    const description = document.getElementById('ticketDescription').value.trim();
    
    if (!title || !description) {
      alert('⚠️ Preencha pelo menos o título e descrição para análise');
      return;
    }
    
    const button = this;
    const originalText = button.innerHTML;
    button.disabled = true;
    button.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>Analisando...';
    
    fetch(CLASSIFY_ENDPOINT, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrf()
      },
      body: JSON.stringify({ title, description })
    })
    .then(response => response.json())
    .then(data => {
      // Mostrar preview da análise
      document.getElementById('aiAnalysisPreview').style.display = 'block';
      document.getElementById('aiSuggestedCategory').textContent = data.category || 'Geral';
      document.getElementById('aiSuggestedPriority').textContent = getPriorityText(data.priority);
      document.getElementById('aiSuggestedPriority').className = `badge bg-${getPriorityColor(data.priority)} fs-6`;
      
      const confidence = Math.round((data.confidence || 0) * 100);
      document.getElementById('aiConfidenceBar').style.width = `${confidence}%`;
      document.getElementById('aiConfidenceBar').textContent = `${confidence}%`;
      
      document.getElementById('aiSuggestions').innerHTML = data.suggestion || 'Chamado será processado normalmente.';
    })
    .catch(error => {
      console.error('Erro na análise:', error);
      alert('❌ Erro ao analisar. Você pode criar o chamado normalmente.');
    })
    .finally(() => {
      button.disabled = false;
      button.innerHTML = originalText;
    });
  });

  document.getElementById('createTicketBtn')?.addEventListener('click', function() {
    const title = document.getElementById('ticketTitle').value.trim();
    const description = document.getElementById('ticketDescription').value.trim();
    const local = document.getElementById('ticketLocal').value.trim();
    const userName = document.getElementById('ticketUserName').value.trim();
    
    if (!title || !description || !local || !userName) {
      alert('⚠️ Preencha todos os campos obrigatórios');
      return;
    }
    
    showLoadingState();
    
    const originalMessage = getLastUserMessage();
    const requestData = {
      title, description, local, user_name: userName,
      original_message: originalMessage || null
    };
    
    fetch(CREATE_TICKET_ENDPOINT, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': getCsrf()
      },
      body: JSON.stringify(requestData)
    })
    .then(async response => {
      const data = await response.json();
      if (!response.ok) {
        throw new Error(data.error || 'Erro ao criar chamado');
      }
      return data;
    })
    .then(data => {
      if (data.success) {
        showSuccessState(data.ticket);
        
        // Adicionar mensagem no chat
        const successMessage = `✅ **Chamado criado com sucesso!**\n\n` +
                             `🎫 **Número:** #${data.ticket.id}\n` +
                             `📋 **Título:** ${data.ticket.title}\n` +
                             `📍 **Local:** ${data.ticket.local}\n` +
                             `⚡ **Prioridade:** ${getPriorityText(data.ticket.priority)}`;
        
        addMessage({text: successMessage, sender: 'ai'});
        
        if (!isOpen) openChat();
        
      } else {
        throw new Error(data.error || 'Erro desconhecido');
      }
    })
    .catch(error => {
      console.error('Erro ao criar chamado:', error);
      showFormState();
      alert(`❌ Erro ao criar chamado: ${error.message}`);
    });
  });

  document.getElementById('createAnotherBtn')?.addEventListener('click', function() {
    document.getElementById('createTicketForm').reset();
    document.getElementById('aiAnalysisPreview').style.display = 'none';
    showFormState();
  });

  document.getElementById('viewTicketBtn')?.addEventListener('click', function() {
    window.location.href = '/tickets';
  });

  // Sugestões rápidas
  document.addEventListener('click', function(e) {
    if (e.target.classList.contains('suggestion-btn')) {
      const message = e.target.getAttribute('data-message');
      if (message) {
        aiFloatChatInput.value = message;
        sendMessage();
      }
    }
  });

  // Fechar chat ao clicar fora
  document.addEventListener('click', function(e) {
    if (isOpen && !aiFloatChat.contains(e.target)) {
      closeChat();
    }
  });

  aiChatContainer.addEventListener('click', function(e) {
    e.stopPropagation();
  });

  // Esc para fechar
  document.addEventListener('keydown', (e)=>{
    if(e.key==='Escape' && isOpen) closeChat();
  });

})();
</script>
