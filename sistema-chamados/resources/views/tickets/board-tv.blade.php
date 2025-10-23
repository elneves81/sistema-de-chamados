<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Painel de Chamados - Modo TV</title>
    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Custom CSS -->
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            background-color: #111;
            color: white;
            font-family: Arial, sans-serif;
            transition: background-color 0.3s ease, color 0.3s ease;
        }
        
        /* Tema claro */
        body.light-theme {
            background-color: #f8f9fa;
            color: #333;
        }
        
        body.light-theme .bg-dark {
            background-color: #f0f0f0 !important;
        }
        
        body.light-theme .text-white {
            color: #333 !important;
        }
        
        body.light-theme .card-header {
            color: white !important;
        }
        
        body.light-theme .ticket-marquee {
            background-color: #e9ecef !important;
            color: #333 !important;
        }
        
        /* Estilo específico para o modo TV */
        .ticket-card-tv {
            border: 1px solid #ddd;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 15px;
            background: white;
            transition: all 0.3s ease;
        }
        
        .ticket-card-tv:hover {
            box-shadow: 0 8px 15px rgba(0,0,0,0.2);
        }
        
        .ticket-card-tv.new-ticket {
            animation: newTicketPulse 2s infinite;
            border-color: #007bff;
        }
        
        @keyframes newTicketPulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(0, 123, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 123, 255, 0); }
        }
        
        .priority-high {
            border-left: 8px solid #dc3545;
        }
        
        .priority-medium {
            border-left: 8px solid #ffc107;
        }
        
        .priority-low {
            border-left: 8px solid #28a745;
        }
        
        .ticket-title-tv {
            font-weight: bold;
            font-size: 1.4em;
            margin-bottom: 12px;
            color: #2c3e50;
        }
        
        .ticket-description-tv {
            font-size: 1.0em;
            color: #555;
            margin-bottom: 15px;
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border-left: 3px solid #007bff;
        }
        
        .ticket-description-tv p {
            margin: 5px 0 0 0;
            line-height: 1.4;
            font-style: italic;
        }
        
        .ticket-meta-tv {
            font-size: 1.1em;
            color: #333;
        }
        
        .ticket-category-tv {
            background: #e9ecef;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.9em;
            display: inline-block;
        }
        
        /* Estilo do texto em movimento */
        .ticket-marquee {
            width: 100%;
            overflow: hidden;
            background: #222;
            padding: 15px;
            border-radius: 8px;
        }
        
        .ticket-marquee-content {
            display: inline-block;
            white-space: nowrap;
            animation: marquee var(--marquee-duration, 40s) linear infinite;
            padding-left: 100%;
        }
        
        @keyframes marquee {
            0% {
                transform: translate(0, 0);
            }
            100% {
                transform: translate(-100%, 0);
            }
        }
        
        /* Velocidades do marquee */
        .marquee-speed-slow {
            --marquee-duration: 60s;
        }
        
        .marquee-speed-normal {
            --marquee-duration: 40s;
        }
        
        .marquee-speed-fast {
            --marquee-duration: 20s;
        }
        
        /* Estilos para a notificação de tela cheia */
        .fullscreen-notification {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 9999;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .notification-content {
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            max-width: 500px;
            text-align: center;
            color: #333;
        }
        
        /* Toast notifications */
        .toast-notification, .custom-toast {
            position: fixed;
            top: -100px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 15px 20px;
            border-radius: 5px;
            z-index: 10000;
            opacity: 0;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
        }
        
        .toast-success, .custom-toast {
            background: #28a745;
        }
        
        .toast-info {
            background: #17a2b8;
        }
        
        .toast-warning {
            background: #ffc107;
            color: #333;
        }
        
        .toast-error {
            background: #dc3545;
            color: white;
        }
        
        #toast.show {
            top: 20px;
            opacity: 1;
        }
        
        /* Animações desabilitadas */
        .no-animation * {
            animation: none !important;
            transition: none !important;
        }
        
        /* Filtros */
        #filtersBar {
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 8px;
            padding: 15px;
            margin-top: 10px;
        }
        
        #filtersBar .form-select,
        #filtersBar .form-control {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        #filtersBar .form-select:focus,
        #filtersBar .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }
        
        #filtersBar .input-group-text {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        #filtersBar .input-group-text i {
            color: #007bff;
        }
        
        /* Estilos para drag & drop */
        .ticket-ghost {
            opacity: 0.5;
            background: #c8ebfb;
            border: 2px dashed #0d6efd;
        }
        
        .ticket-drag {
            cursor: move;
            opacity: 0.8;
        }
        
        .ticket-card-tv {
            cursor: grab;
        }
        
        .ticket-card-tv:active {
            cursor: grabbing;
        }
        
        .ticket-loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 8px;
            z-index: 10;
        }
        
        .ticket-loading-overlay i {
            font-size: 2rem;
            color: #0d6efd;
        }
        
        .ticket-updated {
            animation: ticketUpdated 2s;
        }
        
        @keyframes ticketUpdated {
            0% { background-color: #d1e7dd; }
            50% { background-color: #d1e7dd; }
            100% { background-color: white; }
        }
        
        /* Estilos para notificações push */
        .push-notification {
            position: fixed;
            bottom: 20px;
            right: 20px;
            max-width: 350px;
            background: rgba(52, 58, 64, 0.9);
            color: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
            z-index: 1080;
            opacity: 0;
            transform: translateY(20px);
            transition: all 0.3s ease;
        }
        
        .push-notification.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-dark">

@php
    use Illuminate\Support\Str;
@endphp

<!-- Conteúdo principal -->
<div class="vh-100 d-flex flex-column">
    <!-- Cabeçalho -->
    <div class="bg-dark text-white p-2 d-flex justify-content-between align-items-center">
        <div>
            <h1 class="display-4 mb-0">
                <i class="fas fa-desktop me-2"></i>
                PAINEL DE CHAMADOS
            </h1>
        </div>
        <div class="d-flex gap-2">
            <button id="toggleFiltersBtn" class="btn btn-outline-primary">
                <i class="fas fa-filter"></i>
            </button>
            <a href="{{ route('tickets.board') }}" class="btn btn-outline-light">
                <i class="fas fa-table me-2"></i>Modo Normal
            </a>
            <button id="toggleFullscreen" class="btn btn-outline-warning">
                <i class="fas fa-expand"></i>
            </button>
            <button id="soundToggle" class="btn btn-outline-info">
                <i class="fas fa-volume-up"></i>
            </button>
            <button id="toggleTheme" class="btn btn-outline-secondary">
                <i class="fas fa-moon"></i>
            </button>
            <button id="showSettingsBtn" class="btn btn-outline-light">
                <i class="fas fa-cog"></i>
            </button>
            <button id="showMetricsBtn" class="btn btn-outline-success">
                <i class="fas fa-chart-bar"></i>
            </button>
            <button id="showDashboardBtn" class="btn btn-outline-primary">
                <i class="fas fa-tachometer-alt"></i>
            </button>
        </div>
    </div>
    
    <!-- Barra de filtros (inicialmente oculta) -->
    <div id="filtersBar" class="bg-dark text-white p-2 border-top border-secondary" style="display: none;">
        <div class="row g-2">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" id="searchFilter" class="form-control" placeholder="Buscar tickets...">
                </div>
            </div>
            <div class="col-md-3">
                <select id="priorityFilter" class="form-select">
                    <option value="">Todas as prioridades</option>
                    <option value="high">Alta</option>
                    <option value="medium">Média</option>
                    <option value="low">Baixa</option>
                </select>
            </div>
            <div class="col-md-3">
                <select id="categoryFilter" class="form-select">
                    <option value="">Todas as categorias</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select id="assignedToFilter" class="form-select">
                    <option value="">Todos os responsáveis</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="bg-dark text-white d-flex justify-content-between p-2">
        <div class="flex-grow-1">
            <div class="card bg-danger text-white h-100">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title fs-2 mb-0">Abertos</h2>
                            <h1 id="open-count" class="display-1 fw-bold mb-0">{{ $ticketsByStatus['open']->count() }}</h1>
                        </div>
                        <div>
                            <i class="fas fa-exclamation-circle fa-4x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-grow-1 mx-2">
            <div class="card bg-warning text-dark h-100">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title fs-2 mb-0">Em Andamento</h2>
                            <h1 id="progress-count" class="display-1 fw-bold mb-0">{{ $ticketsByStatus['in_progress']->count() }}</h1>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-4x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-grow-1 mx-2">
            <div class="card bg-info text-white h-100">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title fs-2 mb-0">Resolvidos</h2>
                            <h1 id="resolved-count" class="display-1 fw-bold mb-0">{{ $ticketsByStatus['resolved']->count() }}</h1>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-4x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="flex-grow-1">
            <div class="card bg-success text-white h-100">
                <div class="card-body p-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="card-title fs-2 mb-0">Fechados</h2>
                            <h1 id="closed-count" class="display-1 fw-bold mb-0">{{ $ticketsByStatus['closed']->count() }}</h1>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-4x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Painel de Tickets -->
    <div class="flex-grow-1 d-flex">
        <!-- Coluna 1: Abertos -->
        <div class="flex-grow-1 d-flex flex-column">
            <div class="card-header bg-danger text-white py-2">
                <h2 class="mb-0 fs-2 text-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    ABERTOS
                </h2>
            </div>
            <div class="flex-grow-1 overflow-auto bg-light p-1">
                <div id="open-tickets">
                    @foreach($ticketsByStatus['open'] as $ticket)
                        <div class="ticket-card-tv priority-{{ $ticket->priority }}" data-ticket-id="{{ $ticket->id }}">
                            <div class="ticket-title-tv">#{{ $ticket->id }} - {{ $ticket->title }}</div>
                            <div class="ticket-description-tv">
                                <strong>Descrição do Problema:</strong>
                                <p>{{ Str::limit($ticket->description, 150) }}</p>
                            </div>
                            <div class="ticket-meta-tv">
                                <div class="mb-2">
                                    <span class="ticket-category-tv">
                                        {{ $ticket->category ? $ticket->category->name : 'Sem categoria' }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div><strong>Solicitante:</strong> {{ $ticket->user->name }}</div>
                                        <div><strong>Atribuído:</strong> {{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Não atribuído' }}</div>
                                    </div>
                                    <div>
                                        <div><strong>Prioridade:</strong> 
                                            @switch($ticket->priority)
                                                @case('high')
                                                    <span class="badge bg-danger fs-5">Alta</span>
                                                    @break
                                                @case('medium')
                                                    <span class="badge bg-warning text-dark fs-5">Média</span>
                                                    @break
                                                @case('low')
                                                    <span class="badge bg-success fs-5">Baixa</span>
                                                    @break
                                            @endswitch
                                        </div>
                                        <div><strong>Criado:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Coluna 2: Em Andamento -->
        <div class="flex-grow-1 d-flex flex-column mx-1">
            <div class="card-header bg-warning text-dark py-2">
                <h2 class="mb-0 fs-2 text-center">
                    <i class="fas fa-clock me-2"></i>
                    EM ANDAMENTO
                </h2>
            </div>
            <div class="flex-grow-1 overflow-auto bg-light p-1">
                <div id="progress-tickets">
                    @foreach($ticketsByStatus['in_progress'] as $ticket)
                        <div class="ticket-card-tv priority-{{ $ticket->priority }}" data-ticket-id="{{ $ticket->id }}">
                            <div class="ticket-title-tv">#{{ $ticket->id }} - {{ $ticket->title }}</div>
                            <div class="ticket-description-tv">
                                <strong>Descrição do Problema:</strong>
                                <p>{{ Str::limit($ticket->description, 150) }}</p>
                            </div>
                            <div class="ticket-meta-tv">
                                <div class="mb-2">
                                    <span class="ticket-category-tv">
                                        {{ $ticket->category ? $ticket->category->name : 'Sem categoria' }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div><strong>Solicitante:</strong> {{ $ticket->user->name }}</div>
                                        <div><strong>Atribuído:</strong> {{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Não atribuído' }}</div>
                                    </div>
                                    <div>
                                        <div><strong>Prioridade:</strong> 
                                            @switch($ticket->priority)
                                                @case('high')
                                                    <span class="badge bg-danger fs-5">Alta</span>
                                                    @break
                                                @case('medium')
                                                    <span class="badge bg-warning text-dark fs-5">Média</span>
                                                    @break
                                                @case('low')
                                                    <span class="badge bg-success fs-5">Baixa</span>
                                                    @break
                                            @endswitch
                                        </div>
                                        <div><strong>Criado:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Coluna 3: Resolvidos -->
        <div class="flex-grow-1 d-flex flex-column mx-1">
            <div class="card-header bg-info text-white py-2">
                <h2 class="mb-0 fs-2 text-center">
                    <i class="fas fa-check-circle me-2"></i>
                    RESOLVIDOS
                </h2>
            </div>
            <div class="flex-grow-1 overflow-auto bg-light p-1">
                <div id="resolved-tickets">
                    @foreach($ticketsByStatus['resolved'] as $ticket)
                        <div class="ticket-card-tv priority-{{ $ticket->priority }}" data-ticket-id="{{ $ticket->id }}">
                            <div class="ticket-title-tv">#{{ $ticket->id }} - {{ $ticket->title }}</div>
                            <div class="ticket-description-tv">
                                <strong>Descrição do Problema:</strong>
                                <p>{{ Str::limit($ticket->description, 150) }}</p>
                            </div>
                            <div class="ticket-meta-tv">
                                <div class="mb-2">
                                    <span class="ticket-category-tv">
                                        {{ $ticket->category ? $ticket->category->name : 'Sem categoria' }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div><strong>Solicitante:</strong> {{ $ticket->user->name }}</div>
                                        <div><strong>Atribuído:</strong> {{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Não atribuído' }}</div>
                                    </div>
                                    <div>
                                        <div><strong>Prioridade:</strong> 
                                            @switch($ticket->priority)
                                                @case('high')
                                                    <span class="badge bg-danger fs-5">Alta</span>
                                                    @break
                                                @case('medium')
                                                    <span class="badge bg-warning text-dark fs-5">Média</span>
                                                    @break
                                                @case('low')
                                                    <span class="badge bg-success fs-5">Baixa</span>
                                                    @break
                                            @endswitch
                                        </div>
                                        <div><strong>Criado:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <!-- Coluna 4: Fechados -->
        <div class="flex-grow-1 d-flex flex-column">
            <div class="card-header bg-success text-white py-2">
                <h2 class="mb-0 fs-2 text-center">
                    <i class="fas fa-times-circle me-2"></i>
                    FECHADOS
                </h2>
            </div>
            <div class="flex-grow-1 overflow-auto bg-light p-1">
                <div id="closed-tickets">
                    @foreach($ticketsByStatus['closed'] as $ticket)
                        <div class="ticket-card-tv priority-{{ $ticket->priority }}" data-ticket-id="{{ $ticket->id }}">
                            <div class="ticket-title-tv">#{{ $ticket->id }} - {{ $ticket->title }}</div>
                            <div class="ticket-description-tv">
                                <strong>Descrição do Problema:</strong>
                                <p>{{ Str::limit($ticket->description, 150) }}</p>
                            </div>
                            <div class="ticket-meta-tv">
                                <div class="mb-2">
                                    <span class="ticket-category-tv">
                                        {{ $ticket->category ? $ticket->category->name : 'Sem categoria' }}
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <div><strong>Solicitante:</strong> {{ $ticket->user->name }}</div>
                                        <div><strong>Atribuído:</strong> {{ $ticket->assignedTo ? $ticket->assignedTo->name : 'Não atribuído' }}</div>
                                    </div>
                                    <div>
                                        <div><strong>Prioridade:</strong> 
                                            @switch($ticket->priority)
                                                @case('high')
                                                    <span class="badge bg-danger fs-5">Alta</span>
                                                    @break
                                                @case('medium')
                                                    <span class="badge bg-warning text-dark fs-5">Média</span>
                                                    @break
                                                @case('low')
                                                    <span class="badge bg-success fs-5">Baixa</span>
                                                    @break
                                            @endswitch
                                        </div>
                                        <div><strong>Criado:</strong> {{ $ticket->created_at->format('d/m/Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Últimos chamados (SGA style) -->
    <div class="bg-dark text-white p-2">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0 fs-2">ÚLTIMOS CHAMADOS</h2>
            <div id="current-time" class="fs-2"></div>
        </div>
        
        <div class="ticket-marquee mt-1">
            <div class="ticket-marquee-content">
                @foreach($ticketsByStatus['open']->merge($ticketsByStatus['in_progress'])->sortByDesc('created_at')->take(10) as $ticket)
                    <span class="fs-4 me-5">
                        <strong>#{{ $ticket->id }}</strong> - 
                        {{ $ticket->title }} - 
                        <span class="badge 
                            @if($ticket->priority == 'high') bg-danger 
                            @elseif($ticket->priority == 'medium') bg-warning text-dark 
                            @else bg-success @endif fs-5">
                            {{ ucfirst($ticket->priority) }}
                        </span>
                    </span>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Audio element for notifications -->
<audio id="notificationSound" preload="auto">
    <source src="{{ asset('sounds/notification.mp3') }}" type="audio/mpeg">
    <source src="{{ asset('sounds/notification.ogg') }}" type="audio/ogg">
    <source src="{{ asset('sounds/notification.wav') }}" type="audio/wav">
</audio>

<!-- Modal de configurações -->
<div class="modal fade" id="settingsModal" tabindex="-1" aria-labelledby="settingsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-dark text-white">
                <h5 class="modal-title" id="settingsModalLabel"><i class="fas fa-cog me-2"></i> Configurações do Painel</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-clock me-2"></i> Atualização</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label class="form-label">Intervalo de atualização:</label>
                                    <div class="input-group">
                                        <input type="number" id="refreshInterval" class="form-control" value="15" min="5" max="60">
                                        <span class="input-group-text">segundos</span>
                                    </div>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="autoFullscreen" checked>
                                    <label class="form-check-label" for="autoFullscreen">
                                        Ativar tela cheia automaticamente
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-eye me-2"></i> Visualização</h5>
                            </div>
                            <div class="card-body">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="showDescriptions" checked>
                                    <label class="form-check-label" for="showDescriptions">
                                        Mostrar descrições nos tickets
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="enableAnimations" checked>
                                    <label class="form-check-label" for="enableAnimations">
                                        Habilitar animações
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="enableDragDrop" checked>
                                    <label class="form-check-label" for="enableDragDrop">
                                        Habilitar drag & drop de tickets
                                    </label>
                                </div>
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" id="enablePushNotifications" checked>
                                    <label class="form-check-label" for="enablePushNotifications">
                                        Habilitar notificações push
                                    </label>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tamanho dos cards:</label>
                                    <select id="cardSize" class="form-select">
                                        <option value="normal">Normal</option>
                                        <option value="small">Pequeno</option>
                                        <option value="large">Grande</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Velocidade da faixa de notícias:</label>
                                    <select id="marqueeSpeed" class="form-select">
                                        <option value="normal">Normal</option>
                                        <option value="slow">Lenta</option>
                                        <option value="fast">Rápida</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="saveSettings">Salvar configurações</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de métricas avançadas -->
<div class="modal fade" id="metricsModal" tabindex="-1" aria-labelledby="metricsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="metricsModalLabel"><i class="fas fa-chart-bar me-2"></i> Métricas Avançadas</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <!-- Métricas diárias -->
                    <div class="col-md-6">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i> Chamados Hoje vs Ontem</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-6">
                                        <h6>Hoje:</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Novos
                                                <span id="today-created" class="badge bg-primary rounded-pill">-</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Resolvidos
                                                <span id="today-resolved" class="badge bg-success rounded-pill">-</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Fechados
                                                <span id="today-closed" class="badge bg-secondary rounded-pill">-</span>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="col-6">
                                        <h6>Ontem:</h6>
                                        <ul class="list-group">
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Novos
                                                <span id="yesterday-created" class="badge bg-primary rounded-pill">-</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Resolvidos
                                                <span id="yesterday-resolved" class="badge bg-success rounded-pill">-</span>
                                            </li>
                                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                Fechados
                                                <span id="yesterday-closed" class="badge bg-secondary rounded-pill">-</span>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Distribuição por prioridade -->
                    <div class="col-md-6">
                        <div class="card border-warning h-100">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Distribuição por Prioridade</h5>
                            </div>
                            <div class="card-body">
                                <div class="progress mb-3" style="height: 30px;">
                                    <div id="priority-high" class="progress-bar bg-danger" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">Alta: -</div>
                                </div>
                                <div class="progress mb-3" style="height: 30px;">
                                    <div id="priority-medium" class="progress-bar bg-warning text-dark" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">Média: -</div>
                                </div>
                                <div class="progress" style="height: 30px;">
                                    <div id="priority-low" class="progress-bar bg-success" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">Baixa: -</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tempo médio de resolução -->
                    <div class="col-md-6">
                        <div class="card border-info h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-stopwatch me-2"></i> Tempo Médio de Resolução</h5>
                            </div>
                            <div class="card-body text-center">
                                <h1 id="avg-resolution-time" class="display-1 fw-bold">-</h1>
                                <p class="lead">Horas em média</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SLA -->
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i> Cumprimento de SLA</h5>
                            </div>
                            <div class="card-body text-center">
                                <div class="position-relative">
                                    <h1 id="sla-compliance" class="display-1 fw-bold">-%</h1>
                                    <p class="lead">dos chamados resolvidos dentro do SLA</p>
                                    <div class="progress position-relative mt-3" style="height: 40px;">
                                        <div id="sla-progress-bar" class="progress-bar bg-success" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="exportMetricsPDF">
                    <i class="fas fa-file-pdf me-2"></i> Exportar PDF
                </button>
                <button type="button" class="btn btn-success" id="refreshMetrics">
                    <i class="fas fa-sync-alt me-2"></i> Atualizar métricas
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal do Dashboard Executivo -->
<div class="modal fade" id="dashboardModal" tabindex="-1" aria-labelledby="dashboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="dashboardModalLabel"><i class="fas fa-tachometer-alt me-2"></i> Dashboard Executivo</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body bg-light">
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card h-100 border-danger">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-danger">Total de Chamados</h5>
                                    <h1 id="dashboard-total-tickets" class="display-4">0</h1>
                                    <p class="text-muted">Todos os chamados do sistema</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-warning">Chamados Abertos</h5>
                                    <h1 id="dashboard-open-tickets" class="display-4">0</h1>
                                    <p class="text-muted">Chamados que precisam de atenção</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-success">Resolvidos Hoje</h5>
                                    <h1 id="dashboard-resolved-today" class="display-4">0</h1>
                                    <p class="text-muted">Chamados resolvidos no dia</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <h5 class="card-title text-info">Tempo Médio</h5>
                                    <h1 id="dashboard-avg-time" class="display-4">0</h1>
                                    <p class="text-muted">Horas até a resolução</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Chamados por Categoria</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="categoryChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Chamados por Prioridade</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="priorityChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Tendência de Chamados (7 dias)</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="trendsChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Desempenho por Atendente</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="agentPerformanceChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" id="exportDashboardPDF">
                    <i class="fas fa-file-pdf me-2"></i> Exportar Relatório
                </button>
                <button type="button" class="btn btn-success" id="refreshDashboard">
                    <i class="fas fa-sync-alt me-2"></i> Atualizar Dados
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast para notificações -->
<div id="toast" class="custom-toast">
    <i class="fas fa-check-circle me-2"></i> <span id="toast-message"></span>
</div>

<!-- jQuery primeiro, depois o Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.14.0/Sortable.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/push.js@1.0.12/bin/push.min.js"></script>

<script>
let lastCheck = new Date().toISOString();
let soundEnabled = true;
let refreshInterval;
let currentTheme = 'dark';
let animationsEnabled = true;
let showDescriptions = true;
let userSettings = {};

// Variáveis para gráficos
let categoryChart, priorityChart, trendsChart, agentPerformanceChart;

// Configurações padrão
const defaultSettings = {
    refreshInterval: 15,
    autoFullscreen: true,
    showDescriptions: true,
    enableAnimations: true,
    dragdropEnabled: true,
    cardSize: 'normal',
    theme: 'dark',
    marqueeSpeed: 'normal',
    pushNotifications: true
};

$(document).ready(function() {
    // Carregar configurações do usuário
    loadUserSettings();
    
    // Solicitar permissão para notificações push
    if (userSettings.pushNotifications) {
        requestNotificationPermission();
    }
    
    // Inicializar o relógio
    updateTime();
    setInterval(updateTime, 1000);
    
    // Aplicar tema
    applyTheme(userSettings.theme);
    
    // Aplicar tamanho dos cards
    applyCardSize(userSettings.cardSize);
    
    // Aplicar velocidade do marquee
    applyMarqueeSpeed(userSettings.marqueeSpeed);
    
    // Aplicar animações ou remover
    if (!userSettings.enableAnimations) {
        $('body').addClass('no-animation');
    }
    
    // Iniciar polling para novos tickets
    startTicketPolling();
    
    // Inicializar Sortable para drag & drop
    initializeSortable();
    
    // Toggle do som
    $('#soundToggle').click(function() {
        soundEnabled = !soundEnabled;
        $(this).html(soundEnabled ? 
            '<i class="fas fa-volume-up"></i>' : 
            '<i class="fas fa-volume-off"></i>'
        );
    });
    
    // Toggle fullscreen
    $('#toggleFullscreen').click(function() {
        toggleFullScreen();
    });
    
    // Toggle tema
    $('#toggleTheme').click(function() {
        currentTheme = (currentTheme === 'dark') ? 'light' : 'dark';
        applyTheme(currentTheme);
        userSettings.theme = currentTheme;
        saveUserSettings();
    });
    
    // Toggle barra de filtros
    $('#toggleFiltersBtn').click(function() {
        $('#filtersBar').slideToggle(300);
    });
    
    // Mostrar modal de configurações
    $('#showSettingsBtn').click(function() {
        populateSettingsModal();
        $('#settingsModal').modal('show');
    });
    
    // Mostrar modal de métricas
    $('#showMetricsBtn').click(function() {
        loadMetrics();
        $('#metricsModal').modal('show');
    });
    
    // Mostrar modal do dashboard executivo
    $('#showDashboardBtn').click(function() {
        loadDashboard();
        $('#dashboardModal').modal('show');
    });
    
    // Atualizar métricas
    $('#refreshMetrics').click(function() {
        loadMetrics();
    });
    
    // Salvar configurações
    $('#saveSettings').click(function() {
        saveSettingsFromModal();
    });
    
    // Implementar filtros
    $('#searchFilter, #priorityFilter, #categoryFilter, #assignedToFilter').on('input change', function() {
        applyFilters();
    });
    
    // Mostrar dica sobre modo de tela cheia
    if (userSettings.autoFullscreen) {
        showFullscreenNotification();
    }
    
    // Inicializar drag & drop se estiver habilitado
    if (userSettings.dragdropEnabled !== false) {
        initializeSortable();
    }
    
    // Configurar AJAX para incluir o token CSRF
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
    
    // Event handlers para botões de exportação
    $(document).on('click', '#exportMetricsPDF', function() {
        window.openReport("{{ route('api.tickets.metrics.export') }}");
    });

    $(document).on('click', '#exportDashboardPDF', function() {
        window.openReport("{{ route('api.tickets.dashboard.export') }}");
    });

    $(document).on('click', '#refreshDashboard', function() {
        loadDashboard();
    });
});

function loadUserSettings() {
    const savedSettings = localStorage.getItem('ticketBoardSettings');
    
    if (savedSettings) {
        userSettings = JSON.parse(savedSettings);
    } else {
        userSettings = {...defaultSettings};
        saveUserSettings();
    }
    
    // Atualizar variáveis globais
    currentTheme = userSettings.theme;
    animationsEnabled = userSettings.enableAnimations;
    showDescriptions = userSettings.showDescriptions;
}

function saveUserSettings() {
    localStorage.setItem('ticketBoardSettings', JSON.stringify(userSettings));
}

function populateSettingsModal() {
    $('#refreshInterval').val(userSettings.refreshInterval);
    $('#autoFullscreen').prop('checked', userSettings.autoFullscreen);
    $('#showDescriptions').prop('checked', userSettings.showDescriptions);
    $('#enableAnimations').prop('checked', userSettings.enableAnimations);
    $('#enableDragDrop').prop('checked', userSettings.dragdropEnabled);
    $('#enablePushNotifications').prop('checked', userSettings.pushNotifications);
    $('#cardSize').val(userSettings.cardSize);
    $('#marqueeSpeed').val(userSettings.marqueeSpeed);
}

function saveSettingsFromModal() {
    userSettings.refreshInterval = parseInt($('#refreshInterval').val());
    userSettings.autoFullscreen = $('#autoFullscreen').prop('checked');
    userSettings.showDescriptions = $('#showDescriptions').prop('checked');
    userSettings.enableAnimations = $('#enableAnimations').prop('checked');
    userSettings.dragdropEnabled = $('#enableDragDrop').prop('checked');
    userSettings.pushNotifications = $('#enablePushNotifications').prop('checked');
    userSettings.cardSize = $('#cardSize').val();
    userSettings.marqueeSpeed = $('#marqueeSpeed').val();
    
    saveUserSettings();
    
    // Aplicar configurações
    clearInterval(refreshInterval);
    startTicketPolling();
    applyTheme(userSettings.theme);
    applyCardSize(userSettings.cardSize);
    applyMarqueeSpeed(userSettings.marqueeSpeed);
    
    // Atualizar variáveis globais
    animationsEnabled = userSettings.enableAnimations;
    showDescriptions = userSettings.showDescriptions;
    
    // Atualizar visualização
    $('.ticket-description-tv').toggle(showDescriptions);
    
    // Aplicar animações ou remover
    if (!animationsEnabled) {
        $('body').addClass('no-animation');
    } else {
        $('body').removeClass('no-animation');
    }
    
    // Recarregar a página se a configuração de drag & drop mudou
    const dragdropChanged = userSettings.dragdropEnabled !== $('#enableDragDrop').prop('checked');
    
    // Fechar modal e mostrar confirmação
    $('#settingsModal').modal('hide');
    showToast('Configurações salvas com sucesso!');
    
    // Se a configuração de drag & drop mudou, é preciso reiniciar
    if (dragdropChanged) {
        setTimeout(() => {
            location.reload();
        }, 1000);
    }
}

function applyTheme(theme) {
    if (theme === 'light') {
        $('body').addClass('light-theme');
        $('#toggleTheme').html('<i class="fas fa-sun"></i>');
    } else {
        $('body').removeClass('light-theme');
        $('#toggleTheme').html('<i class="fas fa-moon"></i>');
    }
}

function applyCardSize(size) {
    $('body').removeClass('card-size-small card-size-large');
    if (size !== 'normal') {
        $('body').addClass('card-size-' + size);
    }
}

function applyMarqueeSpeed(speed) {
    $('.ticket-marquee-content').removeClass('marquee-speed-slow marquee-speed-normal marquee-speed-fast');
    if (speed !== 'normal') {
        $('.ticket-marquee-content').addClass('marquee-speed-' + speed);
    }
}

// Função para reproduzir som de notificação
function playNotificationSound() {
    const audio = document.getElementById('notificationSound');
    if (audio) {
        audio.play().catch(e => {
            console.log('Erro ao reproduzir som:', e);
        });
    }
}

// Função para solicitar permissão para notificações
function requestNotificationPermission() {
    if ('Notification' in window) {
        if (Notification.permission === 'default') {
            Notification.requestPermission().then(permission => {
                if (permission === 'granted') {
                    console.log('Permissão para notificações concedida');
                } else {
                    console.log('Permissão para notificações negada');
                }
            });
        }
    }
}

// Função para enviar notificação push
function sendPushNotification(title, options = {}) {
    if ('Notification' in window && Notification.permission === 'granted') {
        const notification = new Notification(title, {
            body: options.body || 'Novo chamado disponível',
            icon: options.icon || '/favicon.ico',
            badge: options.badge || '/favicon.ico',
            tag: options.tag || 'ticket-notification',
            renotify: true,
            requireInteraction: false
        });
        
        // Auto-fechar após 5 segundos
        setTimeout(() => {
            notification.close();
        }, 5000);
        
        // Evento de clique na notificação
        notification.onclick = function() {
            window.focus();
            notification.close();
        };
    }
}

// Função para mostrar notificação de novo ticket
function showNewTicketNotification(ticket) {
    const notification = $(`
        <div class="push-notification">
            <div class="d-flex align-items-center">
                <i class="fas fa-bell me-3 text-primary"></i>
                <div>
                    <strong>Novo Chamado #${ticket.id}</strong>
                    <br>
                    <small>${ticket.title}</small>
                </div>
            </div>
        </div>
    `);
    
    $('body').append(notification);
    
    // Mostrar notificação
    setTimeout(() => {
        notification.addClass('show');
    }, 100);
    
    // Ocultar notificação após 5 segundos
    setTimeout(() => {
        notification.removeClass('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 5000);
}

// Função para inicializar drag & drop
function initializeSortable() {
    if (!userSettings.dragdropEnabled) return;
    
    const columns = ['open-tickets', 'progress-tickets', 'resolved-tickets', 'closed-tickets'];
    
    columns.forEach(columnId => {
        const column = document.getElementById(columnId);
        if (column) {
            new Sortable(column, {
                group: 'tickets',
                animation: 150,
                ghostClass: 'ticket-ghost',
                chosenClass: 'ticket-drag',
                onEnd: function(evt) {
                    const ticketId = evt.item.getAttribute('data-ticket-id');
                    const newStatus = getStatusFromColumnId(evt.to.id);
                    
                    // Mostrar loading no ticket
                    const loadingOverlay = $('<div class="ticket-loading-overlay"><i class="fas fa-spinner fa-spin"></i></div>');
                    $(evt.item).css('position', 'relative').append(loadingOverlay);
                    
                    // Enviar atualização para o servidor
                    updateTicketStatus(ticketId, newStatus, evt.item);
                }
            });
        }
    });
}

// Função para obter status da coluna
function getStatusFromColumnId(columnId) {
    const statusMap = {
        'open-tickets': 'open',
        'progress-tickets': 'in_progress',
        'resolved-tickets': 'resolved',
        'closed-tickets': 'closed'
    };
    return statusMap[columnId] || 'open';
}

// Função para atualizar status do ticket
function updateTicketStatus(ticketId, newStatus, element) {
    $.ajax({
        url: "{{ route('api.tickets.updateStatus') }}",
        method: 'POST',
        data: {
            ticket_id: ticketId,
            status: newStatus
        },
        success: function(response) {
            // Remover loading overlay
            $(element).find('.ticket-loading-overlay').remove();
            
            // Adicionar classe de sucesso
            $(element).addClass('ticket-updated');
            
            // Atualizar contadores
            updateFilteredCounts();
            
            // Mostrar toast de sucesso
            showToast(`Chamado #${ticketId} movido para ${getStatusDisplayName(newStatus)}`);
            
            // Remover classe de sucesso após animação
            setTimeout(() => {
                $(element).removeClass('ticket-updated');
            }, 2000);
        },
        error: function(xhr, status, error) {
            console.error('Erro ao atualizar status do ticket:', error);
            
            // Remover loading overlay
            $(element).find('.ticket-loading-overlay').remove();
            
            // Reverter posição do ticket
            location.reload();
            
            // Mostrar erro
            showToast('Erro ao mover o chamado. Tente novamente.', 'error');
        }
    });
}

// Função para obter nome de exibição do status
function getStatusDisplayName(status) {
    const statusNames = {
        'open': 'Abertos',
        'in_progress': 'Em Andamento',
        'resolved': 'Resolvidos',
        'closed': 'Fechados'
    };
    return statusNames[status] || status;
}

// Função melhorada para toast com tipos diferentes
function showToast(message, type = 'success') {
    const toast = $('#toast');
    const toastMessage = $('#toast-message');
    
    // Remover classes anteriores
    toast.removeClass('toast-success toast-error toast-warning toast-info');
    
    // Adicionar classe do tipo
    toast.addClass(`toast-${type}`);
    
    // Definir ícone baseado no tipo
    let icon = 'fas fa-check-circle';
    switch (type) {
        case 'error':
            icon = 'fas fa-exclamation-circle';
            break;
        case 'warning':
            icon = 'fas fa-exclamation-triangle';
            break;
        case 'info':
            icon = 'fas fa-info-circle';
            break;
    }
    
    // Atualizar conteúdo
    toastMessage.html(`<i class="${icon} me-2"></i>${message}`);
    
    // Mostrar toast
    toast.addClass('show');
    
    // Ocultar após 4 segundos
    setTimeout(() => {
        toast.removeClass('show');
    }, 4000);
}

function updateTime() {
    const now = new Date();
    const formattedTime = now.toLocaleTimeString('pt-BR');
    const formattedDate = now.toLocaleDateString('pt-BR');
    $('#current-time').text(`${formattedDate} ${formattedTime}`);
}

function toggleFullScreen() {
    if (!document.fullscreenElement) {
        const docElem = document.documentElement;
        
        // Tenta diversos métodos de fullscreen para compatibilidade com diferentes navegadores
        if (docElem.requestFullscreen) {
            docElem.requestFullscreen().catch(err => {
                showFullscreenError();
            });
        } else if (docElem.webkitRequestFullscreen) { // Safari
            docElem.webkitRequestFullscreen();
        } else if (docElem.msRequestFullscreen) { // IE11
            docElem.msRequestFullscreen();
        } else if (docElem.mozRequestFullScreen) { // Firefox
            docElem.mozRequestFullScreen();
        } else {
            showFullscreenError();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        }
    }
}

function showFullscreenNotification() {
    const notification = $('<div class="fullscreen-notification">' +
        '<div class="notification-content">' +
            '<h3>Modo TV</h3>' +
            '<p>Clique no botão abaixo para ativar o modo de tela cheia para melhor visualização.</p>' +
            '<button id="enterFullscreenBtn" class="btn btn-warning btn-lg">' +
                '<i class="fas fa-expand me-2"></i>Entrar em Tela Cheia' +
            '</button>' +
            '<div class="mt-2">' +
                '<small class="text-muted">Você também pode pressionar F11 para entrar em tela cheia a qualquer momento</small>' +
            '</div>' +
        '</div>' +
    '</div>');
    
    $('body').append(notification);
    
    $('#enterFullscreenBtn').click(function() {
        toggleFullScreen();
        notification.fadeOut(500, function() {
            $(this).remove();
        });
    });
}

function showFullscreenError() {
    const alertBox = $('<div class="alert alert-warning alert-dismissible fade show fullscreen-alert" role="alert">' +
        'Não foi possível ativar o modo de tela cheia automaticamente. ' +
        'Tente pressionar F11 no seu teclado ou use o botão <i class="fas fa-expand"></i> no canto superior direito.' +
        '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>' +
    '</div>');
    
    alertBox.css({
        'position': 'fixed',
        'top': '10px',
        'left': '50%',
        'transform': 'translateX(-50%)',
        'z-index': '9999',
        'width': '80%',
        'max-width': '600px'
    });
    
    $('body').append(alertBox);
    
    setTimeout(function() {
        alertBox.alert('close');
    }, 7000);
}

function startTicketPolling() {
    // Atualizar baseado nas configurações do usuário
    const interval = userSettings.refreshInterval * 1000 || 15000;
    
    refreshInterval = setInterval(function() {
        checkForNewTickets();
    }, interval);
    
    // Atualização completa a cada 5 minutos
    setInterval(function() {
        refreshBoard();
    }, 300000);
}

function checkForNewTickets() {
    $.ajax({
        url: '{{ route("api.tickets.new") }}',
        method: 'GET',
        data: { last_check: lastCheck },
        success: function(response) {
            if (response.count > 0) {
                // Reproduzir som se habilitado
                if (soundEnabled) {
                    playNotificationSound();
                }
                
                // Enviar notificação push
                if (userSettings.pushNotifications) {
                    const plural = response.count > 1 ? 's' : '';
                    sendPushNotification(
                        `${response.count} novo${plural} chamado${plural}!`, 
                        {
                            body: `Você tem ${response.count} novo${plural} chamado${plural} para verificar.`,
                            icon: '/favicon.ico'
                        }
                    );
                }
                
                // Atualizar o painel
                refreshBoard();
            }
            
            lastCheck = response.last_check;
        },
        error: function(xhr, status, error) {
            console.error('Erro ao verificar novos tickets:', error);
        }
    });
}

function refreshBoard() {
    $.ajax({
        url: '{{ route("api.tickets.all") }}',
        method: 'GET',
        success: function(response) {
            updateTicketColumns(response.tickets);
            updateCounters(response.tickets);
            updateMarquee(response.tickets);
            lastCheck = response.last_check;
            
            // Aplicar filtros se estiverem ativos
            if ($('#filtersBar').is(':visible')) {
                applyFilters();
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro ao atualizar painel:', error);
        }
    });
}

function updateTicketColumns(tickets) {
    // Atualizar cada coluna
    Object.keys(tickets).forEach(function(status) {
        let columnId = status.replace('_', '-') + '-tickets';
        let column = $('#' + columnId);
        
        column.empty();
        
        tickets[status].forEach(function(ticket) {
            let card = createTicketCard(ticket);
            column.append(card);
            
            // Aplicar animação se habilitada
            if (animationsEnabled) {
                setTimeout(function() {
                    column.find('.ticket-card-tv:last').addClass('animate');
                }, 10);
            }
        });
        
        // Mostrar/ocultar descrições baseado na configuração
        if (!showDescriptions) {
            column.find('.ticket-description-tv').hide();
        }
    });
}

function updateCounters(tickets) {
    $('#open-count').text(tickets.open.length);
    $('#progress-count').text(tickets.in_progress.length);
    $('#resolved-count').text(tickets.resolved.length);
    $('#closed-count').text(tickets.closed.length);
}

function updateMarquee(tickets) {
    // Combinar tickets abertos e em andamento para o marquee
    let marqueeTickets = [...tickets.open, ...tickets.in_progress].sort((a, b) => 
        new Date(b.created_at) - new Date(a.created_at)
    ).slice(0, 10);
    
    let marqueeContent = '';
    marqueeTickets.forEach(function(ticket) {
        let priorityClass = '';
        let priorityText = '';
        
        if (ticket.priority === 'high') {
            priorityClass = 'bg-danger';
            priorityText = 'Alta';
        } else if (ticket.priority === 'medium') {
            priorityClass = 'bg-warning text-dark';
            priorityText = 'Média';
        } else {
            priorityClass = 'bg-success';
            priorityText = 'Baixa';
        }
        
        marqueeContent += `<span class="fs-4 me-5">
            <strong>#${ticket.id}</strong> - 
            ${ticket.title} - 
            <span class="badge ${priorityClass} fs-5">
                ${priorityText}
            </span>
        </span>`;
    });
    
    $('.ticket-marquee-content').html(marqueeContent);
}

function createTicketCard(ticket) {
    let priorityClass = 'priority-' + ticket.priority;
    let categoryName = ticket.category ? ticket.category.name : 'Sem categoria';
    let assignedTo = ticket.assigned_to ? ticket.assigned_to.name : 'Não atribuído';
    let priorityBadge = '';
    
    if (ticket.priority === 'high') {
        priorityBadge = '<span class="badge bg-danger fs-5">Alta</span>';
    } else if (ticket.priority === 'medium') {
        priorityBadge = '<span class="badge bg-warning text-dark fs-5">Média</span>';
    } else {
        priorityBadge = '<span class="badge bg-success fs-5">Baixa</span>';
    }
    
    // Limitar a descrição para exibição
    let description = ticket.description || 'Sem descrição';
    if (description.length > 150) {
        description = description.substring(0, 150) + '...';
    }
    
    return `
        <div class="ticket-card-tv ${priorityClass}" data-ticket-id="${ticket.id}" 
             data-category="${ticket.category ? ticket.category.id : ''}" 
             data-priority="${ticket.priority}"
             data-assigned="${ticket.assigned_to ? ticket.assigned_to.id : ''}">
            <div class="ticket-title-tv">#${ticket.id} - ${ticket.title}</div>
            <div class="ticket-description-tv">
                <strong>Descrição do Problema:</strong>
                <p>${description}</p>
            </div>
            <div class="ticket-meta-tv">
                <div class="mb-2">
                    <span class="ticket-category-tv">${categoryName}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <div>
                        <div><strong>Solicitante:</strong> ${ticket.user.name}</div>
                        <div><strong>Atribuído:</strong> ${assignedTo}</div>
                    </div>
                    <div>
                        <div><strong>Prioridade:</strong> ${priorityBadge}</div>
                        <div><strong>Criado:</strong> ${new Date(ticket.created_at).toLocaleString()}</div>
                    </div>
                </div>
            </div>
        </div>
    `;
}

function applyFilters() {
    const searchTerm = $('#searchFilter').val().toLowerCase();
    const priorityFilter = $('#priorityFilter').val();
    const categoryFilter = $('#categoryFilter').val();
    const assignedToFilter = $('#assignedToFilter').val();
    
    // Percorrer todos os tickets em todas as colunas
    $('.ticket-card-tv').each(function() {
        const card = $(this);
        let visible = true;
        
        // Filtro de busca
        if (searchTerm) {
            const ticketText = card.text().toLowerCase();
            if (!ticketText.includes(searchTerm)) {
                visible = false;
            }
        }
        
        // Filtro de prioridade
        if (priorityFilter && visible) {
            const ticketPriority = card.attr('data-priority');
            if (ticketPriority !== priorityFilter) {
                visible = false;
            }
        }
        
        // Filtro de categoria
        if (categoryFilter && visible) {
            const categoryId = card.attr('data-category');
            if (categoryId !== categoryFilter) {
                visible = false;
            }
        }
        
        // Filtro de responsável
        if (assignedToFilter && visible) {
            const assignedId = card.attr('data-assigned');
            if (assignedId !== assignedToFilter) {
                visible = false;
            }
        }
        
        // Aplicar visibilidade
        card.toggle(visible);
    });
    
    // Atualizar contadores para refletir apenas os tickets visíveis
    updateFilteredCounts();
}

function updateFilteredCounts() {
    // Atualizar contadores com base nos tickets visíveis
    $('#open-count').text($('#open-tickets .ticket-card-tv:visible').length);
    $('#progress-count').text($('#progress-tickets .ticket-card-tv:visible').length);
    $('#resolved-count').text($('#resolved-tickets .ticket-card-tv:visible').length);
    $('#closed-count').text($('#closed-tickets .ticket-card-tv:visible').length);
}

function loadMetrics() {
    // Exibir carregamento
    $('#metricsModal .modal-body').html('<div class="text-center my-5"><i class="fas fa-spinner fa-spin fa-4x"></i><p class="mt-3 fs-4">Carregando métricas...</p></div>');
    
    // Buscar dados de métricas via AJAX
    $.ajax({
        url: "{{ route('api.tickets.metrics') }}",
        method: 'GET',
        success: function(metrics) {
            // Restaurar estrutura original do modal
            $('#metricsModal .modal-body').html(`
                <div class="row g-3">
                    <!-- Métricas diárias -->
                    <div class="col-md-6">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i> Chamados Hoje vs Ontem</h5>
                            </div>
                            <div class="card-body" id="daily-metrics-content">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Distribuição por prioridade -->
                    <div class="col-md-6">
                        <div class="card border-warning h-100">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Distribuição por Prioridade</h5>
                            </div>
                            <div class="card-body" id="priority-metrics-content">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tempo médio de resolução -->
                    <div class="col-md-6">
                        <div class="card border-info h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-stopwatch me-2"></i> Tempo Médio de Resolução</h5>
                            </div>
                            <div class="card-body text-center" id="avg-time-content">
                            </div>
                        </div>
                    </div>
                    
                    <!-- SLA -->
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i> Cumprimento de SLA</h5>
                            </div>
                            <div class="card-body text-center" id="sla-content">
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            // Calcular porcentagens
            const totalTickets = metrics.priority_distribution.high + 
                                metrics.priority_distribution.medium + 
                                metrics.priority_distribution.low;
            const highPercent = totalTickets > 0 ? (metrics.priority_distribution.high / totalTickets * 100).toFixed(1) : 0;
            const mediumPercent = totalTickets > 0 ? (metrics.priority_distribution.medium / totalTickets * 100).toFixed(1) : 0;
            const lowPercent = totalTickets > 0 ? (metrics.priority_distribution.low / totalTickets * 100).toFixed(1) : 0;
            
            // Atualizar métricas diárias
            const dailyMetricsHtml = `
                <div class="row">
                    <div class="col-6">
                        <h6>Hoje:</h6>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Criados <span class="badge bg-primary rounded-pill">${metrics.today.created}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Resolvidos <span class="badge bg-success rounded-pill">${metrics.today.resolved}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Fechados <span class="badge bg-secondary rounded-pill">${metrics.today.closed}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <h6>Ontem:</h6>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Criados <span class="badge bg-primary rounded-pill">${metrics.yesterday.created}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Resolvidos <span class="badge bg-success rounded-pill">${metrics.yesterday.resolved}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Fechados <span class="badge bg-secondary rounded-pill">${metrics.yesterday.closed}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            `;
            
            // Atualizar barras de prioridade
            const priorityBarHtml = `
                <div class="progress mb-3" style="height: 30px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: ${highPercent}%" 
                        aria-valuenow="${metrics.priority_distribution.high}" aria-valuemin="0" aria-valuemax="${totalTickets}">
                        Alta: ${metrics.priority_distribution.high} (${highPercent}%)
                    </div>
                </div>
                <div class="progress mb-3" style="height: 30px;">
                    <div class="progress-bar bg-warning text-dark" role="progressbar" style="width: ${mediumPercent}%" 
                        aria-valuenow="${metrics.priority_distribution.medium}" aria-valuemin="0" aria-valuemax="${totalTickets}">
                        Média: ${metrics.priority_distribution.medium} (${mediumPercent}%)
                    </div>
                </div>
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: ${lowPercent}%" 
                        aria-valuenow="${metrics.priority_distribution.low}" aria-valuemin="0" aria-valuemax="${totalTickets}">
                        Baixa: ${metrics.priority_distribution.low} (${lowPercent}%)
                    </div>
                </div>
                <div class="text-center mt-2">
                    <strong>Total: ${totalTickets} chamados ativos</strong>
                </div>
            `;
            
            // Atualizar tempo médio de resolução
            const avgTimeHtml = `
                <h1 class="display-1">${metrics.avg_resolution_time}</h1>
                <h3>horas</h3>
            `;
            
            // Atualizar SLA
            const slaClass = metrics.sla_compliance >= 90 ? 'text-success' : 
                            (metrics.sla_compliance >= 75 ? 'text-warning' : 'text-danger');
            
            const slaHtml = `
                <h1 class="display-1 ${slaClass}">${metrics.sla_compliance}%</h1>
                <h3>dos chamados resolvidos dentro do SLA</h3>
            `;
            
            // Atualizar os elementos no modal
            $('#daily-metrics-content').html(dailyMetricsHtml);
            $('#priority-metrics-content').html(priorityBarHtml);
            $('#avg-time-content').html(avgTimeHtml);
            $('#sla-content').html(slaHtml);
            
            // Atualizar data de geração
            $('#metricsModal .modal-title').html(`<i class="fas fa-chart-bar me-2"></i> Métricas Avançadas <small>(atualizado em ${new Date().toLocaleString()})</small>`);
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar métricas:', error);
            $('#metricsModal .modal-body').html('<div class="alert alert-danger text-center my-5"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><p class="fs-4">Erro ao carregar métricas. Tente novamente.</p></div>');
        }
    });
}

// Função para carregar o dashboard executivo
function loadDashboard() {
    // Mostrar indicador de carregamento
    $('#dashboardModal .modal-body').html('<div class="text-center my-5"><i class="fas fa-spinner fa-spin fa-4x"></i><p class="mt-3 fs-4">Carregando dashboard...</p></div>');
    
    // Carregar dados via AJAX
    $.ajax({
        url: "{{ route('api.tickets.dashboard') }}",
        method: 'GET',
        success: function(data) {
            // Restaurar estrutura original do modal
            $('#dashboardModal .modal-body').html(`
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card h-100 border-danger">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                                    <h5 class="card-title">Total de Chamados</h5>
                                    <h2 class="text-danger" id="dashboard-total-tickets">${data.total_tickets}</h2>
                                    <p class="text-muted">Chamados ativos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Chamados Abertos</h5>
                                    <h2 class="text-warning" id="dashboard-open-tickets">${data.open_tickets}</h2>
                                    <p class="text-muted">Aguardando atendimento</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Resolvidos Hoje</h5>
                                    <h2 class="text-success" id="dashboard-resolved-today">${data.resolved_today}</h2>
                                    <p class="text-muted">Chamados concluídos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-stopwatch fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Tempo Médio</h5>
                                    <h2 class="text-info" id="dashboard-avg-time">${data.avg_resolution_time}h</h2>
                                    <p class="text-muted">Horas para resolução</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Chamados por Categoria</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="categoryChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Chamados por Prioridade</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="priorityChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Tendência de Chamados (7 dias)</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="trendsChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Desempenho por Atendente</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="agentPerformanceChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            // Criar/atualizar gráficos
            renderCategoryChart(data.categories);
            renderPriorityChart(data.priorities);
            renderTrendsChart(data.trends);
            renderAgentPerformanceChart(data.agent_performance);
            
            // Adicionar event handlers para botões
            $('#refreshDashboard').off('click').on('click', function() {
                loadDashboard();
            });
            
            $('#exportDashboardPDF').off('click').on('click', function() {
                window.openReport("{{ route('api.tickets.dashboard.export') }}");
            });
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar dashboard:', error);
            $('#dashboardModal .modal-body').html('<div class="alert alert-danger text-center my-5"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><p class="fs-4">Erro ao carregar dados do dashboard.</p><button class="btn btn-danger" id="retryDashboard">Tentar novamente</button></div>');
            
            $('#retryDashboard').on('click', function() {
                loadDashboard();
            });
        }
    });
}

// Função para renderizar gráfico de categorias
function renderCategoryChart(data) {
    const ctx = document.getElementById('categoryChart').getContext('2d');
    
    // Destruir gráfico anterior se existir
    if (categoryChart) {
        categoryChart.destroy();
    }
    
    categoryChart = new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: data.map(item => item.name),
            datasets: [{
                data: data.map(item => item.count),
                backgroundColor: [
                    '#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b',
                    '#5a5c69', '#6610f2', '#fd7e14', '#20c997', '#6f42c1'
                ],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
}

// Função para renderizar gráfico de prioridades
function renderPriorityChart(data) {
    const ctx = document.getElementById('priorityChart').getContext('2d');
    
    // Destruir gráfico anterior se existir
    if (priorityChart) {
        priorityChart.destroy();
    }
    
    const colors = {
        high: '#e74a3b',
        medium: '#f6c23e',
        low: '#1cc88a'
    };
    
    priorityChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: ['Alta', 'Média', 'Baixa'],
            datasets: [{
                label: 'Chamados por Prioridade',
                data: [data.high, data.medium, data.low],
                backgroundColor: [colors.high, colors.medium, colors.low]
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Função para renderizar gráfico de tendências
function renderTrendsChart(data) {
    const ctx = document.getElementById('trendsChart').getContext('2d');
    
    // Destruir gráfico anterior se existir
    if (trendsChart) {
        trendsChart.destroy();
    }
    
    trendsChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Novos Chamados',
                data: data.created,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                borderWidth: 2,
                fill: true
            }, {
                label: 'Chamados Resolvidos',
                data: data.resolved,
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}

// Função para renderizar gráfico de desempenho dos atendentes
function renderAgentPerformanceChart(data) {
    const ctx = document.getElementById('agentPerformanceChart').getContext('2d');
    
    // Destruir gráfico anterior se existir
    if (agentPerformanceChart) {
        agentPerformanceChart.destroy();
    }
    
    agentPerformanceChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.name),
            datasets: [{
                label: 'Chamados Resolvidos',
                data: data.map(item => item.resolved),
                backgroundColor: '#4e73df'
            }, {
                label: 'Tempo Médio (horas)',
                data: data.map(item => item.avg_time),
                backgroundColor: '#1cc88a',
                yAxisID: 'y1'
            }]
        },
        options: {
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Chamados Resolvidos'
                    }
                },
                y1: {
                    beginAtZero: true,
                    position: 'right',
                    title: {
                        display: true,
                        text: 'Tempo Médio (horas)'
                    },
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
}
}

// Função para atualizar dashboard
function updateDashboard() {
    $('#refreshDashboard').off('click').on('click', function() {
        loadDashboard();
    });
}

// Função para exportar dashboard
function exportDashboardPDF() {
    $('#exportDashboardPDF').off('click').on('click', function() {
        window.openReport("{{ route('api.tickets.dashboard.export') }}");
    });
}

// Event handlers para botões de exportação
function setupExportHandlers() {
    $('#exportMetricsPDF').off('click').on('click', function() {
        window.openReport("{{ route('api.tickets.metrics.export') }}");
    });
}

/* Novas funções loadMetrics e loadDashboard atualizadas */
function loadMetrics() {
    // Exibir carregamento
    $('#metricsModal .modal-body').html('<div class="text-center my-5"><i class="fas fa-spinner fa-spin fa-4x"></i><p class="mt-3 fs-4">Carregando métricas...</p></div>');
    
    // Buscar dados de métricas via AJAX
    $.ajax({
        url: "{{ route('api.tickets.metrics') }}",
        method: 'GET',
        success: function(metrics) {
            // Restaurar estrutura original do modal
            $('#metricsModal .modal-body').html(`
                <div class="row g-3">
                    <!-- Métricas diárias -->
                    <div class="col-md-6">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="fas fa-calendar-day me-2"></i> Chamados Hoje vs Ontem</h5>
                            </div>
                            <div class="card-body" id="daily-metrics-content">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Distribuição por prioridade -->
                    <div class="col-md-6">
                        <div class="card border-warning h-100">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Distribuição por Prioridade</h5>
                            </div>
                            <div class="card-body" id="priority-metrics-content">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Tempo médio de resolução -->
                    <div class="col-md-6">
                        <div class="card border-info h-100">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-stopwatch me-2"></i> Tempo Médio de Resolução</h5>
                            </div>
                            <div class="card-body text-center" id="avg-time-content">
                            </div>
                        </div>
                    </div>
                    
                    <!-- SLA -->
                    <div class="col-md-6">
                        <div class="card border-success h-100">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i> Cumprimento de SLA</h5>
                            </div>
                            <div class="card-body text-center" id="sla-content">
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            // Calcular porcentagens
            const totalTickets = metrics.priority_distribution.high + 
                                metrics.priority_distribution.medium + 
                                metrics.priority_distribution.low;
            const highPercent = totalTickets > 0 ? (metrics.priority_distribution.high / totalTickets * 100).toFixed(1) : 0;
            const mediumPercent = totalTickets > 0 ? (metrics.priority_distribution.medium / totalTickets * 100).toFixed(1) : 0;
            const lowPercent = totalTickets > 0 ? (metrics.priority_distribution.low / totalTickets * 100).toFixed(1) : 0;
            
            // Atualizar métricas diárias
            const dailyMetricsHtml = `
                <div class="row">
                    <div class="col-6">
                        <h6>Hoje:</h6>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Criados <span class="badge bg-primary rounded-pill">${metrics.today.created}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Resolvidos <span class="badge bg-success rounded-pill">${metrics.today.resolved}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Fechados <span class="badge bg-secondary rounded-pill">${metrics.today.closed}</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-6">
                        <h6>Ontem:</h6>
                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Criados <span class="badge bg-primary rounded-pill">${metrics.yesterday.created}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Resolvidos <span class="badge bg-success rounded-pill">${metrics.yesterday.resolved}</span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Fechados <span class="badge bg-secondary rounded-pill">${metrics.yesterday.closed}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            `;
            
            // Atualizar barras de prioridade
            const priorityBarHtml = `
                <div class="progress mb-3" style="height: 30px;">
                    <div class="progress-bar bg-danger" role="progressbar" style="width: ${highPercent}%" 
                        aria-valuenow="${metrics.priority_distribution.high}" aria-valuemin="0" aria-valuemax="${totalTickets}">
                        Alta: ${metrics.priority_distribution.high} (${highPercent}%)
                    </div>
                </div>
                <div class="progress mb-3" style="height: 30px;">
                    <div class="progress-bar bg-warning text-dark" role="progressbar" style="width: ${mediumPercent}%" 
                        aria-valuenow="${metrics.priority_distribution.medium}" aria-valuemin="0" aria-valuemax="${totalTickets}">
                        Média: ${metrics.priority_distribution.medium} (${mediumPercent}%)
                    </div>
                </div>
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: ${lowPercent}%" 
                        aria-valuenow="${metrics.priority_distribution.low}" aria-valuemin="0" aria-valuemax="${totalTickets}">
                        Baixa: ${metrics.priority_distribution.low} (${lowPercent}%)
                    </div>
                </div>
                <div class="text-center mt-2">
                    <strong>Total: ${totalTickets} chamados ativos</strong>
                </div>
            `;
            
            // Atualizar tempo médio de resolução
            const avgTimeHtml = `
                <h1 class="display-1">${metrics.avg_resolution_time}</h1>
                <h3>horas</h3>
            `;
            
            // Atualizar SLA
            const slaClass = metrics.sla_compliance >= 90 ? 'text-success' : 
                            (metrics.sla_compliance >= 75 ? 'text-warning' : 'text-danger');
            
            const slaHtml = `
                <h1 class="display-1 ${slaClass}">${metrics.sla_compliance}%</h1>
                <h3>dos chamados resolvidos dentro do SLA</h3>
            `;
            
            // Atualizar os elementos no modal
            $('#daily-metrics-content').html(dailyMetricsHtml);
            $('#priority-metrics-content').html(priorityBarHtml);
            $('#avg-time-content').html(avgTimeHtml);
            $('#sla-content').html(slaHtml);
            
            // Atualizar data de geração
            $('#metricsModal .modal-title').html(`<i class="fas fa-chart-bar me-2"></i> Métricas Avançadas <small>(atualizado em ${new Date().toLocaleString()})</small>`);
            
            // Adicionar event handler para exportação
            $('#exportMetricsPDF').off('click').on('click', function() {
                window.openReport("{{ route('api.tickets.metrics.export') }}");
            });
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar métricas:', error);
            $('#metricsModal .modal-body').html('<div class="alert alert-danger text-center my-5"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><p class="fs-4">Erro ao carregar métricas. Tente novamente.</p></div>');
        }
    });
}

function loadDashboard() {
    // Mostrar indicador de carregamento
    $('#dashboardModal .modal-body').html('<div class="text-center my-5"><i class="fas fa-spinner fa-spin fa-4x"></i><p class="mt-3 fs-4">Carregando dashboard...</p></div>');
    
    // Carregar dados via AJAX
    $.ajax({
        url: "{{ route('api.tickets.dashboard') }}",
        method: 'GET',
        success: function(data) {
            // Restaurar estrutura original do modal
            $('#dashboardModal .modal-body').html(`
                <div class="container-fluid">
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card h-100 border-danger">
                                <div class="card-body text-center">
                                    <i class="fas fa-exclamation-circle fa-3x text-danger mb-3"></i>
                                    <h5 class="card-title">Total de Chamados</h5>
                                    <h2 class="text-danger" id="dashboard-total-tickets">${data.total_tickets}</h2>
                                    <p class="text-muted">Chamados ativos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                                    <h5 class="card-title">Chamados Abertos</h5>
                                    <h2 class="text-warning" id="dashboard-open-tickets">${data.open_tickets}</h2>
                                    <p class="text-muted">Aguardando atendimento</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <h5 class="card-title">Resolvidos Hoje</h5>
                                    <h2 class="text-success" id="dashboard-resolved-today">${data.resolved_today}</h2>
                                    <p class="text-muted">Chamados concluídos</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-stopwatch fa-3x text-info mb-3"></i>
                                    <h5 class="card-title">Tempo Médio</h5>
                                    <h2 class="text-info" id="dashboard-avg-time">${data.avg_resolution_time}h</h2>
                                    <p class="text-muted">Horas para resolução</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Chamados por Categoria</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="categoryChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Chamados por Prioridade</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="priorityChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Tendência de Chamados (7 dias)</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="trendsChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Desempenho por Atendente</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="agentPerformanceChart" width="400" height="300"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `);
            
            // Criar/atualizar gráficos
            renderCategoryChart(data.categories);
            renderPriorityChart(data.priorities);
            renderTrendsChart(data.trends);
            renderAgentPerformanceChart(data.agent_performance);
            
            // Adicionar event handlers para botões
            $('#refreshDashboard').off('click').on('click', function() {
                loadDashboard();
            });
            
            $('#exportDashboardPDF').off('click').on('click', function() {
                window.openReport("{{ route('api.tickets.dashboard.export') }}");
            });
        },
        error: function(xhr, status, error) {
            console.error('Erro ao carregar dashboard:', error);
            $('#dashboardModal .modal-body').html('<div class="alert alert-danger text-center my-5"><i class="fas fa-exclamation-triangle fa-2x mb-3"></i><p class="fs-4">Erro ao carregar dados do dashboard.</p><button class="btn btn-danger" id="retryDashboard">Tentar novamente</button></div>');
            
            $('#retryDashboard').on('click', function() {
                loadDashboard();
            });
        }
    });
}
