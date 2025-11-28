# Sistema de Validação de Assinatura Digital

## Como Funciona

A validação de assinatura digital permite que **APENAS O RECEBEDOR DA MÁQUINA** possa validar a assinatura usando suas próprias credenciais de rede.

## Processo de Validação

1. **Recebedor acessa a página da máquina** que ele recebeu
2. **Clica em "Solicitar Validação ao Recebedor"**
3. **Modal de validação aparece**
4. **Recebedor insere SUAS PRÓPRIAS credenciais** (login e senha de rede)
5. **Sistema valida:**
   - Se o usuário é realmente o recebedor da máquina
   - Se as credenciais estão corretas (LDAP ou local)
6. **Se válido:** Assinatura é marcada como validada
7. **Registro fica salvo:** Data, hora e quem validou

## Regra de Validação

⚠️ **IMPORTANTE:** Apenas o recebedor pode validar!

- ✅ Login fornecido deve ser do recebedor da máquina
- ❌ Outros usuários NÃO podem validar (mesmo com credenciais corretas)
- ✅ Validação via LDAP (preferencial) ou senha local (fallback)

## Exemplos de Uso

### Cenário 1: Recebedor Valida (CORRETO ✅)
- **Máquina recebida por:** Deysianne Souza (ID: 5213)
- **Quem tenta validar:** Deysianne Souza
- **Login usado:** `Deysianne.souza` ou `deysianne.souza`
- **Senha:** Senha dela do Windows
- ✅ **Resultado:** Validado com sucesso

### Cenário 2: Outra Pessoa Tenta Validar (BLOQUEADO ❌)
- **Máquina recebida por:** Deysianne Souza (ID: 5213)
- **Quem tenta validar:** Elber (técnico, ID: 2634)
- **Login usado:** `Elber.pmg`
- **Senha:** Senha do Elber (correta)
- ❌ **Resultado:** "Apenas o recebedor da máquina pode validar a assinatura"
- 📝 **Log:** Tentativa bloqueada por não ser o recebedor

### Cenário 3: Recebedor com Login Errado (BLOQUEADO ❌)
- **Máquina recebida por:** João Silva (ID: 1000)
- **Quem tenta validar:** Usando login `maria.santos`
- ❌ **Resultado:** "Apenas o recebedor da máquina pode validar a assinatura"

## Métodos de Autenticação

### 1. LDAP/Active Directory (Preferencial)
- Tenta autenticar no servidor LDAP
- Formatos tentados automaticamente:
  - `usuario@guarapuava.pr.gov.br`
  - `usuario`
  - `USUARIO@guarapuava.pr.gov.br`
  - `GUARAPUAVA\usuario`

### 2. Autenticação Local (Fallback)
- Se LDAP falhar, tenta autenticação local
- Usa password hash armazenado no banco
- Garante que todos possam validar

## Requisitos

- ✅ Estar logado no sistema
- ✅ Ter credenciais válidas (LDAP ou local)
- ✅ Sessão ativa (não expirada)

## Erros Comuns

### Erro 401 - Não Autorizado
**Causa:** Sessão expirada ou sem permissão
**Solução:** Fazer logout e login novamente

### Erro 419 - Token CSRF Expirado
**Causa:** Página aberta por muito tempo
**Solução:** Recarregar a página (F5)

### "Login ou senha inválidos"
**Causa:** Credenciais incorretas
**Solução:** Verificar login e senha (mesmos usados no Windows)

## Logs de Validação

O sistema registra:
- Quem estava logado no sistema
- Qual login foi usado para validar
- Data e hora da validação
- IP do computador
- Se foi via LDAP ou autenticação local

## Segurança

✅ Senha não é armazenada
✅ Validação via LDAP quando possível
✅ Fallback seguro para autenticação local
✅ Registro completo de auditoria
✅ Sessão verificada antes de validar
✅ Token CSRF renovado automaticamente

## Atualizado em
18/11/2025 16:25
