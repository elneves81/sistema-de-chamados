#!/bin/bash

#####################################
# Instalar Cron para Laravel Scheduler
# Sistema de Chamados - Guarapuava
#####################################

echo "=========================================="
echo "CONFIGURANDO CRON DO LARAVEL"
echo "=========================================="
echo ""

# Verificar se o cron já está configurado
if crontab -l 2>/dev/null | grep -q "sistema-de-chamados/artisan schedule:run"; then
    echo "✓ Cron do Laravel já está configurado!"
    echo ""
    echo "Cron atual:"
    crontab -l | grep "artisan schedule:run"
    exit 0
fi

echo "Configurando cron para executar o Laravel Scheduler..."
echo ""

# Adicionar ao crontab
(crontab -l 2>/dev/null; echo "* * * * * cd /home/elber/sistema-de-chamados && php artisan schedule:run >> /dev/null 2>&1") | crontab -

if [ $? -eq 0 ]; then
    echo "✓ Cron configurado com sucesso!"
    echo ""
    echo "O Laravel Scheduler vai executar:"
    echo "  - Backup completo: Diariamente às 3h"
    echo "  - Backup do banco: A cada 6 horas"
    echo "  - Importação LDAP: Diariamente às 2h"
    echo ""
    echo "Para verificar o cron:"
    echo "  crontab -l"
    echo ""
    echo "Para ver logs de backup:"
    echo "  tail -f /home/elber/sistema-de-chamados/storage/backups/backup.log"
else
    echo "✗ Erro ao configurar cron"
    exit 1
fi

echo "=========================================="
echo "CONFIGURAÇÃO CONCLUÍDA"
echo "=========================================="
