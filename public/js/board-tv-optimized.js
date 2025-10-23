// Script otimizado para board-tv - evita loops e múltiplos timers
document.addEventListener('DOMContentLoaded', function() {
    
    // Função otimizada para polling de tickets
    function startOptimizedPolling() {
        // Limpar timers existentes
        if (window.timerManager) {
            window.timerManager.clearAll();
            
            // Timer para relógio (1 segundo)
            window.timerManager.setInterval('clock', updateTime, 1000);
            
            // Timer para footer clock (3 segundos)
            window.timerManager.setInterval('footerClock', updateFooterClockBar, 3000);
            
            // Timer para live clock (1 segundo)
            window.timerManager.setInterval('liveClock', updateLiveClock, 1000);
            
            // Timer para verificar novos tickets (30 segundos)
            window.timerManager.setInterval('newTickets', checkForNewTickets, 30000);
            
            // Timer para refresh completo (5 minutos)
            window.timerManager.setInterval('fullRefresh', refreshBoard, 300000);
        }
    }
    
    // Substituir a função original
    window.startTicketPolling = startOptimizedPolling;
    
    // Iniciar automaticamente se não estiver rodando
    if (typeof refreshInterval === 'undefined') {
        startOptimizedPolling();
    }
    
    console.log('Board-TV otimizado carregado');
});

// Função de limpeza global
function stopAllPolling() {
    if (window.timerManager) {
        window.timerManager.clearAll();
        console.log('Todos os timers parados');
    }
}
