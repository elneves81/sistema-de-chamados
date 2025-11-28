# 📱 Implementação: Mobile App + Gestão de Ativos + IA

## ✅ Status Atual (Concluído)

### 1. Estrutura de Banco de Dados
- ✅ Migrations criadas para:
  - **Gestão de Ativos**: asset_types, manufacturers, asset_models, assets, asset_maintenances
  - **Sistema de IA**: ai_classifications, similar_tickets, technician_expertise, ticket_keywords
  - Vínculo de tickets com ativos

### 2. Próximas Etapas (Em Ordem de Prioridade)

## 🎯 FASE 1: API REST para Mobile (1-2 dias)

### Rotas API a Criar:
```php
// routes/api.php

// Autenticação
POST /api/auth/login
POST /api/auth/logout
POST /api/auth/refresh

// Tickets (Mobile)
GET /api/mobile/tickets              // Lista tickets do técnico
GET /api/mobile/tickets/{id}         // Detalhes do ticket
PUT /api/mobile/tickets/{id}/status  // Atualizar status
POST /api/mobile/tickets/{id}/comment // Adicionar comentário
POST /api/mobile/tickets/{id}/photo   // Upload de foto
GET /api/mobile/tickets/nearby        // Tickets próximos (GPS)

// Ativos (Mobile)
GET /api/mobile/assets/scan/{qrcode}  // Escanear QR Code
GET /api/mobile/assets/{id}           // Detalhes do ativo
POST /api/mobile/assets/{id}/maintenance // Registrar manutenção

// Notificações Push
POST /api/mobile/register-device      // Registrar token FCM
```

### Controllers Necessários:
- `app/Http/Controllers/Api/MobileAuthController.php`
- `app/Http/Controllers/Api/MobileTicketController.php`
- `app/Http/Controllers/Api/MobileAssetController.php`

### Middleware:
- API rate limiting
- JWT/Sanctum authentication
- Verificação de role (técnico)

## 🏢 FASE 2: Gestão de Ativos (2-3 dias)

### Controllers:
- `AssetController` - CRUD de ativos
- `AssetTypeController` - Tipos de ativos
- `ManufacturerController` - Fabricantes
- `AssetModelController` - Modelos
- `AssetMaintenanceController` - Manutenções

### Views Principais:
1. **Lista de Ativos** (`resources/views/assets/index.blade.php`)
   - Filtros: tipo, status, localização, garantia
   - Busca por asset_tag, serial
   - Cards com foto, status, garantia

2. **Detalhes do Ativo** (`resources/views/assets/show.blade.php`)
   - Informações completas
   - Histórico de manutenções
   - Tickets relacionados
   - QR Code para impressão
   - Timeline de eventos

3. **Formulário** (`resources/views/assets/create.blade.php` / `edit.blade.php`)
   - Upload de fotos
   - Campos dinâmicos por tipo
   - Cálculo automático de garantia
   - Geração de asset_tag

4. **Dashboard de Ativos** (`resources/views/assets/dashboard.blade.php`)
   - Total de ativos por status
   - Alertas de garantia próxima do vencimento
   - Manutenções agendadas
   - Ativos sem manutenção há X meses

### Funcionalidades Especiais:
- **Gerador de QR Code** (biblioteca `simplesoftwareio/simple-qrcode`)
- **Scanner de QR** (mobile - biblioteca ZXing)
- **Alertas automáticos** de garantia/manutenção
- **Relatórios**: inventário, depreciação, custo de manutenção

## 🤖 FASE 3: IA - Classificação Automática (3-4 dias)

### Serviço de IA:
`app/Services/AIClassificationService.php`

```php
class AIClassificationService
{
    // Classificar novo ticket
    public function classifyTicket(Ticket $ticket): array
    {
        return [
            'category' => $this->predictCategory($ticket),
            'priority' => $this->predictPriority($ticket),
            'technician' => $this->recommendTechnician($ticket),
            'similar_tickets' => $this->findSimilarTickets($ticket),
        ];
    }
    
    // Análise de texto (NLP básico)
    private function extractKeywords(string $text): array;
    
    // Calcular similaridade entre tickets
    private function calculateSimilarity(Ticket $t1, Ticket $t2): float;
    
    // Recomendar técnico baseado em expertise
    private function recommendTechnician(Ticket $ticket): ?User;
    
    // Aprender com feedback (quando ticket é resolvido)
    public function learnFromFeedback(Ticket $ticket): void;
}
```

### Algoritmos:
1. **Classificação de Categoria**: 
   - TF-IDF + K-Nearest Neighbors
   - Palavras-chave associadas a categorias
   
2. **Prioridade**:
   - Análise de palavras de urgência
   - Histórico de prioridades similares
   
3. **Recomendação de Técnico**:
   - Score baseado em:
     - Expertise na categoria
     - Taxa de resolução
     - Tempo médio de resolução
     - Carga de trabalho atual
     - Localização (se aplicável)

4. **Tickets Similares**:
   - Similaridade de texto (Cosine Similarity)
   - Mesma categoria/localização
   - Sugerir solução usada anteriormente

### Jobs/Commands:
```php
// app/Console/Commands/CalculateExpertiseScores.php
// Executar diariamente para recalcular scores dos técnicos

// app/Jobs/ClassifyNewTicket.php
// Queue job para classificar async

// app/Jobs/FindSimilarTickets.php
// Queue job para buscar tickets similares
```

## 📱 FASE 4: PWA Mobile-First (2-3 dias)

### Estrutura:
```
resources/views/mobile/
├── layout.blade.php          // Layout base mobile
├── dashboard.blade.php        // Dashboard técnico
├── tickets/
│   ├── index.blade.php       // Lista
│   ├── show.blade.php        // Detalhes + ações rápidas
│   └── nearby.blade.php      // Mapa com tickets próximos
└── assets/
    ├── scan.blade.php        // Scanner QR
    └── show.blade.php        // Detalhes do ativo
```

### Funcionalidades PWA:
- **Offline-first**: Cache de tickets atribuídos
- **Sincronização**: Upload quando voltar online
- **Geo-localização**: Ver tickets próximos
- **Câmera**: Foto direto do ticket
- **Notificações Push**: Web Push API
- **Install prompt**: Add to Home Screen

### Service Worker (`public/sw-mobile.js`):
```javascript
// Cache strategies
const CACHE_NAME = 'tech-app-v1';
const urlsToCache = [
  '/mobile/dashboard',
  '/mobile/tickets',
  '/css/mobile.css',
  '/js/mobile-app.js'
];

// Background sync para upload de fotos/comentários offline
```

## 🚀 Comandos para Executar

### 1. Rodar Migrations:
```bash
php artisan migrate
```

### 2. Instalar Dependências Adicionais:
```bash
# QR Code generator
composer require simplesoftwareio/simple-qrcode

# Image processing
composer require intervention/image

# API resources
composer require --dev laravel/telescope  # debugging API

# PHP ML (opcional - para IA avançada)
composer require php-ai/php-ml
```

### 3. Criar Seeders para Dados de Teste:
```bash
php artisan make:seeder AssetSeeder
php artisan make:seeder AITrainingSeeder
```

### 4. Gerar API Documentation:
```bash
composer require darkaonline/l5-swagger
php artisan l5-swagger:generate
```

## 📊 Métricas de Sucesso

### Mobile App:
- ✅ Tempo de resposta API < 200ms
- ✅ Taxa de uso offline > 30%
- ✅ Upload de fotos em chamados +50%

### Gestão de Ativos:
- ✅ 100% dos ativos cadastrados com QR Code
- ✅ Redução de 40% em ativos "perdidos"
- ✅ Alertas de garantia com 90 dias de antecedência

### IA:
- ✅ Acurácia de categoria > 80%
- ✅ Acurácia de prioridade > 75%
- ✅ Recomendação de técnico aceita em > 70% dos casos
- ✅ Tickets similares relevantes em > 60% dos casos

## 🎨 Design Mobile-First

### Telas Principais:
1. **Dashboard**
   - Cards grandes com métricas
   - Ações rápidas (escanear QR, novo ticket)
   - Lista de tickets atribuídos

2. **Detalhes do Ticket**
   - Status visual (timeline)
   - Botões grandes para ações
   - Campo de comentário com voz-para-texto
   - Galeria de fotos

3. **Scanner QR**
   - Câmera fullscreen
   - Feedback visual ao escanear
   - Informações do ativo em overlay

## 🔐 Segurança

### API:
- Sanctum tokens com expiração
- Rate limiting (60 req/min)
- Validação de input
- CORS configurado

### Mobile:
- Biometria para login
- Token armazenado em secure storage
- SSL pinning (produção)
- Criptografia local de dados offline

## 📝 Documentação

Criar arquivos:
- `API.md` - Documentação completa da API
- `MOBILE_SETUP.md` - Guia de setup do app mobile
- `AI_TRAINING.md` - Como treinar e melhorar a IA
- `ASSETS_GUIDE.md` - Manual de gestão de ativos

## 🎯 Próximo Passo Imediato

**Você quer que eu:**
1. ✅ Rode as migrations e crie os Controllers base?
2. ✅ Crie a API REST completa para mobile?
3. ✅ Implemente o sistema de Gestão de Ativos primeiro?
4. ✅ Foque na IA de classificação?

**Ou prefere que eu crie tudo de forma integrada sequencialmente?**

Aguardo sua decisão para prosseguir! 🚀
