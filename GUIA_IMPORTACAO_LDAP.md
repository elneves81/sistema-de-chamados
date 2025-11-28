# 🔐 Guia de Importação LDAP em Lotes

## ⚠️ Problema Atual: Erro 401 (Não Autenticado)

O erro `401 Unauthorized` indica que você precisa **fazer login primeiro**.

### 📝 Passo 1: Fazer Login

1. Acesse: http://127.0.0.1:8001 (ou 8000)
2. Use as credenciais:
   - **Email:** `admin@admin.com`
   - **Senha:** `admin123`

### 🔧 Passo 2: Acessar a Importação LDAP

Após fazer login:
1. Vá para: **Admin → Importação LDAP**
2. Ou acesse diretamente: http://127.0.0.1:8001/admin/ldap/import

---

## 📊 Como Fazer Importação em Lotes

### Configurações LDAP (já estão no .env):

```
Host: 10.0.0.31
Porta: 389
Base DN: DC=guarapuava,DC=pr,DC=gov,DC=br
Usuário: CN=Elber Luiz Neves,OU=DGSIS,OU=Secretaria de Saude,OU=Usuarios,OU=PMG,DC=guarapuava,DC=pr,DC=gov,DC=br
Senha: elber@2023
SSL: Desabilitado
```

### 🎯 Opções de Importação:

#### Opção 1: Importação Direta (até 2000 usuários)
1. Preencha os campos LDAP
2. Clique em "Testar Conexão" (deve estar logado!)
3. Defina o limite (ex: 500, 1000, 2000)
4. Clique em "Buscar Preview"
5. Selecione os usuários desejados
6. Clique em "Importar Selecionados"

#### Opção 2: Importação em Lotes (para milhares de usuários)
1. Use o botão "Importação em Lotes"
2. Configure:
   - Tamanho do lote: 100-500 usuários por lote
   - Total de usuários a importar
3. O sistema processará em background usando Jobs

---

## 🚀 Importação Via Linha de Comando (RECOMENDADO para muitos usuários)

### Método 1: Comando Artisan Direto

```bash
# Importar todos os usuários do LDAP
php artisan ldap:sync-users

# Importar com limite
php artisan ldap:sync-users --limit=1000

# Importar com filtro
php artisan ldap:sync-users --filter="nome_parcial"

# Modo dry-run (simular sem importar)
php artisan ldap:sync-users --dry-run
```

### Método 2: Executar o Worker de Fila

```bash
# Terminal 1: Iniciar o worker de fila
php artisan queue:work --queue=ldap-import --tries=3

# Terminal 2: Disparar a importação em lotes
php artisan ldap:bulk-import --batch-size=500
```

---

## 🔍 Monitoramento da Importação

### Ver Logs em Tempo Real:

```bash
tail -f storage/logs/laravel.log | grep LDAP
```

### Verificar Jobs na Fila:

```bash
# Ver jobs pendentes
php artisan queue:work --once

# Limpar jobs falhados
php artisan queue:flush

# Ver status dos jobs
php artisan queue:failed
```

---

## 💡 Dicas Importantes

### Para Evitar Problemas:

1. **Sempre teste a conexão primeiro** (com login feito)
2. **Comece com lotes pequenos** (100-200 usuários)
3. **Use filtros** para importar departamentos específicos
4. **Monitore o uso de memória** para lotes grandes

### Configurações Recomendadas:

```env
# No .env, ajuste se necessário:
QUEUE_CONNECTION=database
LDAP_TIMEOUT=10
LDAP_CACHE_ENABLED=true
```

### Limites Sugeridos:

- **Preview**: 500-1000 usuários
- **Importação Direta**: até 2000 usuários
- **Importação em Lotes**: 100-500 por lote
- **Linha de Comando**: sem limite (recomendado para 5000+)

---

## 🐛 Solução de Problemas

### Erro 401 - Não Autenticado
✅ **Solução**: Faça login no sistema antes de testar a conexão

### Timeout na Importação
✅ **Solução**: Reduza o tamanho do lote ou use linha de comando

### Memória Insuficiente
✅ **Solução**: Aumente `memory_limit` no php.ini ou use lotes menores

### Credenciais LDAP Inválidas
✅ **Solução**: Verifique o formato do usuário:
   - Tente: `elber.neves` (samAccountName)
   - Ou: `elber.neves@guarapuava.pr.gov.br` (UPN)
   - Ou use o DN completo (já configurado)

---

## 📞 Próximos Passos

1. ✅ Faça login no sistema
2. ✅ Teste a conexão LDAP
3. ✅ Faça um preview com 100 usuários
4. ✅ Importe um lote pequeno de teste
5. ✅ Se tudo estiver OK, importe o resto em lotes

---

## 🔧 Comandos Úteis Rápidos

```bash
# Login como admin (se necessário resetar senha)
php artisan tinker
>>> $user = App\Models\User::where('email', 'admin@admin.com')->first();
>>> $user->password = bcrypt('admin123');
>>> $user->save();

# Limpar cache
php artisan cache:clear
php artisan config:clear

# Ver usuários importados
php artisan tinker
>>> App\Models\User::count();

# Excluir usuários de teste (CUIDADO!)
>>> App\Models\User::where('email', 'LIKE', '%ldap%')->delete();
```

---

**Lembre-se**: O erro 401 significa que você precisa estar **LOGADO** no sistema! 🔐
