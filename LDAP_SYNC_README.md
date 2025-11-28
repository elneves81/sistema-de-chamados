# 📋 Sincronização Automática LDAP/Active Directory

## 📌 Visão Geral

Sistema de importação automática de usuários do LDAP/Active Directory para o sistema de chamados.

## ⚙️ Configuração

### 1. Variáveis de Ambiente (.env)

Adicione as seguintes configurações no seu arquivo `.env`:

```bash
# LDAP / Active Directory Configuration
LDAP_HOSTS=10.0.0.31
LDAP_BASE_DN=DC=guarapuava,DC=pr,DC=gov,DC=br
LDAP_USERNAME=usuario_ldap@guarapuava.pr.gov.br
LDAP_PASSWORD=senha_do_ldap
LDAP_PORT=389
LDAP_USE_SSL=false
LDAP_USE_TLS=false
LDAP_TIMEOUT=10
```

**Parâmetros:**
- `LDAP_HOSTS`: Endereço IP ou hostname do servidor AD
- `LDAP_BASE_DN`: Distinguished Name base para busca
- `LDAP_USERNAME`: Usuário com permissão de leitura no AD
- `LDAP_PASSWORD`: Senha do usuário LDAP
- `LDAP_PORT`: Porta (389 para LDAP, 636 para LDAPS)
- `LDAP_USE_SSL`: Usar SSL (true/false)
- `LDAP_USE_TLS`: Usar TLS (true/false)
- `LDAP_TIMEOUT`: Timeout da conexão em segundos

### 2. Configurar Cron Job (Agendamento Automático)

Para executar automaticamente a sincronização, configure o cron do sistema:

#### Passo 1: Editar crontab
```bash
crontab -e
```

#### Passo 2: Adicionar linha do Laravel Scheduler
```bash
* * * * * cd /home/elber/sistema-de-chamados && php artisan schedule:run >> /dev/null 2>&1
```

**Importante:** Substitua `/home/elber/sistema-de-chamados` pelo caminho real do seu projeto.

### 3. Agendamento Configurado

A importação LDAP está configurada para executar:
- **Diariamente às 02:00** (horário padrão)
- Importa até **1000 usuários** por execução
- Usa proteção contra sobreposição (`withoutOverlapping`)
- Executa em apenas um servidor (`onOneServer`)

Para mudar o horário, edite `app/Console/Kernel.php`:

```php
$schedule->command('ldap:import-users --limit=1000')
         ->daily()
         ->at('02:00')  // <-- Altere aqui
         ->withoutOverlapping()
         ->onOneServer()
         ->runInBackground();
```

**Outras opções de agendamento:**
```php
->everyTwoHours()        // A cada 2 horas
->everySixHours()        // A cada 6 horas
->hourly()               // A cada hora
->weeklyOn(1, '03:00')   // Toda segunda às 03:00
```

## 🚀 Comandos Manuais

### Executar importação manualmente
```bash
php artisan ldap:import-users
```

### Opções disponíveis

#### Limitar número de usuários
```bash
php artisan ldap:import-users --limit=500
```

#### Tamanho do lote (batch)
```bash
php artisan ldap:import-users --batch-size=50
```

#### Filtrar usuários específicos
```bash
php artisan ldap:import-users --filter="Silva"
```

#### Modo simulação (dry-run) - não salva no banco
```bash
php artisan ldap:import-users --dry-run
```

#### Combinando opções
```bash
php artisan ldap:import-users --limit=1000 --batch-size=100 --filter="TI"
```

## 📊 Relatórios e Logs

### Ver últimas execuções
```bash
tail -f storage/logs/laravel.log | grep LDAP
```

### Visualizar execuções agendadas
```bash
php artisan schedule:list
```

### Testar agendamento sem esperar
```bash
php artisan schedule:run
```

## 🔍 O que o comando faz

1. **Conecta** ao servidor LDAP/AD usando as credenciais configuradas
2. **Busca** usuários com os atributos:
   - Nome (CN, DisplayName)
   - Email
   - Username (SAMAccountName)
   - Departamento
   - Cargo (Title)
   - Distinguished Name (DN)

3. **Processa** os usuários em lotes:
   - **Novos usuários**: Cria com senha padrão `Senha@123`
   - **Usuários existentes**: Atualiza nome e departamento
   - **Role padrão**: `customer` (usuário comum)
   - **Status**: Ativo

4. **Gera relatório** com estatísticas:
   - Total encontrado no LDAP
   - Novos importados
   - Atualizados
   - Erros

## 🛡️ Segurança

### Senha Padrão
Usuários importados recebem senha padrão `Senha@123`. Você pode alterá-la em:
`app/Console/Commands/ImportLdapUsers.php` linha ~216:

```php
'password' => bcrypt('Senha@123'),  // <-- Altere aqui
```

### Permissões
- Usuários importados têm `role: 'customer'` (acesso básico)
- Para dar permissões específicas, use a interface de administração

## 🐛 Troubleshooting

### Erro: "Connection could not be established"
- Verifique se o servidor LDAP está acessível
- Teste conectividade: `ping 10.0.0.31`
- Verifique firewall/portas

### Erro: "Invalid credentials"
- Confirme usuário e senha no `.env`
- Teste credenciais manualmente no AD

### Cron não executa
```bash
# Ver se o cron está rodando
service cron status

# Ver logs do cron
grep CRON /var/log/syslog

# Testar comando manualmente
cd /home/elber/sistema-de-chamados && php artisan schedule:run
```

### Usuários duplicados
O sistema verifica por:
- Email duplicado
- DN (Distinguished Name) duplicado

Se houver duplicatas, atualiza o existente ao invés de criar novo.

## 📞 Suporte

Para problemas ou dúvidas:
1. Verifique logs: `storage/logs/laravel.log`
2. Execute em modo dry-run para teste
3. Contate o administrador do sistema
