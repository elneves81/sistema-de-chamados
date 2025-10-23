// Monitor de Performance para Sistema de Chamados
class PerformanceMonitor {
    constructor() {
        this.metrics = {
            timers: 0,
            memoryUsage: 0,
            cpuUsage: 0,
            popupsBlocked: 0,
            lastUpdate: Date.now()
        };
        this.init();
    }
    
    init() {
        // Monitorar a cada 30 segundos
        setInterval(() => this.collectMetrics(), 30000);
        
        // Log inicial
        this.collectMetrics();
    }
    
    collectMetrics() {
        // Contar timers ativos
        if (window.timerManager) {
            this.metrics.timers = window.timerManager.timers.size;
        }
        
        // Memória (se disponível)
        if (performance.memory) {
            this.metrics.memoryUsage = Math.round(performance.memory.usedJSHeapSize / 1048576); // MB
        }
        
        // Status dos pop-ups
        if (window.popupManager) {
            this.metrics.popupsBlocked = window.popupManager.popupsBlocked ? 1 : 0;
        }
        
        this.metrics.lastUpdate = Date.now();
        
        // Log no console (apenas se em desenvolvimento)
        if (window.location.hostname === 'localhost' || window.location.hostname === '10.0.50.79') {
            console.log('📊 Performance Monitor:', this.metrics);
        }
    }
    
    getReport() {
        return {
            ...this.metrics,
            timestamp: new Date().toLocaleString()
        };
    }
    
    // Limpeza de emergência
    emergencyCleanup() {
        console.warn('🚨 Performance: Executando limpeza de emergência');
        
        // Limpar todos os timers
        if (window.timerManager) {
            window.timerManager.clearAll();
        }
        
        // Limpar intervalos nativos
        for (let i = 1; i < 99999; i++) {
            clearInterval(i);
            clearTimeout(i);
        }
        
        // Force garbage collection se disponível
        if (window.gc) {
            window.gc();
        }
        
        console.log('✅ Limpeza concluída');
    }
}

// Inicializar monitor apenas se não existir
if (!window.performanceMonitor) {
    window.performanceMonitor = new PerformanceMonitor();
}

// Função global para emergência
window.emergencyCleanup = () => window.performanceMonitor.emergencyCleanup();

// Tecla de atalho para limpeza (Ctrl+Alt+C)
document.addEventListener('keydown', (e) => {
    if (e.ctrlKey && e.altKey && e.key === 'c') {
        window.emergencyCleanup();
    }
});
