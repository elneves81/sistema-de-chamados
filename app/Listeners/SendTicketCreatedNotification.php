<?php

namespace App\Listeners;

use App\Events\TicketCreated;
use App\Services\NotificationService;

class SendTicketCreatedNotification
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(TicketCreated $event)
    {
        $ticket = $event->ticket;
        
        // Notificar o criador do ticket
        if ($ticket->user) {
            $data = [
                'ticket_id' => $ticket->id,
                'title' => $ticket->title,
                'description' => $ticket->description,
                'priority' => $ticket->priority,
                'status' => $this->translateStatus($ticket->status),
                'url' => route('tickets.show', $ticket),
                'user_name' => $ticket->user->name,
            ];

            $this->notificationService->notify(
                $ticket->user,
                'ticket.created',
                $data
            );
        }

        // Notificar admins/técnicos sobre novo ticket urgente
        if ($ticket->priority === 'urgent') {
            $admins = \App\Models\User::where('role', 'admin')
                ->orWhere('role', 'technician')
                ->get();

            foreach ($admins as $admin) {
                $data = [
                    'ticket_id' => $ticket->id,
                    'title' => $ticket->title,
                    'priority' => $ticket->priority,
                    'status' => $this->translateStatus($ticket->status),
                    'url' => route('tickets.show', $ticket),
                    'user_name' => $ticket->user->name ?? 'N/A',
                ];

                $this->notificationService->notify(
                    $admin,
                    'ticket.created',
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
