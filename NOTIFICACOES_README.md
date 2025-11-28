# 🔔 Sistema de Notificações Multicanal

## 📋 Índice

1. [Visão Geral](#visão-geral)
2. [Integração Completa](#integração-completa)
3. [Configuração dos Canais](#configuração-dos-canais)
4. [Como Usar](#como-usar)
5. [Testes](#testes)
6. [Troubleshooting](#troubleshooting)
7. [Custos](#custos)
8. [Segurança](#segurança)

---

## 🎯 Visão Geral

O sistema envia notificações automaticamente em 5 situações:

1. **ticket.created** - Quando um novo chamado é criado
2. **ticket.assigned** - Quando um chamado é atribuído a um técnico
3. **ticket.status_changed** - Quando o status do chamado muda
4. **ticket.sla_warning** - Quando o SLA está próximo de vencer
5. **ticket.commented** - Quando há novos comentários (futuro)

---

## 🔗 Integração Completa

### Sistema de Eventos Laravel

O sistema de notificações está completamente integrado ao ciclo de vida dos tickets através de **eventos Laravel**:

#### Eventos Disponíveis

1. **TicketCreated** - Disparado quando um novo ticket é criado
2. **TicketAssigned** - Disparado quando um ticket é atribuído a um técnico
3. **TicketStatusChanged** - Disparado quando o status de um ticket muda

#### Listeners Registrados

Cada evento possui um listener correspondente:

- `SendTicketCreatedNotification` - Notifica criador e admins/técnicos (tickets urgentes)
- `SendTicketAssignedNotification` - Notifica técnico atribuído e criador do ticket
- `SendTicketStatusChangedNotification` - Notifica criador e técnico sobre mudança de status

#### Integração no TicketController

O sistema está integrado nos seguintes pontos:

**1. Criação de Ticket (`store` method)**
```php
// Após criar o ticket
event(new TicketCreated($ticket));
```

**2. Atualização de Ticket (`update` method)**
```php
// Após atualizar ticket
if ($originalAssignedTo !== $ticket->assigned_to && $ticket->assigned_to) {
    $technician = User::find($ticket->assigned_to);
    event(new TicketAssigned($ticket, $technician));
}

if ($originalStatus !== $ticket->status) {
    event(new TicketStatusChanged($ticket, $originalStatus, $ticket->status));
}
```

**3. Ações em Lote (`bulkAction` method)**
```php
// Dispara eventos automaticamente para cada ticket modificado
if ($oldStatus !== $ticket->status) {
    event(new TicketStatusChanged($ticket, $oldStatus, $ticket->status));
}

if ($action === 'assign' && $request->assigned_user_id) {
    $technician = User::find($request->assigned_user_id);
    event(new TicketAssigned($ticket, $technician));
}
```

### Como Funciona

```
┌─────────────────┐
│  Criar Ticket   │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│ event(TicketCreated) │
└────────┬────────┘
         │
         ▼
┌──────────────────────────────┐
│ SendTicketCreatedNotification │
└────────┬─────────────────────┘
         │
         ▼
┌─────────────────────────────┐
│  NotificationService::notify │
└────────┬────────────────────┘
         │
         ├──> Email (SMTP)
         ├──> SMS (Twilio)
         ├──> Telegram (Bot API)
         └──> WhatsApp (Twilio)
```

### Arquivo de Configuração de Eventos

Os eventos e listeners estão registrados em `app/Providers/EventServiceProvider.php`:

```php
protected $listen = [
    TicketCreated::class => [
        SendTicketCreatedNotification::class,
    ],
    TicketAssigned::class => [
        SendTicketAssignedNotification::class,
    ],
    TicketStatusChanged::class => [
        SendTicketStatusChangedNotification::class,
    ],
];
```

### Arquivos Criados

**Eventos:**
- `app/Events/TicketCreated.php`
- `app/Events/TicketAssigned.php`
- `app/Events/TicketStatusChanged.php`

**Listeners:**
- `app/Listeners/SendTicketCreatedNotification.php`
- `app/Listeners/SendTicketAssignedNotification.php`
- `app/Listeners/SendTicketStatusChangedNotification.php`

**Service:**
- `app/Services/NotificationService.php`

**Controller:**
- `app/Http/Controllers/NotificationPreferenceController.php`

**Views:**
- `resources/views/notifications/preferences.blade.php`
- `resources/views/emails/tickets/created.blade.php`
- `resources/views/emails/tickets/assigned.blade.php`
- `resources/views/emails/tickets/status_changed.blade.php`
- `resources/views/emails/tickets/sla_warning.blade.php`

---

### Preferências por Usuário

Cada usuário pode configurar:
- Quais tipos de eventos quer receber
- Por quais canais quer ser notificado
- Seus dados de contato (telefone, WhatsApp, Telegram ID)

## Configuração

### 1. Email (SMTP)

Já configurado no `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=seu-servidor-smtp.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@dominio.com
MAIL_PASSWORD=sua-senha
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@sistema.com"
MAIL_FROM_NAME="Sistema de Chamados"
```

### 2. SMS e WhatsApp (Twilio)

#### Passo 1: Criar Conta na Twilio

1. Acesse https://www.twilio.com/try-twilio
2. Crie uma conta gratuita (crédito de teste)
3. Obtenha suas credenciais no Console:
   - Account SID
   - Auth Token
   - Número de telefone Twilio

#### Passo 2: Configurar no `.env`

```env
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=xxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_FROM=+15551234567
TWILIO_WHATSAPP_FROM=whatsapp:+14155238886
```

#### Passo 3: Para WhatsApp

1. No Console da Twilio, ative o WhatsApp Sandbox
2. Siga as instruções para conectar seu número
3. Use o número sandbox fornecido pela Twilio

**Nota**: Para produção, é necessário ter um número WhatsApp Business aprovado.

### 3. Telegram

#### Passo 1: Criar um Bot

1. No Telegram, busque por `@BotFather`
2. Envie `/newbot`
3. Escolha um nome e username para o bot
4. Copie o **token** fornecido

#### Passo 2: Configurar no `.env`

```env
TELEGRAM_BOT_TOKEN=123456789:ABCdefGHIjklMNOpqrsTUVwxyz
TELEGRAM_BOT_USERNAME=seu_bot_username
```

#### Passo 3: Obter Chat ID dos Usuários

Cada usuário precisa:

1. Buscar pelo bot no Telegram (exemplo: `@seu_bot_username`)
2. Iniciar conversa enviando `/start`
3. Enviar `/getid` para receber o Chat ID
4. Configurar o Chat ID nas preferências de notificação do sistema

#### Passo 4: Criar Comandos do Bot (Opcional)

No BotFather, envie `/setcommands` e adicione:

```
start - Iniciar conversa com o bot
getid - Obter seu Chat ID para notificações
help - Ajuda sobre como usar o bot
```

### 4. Código do Bot Telegram (Opcional)

Para um bot mais completo que responde comandos, crie:

```php
// app/Console/Commands/TelegramBotCommand.php
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TelegramBotCommand extends Command
{
    protected $signature = 'telegram:bot';
    protected $description = 'Executa o bot do Telegram';

    public function handle()
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $offset = 0;

        $this->info('Bot Telegram iniciado...');

        while (true) {
            $response = Http::get("https://api.telegram.org/bot{$token}/getUpdates", [
                'offset' => $offset,
                'timeout' => 30
            ]);

            if ($response->successful()) {
                $updates = $response->json()['result'];

                foreach ($updates as $update) {
                    $offset = $update['update_id'] + 1;
                    
                    if (isset($update['message'])) {
                        $this->processMessage($update['message'], $token);
                    }
                }
            }

            sleep(1);
        }
    }

    private function processMessage($message, $token)
    {
        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        $response = match($text) {
            '/start' => "Olá! Sou o bot do Sistema de Chamados.\n\nPara receber notificações, configure seu Chat ID nas preferências do sistema.\n\nComandos disponíveis:\n/getid - Obter seu Chat ID\n/help - Ajuda",
            '/getid' => "Seu Chat ID é: <code>{$chatId}</code>\n\nCopie este número e cole nas preferências de notificação do sistema.",
            '/help' => "📌 Comandos disponíveis:\n\n/start - Iniciar\n/getid - Ver seu Chat ID\n/help - Esta mensagem",
            default => "Comando não reconhecido. Use /help para ver os comandos disponíveis."
        };

        Http::post("https://api.telegram.org/bot{$token}/sendMessage", [
            'chat_id' => $chatId,
            'text' => $response,
            'parse_mode' => 'HTML'
        ]);
    }
}
```

Registre o comando no `Kernel.php`:

```php
protected $commands = [
    \App\Console\Commands\TelegramBotCommand::class,
];
```

Execute com:
```bash
php artisan telegram:bot
```

## Como Usar

### Para Usuários

1. Acesse **Notificações** no menu lateral
2. Configure seus canais de contato:
   - WhatsApp (formato: +55 11 98765-4321)
   - Telegram Chat ID
3. Escolha quais eventos quer receber
4. Selecione os canais para cada tipo de evento
5. Teste as notificações com os botões de teste
6. Salve as preferências

### Para Desenvolvedores

#### Enviar Notificação Programaticamente

```php
use App\Services\NotificationService;
use App\Models\User;

$user = User::find(1);
$notificationService = app(NotificationService::class);

$data = [
    'ticket_id' => $ticket->id,
    'title' => $ticket->title,
    'priority' => $ticket->priority,
    'status' => $ticket->status,
    'url' => route('tickets.show', $ticket),
    'user_name' => $user->name,
];

$notificationService->notify($user, 'ticket.created', $data);
```

#### Criar Novo Tipo de Notificação

1. Adicione o tipo no `NotificationService.php`:

```php
private function getEmailTemplate(string $type): string
{
    $templates = [
        // ... existentes
        'seu.novo.evento' => 'emails.seu-template',
    ];
    return $templates[$type] ?? 'emails.notification';
}
```

2. Crie o template em `resources/views/emails/seu-template.blade.php`

3. Use nas preferências do usuário:

```php
$preferences['events']['seu.novo.evento'] = [
    'enabled' => true,
    'channels' => ['email', 'telegram']
];
```

## Filas e Performance

Para melhor performance em produção, use filas:

### 1. Configurar Queue

```env
QUEUE_CONNECTION=database
```

```bash
php artisan queue:table
php artisan migrate
```

### 2. Processar Filas

```bash
php artisan queue:work
```

### 3. Usar em Produção

Configure o supervisor para manter o worker rodando:

```ini
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /caminho/para/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=seu-usuario
numprocs=2
redirect_stderr=true
stdout_logfile=/caminho/para/logs/worker.log
```

## Testes

### Testar Email Localmente

Use Mailtrap.io ou MailHog:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=seu-username
MAIL_PASSWORD=sua-senha
```

### Testar SMS/WhatsApp

Use o número de teste da Twilio no Sandbox

### Testar Telegram

Use seu próprio bot em desenvolvimento

## Troubleshooting

### Email não chega

- Verifique as configurações SMTP
- Veja os logs em `storage/logs/laravel.log`
- Teste com `php artisan tinker`:

```php
Mail::raw('Teste', function($msg) {
    $msg->to('seu@email.com')->subject('Teste');
});
```

### SMS/WhatsApp não envia

- Verifique se as credenciais Twilio estão corretas
- Verifique se o número tem crédito
- Veja os logs da Twilio no Console

### Telegram não funciona

- Verifique se o token está correto
- Certifique-se que o bot está ativo
- Teste diretamente na API:

```bash
curl https://api.telegram.org/bot<TOKEN>/getMe
```

## Custos

- **Email**: Grátis (SMTP próprio) ou barato (~$0.10/1000 emails com serviços como SendGrid)
- **SMS**: ~$0.01-0.05 por SMS (Twilio)
- **WhatsApp**: ~$0.005-0.01 por mensagem (Twilio)
- **Telegram**: Totalmente grátis

## Segurança

- Nunca compartilhe tokens/credenciais
- Use variáveis de ambiente (`.env`)
- Não commite o `.env` no Git
- Rotacione tokens periodicamente
- Use HTTPS em produção

## Próximas Melhorias

- [ ] Digest diário/semanal de atividades
- [ ] Push notifications no navegador
- [ ] Integração com Slack
- [ ] Templates personalizáveis por usuário
- [ ] Agendamento de notificações
- [ ] Analytics de notificações (taxa de abertura, etc)

---

Para mais informações, consulte:
- Twilio: https://www.twilio.com/docs
- Telegram Bot API: https://core.telegram.org/bots/api
- Laravel Mail: https://laravel.com/docs/mail
