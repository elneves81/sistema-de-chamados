#!/bin/bash

#####################################
# Script de Backup Automático
# Sistema de Chamados - Guarapuava
#####################################

# Configurações
APP_DIR="/home/elber/sistema-de-chamados"
BACKUP_DIR="$APP_DIR/storage/backups"
LOG_FILE="$BACKUP_DIR/backup.log"
RETENTION_DAYS=7

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Função de log
log() {
    echo "[$(date '+%Y-%m-%d %H:%M:%S')] $1" | tee -a "$LOG_FILE"
}

log_success() {
    echo -e "${GREEN}[$(date '+%Y-%m-%d %H:%M:%S')] ✓ $1${NC}" | tee -a "$LOG_FILE"
}

log_error() {
    echo -e "${RED}[$(date '+%Y-%m-%d %H:%M:%S')] ✗ $1${NC}" | tee -a "$LOG_FILE"
}

log_warning() {
    echo -e "${YELLOW}[$(date '+%Y-%m-%d %H:%M:%S')] ⚠ $1${NC}" | tee -a "$LOG_FILE"
}

# Criar diretório de backups se não existir
mkdir -p "$BACKUP_DIR"

# Início do backup
log "=========================================="
log "INICIANDO BACKUP AUTOMÁTICO"
log "=========================================="

# Ir para o diretório da aplicação
cd "$APP_DIR" || {
    log_error "Não foi possível acessar o diretório: $APP_DIR"
    exit 1
}

# Executar comando de backup
log "Executando backup completo do sistema..."

php artisan backup:create --full

if [ $? -eq 0 ]; then
    log_success "Backup completo criado com sucesso"
else
    log_error "Falha ao criar backup completo"
    
    # Tentar apenas banco de dados
    log_warning "Tentando backup apenas do banco de dados..."
    php artisan backup:create --database-only
    
    if [ $? -eq 0 ]; then
        log_success "Backup do banco de dados criado com sucesso"
    else
        log_error "Falha ao criar backup do banco de dados"
        exit 1
    fi
fi

# Listar backups criados hoje
log ""
log "Backups criados hoje:"
find "$BACKUP_DIR" -name "*.zip" -o -name "*.sql" -o -name "*.gz" | while read -r file; do
    if [ -f "$file" ]; then
        size=$(du -h "$file" | cut -f1)
        log "  - $(basename "$file") [$size]"
    fi
done

# Espaço em disco
log ""
log "Uso do disco no diretório de backups:"
du -sh "$BACKUP_DIR" | tee -a "$LOG_FILE"

# Opcional: Sincronizar com servidor remoto (descomente e configure)
# log ""
# log "Sincronizando backups com servidor remoto..."
# rsync -avz --progress "$BACKUP_DIR"/*.zip user@backup-server:/backups/sistema-chamados/
# if [ $? -eq 0 ]; then
#     log_success "Backups sincronizados com servidor remoto"
# else
#     log_error "Falha ao sincronizar com servidor remoto"
# fi

log "=========================================="
log "BACKUP CONCLUÍDO"
log "=========================================="

exit 0
