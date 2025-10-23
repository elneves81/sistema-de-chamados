/**
 * Helper para gerenciar pop-ups e evitar bloqueio pelos navegadores
 */
class PopupManager {
    constructor() {
        this.preOpenedWindows = new Map();
        this.popupQueue = [];
        this.isProcessingQueue = false;
        this.statusIndicator = null;
        this.activeNotifications = new Set(); // Para evitar notificações duplicadas
        this.lastAttempts = new Map(); // Para controlar frequência de tentativas
        this.init();
    }

    init() {
        // Detectar se pop-ups estão sendo bloqueados
        this.detectPopupBlocking();
        
        // Pre-abrir janelas em eventos de usuário quando possível
        this.setupPreOpenedWindows();
        
        // Criar indicador de status
        this.createStatusIndicator();
        
        // Solicitar permissões de notificação
        this.requestNotificationPermission();
        
        // Setup de event listeners
        this.setupEventListeners();
    }

    /**
     * Detecta se o navegador está bloqueando pop-ups
     */
    detectPopupBlocking() {
        try {
            const testPopup = window.open('', 'test', 'width=1,height=1,left=99999,top=99999');
            if (testPopup && !testPopup.closed) {
                testPopup.close();
                this.popupsBlocked = false;
            } else {
                this.popupsBlocked = true;
            }
        } catch (e) {
            this.popupsBlocked = true;
        }
        
        this.updateStatusIndicator();
    }

    /**
     * Cria indicador visual de status dos pop-ups
     */
    createStatusIndicator() {
        this.statusIndicator = document.createElement('div');
        this.statusIndicator.className = 'popup-status-indicator';
        this.statusIndicator.style.display = 'none'; // Inicialmente oculto
        document.body.appendChild(this.statusIndicator);
    }

    /**
     * Atualiza o indicador de status
     */
    updateStatusIndicator() {
        if (!this.statusIndicator) return;
        
        if (this.popupsBlocked) {
            this.statusIndicator.className = 'popup-status-indicator blocked';
            this.statusIndicator.textContent = 'Pop-ups Bloqueados';
            this.statusIndicator.title = 'Clique no ícone de bloqueio na barra de endereços para permitir pop-ups';
        } else {
            this.statusIndicator.className = 'popup-status-indicator allowed';
            this.statusIndicator.textContent = 'Pop-ups Permitidos';
            this.statusIndicator.title = 'Pop-ups estão funcionando normalmente';
        }
    }

    /**
     * Mostra/oculta o indicador de status
     */
    showStatusIndicator(show = true) {
        if (this.statusIndicator) {
            this.statusIndicator.style.display = show ? 'block' : 'none';
        }
    }

    /**
     * Abre uma URL tratando bloqueio de pop-ups
     */
    openUrl(url, target = '_blank', features = '') {
        try {
            // Tentar abrir normalmente primeiro
            const popup = window.open(url, target, features);
            
            if (!popup || popup.closed || typeof popup.closed == 'undefined') {
                // Pop-up foi bloqueado, usar alternativa
                this.handleBlockedPopup(url, target);
                return null;
            }
            
            // Pop-up aberto com sucesso
            this.showToast('Pop-up aberto com sucesso', 'success');
            return popup;
        } catch (e) {
            // Erro ao abrir, usar alternativa
            this.handleBlockedPopup(url, target);
            return null;
        }
    }

    /**
     * Gerencia quando pop-up é bloqueado
     */
    handleBlockedPopup(url, target) {
        this.popupsBlocked = true;
        this.updateStatusIndicator();
        this.showStatusIndicator(true);
        
        // Verificar se já tentou recentemente para evitar spam
        const now = Date.now();
        const lastAttempt = this.lastAttempts.get(url);
        if (lastAttempt && (now - lastAttempt) < 5000) { // 5 segundos
            return; // Não fazer nada se tentou muito recentemente
        }
        this.lastAttempts.set(url, now);
        
        // Mostrar notificação ao usuário (apenas se não existe uma ativa)
        if (!this.activeNotifications.has(url)) {
            this.showPopupBlockedNotification(url);
        }
        
        // Tentar alternativas
        if (target === '_blank') {
            // Adicionar à fila para tentar novamente (com limite)
            this.addToQueue(url, target);
        } else {
            // Abrir na mesma aba
            window.location.href = url;
        }
    }

    /**
     * Adiciona URL à fila para tentativa posterior
     */
    addToQueue(url, target) {
        // Verificar se já existe na fila para evitar duplicatas
        const exists = this.popupQueue.some(item => item.url === url);
        if (!exists) {
            this.popupQueue.push({ url, target, timestamp: Date.now(), attempts: 0 });
        }
        this.processQueue();
    }

    /**
     * Processa a fila de pop-ups
     */
    processQueue() {
        if (this.isProcessingQueue || this.popupQueue.length === 0) return;
        
        this.isProcessingQueue = true;
        
        const item = this.popupQueue.shift();
        const timeDiff = Date.now() - item.timestamp;
        
        // Se passou muito tempo ou muitas tentativas, descartar
        if (timeDiff > 30000 || item.attempts >= 3) { // 30 segundos ou 3 tentativas
            this.isProcessingQueue = false;
            this.processQueue();
            return;
        }
        
        // Incrementar tentativas
        item.attempts = (item.attempts || 0) + 1;
        
        // Tentar abrir novamente sem adicionar à fila (para evitar loop)
        setTimeout(() => {
            try {
                const popup = window.open(item.url, item.target);
                if (!popup || popup.closed || typeof popup.closed == 'undefined') {
                    // Ainda bloqueado, re-adicionar à fila se não excedeu tentativas
                    if (item.attempts < 3) {
                        this.popupQueue.push(item);
                    }
                } else {
                    this.showToast('Pop-up aberto com sucesso após retry', 'success');
                }
            } catch (e) {
                // Erro, re-adicionar à fila se não excedeu tentativas
                if (item.attempts < 3) {
                    this.popupQueue.push(item);
                }
            }
            
            this.isProcessingQueue = false;
            this.processQueue();
        }, 1000);
    }

    /**
     * Mostra notificação sobre pop-up bloqueado
     */
    showPopupBlockedNotification(url) {
        // Marcar como ativa para evitar duplicatas
        this.activeNotifications.add(url);
        
        // Criar notificação visual
        const notification = document.createElement('div');
        notification.className = 'popup-blocked-notification';
        notification.innerHTML = `
            <div class="alert alert-warning alert-dismissible fade show" role="alert" style="position: fixed; top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
                <strong><i class="bi bi-exclamation-triangle"></i> Pop-up Bloqueado!</strong><br>
                O navegador bloqueou a abertura de uma nova janela.
                <br><br>
                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                    <button type="button" class="btn btn-sm btn-primary me-md-2" onclick="window.popupManager.retryPopup('${url}')">
                        <i class="bi bi-arrow-clockwise"></i> Tentar Novamente
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" onclick="window.location.href='${url}'">
                        <i class="bi bi-box-arrow-up-right"></i> Abrir Aqui
                    </button>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Remover após 15 segundos e limpar do conjunto de ativas
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.activeNotifications.delete(url);
        }, 15000);
    }

    /**
     * Tenta novamente abrir um pop-up
     */
    retryPopup(url) {
        this.detectPopupBlocking();
        if (!this.popupsBlocked) {
            // Tentar diretamente sem usar openUrl para evitar loop
            try {
                const popup = window.open(url, '_blank');
                if (popup && !popup.closed) {
                    this.showToast('Pop-up aberto com sucesso!', 'success');
                } else {
                    this.showToast('Pop-up ainda está bloqueado. Tente permitir nas configurações do navegador.', 'warning');
                }
            } catch (e) {
                this.showToast('Erro ao abrir pop-up. Verifique as configurações do navegador.', 'error');
            }
        } else {
            this.showToast('Pop-ups ainda estão bloqueados. Verifique as configurações do navegador.', 'warning');
            this.showPopupInstructions();
        }
    }

    /**
     * Setup para pre-abrir janelas em eventos de usuário
     */
    setupPreOpenedWindows() {
        // Para downloads e exports, pre-abrir janela em cliques
        document.addEventListener('click', (e) => {
            const target = e.target.closest('[data-popup-url]');
            if (target) {
                e.preventDefault();
                const url = target.getAttribute('data-popup-url');
                this.openUrl(url);
            }
        });
    }

    /**
     * Setup de event listeners globais
     */
    setupEventListeners() {
        // Detectar mudanças no foco da janela para verificar pop-ups
        window.addEventListener('focus', () => {
            this.detectPopupBlocking();
        });
        
        // Verificar periodicamente o status dos pop-ups
        setInterval(() => {
            this.detectPopupBlocking();
        }, 10000); // A cada 10 segundos
        
        // Limpeza periódica para evitar acúmulo de dados
        setInterval(() => {
            this.cleanupOldAttempts();
        }, 60000); // A cada 1 minuto
    }

    /**
     * Limpa tentativas antigas para evitar acúmulo de memória
     */
    cleanupOldAttempts() {
        const now = Date.now();
        const maxAge = 5 * 60 * 1000; // 5 minutos
        
        // Limpar tentativas antigas
        for (const [url, timestamp] of this.lastAttempts.entries()) {
            if (now - timestamp > maxAge) {
                this.lastAttempts.delete(url);
            }
        }
        
        // Limpar fila de itens muito antigos
        this.popupQueue = this.popupQueue.filter(item => {
            return (now - item.timestamp) < maxAge;
        });
    }

    /**
     * Força download de arquivo (alternativa a pop-ups)
     */
    downloadFile(url, filename = null) {
        this.showToast('Iniciando download...', 'info');
        
        const link = document.createElement('a');
        link.href = url;
        if (filename) {
            link.download = filename;
        }
        link.target = '_blank';
        
        // Adicionar ao DOM temporariamente
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Mostrar indicador de loading
        this.showDownloadProgress();
    }

    /**
     * Mostra progresso de download
     */
    showDownloadProgress() {
        const progress = document.createElement('div');
        progress.className = 'download-loading';
        progress.style.position = 'fixed';
        progress.style.top = '50%';
        progress.style.left = '50%';
        progress.style.transform = 'translate(-50%, -50%)';
        progress.style.zIndex = '10000';
        
        document.body.appendChild(progress);
        
        setTimeout(() => {
            if (progress.parentNode) {
                progress.parentNode.removeChild(progress);
                this.showToast('Download iniciado com sucesso!', 'success');
            }
        }, 2000);
    }

    /**
     * Abre ticket em nova aba (específico para o sistema)
     */
    openTicket(ticketId) {
        const url = `/tickets/${ticketId}`;
        return this.openUrl(url, '_blank');
    }

    /**
     * Abre relatório/export
     */
    openReport(url) {
        // Tentar download direto primeiro
        this.downloadFile(url);
        
        // Se não funcionar, usar pop-up após um delay
        setTimeout(() => {
            this.openUrl(url, '_blank');
        }, 1000);
    }

    /**
     * Configurar permissões de notificação para melhor UX
     */
    requestNotificationPermission() {
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    this.showToast('Notificações ativadas!', 'success');
                }
            });
        }
    }

    /**
     * Mostrar notificação nativa quando disponível
     */
    showNativeNotification(title, message, url = null) {
        if ('Notification' in window && Notification.permission === 'granted') {
            const notification = new Notification(title, {
                body: message,
                icon: '/favicon.ico',
                badge: '/favicon.ico'
            });
            
            if (url) {
                notification.onclick = () => {
                    window.focus();
                    this.openUrl(url);
                    notification.close();
                };
            }
            
            // Auto-close após 5 segundos
            setTimeout(() => notification.close(), 5000);
        }
    }

    /**
     * Mostra toast notification
     */
    showToast(message, type = 'info', duration = 3000) {
        const toast = document.createElement('div');
        toast.className = `popup-toast ${type}`;
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            if (toast.parentNode) {
                toast.style.opacity = '0';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }
        }, duration);
    }

    /**
     * Instrução para permitir pop-ups
     */
    showPopupInstructions() {
        const userAgent = navigator.userAgent.toLowerCase();
        let instructions = '';
        
        if (userAgent.includes('chrome')) {
            instructions = 'No Chrome: Clique no ícone de bloqueio (🚫) na barra de endereços e selecione "Sempre permitir pop-ups"';
        } else if (userAgent.includes('firefox')) {
            instructions = 'No Firefox: Clique no ícone de escudo na barra de endereços e desative o bloqueio de pop-ups';
        } else if (userAgent.includes('safari')) {
            instructions = 'No Safari: Vá em Preferências > Sites > Pop-ups e permita para este site';
        } else if (userAgent.includes('edge')) {
            instructions = 'No Edge: Clique no ícone de bloqueio na barra de endereços e permita pop-ups';
        } else {
            instructions = 'Nas configurações do navegador, permita pop-ups para este site';
        }
        
        this.showToast(instructions, 'info', 8000);
    }

    /**
     * Limpa a fila de pop-ups
     */
    clearQueue() {
        this.popupQueue = [];
        this.isProcessingQueue = false;
        this.activeNotifications.clear();
        this.lastAttempts.clear();
    }

    /**
     * Obtém estatísticas do gerenciador
     */
    getStats() {
        return {
            popupsBlocked: this.popupsBlocked,
            queueLength: this.popupQueue.length,
            activeNotifications: this.activeNotifications.size,
            notificationPermission: 'Notification' in window ? Notification.permission : 'not-supported'
        };
    }
}

// Inicializar globalmente
window.popupManager = new PopupManager();

// Funções de conveniência globais
window.openTicket = (ticketId) => window.popupManager.openTicket(ticketId);
window.openReport = (url) => window.popupManager.openReport(url);
window.safeWindowOpen = (url, target, features) => window.popupManager.openUrl(url, target, features);

// Event listener para verificar mudanças de permissão
document.addEventListener('visibilitychange', () => {
    if (!document.hidden) {
        window.popupManager.detectPopupBlocking();
    }
});
