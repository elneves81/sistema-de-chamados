# Sistema de Permissões Granulares

## Visão Geral

O sistema agora conta com um controle de permissões granular que substitui o sistema simples baseado apenas em roles (customer, technician, admin). 

## Funcionalidades Implementadas

### 1. Super Administrador
- **Usuário**: superadmin@sistema.com
- **Senha**: SuperAdmin@123 (altere após o primeiro login)
- **Características**:
  - Tem acesso total a todas as funcionalidades
  - Pode gerenciar permissões de outros usuários
  - Não pode ter suas próprias permissões editadas

### 2. Sistema de Permissões

#### Permissões de Tickets
- `tickets.view.own` - Ver apenas próprios chamados
- `tickets.view.all` - Ver todos os chamados
- `tickets.create` - Criar novos chamados
- `tickets.edit.own` - Editar próprios chamados
- `tickets.edit.all` - Editar qualquer chamado
- `tickets.assign` - Atribuir chamados a técnicos
- `tickets.close` - Fechar chamados

#### Permissões de Dashboard
- `dashboard.view` - Acessar dashboard principal
- `dashboard.metrics` - Ver métricas e relatórios
- `dashboard.export` - Exportar relatórios

#### Permissões de Painel TV
- `board.view` - Acessar painel de monitoramento TV

#### Permissões de Usuários
- `users.view` - Ver lista de usuários
- `users.create` - Criar novos usuários
- `users.edit` - Editar usuários
- `users.delete` - Excluir usuários
- `users.permissions` - Gerenciar permissões (apenas super admin)

#### Permissões de Categorias
- `categories.view` - Ver categorias
- `categories.manage` - Criar, editar e excluir categorias

#### Permissões de Sistema
- `system.ldap` - Gerenciar integração LDAP/AD
- `system.monitoring` - Acessar monitoramento do sistema

### 3. Permissões Padrão por Role

#### Customer (Cliente)
- Ver apenas próprios chamados
- Criar chamados
- Editar próprios chamados

#### Technician (Técnico)
- Ver todos os chamados
- Criar e editar chamados
- Atribuir e fechar chamados
- Acessar dashboard e métricas
- Ver categorias

#### Admin (Administrador)
- Acesso completo a tickets
- Dashboard com exportação
- Painel TV
- Gerenciar usuários
- Gerenciar categorias
- Configurações de sistema

## Como Usar

### 1. Acessar Gerenciamento de Permissões
1. Faça login como super administrador
2. Vá para "Administração" > "Permissões"
3. Selecione o usuário que deseja configurar

### 2. Configurar Permissões
1. Marque/desmarque as permissões desejadas
2. Use "Aplicar Permissões Padrão" para resetar com base no role
3. Salve as alterações

### 3. Comandos Úteis

```bash
# Aplicar permissões padrão para todos os usuários
php artisan permissions:apply-default

# Criar super admin
php artisan db:seed --class=SuperAdminSeeder

# Popular permissões no banco
php artisan db:seed --class=PermissionSeeder
```

## Compatibilidade

O sistema mantém compatibilidade com o código anterior baseado em roles. Se um usuário não tem permissões específicas configuradas, o sistema fallback para as permissões baseadas no role.

## Segurança

- Usuários cliente só podem ver seus próprios chamados por padrão
- Super administradores não podem ter suas permissões alteradas
- O painel TV agora requer permissão específica
- Dashboard e funcionalidades administrativas são protegidas

## Migração

Para usuários existentes, execute:
```bash
php artisan permissions:apply-default
```

Isso aplicará as permissões padrão baseadas nos roles atuais de cada usuário.
