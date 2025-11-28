#!/bin/bash
# Script de gerenciamento do Sistema de Chamados

case "$1" in
    start)
        echo "Iniciando serviços do Sistema de Chamados..."
        sudo systemctl start laravel-server.service
        sudo systemctl start laravel-worker.service
        echo "✓ Serviços iniciados"
        ;;
    stop)
        echo "Parando serviços do Sistema de Chamados..."
        sudo systemctl stop laravel-server.service
        sudo systemctl stop laravel-worker.service
        echo "✓ Serviços parados"
        ;;
    restart)
        echo "Reiniciando serviços do Sistema de Chamados..."
        sudo systemctl restart laravel-server.service
        sudo systemctl restart laravel-worker.service
        echo "✓ Serviços reiniciados"
        ;;
    status)
        echo "=== Status do Servidor Web ==="
        sudo systemctl status laravel-server.service --no-pager -l | head -15
        echo ""
        echo "=== Status do Queue Worker ==="
        sudo systemctl status laravel-worker.service --no-pager -l | head -15
        ;;
    logs-server)
        sudo journalctl -u laravel-server.service -f
        ;;
    logs-worker)
        sudo journalctl -u laravel-worker.service -f
        ;;
    *)
        echo "Sistema de Chamados - Gerenciamento de Serviços"
        echo ""
        echo "Uso: $0 {start|stop|restart|status|logs-server|logs-worker}"
        echo ""
        echo "Comandos:"
        echo "  start        - Inicia os serviços"
        echo "  stop         - Para os serviços"
        echo "  restart      - Reinicia os serviços"
        echo "  status       - Mostra o status dos serviços"
        echo "  logs-server  - Mostra logs do servidor web em tempo real"
        echo "  logs-worker  - Mostra logs do queue worker em tempo real"
        exit 1
        ;;
esac
