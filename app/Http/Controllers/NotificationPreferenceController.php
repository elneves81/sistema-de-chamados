<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationPreferenceController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Exibe a página de preferências de notificação
     */
    public function index()
    {
        $user = Auth::user();
        $preferences = $user->getNotificationPreferences();
        
        return view('notifications.preferences', compact('preferences'));
    }

    /**
     * Atualiza as preferências de notificação
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'telegram_id' => 'nullable|string|max:255',
            'whatsapp' => 'nullable|string|max:20',
            'channels' => 'nullable|array',
            'channels.*' => 'string|in:email,sms,telegram,whatsapp',
            'events' => 'nullable|array',
        ]);

        // Atualizar contatos
        $user->telegram_id = $validated['telegram_id'] ?? null;
        $user->whatsapp = $validated['whatsapp'] ?? null;

        // Montar preferências de notificação
        $preferences = [
            'channels' => $validated['channels'] ?? ['email'],
            'events' => []
        ];

        // Processar eventos
        if (isset($validated['events'])) {
            foreach ($validated['events'] as $eventType => $eventConfig) {
                $preferences['events'][$eventType] = [
                    'enabled' => isset($eventConfig['enabled']) && $eventConfig['enabled'] == '1',
                    'channels' => $eventConfig['channels'] ?? ['email']
                ];
            }
        } else {
            // Configuração padrão se nenhum evento foi enviado
            $defaultEvents = [
                'ticket.created',
                'ticket.assigned',
                'ticket.status_changed',
                'ticket.commented',
                'ticket.sla_warning'
            ];

            foreach ($defaultEvents as $event) {
                $preferences['events'][$event] = [
                    'enabled' => true,
                    'channels' => ['email']
                ];
            }
        }

        $user->updateNotificationPreferences($preferences);

        return redirect()->route('notifications.preferences')
                        ->with('success', 'Preferências de notificação atualizadas com sucesso!');
    }

    /**
     * Testa o envio de uma notificação
     */
    public function test(Request $request)
    {
        $validated = $request->validate([
            'channel' => 'required|string|in:email,sms,telegram,whatsapp'
        ]);

        $user = Auth::user();
        $channel = $validated['channel'];

        // Verificar se o canal está configurado
        if (!$user->hasValidChannel($channel)) {
            return back()->with('error', "Canal {$channel} não está configurado. Por favor, adicione os dados de contato.");
        }

        try {
            $notificationService = app(\App\Services\NotificationService::class);
            
            $testData = [
                'ticket_id' => '9999',
                'title' => 'Teste de Notificação',
                'priority' => 'medium',
                'status' => 'Teste',
                'url' => route('dashboard'),
                'user_name' => $user->name,
            ];

            // Forçar envio apenas para o canal testado
            $originalPrefs = $user->notification_preferences;
            $user->notification_preferences = [
                'events' => [
                    'test.notification' => [
                        'enabled' => true,
                        'channels' => [$channel]
                    ]
                ]
            ];

            $notificationService->notify($user, 'test.notification', $testData);

            // Restaurar preferências
            $user->notification_preferences = $originalPrefs;

            return back()->with('success', "Notificação de teste enviada via {$channel}! Verifique seu {$channel}.");
        } catch (\Exception $e) {
            return back()->with('error', "Erro ao enviar notificação de teste: " . $e->getMessage());
        }
    }

    /**
     * API para obter instruções de configuração do Telegram
     */
    public function getTelegramInstructions()
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $botUsername = env('TELEGRAM_BOT_USERNAME', 'seu_bot');

        return response()->json([
            'instructions' => [
                "1. Abra o Telegram e busque por @{$botUsername}",
                "2. Inicie uma conversa enviando /start",
                "3. Envie o comando /getid para obter seu Chat ID",
                "4. Copie o número que aparece e cole no campo acima",
                "5. Clique em Salvar para ativar as notificações"
            ],
            'bot_url' => "https://t.me/{$botUsername}",
            'configured' => !empty($botToken)
        ]);
    }
}
