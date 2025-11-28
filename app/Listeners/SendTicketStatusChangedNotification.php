<?php

namespace App\Listeners;

use App\Events\TicketStatusChanged;
use App\Services\NotificationService;

class SendTicketStatusChangedNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(TicketStatusChanged $event)
    {
        $ticket = $event->ticket;
        $oldStatus = $event->oldStatus;
        $newStatus = $event->newStatus;

        $data = [
            'ticket_id' => $ticket->id,
            'title' => $ticket->title,
            'priority' => $ticket->priority,
            'old_status' => $this->translateStatus($oldStatus),
            'new_status' => $this->translateStatus($newStatus),
            'url' => route('tickets.show', $ticket),
            'user_name' => $ticket->user->name ?? 'N/A',
        ];

        // Notificar o criador do ticket
        if ($ticket->user) {
            $this->notificationService->notify(
                $ticket->user,
                'ticket.status_changed',
                $data
            );
        }

        // Notificar o técnico atribuído
        if ($ticket->assigned_to && $ticket->assigned_to != $ticket->user_id) {
            $technician = \App\Models\User::find($ticket->assigned_to);
            if ($technician) {
                $this->notificationService->notify(
                    $technician,
                    'ticket.status_changed',
                    $data
                );
            }
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
