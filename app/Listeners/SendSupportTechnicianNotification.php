<?php

namespace App\Listeners;

use App\Events\SupportTechnicianAssigned;
use App\Services\NotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendSupportTechnicianNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     *
     * @param  \App\Events\SupportTechnicianAssigned  $event
     * @return void
     */
    public function handle(SupportTechnicianAssigned $event)
    {
        $ticket = $event->ticket;
        $supportTechnician = $event->supportTechnician;

        try {
            // Notificar o técnico de suporte
            $this->notificationService->send(
                user: $supportTechnician,
                event: 'ticket.support_assigned',
                data: [
                    'ticket_id' => $ticket->id,
                    'ticket_title' => $ticket->title,
                    'ticket_priority' => $ticket->priority,
                    'ticket_category' => $ticket->category->name ?? 'N/A',
                    'requester' => $ticket->user->name,
                    'main_technician' => $ticket->assignedUser->name ?? 'Não atribuído',
                    'url' => route('tickets.show', $ticket->id)
                ],
                title: 'Você foi adicionado como técnico de suporte',
                message: "Você foi designado para auxiliar no chamado #{$ticket->id}: {$ticket->title}"
            );

            Log::info("Notificação de técnico de suporte enviada", [
                'ticket_id' => $ticket->id,
                'support_technician_id' => $supportTechnician->id,
                'support_technician_name' => $supportTechnician->name
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao enviar notificação de técnico de suporte: " . $e->getMessage(), [
                'ticket_id' => $ticket->id,
                'support_technician_id' => $supportTechnician->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
