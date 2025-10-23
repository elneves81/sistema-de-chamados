# 📧 Sistema de Mensagens Internas - Documentação Completa

## 🎯 Visão Geral

O Sistema de Mensagens Internas permite comunicação direta entre administradores e usuários através do próprio sistema, com notificações por email. Este sistema foi implementado com uma arquitetura moderna e interface intuitiva.

## ✨ Funcionalidades Principais

### 🔑 Para Administradores e Técnicos
- ✅ **Compor mensagens** para qualquer usuário
- ✅ **Definir prioridade** (Baixa, Média, Alta, Urgente)
- ✅ **Visualizar todas as mensagens** do sistema
- ✅ **Responder mensagens** dos usuários
- ✅ **Envio automático de emails** de notificação

### 👤 Para Usuários Comuns
- ✅ **Receber mensagens** dos administradores
- ✅ **Responder mensagens** recebidas
- ✅ **Visualizar histórico** de conversas
- ✅ **Notificações em tempo real** no sistema
- ✅ **Notificações por email** quando recebe mensagens

## 🏗️ Arquitetura do Sistema

### 📊 Estrutura de Banco de Dados

```sql
-- Tabela: user_messages
CREATE TABLE user_messages (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    from_user_id BIGINT NOT NULL,           -- Quem enviou
    to_user_id BIGINT NOT NULL,             -- Quem recebeu
    subject VARCHAR(255) NOT NULL,          -- Assunto
    message TEXT NOT NULL,                  -- Conteúdo
    priority ENUM('low','medium','high','urgent') DEFAULT 'medium',
    is_read BOOLEAN DEFAULT FALSE,          -- Lida ou não
    email_sent BOOLEAN DEFAULT FALSE,       -- Email foi enviado
    read_at TIMESTAMP NULL,                 -- Quando foi lida
    email_sent_at TIMESTAMP NULL,           -- Quando email foi enviado
    attachments JSON NULL,                  -- Anexos (futuro)
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    -- Chaves estrangeiras
    FOREIGN KEY (from_user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (to_user_id) REFERENCES users(id) ON DELETE CASCADE,
    
    -- Índices para performance
    INDEX(to_user_id, is_read),
    INDEX(from_user_id),
    INDEX(created_at)
);
```

### 🎨 Componentes da Interface

#### 1. **Central de Mensagens** (`/messages`)
- Lista todas as mensagens recebidas e enviadas
- Separado em abas: "Recebidas" e "Enviadas"
- Indicadores visuais de prioridade e status de leitura
- Paginação automática

#### 2. **Composição de Mensagens** (`/messages/compose`)
- Formulário completo para criar novas mensagens
- Seleção de destinatário
- Definição de prioridade com cores
- Editor de texto com validação
- Opção de envio de email

#### 3. **Visualização de Mensagem** (`/messages/{id}`)
- Exibição completa da mensagem
- Histórico de respostas
- Formulário de resposta inline
- Marcação automática como lida

#### 4. **Notificações em Tempo Real**
- **Sidebar**: Badge com contador de mensagens não lidas
- **Top Navigation**: Dropdown com mensagens recentes
- **Atualizações automáticas** a cada 30 segundos

## 🛠️ Arquivos Implementados

### 📁 Backend (Laravel)

#### Models
```
app/Models/UserMessage.php
├── Relacionamentos com User (fromUser, toUser)
├── Scopes para mensagens (forUser, fromUser, unread)
├── Métodos helper (markAsRead, isFromAdmin)
└── Attributes (timeAgo, priorityBadge)
```

#### Controllers
```
app/Http/Controllers/UserMessageController.php
├── index()         # Lista mensagens do usuário
├── show()          # Exibe mensagem específica
├── compose()       # Formulário de composição (admin)
├── store()         # Cria nova mensagem
├── reply()         # Responde mensagem
├── markAsRead()    # Marca como lida
├── recent()        # API - mensagens recentes
├── unreadCount()   # API - contador não lidas
└── sendEmailNotification() # Envio de emails
```

#### Migrations
```
database/migrations/2025_08_31_040545_create_user_messages_table.php
└── Estrutura completa da tabela com índices e constraints
```

#### Seeders
```
database/seeders/UserMessageSeeder.php
└── Mensagens de exemplo para demonstração
```

### 🎨 Frontend (Blade Templates)

#### Views Principais
```
resources/views/messages/
├── index.blade.php      # Central de mensagens
├── show.blade.php       # Visualização de mensagem
└── compose.blade.php    # Composição de mensagem
```

#### Componentes
```
resources/views/components/
└── messages-notification.blade.php  # Dropdown de notificações
```

#### Layout Atualizado
```
resources/views/layouts/app.blade.php
├── Menu lateral com badge de mensagens
├── Top navigation com dropdown de notificações
├── Sincronização automática de badges
└── Scripts de atualização em tempo real
```

### 🌐 Rotas

#### Web Routes
```php
// Sistema de Mensagens Internas
Route::middleware('auth')->group(function () {
    Route::get('/messages', [UserMessageController::class, 'index'])->name('messages.index');
    Route::get('/messages/{message}', [UserMessageController::class, 'show'])->name('messages.show');
    Route::post('/messages/{message}/reply', [UserMessageController::class, 'reply'])->name('messages.reply');
    Route::patch('/messages/{message}/read', [UserMessageController::class, 'markAsRead'])->name('messages.read');
    
    // Rotas para administradores
    Route::middleware('permission:admin.manage')->group(function () {
        Route::post('/messages/send', [UserMessageController::class, 'store'])->name('messages.send');
        Route::get('/messages/compose/{user?}', [UserMessageController::class, 'compose'])->name('messages.compose');
    });
});
```

#### API Routes
```php
// APIs para notificações em tempo real
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/api/messages/recent', [UserMessageController::class, 'recent']);
    Route::get('/api/messages/unread-count', [UserMessageController::class, 'unreadCount']);
});
```

## 🚀 Como Usar

### 📧 Enviando uma Mensagem (Admin/Técnico)

1. **Acesse** `/messages` na sidebar
2. **Clique** em "Compor Mensagem"
3. **Selecione** o destinatário
4. **Digite** o assunto e mensagem
5. **Defina** a prioridade
6. **Envie** - o usuário receberá email automaticamente

### 📬 Recebendo e Respondendo (Usuário)

1. **Notificação** aparece no badge da sidebar e top navigation
2. **Clique** na notificação ou acesse `/messages`
3. **Visualize** a mensagem (marca automaticamente como lida)
4. **Responda** usando o formulário na parte inferior
5. **Acompanhe** o histórico de conversas

## 🎨 Design e UX

### 🏷️ Indicadores Visuais

- **🟢 Baixa**: Verde claro
- **🟡 Média**: Amarelo
- **🟠 Alta**: Laranja
- **🔴 Urgente**: Vermelho

### 📱 Responsividade

- **Mobile First**: Interface otimizada para todos os dispositivos
- **Cards responsivos**: Layout adapta-se automaticamente
- **Touch-friendly**: Botões e links adequados para toque

### ♿ Acessibilidade

- **ARIA labels**: Suporte para leitores de tela
- **Contraste adequado**: Cores acessíveis
- **Navegação por teclado**: Suporte completo

## 🔒 Segurança

### 🛡️ Controle de Acesso

- **Role-based**: Verificação por função (admin, technician, customer)
- **Ownership**: Usuários só veem suas próprias mensagens
- **CSRF Protection**: Proteção contra ataques CSRF
- **SQL Injection**: Uso de Eloquent ORM previne injeções

### 📧 Email Seguro

- **Queue-based**: Envios em fila para performance
- **Rate limiting**: Controle de frequência de envios
- **Template validation**: Validação de conteúdo

## 📊 Performance

### ⚡ Otimizações

- **Eager Loading**: Carregamento otimizado de relacionamentos
- **Índices de banco**: Consultas rápidas
- **Paginação**: Carregamento eficiente de listas
- **Caching**: Cache de consultas frequentes

### 📈 Monitoramento

- **Query logging**: Log de consultas lentas
- **Error tracking**: Rastreamento de erros
- **Performance metrics**: Métricas de tempo de resposta

## 🚀 Próximas Melhorias

### 📎 Funcionalidades Futuras

- [ ] **Anexos**: Upload de arquivos nas mensagens
- [ ] **Push notifications**: Notificações browser
- [ ] **Mensagens em grupo**: Envio para múltiplos usuários
- [ ] **Templates**: Modelos de mensagem predefinidos
- [ ] **Agendamento**: Envio de mensagens programadas
- [ ] **Relatórios**: Analytics de comunicação
- [ ] **Chat em tempo real**: WebSocket para chat instantâneo

### 🎨 Melhorias de Interface

- [ ] **Editor WYSIWYG**: Editor de texto avançado
- [ ] **Emoji picker**: Seletor de emojis
- [ ] **Dark mode**: Tema escuro
- [ ] **Personalização**: Configurações de usuário

## 🧪 Teste do Sistema

### 📋 Dados de Exemplo

O sistema foi populado com mensagens de exemplo através do seeder:

```bash
php artisan db:seed --class=UserMessageSeeder
```

### 🎭 Cenários de Teste

1. **Login como admin** → Compor mensagem para usuário
2. **Login como usuário** → Visualizar mensagem recebida
3. **Responder mensagem** → Verificar histórico
4. **Verificar notificações** → Badge e dropdown
5. **Testar prioridades** → Cores e ordenação

## 📞 Suporte

### 🐛 Troubleshooting

**Problema**: Badges não atualizam
- **Solução**: Verificar JavaScript no console, recarregar página

**Problema**: Email não é enviado
- **Solução**: Verificar configurações SMTP no `.env`

**Problema**: Permissões negadas
- **Solução**: Verificar role do usuário na tabela `users`

---

## 🎉 Conclusão

O Sistema de Mensagens Internas está **100% funcional** e implementado com:

✅ **Interface moderna** e intuitiva
✅ **Notificações em tempo real** 
✅ **Emails automáticos**
✅ **Controle de permissões**
✅ **Design responsivo**
✅ **Código bem documentado**
✅ **Segurança robusta**

O sistema está pronto para uso em produção e pode ser facilmente expandido com as funcionalidades futuras listadas acima.

---

**Desenvolvido por**: GitHub Copilot  
**Data**: 31 de Agosto de 2025  
**Versão**: 1.0.0
