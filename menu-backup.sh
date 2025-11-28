#!/bin/bash

#####################################
# Menu de Backup e Restauração
# Sistema de Chamados - Guarapuava
#####################################

BACKUP_DIR="/home/elber/sistema-de-chamados/storage/backups"
APP_DIR="/home/elber/sistema-de-chamados"

# Cores
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

clear

echo -e "${BLUE}=========================================="
echo "   SISTEMA DE BACKUP E RESTAURAÇÃO"
echo "   Suporte+ Saúde - Guarapuava PR"
echo "==========================================${NC}"
echo ""

# Função para criar backup
fazer_backup() {
    echo -e "${YELLOW}Escolha o tipo de backup:${NC}"
    echo "  1) Backup completo (banco + arquivos)"
    echo "  2) Apenas banco de dados (mais rápido)"
    echo "  0) Voltar"
    echo ""
    read -p "Opção: " opcao
    
    case $opcao in
        1)
            echo -e "${GREEN}Criando backup completo...${NC}"
            cd "$APP_DIR" && php artisan backup:create --full
            ;;
        2)
            echo -e "${GREEN}Criando backup do banco de dados...${NC}"
            cd "$APP_DIR" && php artisan backup:create --database-only
            ;;
        0)
            return
            ;;
        *)
            echo -e "${RED}Opção inválida!${NC}"
            ;;
    esac
    
    echo ""
    read -p "Pressione ENTER para continuar..."
}

# Função para listar backups
listar_backups() {
    echo -e "${BLUE}Backups disponíveis:${NC}"
    echo ""
    
    if [ ! -d "$BACKUP_DIR" ]; then
        echo -e "${RED}Diretório de backups não encontrado!${NC}"
        return
    fi
    
    # Backups completos
    echo -e "${GREEN}=== Backups Completos ===${NC}"
    ls -lh "$BACKUP_DIR"/sistema-completo_*.zip 2>/dev/null | awk '{print $9, "-", $5, "-", $6, $7, $8}' || echo "Nenhum backup completo"
    echo ""
    
    # Backups de banco
    echo -e "${GREEN}=== Backups do Banco de Dados ===${NC}"
    ls -lh "$BACKUP_DIR"/database_*.sql.gz 2>/dev/null | awk '{print $9, "-", $5, "-", $6, $7, $8}' || echo "Nenhum backup de banco"
    echo ""
    
    # Backups de arquivos
    echo -e "${GREEN}=== Backups de Arquivos ===${NC}"
    ls -lh "$BACKUP_DIR"/files_*.zip 2>/dev/null | awk '{print $9, "-", $5, "-", $6, $7, $8}' || echo "Nenhum backup de arquivos"
    echo ""
    
    # Espaço usado
    echo -e "${YELLOW}Espaço total usado por backups:${NC}"
    du -sh "$BACKUP_DIR" 2>/dev/null || echo "0 B"
    echo ""
    
    read -p "Pressione ENTER para continuar..."
}

# Função para restaurar backup
restaurar_backup() {
    echo -e "${RED}=========================================="
    echo "   ATENÇÃO: RESTAURAÇÃO DE BACKUP"
    echo "==========================================${NC}"
    echo ""
    echo -e "${YELLOW}Esta operação irá SOBRESCREVER os dados atuais!${NC}"
    echo ""
    read -p "Deseja continuar? (sim/não): " confirma
    
    if [ "$confirma" != "sim" ]; then
        echo "Operação cancelada."
        return
    fi
    
    echo ""
    cd "$APP_DIR" && php artisan backup:restore
    
    echo ""
    read -p "Pressione ENTER para continuar..."
}

# Função para verificar status
verificar_status() {
    echo -e "${BLUE}Status do Sistema de Backup${NC}"
    echo ""
    
    # Verificar cron
    echo -e "${YELLOW}Cron configurado:${NC}"
    if crontab -l 2>/dev/null | grep -q "artisan schedule:run"; then
        echo -e "${GREEN}✓ Sim${NC}"
        crontab -l | grep "artisan schedule:run"
    else
        echo -e "${RED}✗ Não configurado${NC}"
        echo "Execute: ./instalar-cron.sh"
    fi
    echo ""
    
    # Último backup
    echo -e "${YELLOW}Último backup completo:${NC}"
    ultimo=$(ls -t "$BACKUP_DIR"/sistema-completo_*.zip 2>/dev/null | head -1)
    if [ -n "$ultimo" ]; then
        echo -e "${GREEN}$(basename "$ultimo")${NC}"
        echo "Data: $(stat -c %y "$ultimo" | cut -d'.' -f1)"
        echo "Tamanho: $(du -h "$ultimo" | cut -f1)"
    else
        echo -e "${RED}Nenhum backup completo encontrado${NC}"
    fi
    echo ""
    
    # Último backup de banco
    echo -e "${YELLOW}Último backup do banco:${NC}"
    ultimo_db=$(ls -t "$BACKUP_DIR"/database_*.sql.gz 2>/dev/null | head -1)
    if [ -n "$ultimo_db" ]; then
        echo -e "${GREEN}$(basename "$ultimo_db")${NC}"
        echo "Data: $(stat -c %y "$ultimo_db" | cut -d'.' -f1)"
        echo "Tamanho: $(du -h "$ultimo_db" | cut -f1)"
    else
        echo -e "${RED}Nenhum backup de banco encontrado${NC}"
    fi
    echo ""
    
    # Total de backups
    echo -e "${YELLOW}Total de backups:${NC}"
    total=$(find "$BACKUP_DIR" -type f \( -name "*.zip" -o -name "*.sql.gz" \) 2>/dev/null | wc -l)
    echo "$total arquivos"
    echo ""
    
    read -p "Pressione ENTER para continuar..."
}

# Menu principal
while true; do
    clear
    echo -e "${BLUE}=========================================="
    echo "   MENU DE BACKUP E RESTAURAÇÃO"
    echo "==========================================${NC}"
    echo ""
    echo "  1) Criar backup"
    echo "  2) Listar backups"
    echo "  3) Restaurar backup"
    echo "  4) Verificar status"
    echo "  5) Ver log de backups"
    echo "  0) Sair"
    echo ""
    read -p "Escolha uma opção: " opcao
    
    case $opcao in
        1)
            fazer_backup
            ;;
        2)
            listar_backups
            ;;
        3)
            restaurar_backup
            ;;
        4)
            verificar_status
            ;;
        5)
            echo ""
            if [ -f "$BACKUP_DIR/backup.log" ]; then
                tail -50 "$BACKUP_DIR/backup.log"
            else
                echo "Nenhum log encontrado"
            fi
            echo ""
            read -p "Pressione ENTER para continuar..."
            ;;
        0)
            echo -e "${GREEN}Até logo!${NC}"
            exit 0
            ;;
        *)
            echo -e "${RED}Opção inválida!${NC}"
            sleep 1
            ;;
    esac
done
