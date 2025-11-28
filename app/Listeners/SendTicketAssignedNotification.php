<?php

namespace App\Listeners;

use App\Events\TicketAssigned;
use App\Services\NotificationService;

class SendTicketAssignedNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(TicketAssigned $event)
    {
        $ticket = $event->ticket;
        $technician = $event->technician;

        if (!$technician) {
            return;
        }

        $data = [
            'ticket_id' => $ticket->id,
            'title' => $ticket->title,
            'description' => $ticket->description,
            'priority' => $ticket->priority,
            'status' => $this->translateStatus($ticket->status),
            'url' => route('tickets.show', $ticket),
            'technician_name' => $technician->name,
            'user_name' => $ticket->user->name ?? 'N/A',
        ];

        // Notificar o técnico atribuído
        $this->notificationService->notify(
            $technician,
            'ticket.assigned',
            $data
        );

        // Notificar o criador do ticket
        if ($ticket->user && $ticket->user->id !== $technician->id) {
            $this->notificationService->notify(
                $ticket->user,
                'ticket.assigned',
                $data
            );
        }
    }

    private function translateStatus($status)
    {
        $translations = [
            'open' => 'Aberto',
            'in_progress' => 'Em Andamento',
            'waiting' => 'Aguardando',
            'resolved' => 'Resolvido',
            'closed' => 'Fechado',
        ];

        return $translations[$status] ?? ucfirst($status);
    }
}
