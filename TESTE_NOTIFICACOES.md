# 🧪 Guia de Testes - Sistema de Notificações

## ✅ Pré-requisitos

Antes de testar, certifique-se de que:

1. ✅ Migration executada: `php artisan migrate`
2. ✅ Caches limpos: `php artisan route:clear && php artisan view:clear && php artisan config:clear && php artisan event:clear`
3. ✅ Servidor reiniciado: `sudo systemctl restart laravel-server.service`
4. ✅ Configuração do .env atualizada (pelo menos para email)

---

## 📧 Teste 1: Configurar Preferências de Notificação

### Passos:

1. Faça login no sistema
2. Acesse o menu lateral e clique em **"Notificações"**
3. Você verá a página de preferências com duas colunas:
   - **Esquerda:** Configuração de canais (WhatsApp, Telegram)
   - **Direita:** Configuração de eventos

### Configure seus canais:

**WhatsApp:**
```
+5511999999999
```

**Telegram ID:**
```
123456789
```

### Configure eventos:

Marque as caixas para cada tipo de notificação que deseja receber:
- ✅ Chamado Criado
- ✅ Chamado Atribuído
- ✅ Status Alterado
- ✅ Alerta de SLA

Para cada evento, selecione os canais desejados:
- ☑️ Email
- ☑️ SMS
- ☑️ Telegram
- ☑️ WhatsApp

Clique em **"Salvar Preferências"**

---

## 🔔 Teste 2: Testar Notificações Individuais

Na mesma página de preferências, você verá botões de teste para cada canal.

### Teste de Email:
1. Clique no botão **"Testar Email"**
2. Verifique sua caixa de entrada
3. Você deve receber um email de teste

### Teste de SMS:
1. Configure Twilio no .env primeiro
2. Clique no botão **"Testar SMS"**
3. Verifique seu celular

### Teste de Telegram:
1. Configure o bot do Telegram no .env
2. Inicie conversa com o bot: `/start`
3. Obtenha seu Chat ID: `/getid`
4. Atualize seu Telegram ID nas preferências
5. Clique no botão **"Testar Telegram"**
6. Verifique o Telegram

### Teste de WhatsApp:
1. Configure Twilio WhatsApp no .env
2. Clique no botão **"Testar WhatsApp"**
3. Verifique o WhatsApp

---

## 🎫 Teste 3: Criar Ticket e Verificar Notificações

### Cenário: Novo Ticket Criado

1. Vá para **Chamados > Novo Chamado**
2. Preencha os dados:
   - **Título:** "Teste de Notificação - Criação"
   - **Descrição:** "Testando sistema de notificações"
   - **Categoria:** Selecione uma categoria
   - **Prioridade:** Urgente (para testar notificação para admins)
   - **Localização:** Selecione uma localização
3. Clique em **"Criar Chamado"**

### Resultado Esperado:

**Como criador do ticket:**
- ✅ Você deve receber notificação nos canais configurados
- ✅ Email com título "Novo Chamado Criado"
- ✅ SMS/Telegram/WhatsApp com informações do ticket

**Como admin/técnico (apenas se ticket for urgente):**
- ✅ Admins e técnicos devem receber notificação
- ✅ Mensagem indicando novo ticket urgente

---

## 👤 Teste 4: Atribuir Ticket

### Cenário: Ticket Atribuído a Técnico

1. Como admin ou técnico, acesse o ticket criado
2. Clique em **"Editar"**
3. Selecione um técnico no campo **"Atribuído a"**
4. Clique em **"Salvar"**

### Resultado Esperado:

**Como técnico atribuído:**
- ✅ Recebe notificação de atribuição
- ✅ Email/SMS/Telegram/WhatsApp com detalhes do ticket

**Como criador do ticket:**
- ✅ Recebe notificação informando que ticket foi atribuído
- ✅ Mensagem com nome do técnico

---

## 📊 Teste 5: Alterar Status

### Cenário: Mudança de Status

1. Acesse um ticket existente
2. Clique em **"Editar"**
3. Altere o **Status** (ex: de "Aberto" para "Em Andamento")
4. Clique em **"Salvar"**

### Resultado Esperado:

**Como criador do ticket:**
- ✅ Recebe notificação de mudança de status
- ✅ Email mostrando status antigo e novo
- ✅ SMS/Telegram/WhatsApp com atualização

**Como técnico atribuído:**
- ✅ Recebe mesma notificação se não for o criador

---

## 🔄 Teste 6: Ações em Lote

### Cenário: Atribuição em Lote

1. Vá para **Chamados**
2. Selecione múltiplos tickets (use checkboxes)
3. No dropdown **"Ações em lote"**, selecione **"Atribuir a técnico"**
4. Escolha um técnico
5. Clique em **"Aplicar"**

### Resultado Esperado:

**Para cada ticket modificado:**
- ✅ Evento `TicketAssigned` disparado
- ✅ Técnico recebe notificações de todos os tickets atribuídos
- ✅ Criadores recebem notificação de atribuição

### Cenário: Mudança de Status em Lote

1. Selecione múltiplos tickets
2. Escolha **"Alterar status"** > **"Resolvido"**
3. Opcionalmente adicione um comentário
4. Clique em **"Aplicar"**

### Resultado Esperado:

**Para cada ticket:**
- ✅ Evento `TicketStatusChanged` disparado
- ✅ Criadores e técnicos recebem notificações
- ✅ Status refletido corretamente

---

## 🔍 Verificar Logs

### Logs Laravel:

```bash
tail -f /home/elber/sistema-de-chamados/storage/logs/laravel.log
```

### Procure por:

- `[NotificationService]` - Logs do serviço de notificação
- Erros de API (Twilio, Telegram)
- Falhas de envio de email

---

## 🧪 Teste com curl (API de Teste)

### Testar endpoint de teste de notificação:

```bash
curl -X POST http://localhost:8000/notifications/preferences/test \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=SEU_COOKIE_AQUI" \
  -d '{"channel": "email"}'
```

### Respostas esperadas:

**Sucesso:**
```json
{
  "success": true,
  "message": "Notificação de teste enviada com sucesso!"
}
```

**Erro (canal não configurado):**
```json
{
  "success": false,
  "message": "Canal email não está configurado para este usuário"
}
```

---

## ⚠️ Troubleshooting Comum

### 1. Notificações não estão sendo enviadas

**Verifique:**
```bash
# Limpar todos os caches
php artisan route:clear
php artisan view:clear
php artisan config:clear
php artisan event:clear

# Verificar se eventos estão registrados
php artisan event:list
```

Você deve ver:
```
App\Events\TicketCreated .................. App\Listeners\SendTicketCreatedNotification
App\Events\TicketAssigned ................. App\Listeners\SendTicketAssignedNotification
App\Events\TicketStatusChanged ............ App\Listeners\SendTicketStatusChangedNotification
```

### 2. Email não está enviando

**Verifique .env:**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seu-email@gmail.com
MAIL_PASSWORD=sua-senha-app
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=seu-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Teste manualmente:**
```bash
php artisan tinker
>>> \Illuminate\Support\Facades\Mail::raw('Teste', function($m) { $m->to('seu-email@gmail.com')->subject('Teste'); });
```

### 3. Twilio não funciona

**Verifique credenciais:**
```bash
# No .env
TWILIO_SID=ACxxxxxxxxxxxxx
TWILIO_AUTH_TOKEN=xxxxxxxxxxxxxx
TWILIO_PHONE_NUMBER=+15551234567
TWILIO_WHATSAPP_NUMBER=whatsapp:+15551234567
```

**Teste diretamente:**
```bash
php artisan tinker
>>> $client = new \Twilio\Rest\Client(env('TWILIO_SID'), env('TWILIO_AUTH_TOKEN'));
>>> $client->messages->create('+5511999999999', ['from' => env('TWILIO_PHONE_NUMBER'), 'body' => 'Teste']);
```

### 4. Telegram não funciona

**Verifique bot:**
```bash
curl https://api.telegram.org/bot<SEU_TOKEN>/getMe
```

**Obtenha chat_id:**
1. Envie `/start` para o bot
2. Acesse: `https://api.telegram.org/bot<SEU_TOKEN>/getUpdates`
3. Procure por `chat.id` na resposta

### 5. Preferências não salvam

**Verifique:**
```bash
# No tinker
php artisan tinker
>>> $user = \App\Models\User::find(1);
>>> $user->notification_preferences;
>>> $user->telegram_id;
>>> $user->whatsapp;
```

Se retornar null, verifique se migration foi executada:
```bash
php artisan migrate:status
```

---

## 📊 Checklist de Testes Completo

### Configuração:
- [ ] .env configurado com credenciais
- [ ] Migration executada
- [ ] Caches limpos
- [ ] Servidor reiniciado

### Interface:
- [ ] Página de preferências acessível
- [ ] Campos de configuração visíveis
- [ ] Salvamento de preferências funciona
- [ ] Botões de teste aparecem

### Notificações de Teste:
- [ ] Teste de email funciona
- [ ] Teste de SMS funciona (se Twilio configurado)
- [ ] Teste de Telegram funciona (se bot configurado)
- [ ] Teste de WhatsApp funciona (se Twilio configurado)

### Eventos de Ticket:
- [ ] Criar ticket envia notificação
- [ ] Atribuir ticket envia notificação
- [ ] Mudar status envia notificação
- [ ] Tickets urgentes notificam admins

### Ações em Lote:
- [ ] Atribuição em lote dispara eventos
- [ ] Mudança de status em lote dispara eventos
- [ ] Múltiplas notificações são enviadas corretamente

### Preferências do Usuário:
- [ ] Desabilitar canal impede envio
- [ ] Desabilitar evento impede notificação
- [ ] Canais inválidos não recebem notificação

---

## 🎯 Próximos Passos

Após validar todos os testes acima:

1. **Configurar Queue System** (Recomendado para produção)
   ```bash
   php artisan queue:table
   php artisan migrate
   ```
   
   Atualizar `.env`:
   ```env
   QUEUE_CONNECTION=database
   ```
   
   Criar job para notificações assíncronas

2. **Implementar Bot do Telegram** (Opcional)
   - Criar comando Artisan para bot
   - Implementar handlers para `/start`, `/getid`, `/help`
   - Rodar bot: `php artisan telegram:bot`

3. **Adicionar mais tipos de notificação**
   - Comentários em tickets
   - Digest diário/semanal
   - Relatórios personalizados

4. **Monitoramento**
   - Adicionar logs estruturados
   - Criar dashboard de status de notificações
   - Rastrear taxa de entrega

---

## 📞 Suporte

Se encontrar problemas:

1. Verifique os logs: `storage/logs/laravel.log`
2. Execute `php artisan event:list` para confirmar eventos registrados
3. Use `php artisan tinker` para debug manual
4. Consulte `NOTIFICACOES_README.md` para configuração detalhada

---

**Última atualização:** $(date)
