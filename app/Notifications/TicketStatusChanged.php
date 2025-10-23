<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;
    public $oldStatus;
    public $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Ticket $ticket, $oldStatus = null, $newStatus = null)
    {
        $this->ticket = $ticket;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus ?? $ticket->status;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $statusLabels = [
            'open' => 'Aberto',
            'in_progress' => 'Em Andamento', 
            'waiting' => 'Aguardando',
            'resolved' => 'Resolvido',
            'closed' => 'Fechado'
        ];

        $subject = "Chamado #{$this->ticket->id} - Status Atualizado";
        
        return (new MailMessage)
            ->subject($subject)
            ->greeting("Olá {$notifiable->name}!")
            ->line("Seu chamado foi atualizado:")
            ->line("**Título:** {$this->ticket->title}")
            ->line("**Status Anterior:** " . ($statusLabels[$this->oldStatus] ?? $this->oldStatus))
            ->line("**Novo Status:** " . ($statusLabels[$this->newStatus] ?? $this->newStatus))
            ->when($this->ticket->assignedUser, function($message) {
                return $message->line("**Técnico Responsável:** {$this->ticket->assignedUser->name}");
            })
            ->action('Ver Chamado', route('tickets.show', $this->ticket))
            ->line('Obrigado por usar nosso sistema de chamados!')
            ->salutation('Atenciosamente, Equipe de Suporte');
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Chamado #{$this->ticket->id} teve seu status alterado"
        ];
    }
}
