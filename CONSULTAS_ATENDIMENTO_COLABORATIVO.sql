-- ============================================
-- CONSULTAS ÚTEIS - ATENDIMENTO COLABORATIVO
-- ============================================

-- 1️⃣ Ver todos os chamados com técnicos de suporte
SELECT 
    t.id,
    t.title,
    t.status,
    t.priority,
    u_solicitante.name AS solicitante,
    u_principal.name AS tecnico_principal,
    u_suporte.name AS tecnico_suporte,
    t.created_at
FROM tickets t
LEFT JOIN users u_solicitante ON t.user_id = u_solicitante.id
LEFT JOIN users u_principal ON t.assigned_to = u_principal.id
LEFT JOIN users u_suporte ON t.support_technician_id = u_suporte.id
WHERE t.support_technician_id IS NOT NULL
ORDER BY t.created_at DESC;

-- 2️⃣ Estatísticas de atendimentos colaborativos
SELECT 
    COUNT(*) as total_colaborativo,
    COUNT(CASE WHEN status = 'resolved' THEN 1 END) as resolvidos,
    COUNT(CASE WHEN status = 'in_progress' THEN 1 END) as em_andamento,
    ROUND(AVG(resolution_time), 2) as tempo_medio_resolucao
FROM tickets
WHERE support_technician_id IS NOT NULL;

-- 3️⃣ Técnicos que mais atuam como suporte
SELECT 
    u.name,
    u.email,
    COUNT(t.id) as vezes_como_suporte,
    COUNT(CASE WHEN t.status = 'resolved' THEN 1 END) as chamados_resolvidos
FROM users u
JOIN tickets t ON u.id = t.support_technician_id
GROUP BY u.id, u.name, u.email
ORDER BY vezes_como_suporte DESC;

-- 4️⃣ Duplas que mais trabalham juntas
SELECT 
    u_principal.name AS tecnico_principal,
    u_suporte.name AS tecnico_suporte,
    COUNT(*) as chamados_juntos,
    COUNT(CASE WHEN t.status = 'resolved' THEN 1 END) as resolvidos
FROM tickets t
JOIN users u_principal ON t.assigned_to = u_principal.id
JOIN users u_suporte ON t.support_technician_id = u_suporte.id
GROUP BY u_principal.id, u_principal.name, u_suporte.id, u_suporte.name
ORDER BY chamados_juntos DESC;

-- 5️⃣ Histórico de mudanças de técnico de suporte
SELECT 
    tal.id,
    tal.ticket_id,
    tal.action,
    tal.description,
    u_autor.name as quem_fez,
    u_alvo.name as afetado,
    tal.created_at
FROM ticket_activity_logs tal
LEFT JOIN users u_autor ON tal.user_id = u_autor.id
LEFT JOIN users u_alvo ON tal.target_user_id = u_alvo.id
WHERE tal.action IN ('support_assigned', 'support_changed', 'support_removed')
ORDER BY tal.created_at DESC;

-- 6️⃣ Chamados ativos com técnico de suporte
SELECT 
    t.id,
    t.title,
    t.priority,
    c.name as categoria,
    u_principal.name as tecnico_principal,
    u_suporte.name as tecnico_suporte,
    t.created_at,
    TIMESTAMPDIFF(HOUR, t.created_at, NOW()) as horas_abertas
FROM tickets t
LEFT JOIN categories c ON t.category_id = c.id
LEFT JOIN users u_principal ON t.assigned_to = u_principal.id
LEFT JOIN users u_suporte ON t.support_technician_id = u_suporte.id
WHERE t.support_technician_id IS NOT NULL
  AND t.status NOT IN ('resolved', 'closed')
ORDER BY t.priority DESC, t.created_at ASC;

-- 7️⃣ Performance: Colaborativo vs Individual
SELECT 
    'Com Suporte' as tipo,
    COUNT(*) as total,
    ROUND(AVG(resolution_time), 2) as tempo_medio,
    MIN(resolution_time) as tempo_minimo,
    MAX(resolution_time) as tempo_maximo
FROM tickets
WHERE support_technician_id IS NOT NULL AND resolution_time IS NOT NULL

UNION ALL

SELECT 
    'Individual' as tipo,
    COUNT(*) as total,
    ROUND(AVG(resolution_time), 2) as tempo_medio,
    MIN(resolution_time) as tempo_minimo,
    MAX(resolution_time) as tempo_maximo
FROM tickets
WHERE support_technician_id IS NULL AND resolution_time IS NOT NULL;

-- 8️⃣ Detalhes completos de um chamado colaborativo
SELECT 
    t.id,
    t.title,
    t.description,
    t.status,
    t.priority,
    u_solicitante.name AS solicitante,
    u_solicitante.email AS email_solicitante,
    u_principal.name AS tecnico_principal,
    u_principal.email AS email_principal,
    u_suporte.name AS tecnico_suporte,
    u_suporte.email AS email_suporte,
    c.name AS categoria,
    l.name AS localizacao,
    t.created_at,
    t.updated_at,
    t.resolved_at,
    t.resolution_time
FROM tickets t
LEFT JOIN users u_solicitante ON t.user_id = u_solicitante.id
LEFT JOIN users u_principal ON t.assigned_to = u_principal.id
LEFT JOIN users u_suporte ON t.support_technician_id = u_suporte.id
LEFT JOIN categories c ON t.category_id = c.id
LEFT JOIN locations l ON t.location_id = l.id
WHERE t.id = 1; -- Substitua pelo ID do chamado

-- 9️⃣ Tickets sem suporte que poderiam precisar (alta prioridade, muito tempo aberto)
SELECT 
    t.id,
    t.title,
    t.priority,
    u_principal.name as tecnico_principal,
    TIMESTAMPDIFF(HOUR, t.created_at, NOW()) as horas_abertas
FROM tickets t
LEFT JOIN users u_principal ON t.assigned_to = u_principal.id
WHERE t.support_technician_id IS NULL
  AND t.status IN ('open', 'in_progress')
  AND t.priority IN ('high', 'urgent')
  AND TIMESTAMPDIFF(HOUR, t.created_at, NOW()) > 24
ORDER BY t.priority DESC, horas_abertas DESC;

-- 🔟 Atividade de suporte por período
SELECT 
    DATE(tal.created_at) as data,
    COUNT(CASE WHEN tal.action = 'support_assigned' THEN 1 END) as atribuicoes,
    COUNT(CASE WHEN tal.action = 'support_removed' THEN 1 END) as remocoes,
    COUNT(CASE WHEN tal.action = 'support_changed' THEN 1 END) as trocas
FROM ticket_activity_logs tal
WHERE tal.action IN ('support_assigned', 'support_changed', 'support_removed')
  AND tal.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY DATE(tal.created_at)
ORDER BY data DESC;

-- ============================================
-- COMANDOS DE MANUTENÇÃO
-- ============================================

-- Verificar integridade dos relacionamentos
SELECT 
    COUNT(*) as tickets_com_suporte_invalido
FROM tickets t
LEFT JOIN users u ON t.support_technician_id = u.id
WHERE t.support_technician_id IS NOT NULL 
  AND u.id IS NULL;

-- Listar chamados onde suporte = principal (não deveria acontecer)
SELECT 
    t.id,
    t.title,
    u.name
FROM tickets t
JOIN users u ON t.assigned_to = u.id AND t.support_technician_id = u.id;

-- Ver todos os índices relacionados
SHOW INDEX FROM tickets WHERE Key_name LIKE '%support%';
