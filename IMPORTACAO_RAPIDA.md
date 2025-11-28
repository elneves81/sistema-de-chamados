# 🚀 Guia Rápido - Importação LDAP

## ✅ Conexão Testada com Sucesso!

**Servidor LDAP:** 10.0.0.200:389

---

## 📋 Comandos Disponíveis

### 1️⃣ Importar POUCOS usuários (teste)
```bash
# Simular importação de 50 usuários
php artisan ldap:import-users --dry-run --limit=50

# Importar de verdade (50 usuários)
php artisan ldap:import-users --limit=50
```

### 2️⃣ Importar MUITOS usuários (em lotes)
```bash
# Simular 500 usuários em lotes de 100
php artisan ldap:import-users --dry-run --limit=500 --batch-size=100

# Importar de verdade 500 usuários
php artisan ldap:import-users --limit=500 --batch-size=100

# Importar TODOS os usuários (sem limite)
php artisan ldap:import-users --batch-size=100
```

### 3️⃣ Importar com FILTRO (buscar nomes específicos)
```bash
# Buscar e importar apenas usuários com "silva" no nome
php artisan ldap:import-users --filter=silva --dry-run

# Buscar e importar departamento específico
php artisan ldap:import-users --filter=TI --limit=100
```

---

## 🎯 Recomendações

### Para seu caso (muitos usuários):

**PASSO 1: Teste pequeno (10 usuários)**
```bash
php artisan ldap:import-users --limit=10
```

**PASSO 2: Lote médio (100 usuários)**
```bash
php artisan ldap:import-users --limit=100 --batch-size=50
```

**PASSO 3: Importação completa**
```bash
# Importar até 2000 usuários em lotes de 200
php artisan ldap:import-users --limit=2000 --batch-size=200

# OU importar TODOS (sem limite)
php artisan ldap:import-users --batch-size=200
```

---

## 📊 Verificar Resultado

```bash
# Ver quantos usuários foram importados
php artisan tinker
>>> App\Models\User::count();

# Ver últimos 10 importados
>>> App\Models\User::latest()->take(10)->pluck('name', 'email');

# Ver todos os do LDAP
>>> App\Models\User::whereNotNull('ldap_dn')->count();
```

---

## 🌐 Usar pelo Navegador

**IMPORTANTE:** Use a porta correta!

1. Acesse: **http://127.0.0.1:8001** (não 8000!)
2. Faça login: `admin@admin.com` / `admin123`
3. Vá em: **Admin → Importação LDAP**
4. Configure:
   - Host: **10.0.0.200**
   - Porta: **389**
   - Base DN: **DC=guarapuava,DC=pr,DC=gov,DC=br**
   - Usuário: (o do .env)
   - Senha: (a do .env)
5. Clique em "Testar Conexão"
6. Se OK, clique em "Buscar Preview"
7. Selecione os usuários e clique em "Importar Selecionados"

---

## 💡 Dicas

- **--dry-run**: Sempre use primeiro para simular!
- **--batch-size**: Recomendado 100-200 para milhares de usuários
- **--limit**: Defina um limite se não quiser todos
- **--filter**: Use para importar apenas um departamento/grupo

---

## 🐛 Resolver Problemas

### Limpar cache antes de importar:
```bash
php artisan config:clear
php artisan cache:clear
```

### Ver logs de erro:
```bash
tail -f storage/logs/laravel.log
```

### Excluir usuários importados (CUIDADO!):
```bash
php artisan tinker
>>> App\Models\User::whereNotNull('ldap_dn')->delete();
```

---

## 📞 Próximo Passo

Execute agora:

```bash
# 1. Teste com 10 usuários primeiro
php artisan ldap:import-users --limit=10

# 2. Se tudo OK, importe mais
php artisan ldap:import-users --limit=500 --batch-size=100
```

✅ **Está tudo pronto para importar!**
