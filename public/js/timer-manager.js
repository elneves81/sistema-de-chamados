// Timer Manager para evitar múltiplos setInterval
class TimerManager {
    constructor() {
        this.timers = new Map();
    }
    
    setInterval(name, callback, interval) {
        // Limpar timer existente se houver
        this.clearInterval(name);
        
        // Criar novo timer
        const timerId = setInterval(callback, interval);
        this.timers.set(name, timerId);
        
        return timerId;
    }
    
    clearInterval(name) {
        if (this.timers.has(name)) {
            clearInterval(this.timers.get(name));
            this.timers.delete(name);
        }
    }
    
    clearAll() {
        this.timers.forEach((timerId) => clearInterval(timerId));
        this.timers.clear();
    }
}

// Instância global
window.timerManager = new TimerManager();

// Limpar todos os timers ao sair da página
window.addEventListener('beforeunload', () => {
    window.timerManager.clearAll();
});
