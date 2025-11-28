#!/bin/bash
# COMANDOS RÁPIDOS DE BACKUP - Suporte+ Saúde Guarapuava

# ============================================
# CRIAR BACKUPS
# ============================================

# Backup completo (recomendado)
alias backup-completo='cd /home/elber/sistema-de-chamados && php artisan backup:create --full'

# Apenas banco de dados (mais rápido)
alias backup-banco='cd /home/elber/sistema-de-chamados && php artisan backup:create --database-only'

# ============================================
# LISTAR BACKUPS
# ============================================

# Ver todos os backups
alias listar-backups='ls -lh /home/elber/sistema-de-chamados/storage/backups/'

# Ver apenas backups completos
alias listar-backups-completos='ls -lh /home/elber/sistema-de-chamados/storage/backups/sistema-completo_*.zip'

# Ver tamanho total
alias tamanho-backups='du -sh /home/elber/sistema-de-chamados/storage/backups/'

# ============================================
# RESTAURAR BACKUP
# ============================================

# Restaurar interativo (mostra menu)
alias restaurar-backup='cd /home/elber/sistema-de-chamados && php artisan backup:restore'

# ============================================
# VERIFICAR STATUS
# ============================================

# Ver log de backups
alias log-backup='tail -f /home/elber/sistema-de-chamados/storage/backups/backup.log'

# Ver último backup
alias ultimo-backup='ls -lt /home/elber/sistema-de-chamados/storage/backups/ | head -5'

# Verificar cron
alias verificar-cron='crontab -l | grep artisan'

# ============================================
# MENU INTERATIVO
# ============================================

# Abrir menu de backup
alias menu-backup='cd /home/elber/sistema-de-chamados && ./menu-backup.sh'

# ============================================
# INFORMAÇÕES
# ============================================

echo "======================================================"
echo "COMANDOS DE BACKUP CARREGADOS!"
echo "======================================================"
echo ""
echo "Comandos disponíveis:"
echo "  backup-completo          - Criar backup completo"
echo "  backup-banco             - Criar backup do banco"
echo "  listar-backups           - Listar todos os backups"
echo "  listar-backups-completos - Listar backups completos"
echo "  tamanho-backups          - Ver espaço usado"
echo "  restaurar-backup         - Restaurar backup"
echo "  log-backup               - Ver log em tempo real"
echo "  ultimo-backup            - Ver último backup"
echo "  verificar-cron           - Verificar agendamento"
echo "  menu-backup              - Menu interativo"
echo ""
echo "Para carregar automaticamente, adicione ao ~/.bashrc:"
echo "  source /home/elber/sistema-de-chamados/comandos-backup.sh"
echo "======================================================"
