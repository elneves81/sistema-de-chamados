# Sistema de Backup - Suporte+ Saúde

## 📋 Visão Geral

Sistema completo de backup e restauração para o Sistema de Chamados da Secretaria Municipal de Saúde de Guarapuava.

## 🔧 Comandos Disponíveis

### Criar Backup

```bash
# Backup completo (banco de dados + arquivos)
php artisan backup:create --full

# Apenas banco de dados (mais rápido)
php artisan backup:create --database-only

# Usando o script bash
./backup-automatico.sh
```

### Restaurar Backup

```bash
# Restaurar interativamente (mostra lista de backups)
php artisan backup:restore

# Restaurar arquivo específico
php artisan backup:restore storage/backups/sistema-completo_2025-11-10_14-00-00.zip

# Restaurar apenas banco de dados
php artisan backup:restore storage/backups/database_2025-11-10_14-00-00.sql.gz --database-only

# Restaurar apenas arquivos
php artisan backup:restore storage/backups/files_2025-11-10_14-00-00.zip --files-only
```

## 📁 Localização dos Backups

Todos os backups são salvos em:
```
/home/elber/sistema-de-chamados/storage/backups/
```

### Tipos de Backup Gerados

1. **database_YYYY-MM-DD_HH-mm-ss.sql.gz** - Backup comprimido do banco de dados
2. **files_YYYY-MM-DD_HH-mm-ss.zip** - Backup de arquivos importantes
3. **sistema-completo_YYYY-MM-DD_HH-mm-ss.zip** - Backup completo (banco + arquivos)

## ⏰ Backup Automático

### Configurar Backup Diário (às 3h da manhã)

```bash
# Editar crontab
crontab -e

# Adicionar esta linha:
0 3 * * * /home/elber/sistema-de-chamados/backup-automatico.sh >> /home/elber/sistema-de-chamados/storage/backups/cron.log 2>&1
```

### Configurar Backup a Cada 6 Horas

```bash
0 */6 * * * /home/elber/sistema-de-chamados/backup-automatico.sh >> /home/elber/sistema-de-chamados/storage/backups/cron.log 2>&1
```

### Verificar Cron Configurado

```bash
crontab -l
```

## 🔄 Retenção de Backups

O sistema mantém automaticamente os backups dos **últimos 7 dias** e remove os mais antigos.

Para alterar o período de retenção, edite o arquivo:
```bash
app/Console/Commands/BackupSystem.php
# Linha: $this->cleanOldBackups($backupDir, 7);
```

## 🚨 Procedimento de Emergência

### Em Caso de Problemas no Sistema

1. **Parar serviços:**
```bash
sudo systemctl stop nginx
sudo systemctl stop php8.2-fpm
```

2. **Restaurar backup:**
```bash
cd /home/elber/sistema-de-chamados
php artisan backup:restore
```

3. **Reiniciar serviços:**
```bash
php artisan optimize:clear
sudo systemctl start php8.2-fpm
sudo systemctl start nginx
```

4. **Verificar funcionamento:**
```bash
php artisan tinker --execute="echo 'Sistema OK: ' . App\Models\User::count() . ' usuários'"
```

## 📊 Monitoramento

### Verificar Backups Criados

```bash
ls -lh /home/elber/sistema-de-chamados/storage/backups/
```

### Verificar Tamanho dos Backups

```bash
du -sh /home/elber/sistema-de-chamados/storage/backups/
```

### Verificar Último Backup

```bash
ls -lt /home/elber/sistema-de-chamados/storage/backups/ | head -n 5
```

### Ver Log de Backups Automáticos

```bash
tail -f /home/elber/sistema-de-chamados/storage/backups/backup.log
```

## 🔐 Backup Remoto (Opcional)

### Configurar Sincronização com Servidor Remoto

Edite o arquivo `backup-automatico.sh` e descomente as linhas:

```bash
# Sincronizar com servidor remoto
rsync -avz --progress "$BACKUP_DIR"/*.zip user@backup-server:/backups/sistema-chamados/
```

Substitua:
- `user` - usuário do servidor remoto
- `backup-server` - endereço do servidor
- `/backups/sistema-chamados/` - diretório no servidor remoto

### Configurar SSH sem senha (para rsync automático)

```bash
# Gerar chave SSH
ssh-keygen -t rsa -b 4096

# Copiar chave para servidor remoto
ssh-copy-id user@backup-server
```

## 💾 O Que é Incluído no Backup

### Banco de Dados
- ✅ Todas as tabelas
- ✅ Dados completos (usuários, tickets, localizações, etc)
- ✅ Estrutura das tabelas
- ✅ Índices e relacionamentos

### Arquivos (Backup Completo)
- ✅ Uploads do sistema (`storage/app/public`)
- ✅ Arquivos públicos (`public/uploads`)
- ✅ Configurações (`.env`)
- ✅ Dependências (`composer.json`, `package.json`)

## 🎯 Boas Práticas

1. **Backup Diário**: Configure cron para backup diário automático
2. **Teste Restauração**: Teste a restauração pelo menos 1x por mês
3. **Backup Remoto**: Configure sincronização com servidor externo
4. **Monitore Espaço**: Verifique regularmente o espaço em disco
5. **Documente**: Mantenha registro de quando restaurações foram feitas

## 📞 Suporte

Em caso de dúvidas ou problemas:
- Email: dtisaude@guarapuava.pr.gov.br
- Telefone: (42) 3142-1512

## 📝 Histórico de Versões

- **v1.0** (2025-11-10): Sistema inicial de backup/restore
  - Backup completo de banco de dados
  - Backup de arquivos importantes
  - Restauração interativa
  - Script de automação
  - Limpeza automática de backups antigos
