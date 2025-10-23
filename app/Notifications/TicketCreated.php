<?php

namespace App\Notifications;

use App\Models\Ticket;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketCreated extends Notification implements ShouldQueue
{
    use Queueable;

    public $ticket;

    public function __construct(Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $priorityLabels = [
            'low' => 'Baixa',
            'medium' => 'Média',
            'high' => 'Alta', 
            'urgent' => 'Urgente'
        ];

        return (new MailMessage)
            ->subject("Novo Chamado #{$this->ticket->id} Criado")
            ->greeting("Olá {$notifiable->name}!")
            ->line("Um novo chamado foi criado no sistema:")
            ->line("**Ticket #:** {$this->ticket->id}")
            ->line("**Título:** {$this->ticket->title}")
            ->line("**Categoria:** {$this->ticket->category->name}")
            ->line("**Prioridade:** " . ($priorityLabels[$this->ticket->priority] ?? $this->ticket->priority))
            ->line("**Solicitante:** {$this->ticket->user->name}")
            ->action('Ver Chamado', route('tickets.show', $this->ticket))
            ->line('Este chamado aguarda sua atenção.')
            ->salutation('Atenciosamente, Sistema de Chamados');
    }

    public function toArray($notifiable)
    {
        return [
            'ticket_id' => $this->ticket->id,
            'ticket_title' => $this->ticket->title,
            'priority' => $this->ticket->priority,
            'user_name' => $this->ticket->user->name,
            'message' => "Novo chamado #{$this->ticket->id} criado por {$this->ticket->user->name}"
        ];
    }
}
