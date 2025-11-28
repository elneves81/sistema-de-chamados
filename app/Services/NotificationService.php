<?php

namespace App\Services;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Http;

class NotificationService
{
    /**
     * Envia notificação para um usuário através dos canais configurados
     */
    public function notify(User $user, string $type, array $data = [])
    {
        $preferences = $user->getNotificationPreferences();
        
        // Verificar se o tipo de notificação está habilitado
        if (!$this->shouldNotify($preferences, $type)) {
            return;
        }

        // Obter canais ativos para este tipo de notificação
        $channels = $this->getActiveChannels($preferences, $type);

        foreach ($channels as $channel) {
            try {
                switch ($channel) {
                    case 'email':
                        $this->sendEmail($user, $type, $data);
                        break;
                    case 'sms':
                        $this->sendSMS($user, $type, $data);
                        break;
                    case 'telegram':
                        $this->sendTelegram($user, $type, $data);
                        break;
                    case 'whatsapp':
                        $this->sendWhatsApp($user, $type, $data);
                        break;
                }
            } catch (\Exception $e) {
                Log::error("Erro ao enviar notificação via {$channel}", [
                    'user_id' => $user->id,
                    'type' => $type,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Verifica se deve notificar baseado nas preferências
     */
    private function shouldNotify($preferences, string $type): bool
    {
        if (!isset($preferences['events'][$type])) {
            return true; // Por padrão, envia se não estiver configurado
        }

        return $preferences['events'][$type]['enabled'] ?? true;
    }

    /**
     * Obtém canais ativos para um tipo de notificação
     */
    private function getActiveChannels($preferences, string $type): array
    {
        $defaultChannels = ['email']; // Email sempre por padrão
        
        if (!isset($preferences['events'][$type]['channels'])) {
            return $defaultChannels;
        }

        return $preferences['events'][$type]['channels'];
    }

    /**
     * Envia notificação por Email
     */
    private function sendEmail(User $user, string $type, array $data)
    {
        if (!$user->email) {
            return;
        }

        $template = $this->getEmailTemplate($type);
        $subject = $this->getEmailSubject($type, $data);
        $content = $this->renderTemplate($template, $data);

        Mail::send([], [], function ($message) use ($user, $subject, $content) {
            $message->to($user->email, $user->name)
                    ->subject($subject)
                    ->html($content);
        });

        Log::info("Email enviado para {$user->email}", ['type' => $type]);
    }

    /**
     * Envia notificação por SMS (Twilio)
     */
    private function sendSMS(User $user, string $type, array $data)
    {
        if (!$user->phone) {
            return;
        }

        $twilioSid = env('TWILIO_SID');
        $twilioToken = env('TWILIO_TOKEN');
        $twilioFrom = env('TWILIO_FROM');

        if (!$twilioSid || !$twilioToken) {
            Log::warning('Twilio não configurado');
            return;
        }

        $message = $this->getSMSMessage($type, $data);

        $response = Http::withBasicAuth($twilioSid, $twilioToken)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json", [
                'To' => $user->phone,
                'From' => $twilioFrom,
                'Body' => $message
            ]);

        if ($response->successful()) {
            Log::info("SMS enviado para {$user->phone}", ['type' => $type]);
        } else {
            Log::error("Erro ao enviar SMS", ['response' => $response->body()]);
        }
    }

    /**
     * Envia notificação via Telegram
     */
    private function sendTelegram(User $user, string $type, array $data)
    {
        if (!$user->telegram_id) {
            return;
        }

        $botToken = env('TELEGRAM_BOT_TOKEN');
        
        if (!$botToken) {
            Log::warning('Telegram bot não configurado');
            return;
        }

        $message = $this->getTelegramMessage($type, $data);

        $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
            'chat_id' => $user->telegram_id,
            'text' => $message,
            'parse_mode' => 'HTML'
        ]);

        if ($response->successful()) {
            Log::info("Telegram enviado para chat_id {$user->telegram_id}", ['type' => $type]);
        } else {
            Log::error("Erro ao enviar Telegram", ['response' => $response->body()]);
        }
    }

    /**
     * Envia notificação via WhatsApp (Twilio WhatsApp API)
     */
    private function sendWhatsApp(User $user, string $type, array $data)
    {
        if (!$user->whatsapp) {
            return;
        }

        $twilioSid = env('TWILIO_SID');
        $twilioToken = env('TWILIO_TOKEN');
        $twilioWhatsAppFrom = env('TWILIO_WHATSAPP_FROM', 'whatsapp:+14155238886');

        if (!$twilioSid || !$twilioToken) {
            Log::warning('Twilio WhatsApp não configurado');
            return;
        }

        $message = $this->getWhatsAppMessage($type, $data);
        $to = 'whatsapp:' . $user->whatsapp;

        $response = Http::withBasicAuth($twilioSid, $twilioToken)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$twilioSid}/Messages.json", [
                'To' => $to,
                'From' => $twilioWhatsAppFrom,
                'Body' => $message
            ]);

        if ($response->successful()) {
            Log::info("WhatsApp enviado para {$user->whatsapp}", ['type' => $type]);
        } else {
            Log::error("Erro ao enviar WhatsApp", ['response' => $response->body()]);
        }
    }

    /**
     * Templates de mensagens
     */
    private function getEmailTemplate(string $type): string
    {
        $templates = [
            'ticket.created' => 'emails.tickets.created',
            'ticket.assigned' => 'emails.tickets.assigned',
            'ticket.status_changed' => 'emails.tickets.status_changed',
            'ticket.commented' => 'emails.tickets.commented',
            'ticket.sla_warning' => 'emails.tickets.sla_warning',
        ];

        return $templates[$type] ?? 'emails.notification';
    }

    private function getEmailSubject(string $type, array $data): string
    {
        $subjects = [
            'ticket.created' => 'Novo Chamado #' . ($data['ticket_id'] ?? ''),
            'ticket.assigned' => 'Chamado Atribuído #' . ($data['ticket_id'] ?? ''),
            'ticket.status_changed' => 'Status do Chamado Alterado #' . ($data['ticket_id'] ?? ''),
            'ticket.commented' => 'Novo Comentário no Chamado #' . ($data['ticket_id'] ?? ''),
            'ticket.sla_warning' => 'Atenção: SLA Próximo do Vencimento #' . ($data['ticket_id'] ?? ''),
        ];

        return $subjects[$type] ?? 'Notificação do Sistema';
    }

    private function getSMSMessage(string $type, array $data): string
    {
        $messages = [
            'ticket.created' => "Novo chamado #{$data['ticket_id']}: {$data['title']}",
            'ticket.assigned' => "Chamado #{$data['ticket_id']} atribuído a você",
            'ticket.status_changed' => "Chamado #{$data['ticket_id']} mudou para: {$data['status']}",
            'ticket.commented' => "Novo comentário no chamado #{$data['ticket_id']}",
            'ticket.sla_warning' => "URGENTE: Chamado #{$data['ticket_id']} próximo do vencimento!",
        ];

        return $messages[$type] ?? 'Notificação do Sistema de Chamados';
    }

    private function getTelegramMessage(string $type, array $data): string
    {
        $messages = [
            'ticket.created' => "🎫 <b>Novo Chamado</b>\n\nChamado: #{$data['ticket_id']}\nTítulo: {$data['title']}\nPrioridade: {$data['priority']}\n\n<a href='{$data['url']}'>Ver Chamado</a>",
            'ticket.assigned' => "👤 <b>Chamado Atribuído</b>\n\nChamado #{$data['ticket_id']} foi atribuído a você.\n\n<a href='{$data['url']}'>Ver Chamado</a>",
            'ticket.status_changed' => "🔄 <b>Status Alterado</b>\n\nChamado #{$data['ticket_id']}\nNovo status: {$data['status']}\n\n<a href='{$data['url']}'>Ver Chamado</a>",
            'ticket.commented' => "💬 <b>Novo Comentário</b>\n\nChamado #{$data['ticket_id']}\n{$data['comment_preview']}\n\n<a href='{$data['url']}'>Ver Chamado</a>",
            'ticket.sla_warning' => "⚠️ <b>ALERTA SLA</b>\n\nChamado #{$data['ticket_id']} está próximo do vencimento!\n\n<a href='{$data['url']}'>Ver Chamado</a>",
        ];

        return $messages[$type] ?? 'Notificação do Sistema';
    }

    private function getWhatsAppMessage(string $type, array $data): string
    {
        return $this->getSMSMessage($type, $data); // Mesma mensagem do SMS
    }

    private function renderTemplate(string $template, array $data): string
    {
        try {
            return view($template, $data)->render();
        } catch (\Exception $e) {
            // Fallback para template simples
            $message = "Notificação do Sistema de Chamados\n\n";
            foreach ($data as $key => $value) {
                if (!is_array($value) && !is_object($value)) {
                    $message .= ucfirst($key) . ": $value\n";
                }
            }
            return $message;
        }
    }
}
