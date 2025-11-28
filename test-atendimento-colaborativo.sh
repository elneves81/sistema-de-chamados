#!/bin/bash

# ============================================
# TESTE DO SISTEMA DE ATENDIMENTO COLABORATIVO
# ============================================

echo "🧪 Iniciando testes do sistema de atendimento colaborativo..."
echo ""

# Cores para output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# 1. Verificar se a migração foi executada
echo -n "✓ Verificando migração... "
COLUMN_EXISTS=$(mysql -u root -p -e "SHOW COLUMNS FROM tickets LIKE 'support_technician_id';" 2>/dev/null | wc -l)
if [ $COLUMN_EXISTS -gt 1 ]; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALHOU${NC}"
    echo "  Coluna support_technician_id não encontrada"
fi

# 2. Verificar se o índice foi criado
echo -n "✓ Verificando índice... "
INDEX_EXISTS=$(mysql -u root -p -e "SHOW INDEX FROM tickets WHERE Key_name LIKE '%support%';" 2>/dev/null | wc -l)
if [ $INDEX_EXISTS -gt 1 ]; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${YELLOW}AVISO${NC}"
    echo "  Índice não encontrado (pode não ser crítico)"
fi

# 3. Verificar rotas
echo -n "✓ Verificando rotas... "
cd /home/elber/sistema-de-chamados
ROUTES=$(php artisan route:list | grep -c "tickets.support")
if [ $ROUTES -eq 2 ]; then
    echo -e "${GREEN}OK${NC} ($ROUTES rotas encontradas)"
else
    echo -e "${RED}FALHOU${NC}"
    echo "  Esperado: 2 rotas, Encontrado: $ROUTES"
fi

# 4. Verificar Model
echo -n "✓ Verificando Model Ticket... "
if grep -q "support_technician_id" app/Models/Ticket.php && grep -q "supportTechnician" app/Models/Ticket.php; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALHOU${NC}"
    echo "  Model não contém as modificações necessárias"
fi

# 5. Verificar Controller
echo -n "✓ Verificando Controller... "
if grep -q "assignSupportTechnician" app/Http/Controllers/TicketController.php && grep -q "removeSupportTechnician" app/Http/Controllers/TicketController.php; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALHOU${NC}"
    echo "  Controller não contém os métodos necessários"
fi

# 6. Verificar Event
echo -n "✓ Verificando Event... "
if [ -f "app/Events/SupportTechnicianAssigned.php" ]; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALHOU${NC}"
    echo "  Arquivo app/Events/SupportTechnicianAssigned.php não encontrado"
fi

# 7. Verificar Listener
echo -n "✓ Verificando Listener... "
if [ -f "app/Listeners/SendSupportTechnicianNotification.php" ]; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALHOU${NC}"
    echo "  Arquivo app/Listeners/SendSupportTechnicianNotification.php não encontrado"
fi

# 8. Verificar View
echo -n "✓ Verificando View... "
if grep -q "supportTechnician" resources/views/tickets/show.blade.php && grep -q "supportTechnicianModal" resources/views/tickets/show.blade.php; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALHOU${NC}"
    echo "  View não contém as modificações necessárias"
fi

# 9. Verificar EventServiceProvider
echo -n "✓ Verificando EventServiceProvider... "
if grep -q "SupportTechnicianAssigned" app/Providers/EventServiceProvider.php; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALHOU${NC}"
    echo "  Evento não registrado no EventServiceProvider"
fi

# 10. Verificar se não há erros de sintaxe
echo -n "✓ Verificando sintaxe PHP... "
php -l app/Models/Ticket.php > /dev/null 2>&1
MODEL_SYNTAX=$?
php -l app/Http/Controllers/TicketController.php > /dev/null 2>&1
CONTROLLER_SYNTAX=$?
php -l app/Events/SupportTechnicianAssigned.php > /dev/null 2>&1
EVENT_SYNTAX=$?
php -l app/Listeners/SendSupportTechnicianNotification.php > /dev/null 2>&1
LISTENER_SYNTAX=$?

if [ $MODEL_SYNTAX -eq 0 ] && [ $CONTROLLER_SYNTAX -eq 0 ] && [ $EVENT_SYNTAX -eq 0 ] && [ $LISTENER_SYNTAX -eq 0 ]; then
    echo -e "${GREEN}OK${NC}"
else
    echo -e "${RED}FALHOU${NC}"
    echo "  Erros de sintaxe encontrados"
fi

echo ""
echo "================================================"
echo "✅ Testes concluídos!"
echo "================================================"
echo ""
echo "📝 Próximos passos:"
echo "1. Acesse um chamado existente"
echo "2. Teste adicionar um técnico de suporte"
echo "3. Verifique se a notificação foi enviada"
echo "4. Teste remover o técnico de suporte"
echo ""
echo "📚 Documentação disponível em:"
echo "  - ATENDIMENTO_COLABORATIVO.md"
echo "  - GUIA_ATENDIMENTO_COLABORATIVO.md"
echo "  - CONSULTAS_ATENDIMENTO_COLABORATIVO.sql"
echo ""
