# API Mobile para Técnicos

Documentação prática da API (v1) para o app mobile dos técnicos. Autenticação via tokens do Laravel Sanctum.

## Autenticação

- Login (gera token):
  - POST /api/auth/login
  - Body JSON (qualquer um dos campos `email` OU `username` é aceito, com `password`):
    {
      "email": "tecnico@empresa.com",
      "password": "sua_senha"
    }
  - Resposta 200:
    {
      "token": "<BEARER_TOKEN>",
      "user": { "id": 1, "name": "Fulano", "email": "tecnico@empresa.com" }
    }
  - 401: Credenciais inválidas
  - Rate limit: 15 req/min (throttle)

- Logout (revoga token atual):
  - POST /api/auth/logout
  - Header: Authorization: Bearer <BEARER_TOKEN>
  - Resposta 204 (sem corpo)

## Tickets (requer Bearer token)

Prefixo: /api/mobile

- Listar tickets do técnico / fila (ajuste de filtros no backend conforme regras):
  - GET /api/mobile/tickets?status=aberto|em_andamento|resolvido
  - Resposta 200: array de tickets

- Detalhar um ticket:
  - GET /api/mobile/tickets/{ticketId}
  - Resposta 200: objeto ticket (inclui relações úteis como solicitante, anexos e, quando existente, `asset`)

- Atualizar status do ticket:
  - PUT /api/mobile/tickets/{ticketId}/status
  - Body JSON:
    {
      "status": "em_andamento"
    }
  - Respostas:
    - 200: ticket atualizado
    - 422: status inválido

- Adicionar comentário (com anexos):
  - POST /api/mobile/tickets/{ticketId}/comment
  - Multipart form-data:
    - message: texto
    - attachments[]: arquivos (0..N)
  - Respostas:
    - 201: comentário criado
    - 422: validação

- Assumir (claim) um ticket:
  - POST /api/mobile/tickets/{ticketId}/claim
  - Efeito: se não atribuído, define `assigned_to = eu` e se status for `open` muda para `in_progress`.
  - Respostas:
    - 200: ticket atualizado (já atribuído ao usuário)
    - 409: já atribuído a outra pessoa (retorna `assigned_to`)

Rate limit padrão para rotas mobile: 60 req/min por token.

## Exemplos (curl)

Login e guardar token:

```bash
curl -sX POST http://localhost:8000/api/auth/login \
  -H 'Content-Type: application/json' \
  -d '{"email":"tecnico@empresa.com","password":"senha"}'
```

Listar tickets:

```bash
curl -s http://localhost:8000/api/mobile/tickets \
  -H 'Authorization: Bearer <BEARER_TOKEN>'
```

Atualizar status:

```bash
curl -sX PUT http://localhost:8000/api/mobile/tickets/123/status \
  -H 'Authorization: Bearer <BEARER_TOKEN>' \
  -H 'Content-Type: application/json' \
  -d '{"status":"em_andamento"}'
```

Comentar com foto:

```bash
curl -sX POST http://localhost:8000/api/mobile/tickets/123/comment \
  -H 'Authorization: Bearer <BEARER_TOKEN>' \
  -F 'message=Iniciando atendimento' \
  -F 'attachments[]=@/caminho/da/foto.jpg'
```

Assumir ticket:

```bash
curl -sX POST http://localhost:8000/api/mobile/tickets/123/claim \
  -H 'Authorization: Bearer <BEARER_TOKEN>'
```

## Devices (push notifications)

- Registrar device para receber push:
  - POST /api/mobile/devices/register
  - Body JSON:
    {
      "platform": "android", // ios|web
      "token": "<FCM_TOKEN>",
      "device_info": {"brand":"Xiaomi","model":"Mi 9"}
    }
  - Respostas:
    - 201: registrado/atualizado
  - Observação: O disparo de push será configurado em breve; este endpoint persiste o token.

## Códigos de erro comuns

- 401 Unauthorized: Token ausente/expirado/inválido
- 403 Forbidden: Sem permissão para o recurso
- 404 Not Found: Ticket não encontrado
- 422 Unprocessable Entity: Validação falhou (mensagens no JSON)
- 429 Too Many Requests: Estouro do rate limit (aguarde 60s)

## Notas

- Autenticação usa Sanctum (Bearer). Para web/SPA, use cookies; para mobile, use tokens.
- Uploads são gravados no disco `public` (requer `php artisan storage:link`).
- Campos extras, filtros e paginação podem ser evoluídos conforme necessidade do app.
- Versão da API: inicial (v1 implícita). Ao estabilizar, podemos versionar a URL (ex.: `/api/v1/mobile/...`).
