// WebSocket para atualizações em tempo real
const socket = new WebSocket('ws://localhost:6001');

socket.onopen = function(event) {
    console.log('Conectado ao WebSocket');
    
    // Subscrever ao canal de tickets
    socket.send(JSON.stringify({
        event: 'pusher:subscribe',
        data: {
            channel: 'tickets'
        }
    }));
};

socket.onmessage = function(event) {
    const data = JSON.parse(event.data);
    
    if (data.event === 'ticket.created') {
        // Adicionar novo ticket ao painel
        addNewTicketToBoard(data.data);
        playNotificationSound();
    }
    
    if (data.event === 'ticket.updated') {
        // Atualizar ticket existente
        updateTicketInBoard(data.data);
    }
};

function addNewTicketToBoard(ticket) {
    const columnId = ticket.status.replace('_', '-') + '-tickets';
    const column = document.getElementById(columnId);
    
    if (column) {
        const ticketCard = createTicketCard(ticket);
        ticketCard.classList.add('new-ticket');
        column.insertAdjacentHTML('afterbegin', ticketCard);
        
        // Remover animação após 3 segundos
        setTimeout(() => {
            const newCard = column.querySelector('.new-ticket');
            if (newCard) {
                newCard.classList.remove('new-ticket');
            }
        }, 3000);
    }
}
