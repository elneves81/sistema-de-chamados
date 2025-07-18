# Sistema de Chamados - Documentação Geral

## Visão Geral
Sistema web robusto para gestão de chamados, inspirado e aprimorado em relação ao GLPI. Possui painel de administração moderno, dashboard com KPIs, filtros, gráficos, painel TV, integração LDAP/Active Directory, ranking de técnicos, timeline, exportação, dark mode e mais.

## Funcionalidades Principais
- **Login moderno e seguro**
- **Dashboard**: KPIs, filtros, gráficos, ranking, timeline, exportação, dark mode
- **Painel TV**: Visualização dinâmica dos chamados para exibição pública
- **Chamados**: CRUD completo, comentários, atribuição, prioridade, status
- **Categorias**: Gerenciamento de categorias de chamados
- **Administração**: Usuários, monitoramento, importação LDAP/AD
- **Importação LDAP/AD**: Busca, pré-visualização e importação de usuários do Active Directory
- **API REST**: Endpoints protegidos para integração

## Estrutura de Pastas
- `app/Http/Controllers/` - Lógica dos controllers (ex: TicketController, LdapImportController)
- `app/Models/` - Models Eloquent (ex: Ticket, User)
- `resources/views/` - Views Blade (ex: dashboard, login, painel TV, LDAP)
- `routes/web.php` - Rotas web (painel, dashboard, LDAP, etc)
- `routes/api.php` - Rotas API REST

## Rotas Importantes
- `/dashboard` - Painel principal
- `/painel-tv` - Painel TV
- `/tickets` - CRUD de chamados
- `/categories` - Categorias
- `/admin/users` - Usuários (admin)
- `/admin/ldap/import` - Importação LDAP/AD (admin)

## Importação LDAP/AD
- Acesse pelo menu lateral (apenas admin)
- Preencha dados do servidor AD (host, porta, usuário DN, senha, base DN)
- Pré-visualize usuários encontrados
- Selecione e importe para o sistema

## API REST
- Endpoints protegidos por Sanctum
- `/api/tickets/all` - Todos os chamados
- `/api/tickets/metrics` - Métricas
- `/api/tickets/dashboard` - Dashboard executivo

## Tecnologias
- Laravel, Blade, Bootstrap, Chart.js, adldap2/adldap2

## Observações
- Para customizações, ajuste as views em `resources/views/`
- Para integrações, use as rotas API
- Para LDAP real, configure corretamente os dados do AD

---

> Para dúvidas, consulte o README ou entre em contato com o administrador do sistema.
