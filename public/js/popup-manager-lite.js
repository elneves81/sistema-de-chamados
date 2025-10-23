/**
 * Helper LEVE para gerenciar pop-ups e evitar bloqueio pelos navegadores
 * Versão otimizada para performance
 */
class PopupManager {
    constructor() {
        this.popupsBlocked = null;
        this.lastCheck = 0;
        this.checking = false;
        this.init();
    }

    init() {
        // Detectar apenas quando necessário
        this.checkPopupStatus();
    }

    /**
     * Detecta se pop-ups estão bloqueados (otimizado)
     */
    checkPopupStatus() {
        if (this.checking || (Date.now() - this.lastCheck) < 10000) {
            return this.popupsBlocked;
        }
        
        this.checking = true;
        this.lastCheck = Date.now();
        
        try {
            const popup = window.open('', 'test', 'width=1,height=1,left=99999,top=99999');
            if (popup && !popup.closed) {
                popup.close();
                this.popupsBlocked = false;
            } else {
                this.popupsBlocked = true;
            }
        } catch (e) {
            this.popupsBlocked = true;
        }
        
        this.checking = false;
        return this.popupsBlocked;
    }

    /**
     * Abre URL com fallback simples
     */
    openUrl(url, target = '_blank') {
        try {
            const popup = window.open(url, target);
            
            if (!popup || popup.closed || typeof popup.closed === 'undefined') {
                this.handleBlocked(url);
                return null;
            }
            
            return popup;
        } catch (e) {
            this.handleBlocked(url);
            return null;
        }
    }

    /**
     * Gerencia pop-up bloqueado (versão simples)
     */
    handleBlocked(url) {
        this.popupsBlocked = true;
        
        // Mostrar confirmação simples
        if (confirm('Pop-up bloqueado. Abrir na mesma aba?')) {
            window.open(url, '_self');
        }
    }

    /**
     * Abre ticket (método específico)
     */
    openTicket(ticketId) {
        return this.openUrl(`/tickets/${ticketId}`);
    }

    /**
     * Abre relatório com download
     */
    openReport(url) {
        // Tentar download primeiro
        const link = document.createElement('a');
        link.href = url;
        link.download = '';
        link.style.display = 'none';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        // Backup com pop-up
        setTimeout(() => this.openUrl(url), 1000);
    }

    /**
     * Status simples
     */
    getStats() {
        return {
            popupsBlocked: this.checkPopupStatus(),
            lastCheck: new Date(this.lastCheck).toLocaleTimeString()
        };
    }
}

// Inicializar versão leve
if (window.popupManager) {
    // Limpar instância anterior se existir
    window.popupManager = null;
}

window.popupManager = new PopupManager();

// Funções globais simples
window.openTicket = (ticketId) => window.popupManager.openTicket(ticketId);
window.openReport = (url) => window.popupManager.openReport(url);
window.safeWindowOpen = (url, target) => window.popupManager.openUrl(url, target);

// Log de inicialização
console.log('PopupManager Lite iniciado:', window.popupManager.getStats());
