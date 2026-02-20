<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Favicon & App Icons -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <meta name="theme-color" content="#667eea">
    <link rel="manifest" href="{{ asset('manifest.json') }}">

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Mobile Improvements CSS -->
    <link href="{{ asset('css/mobile-improvements.css') }}" rel="stylesheet">
    
    <!-- Popup Styles CSS -->
    <link href="{{ asset('css/popup-styles.css') }}" rel="stylesheet">
    
    <!-- Custom Pagination CSS -->
    <link href="{{ asset('css/pagination-custom.css') }}" rel="stylesheet">
    
    <!-- Accessibility Improvements CSS -->
    <link href="{{ asset('css/accessibility-improvements.css') }}" rel="stylesheet">
    
    <!-- Responsive Advanced CSS -->
    <link href="{{ asset('css/responsive-advanced.css') }}" rel="stylesheet">

    @yield('styles')

    <style>
        .dashboard-layout {
            display: flex;
            min-height: 100vh;
        }

        .sidebar-responsive {
            width: 260px;
            flex-shrink: 0;
        }

        .content-wrapper {
            flex: 1;
            overflow-x: hidden;
        }
        html, body {
            height: 100%;
            margin: 0;
            overflow-x: hidden;
        }
        .sidebar, .sidebar-responsive {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        /* Textos da sidebar com contraste adequado */
        .sidebar .text-white, .sidebar-responsive .text-white,
        .sidebar .text-white-50, .sidebar-responsive .text-white-50,
        .sidebar h5, .sidebar-responsive h5,
        .sidebar small, .sidebar-responsive small,
        .sidebar .badge, .sidebar-responsive .badge {
            color: white !important;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }
        
        .sidebar .nav-link, .sidebar-responsive .nav-link {
            color: white !important;
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
            font-weight: 500;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active,
        .sidebar-responsive .nav-link:hover,
        .sidebar-responsive .nav-link.active {
            color: white !important;
            background-color: rgba(255,255,255,0.2);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        }
        
        .sidebar .nav-link i, .sidebar-responsive .nav-link i {
            margin-right: 0.5rem;
            width: 1.2rem;
            color: white !important;
        }
        
        /* Badge na sidebar */
        .sidebar .badge, .sidebar-responsive .badge {
            background-color: white !important;
            color: #667eea !important;
            font-weight: 700;
            text-shadow: none;
        }
        
        /* Badge de notificação (vermelho) */
        .sidebar .badge.bg-danger, .sidebar-responsive .badge.bg-danger {
            background-color: #dc3545 !important;
            color: white !important;
        }
        
        /* Separador */
        .sidebar hr, .sidebar-responsive hr {
            border-color: rgba(255, 255, 255, 0.3) !important;
            opacity: 1;
        }
        
        .content-wrapper {
           min-height: 100vh;

        }
        .status-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }
        .priority-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 0.375rem;
        }
        .priority-low { background-color: #d1ecf1; color: #0c5460; }
        .priority-medium { background-color: #fff3cd; color: #856404; }
        .priority-high { background-color: #f8d7da; color: #721c24; }
        .priority-urgent { background-color: #dc3545; color: white; }
        
        .status-open { background-color: #cff4fc; color: #087990; }
        .status-in_progress { background-color: #fff3cd; color: #856404; }
        .status-waiting { background-color: #f0d0f7; color: #6f42c1; }
        .status-resolved { background-color: #d1e7dd; color: #0f5132; }
        .status-closed { background-color: #e2e3e5; color: #495057; }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        /* Melhorias responsivas para dropdown de usuário */
        .dropdown-menu {
            display: none;
            position: absolute;
            z-index: 1060 !important;
            background: white;
            border: 1px solid rgba(0,0,0,.15);
            border-radius: 0.375rem;
            box-shadow: 0 0.5rem 1rem rgba(0,0,0,.175);
        }
        
        .dropdown-menu.show {
            display: block !important;
        }
        
        .dropdown-toggle {
            cursor: pointer;
            user-select: none;
            -webkit-tap-highlight-color: transparent; /* Remove highlight azul no iOS */
        }
        
        .dropdown-item {
            cursor: pointer;
            -webkit-tap-highlight-color: transparent;
        }
        
        .dropdown-item:hover,
        .dropdown-item:active {
            background-color: #f8f9fa;
        }
        
        /* Menu hambúrguer mobile */
        .menu-toggle {
            position: fixed;
            top: 10px;
            left: 10px;
            z-index: 1100;
            background: white;
            border: 2px solid #667eea;
            border-radius: 8px;
            width: 50px;
            height: 50px;
            display: none; /* Escondido por padrão */
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0,0,0,0.15);
            padding: 0;
        }
        
        /* Mostrar apenas em telas pequenas */
        @media (max-width: 767.98px) {
            .menu-toggle {
                display: flex !important;
            }
        }
        
        .hamburger {
            width: 24px;
            height: 18px;
            position: relative;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        
        .hamburger span {
            display: block;
            height: 3px;
            background-color: #667eea;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .hamburger.active span:nth-child(1) {
            transform: translateY(7.5px) rotate(45deg);
        }
        
        .hamburger.active span:nth-child(2) {
            opacity: 0;
        }
        
        .hamburger.active span:nth-child(3) {
            transform: translateY(-7.5px) rotate(-45deg);
        }
        
        /* Botão fechar dentro da sidebar */
        .btn-close-sidebar {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-close-sidebar:hover {
            background: rgba(255,255,255,0.3);
        }
        
        /* Sidebar mobile */
        .sidebar-backdrop {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1050;
            display: none;
        }
        
        .sidebar-backdrop.show {
            display: block;
        }
        
        @media (max-width: 767.98px) {
            .sidebar-responsive {
                position: fixed !important;
                top: 0;
                left: 0;
                bottom: 0;
                width: 280px;
                transform: translateX(-100%);
                transition: transform 0.3s ease;
                z-index: 1060; /* ACIMA do backdrop */
                overflow-y: auto;
            }
            
            .sidebar-responsive.open {
                transform: translateX(0) !important;
                box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            }
            
            .dropdown-menu {
                max-width: 90vw;
                right: 0 !important;
                left: auto !important;
            }
            
            #userDropdown {
                font-size: 0.9rem;
            }
            
            .dropdown-item {
                padding: 0.65rem 1rem;
                font-size: 0.95rem;
            }
        }
        
        @media (min-width: 768px) {
            .menu-toggle {
                display: none !important;
            }
            
            .sidebar-backdrop {
                display: none !important;
            }
            
            .sidebar-responsive {
                position: static !important;
                transform: none !important;
            }
            
            .btn-close-sidebar {
                display: none !important;
            }
            
            /* Layout colapsado (desktop): encolhe a sidebar e expande conteúdo */
            .layout-collapsed .sidebar-responsive {
                flex: 0 0 64px !important;
                max-width: 64px !important;
            }
            .layout-collapsed .content-wrapper {
                flex: 1 1 auto !important;
                max-width: calc(100% - 64px) !important;
            }
            .layout-collapsed .sidebar-responsive .nav-link {
                font-size: 0 !important; /* esconde texto sem quebrar ícones */
                padding-left: 0.75rem;
                padding-right: 0.75rem;
                text-align: center;
            }
            .layout-collapsed .sidebar-responsive .nav-link i {
                margin-right: 0 !important;
                font-size: 1.2rem;
            }
            .layout-collapsed .sidebar-responsive h5,
            .layout-collapsed .sidebar-responsive small,
            .layout-collapsed .sidebar-responsive .badge,
            .layout-collapsed .sidebar-responsive .collapse,
            .layout-collapsed .sidebar-responsive .bi-chevron-down,
            .layout-collapsed .sidebar-responsive .text-white-50 {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Skip Link para Acessibilidade -->
    <a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>
    
    <div id="app">
        @auth
        <div class="dashboard-layout">
            
                <!-- Botão Toggle Menu Mobile -->
                <button class="menu-toggle" aria-label="Abrir menu de navegação" aria-expanded="false" type="button">
                    <div class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                
                <!-- Backdrop para Sidebar Mobile -->
                <div class="sidebar-backdrop" aria-hidden="true"></div>
                
                <!-- Sidebar -->
                <nav class="sidebar-responsive" 
                     role="navigation" 
                     aria-label="Menu principal"
                     aria-hidden="false">
                    <!-- Botão fechar para mobile -->
                    <button class="btn-close-sidebar d-md-none" aria-label="Fechar menu">
                        <i class="bi bi-x-lg"></i>
                    </button>
                    
                    <div class="position-sticky pt-3">
                        <div class="text-center mb-4">
                            <h5 class="text-white">{{ config('app.name') }}</h5>
                            <small class="text-white-50">{{ auth()->user()->name ?? 'Usuário' }}</small><br>
                            <span class="badge bg-light text-dark">{{ ucfirst(auth()->user()->role ?? 'N/A') }}</span>
                        </div>
                        
                        <ul class="nav flex-column">
                            @if(auth()->user()->hasPermission('dashboard.view'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                                    <i class="bi bi-speedometer2"></i>
                                    Dashboard
                                </a>
                            </li>
                            @endif
                            @if(in_array(auth()->user()->role, ['technician', 'admin']))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('technician.dashboard') ? 'active' : '' }}" href="{{ route('technician.dashboard') }}">
                                    <i class="bi bi-kanban"></i>
                                    Central de Atendimentos
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('tickets.view.own') || auth()->user()->hasPermission('tickets.view.all'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('tickets.*') && !request()->routeIs('tickets.boardTv*') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                                    <i class="bi bi-ticket-perforated"></i>
                                    Chamados
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('board.view'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('tickets.boardTv*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#painelMenu">
                                    <i class="bi bi-tv"></i> 
                                    Painel TV
                                    <i class="bi bi-chevron-down ms-auto"></i>
                                </a>
                                <div class="collapse {{ request()->routeIs('tickets.boardTv*') ? 'show' : '' }}" id="painelMenu">
                                    <ul class="nav flex-column ms-3">
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('tickets.boardTvRepaginado') ? 'active' : '' }}" href="{{ route('tickets.boardTvRepaginado') }}">
                                                <i class="bi bi-magic"></i>
                                                Painel Repaginado
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('tickets.boardTvEnhanced') ? 'active' : '' }}" href="{{ route('tickets.boardTvEnhanced') }}">
                                                <i class="bi bi-grid-3x3-gap"></i>
                                                Painel Completo
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('tickets.boardTvLegacy') ? 'active' : '' }}" href="{{ route('tickets.boardTvLegacy') }}">
                                                <i class="bi bi-lightning"></i>
                                                Painel Legacy
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('dashboard.view'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('ai.*') ? 'active' : '' }}" href="{{ route('ai.dashboard') }}">
                                    <i class="bi bi-robot"></i> 
                                    🤖 Assistente IA
                                </a>
                            </li>
                            @endif
                            @if(auth()->user()->hasPermission('categories.view'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                                    <i class="bi bi-tags"></i>
                                    Categorias
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->hasPermission('machines.view'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('machines.*') ? 'active' : '' }}" href="{{ route('machines.index') }}">
                                    <i class="bi bi-pc-display-horizontal"></i>
                                    Inventário
                                </a>
                            </li>
                            @endif
                            
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('ramais.*') ? 'active' : '' }}" href="{{ route('ramais.index') }}">
                                    <i class="bi bi-telephone"></i>
                                    Ramais
                                </a>
                            </li>
                            
                            @if(auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="#" data-bs-toggle="collapse" data-bs-target="#adminMenu">
                                    <i class="bi bi-gear"></i>
                                    Administração
                                    <i class="bi bi-chevron-down ms-auto"></i>
                                </a>
                                <div class="collapse {{ request()->routeIs('admin.*') ? 'show' : '' }}" id="adminMenu">
                                    <ul class="nav flex-column ms-3">
                                        @if(auth()->user()->hasPermission('users.view'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" href="{{ route('admin.users.index') }}">
                                                <i class="bi bi-people"></i>
                                                Usuários
                                            </a>
                                        </li>
                                        @endif
                                        @if(auth()->user()->hasPermission('users.permissions'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.permissions.*') ? 'active' : '' }}" href="{{ route('admin.permissions.index') }}">
                                                <i class="bi bi-shield-check"></i>
                                                Permissões
                                            </a>
                                        </li>
                                        @endif
                                        @if(auth()->user()->hasPermission('users.view'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.contact.*') ? 'active' : '' }}" href="{{ route('admin.contact.list') }}">
                                                <i class="bi bi-chat-dots"></i>
                                                Mensagens de Contato
                                                @php
                                                    // Contar apenas mensagens pendentes (não respondidas)
                                                    $pendingCount = \App\Models\ContactMessage::where('status', 'pendente')
                                                        ->whereNull('responded_at')
                                                        ->count();
                                                @endphp
                                                @if($pendingCount > 0)
                                                    <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                                                @endif
                                            </a>
                                        </li>
                                        @endif
                                        @if(auth()->user()->hasPermission('users.view'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.locations.*') ? 'active' : '' }}" href="{{ route('admin.locations.index') }}">
                                                <i class="bi bi-geo-alt"></i>
                                                Localizações
                                            </a>
                                        </li>
                                        @endif
                                        @if(auth()->user()->hasPermission('users.view'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.backup.*') ? 'active' : '' }}" href="{{ route('admin.backup.index') }}">
                                                <i class="bi bi-shield-check"></i>
                                                Backups
                                            </a>
                                        </li>
                                        @endif
                                        @if(auth()->user()->hasPermission('users.view'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.ubs.*') ? 'active' : '' }}" href="{{ route('admin.ubs.index') }}">
                                                <i class="bi bi-hospital"></i>
                                                Dashboard UBS
                                            </a>
                                        </li>
                                        @endif
                                        @if(auth()->user()->hasPermission('system.monitoring'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.monitoring') ? 'active' : '' }}" href="{{ route('admin.monitoring') }}">
                                                <i class="bi bi-activity"></i>
                                                Monitoramento
                                            </a>
                                        </li>
                                        @endif
                                        @if(auth()->user()->hasPermission('system.ldap'))
                                        <li class="nav-item">
                                            <a class="nav-link {{ request()->routeIs('admin.ldap.*') ? 'active' : '' }}" href="{{ route('admin.ldap.import.form') }}">
                                                <i class="bi bi-shield-lock"></i>
                                                Importar LDAP (AD)
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </li>
                            @endif
                        </ul>
                        
                        <hr class="text-white-50">
                        
                        <ul class="nav flex-column">
                            <!-- Sistema de Mensagens - Disponível para todos -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" href="{{ route('messages.index') }}">
                                    <i class="bi bi-envelope"></i>
                                    Mensagens
                                    <span class="badge bg-danger ms-2" id="sidebar-messages-badge" style="display: none;">0</span>
                                </a>
                            </li>
                            
                            @if(auth()->user()->role === 'admin')
                            <!-- Preferências de Notificação - Apenas admin -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('notifications.preferences*') ? 'active' : '' }}" href="{{ route('notifications.preferences') }}">
                                    <i class="bi bi-bell"></i>
                                    Notificações
                                </a>
                            </li>
                            @endif
                            
                            <!-- Fale Conosco - Disponível para todos -->
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('contact.*') ? 'active' : '' }}" href="{{ route('contact.index') }}">
                                    <i class="bi bi-headset"></i>
                                    Fale Conosco
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('logout') }}"
                                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <i class="bi bi-box-arrow-right"></i>
                                    Sair
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                    @csrf
                                </form>
                            </li>
                        </ul>
                    </div>
                </nav>

                <!-- Main content -->
                <main class="content-wrapper px-md-4" 
                      id="main-content" 
                      role="main"
                      aria-label="Conteúdo principal">
                    <!-- Top Navigation Bar -->
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom mb-3">
                        <div class="d-flex align-items-center gap-2">
                            <!-- Botão de encolher/expandir a sidebar (desktop) -->
                            <button id="sidebarCollapseBtn" class="btn btn-outline-secondary d-none d-md-inline-flex" type="button" aria-label="Encolher ou expandir menu lateral" title="Encolher/expandir menu">
                                <i class="bi bi-layout-sidebar-inset"></i>
                            </button>
                            <!-- Breadcrumb ou título da página pode ficar aqui -->
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Notificação de Mensagens -->
                            @include('components.messages-notification')
                            
                            <!-- Dropdown do usuário -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center gap-2" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="min-height: 44px; padding: 0.5rem 1rem;">
                                    <i class="bi bi-person-circle" style="font-size: 1.3rem;"></i> 
                                    <span class="d-none d-md-inline">{{ auth()->user()->name }}</span>
                                    <span class="d-md-none">{{ Str::limit(auth()->user()->name, 15) }}</span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm" aria-labelledby="userDropdown" style="min-width: 200px; z-index: 1060;">
                                    <li class="px-3 py-2 border-bottom d-md-none">
                                        <small class="text-muted d-block">Logado como:</small>
                                        <strong class="d-block text-truncate">{{ auth()->user()->name }}</strong>
                                    </li>
                                    <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i> Perfil</a></li>
                                    <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-gear me-2"></i> Configurações</a></li>
                                    <li><hr class="dropdown-divider my-1"></li>
                                    <li>
                                        <a class="dropdown-item text-danger py-2 fw-semibold" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('top-logout-form').submit();">
                                            <i class="bi bi-box-arrow-right me-2"></i> Sair
                                        </a>
                                        <form id="top-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                            @csrf
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    
                    <div class="py-4">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @yield('content')
                    </div>
                    
                    <!-- Footer com marca d'água -->
                    <footer class="text-center py-3 mt-4 border-top">
                        <small class="text-muted" style="font-size: 0.75rem; opacity: 0.6;">
                            <span style="color: #10b981; font-weight: 700;">HUBI</span> 
                            <span style="color: #f97316; font-weight: 700;">SOFTWARE</span>
                        </small>
                    </footer>
                </main>
        </div>
        @else
        <!-- Layout para páginas de login/registro -->
        <main class="py-4">
            @yield('content')
        </main>
        @endauth
    </div>

    <!-- Chat Flutuante da IA (disponível em todas as páginas) -->
    @auth
        @include('components.ai-float-chat')
    @endauth

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Timer Manager (para evitar loops) -->
    <script src="{{ asset('js/timer-manager.js') }}"></script>
    
    <!-- Popup Manager LITE (versão otimizada) -->
    <script src="{{ asset('js/popup-manager-lite.js') }}"></script>
    
    <!-- Performance Monitor (para debug) -->
    <script src="{{ asset('js/performance-monitor.js') }}"></script>
    
    <!-- Accessibility and UX Improvements -->
    <script src="{{ asset('js/accessibility-ux.js') }}"></script>
    
    <!-- Sincronização de badges de mensagens -->
    <script>
        // Função para sincronizar badges de mensagens
        function syncMessagesBadges(count) {
            const sidebarBadge = document.getElementById('sidebar-messages-badge');
            const topBadge = document.getElementById('messagesBadge');
            
            if (sidebarBadge) {
                if (count > 0) {
                    sidebarBadge.textContent = count > 99 ? '99+' : count;
                    sidebarBadge.style.display = 'inline';
                } else {
                    sidebarBadge.style.display = 'none';
                }
            }
            
            // O badge do topo é gerenciado pelo componente messages-notification
        }
        
        // Observar mudanças no badge do topo e sincronizar com o sidebar
        document.addEventListener('DOMContentLoaded', function() {
            // Observer para mudanças no badge de mensagens
            const observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.target.id === 'messagesBadge') {
                        const display = window.getComputedStyle(mutation.target).display;
                        const count = display !== 'none' ? parseInt(mutation.target.textContent) || 0 : 0;
                        syncMessagesBadges(count);
                    }
                });
            });
            
            const topBadge = document.getElementById('messagesBadge');
            if (topBadge) {
                observer.observe(topBadge, { 
                    attributes: true, 
                    childList: true, 
                    subtree: true 
                });
                
                // Sincronizar inicialmente
                const display = window.getComputedStyle(topBadge).display;
                const count = display !== 'none' ? parseInt(topBadge.textContent) || 0 : 0;
                syncMessagesBadges(count);
            }
        });
    </script>
    
    <!-- Script para logout seguro -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Inicializando dropdowns...');
            
            // ========================================
            // TOGGLE SIDEBAR (DESKTOP)
            // ========================================
            const collapseBtn = document.getElementById('sidebarCollapseBtn');
            const BODY = document.body;
            const STORAGE_KEY = 'layoutCollapsed';
            try {
                const saved = localStorage.getItem(STORAGE_KEY);
                if (saved === '1') {
                    BODY.classList.add('layout-collapsed');
                }
            } catch (e) { /* ignore storage errors */ }
            
            if (collapseBtn) {
                collapseBtn.addEventListener('click', function() {
                    BODY.classList.toggle('layout-collapsed');
                    try {
                        localStorage.setItem(STORAGE_KEY, BODY.classList.contains('layout-collapsed') ? '1' : '0');
                    } catch (e) { /* ignore */ }
                });
            }
            
            // SEMPRE usar fallback manual para garantir funcionamento
            const toggles = document.querySelectorAll('[data-bs-toggle="dropdown"]');
            console.log('Dropdowns encontrados:', toggles.length);
            
            toggles.forEach((toggle, index) => {
                const dropdown = toggle.closest('.dropdown');
                const menu = dropdown?.querySelector('.dropdown-menu');
                
                if (!menu) {
                    console.warn('Menu não encontrado para dropdown', index);
                    return;
                }
                
                console.log('Configurando dropdown', index, toggle.id);

                // Abrir/fechar no clique do botão
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const isOpen = menu.classList.contains('show');
                    console.log('Clique no dropdown', this.id, 'aberto:', isOpen);

                    // Fechar todos os outros dropdowns
                    document.querySelectorAll('.dropdown-menu.show').forEach(m => {
                        if (m !== menu) {
                            m.classList.remove('show');
                            m.previousElementSibling?.setAttribute('aria-expanded', 'false');
                        }
                    });

                    // Toggle do dropdown atual
                    if (!isOpen) {
                        menu.classList.add('show');
                        toggle.setAttribute('aria-expanded', 'true');
                        console.log('Dropdown aberto');
                    } else {
                        menu.classList.remove('show');
                        toggle.setAttribute('aria-expanded', 'false');
                        console.log('Dropdown fechado');
                    }
                });

                // Fechar ao clicar fora
                document.addEventListener('click', function(e) {
                    if (!dropdown.contains(e.target)) {
                        menu.classList.remove('show');
                        toggle.setAttribute('aria-expanded', 'false');
                    }
                });
                
                // Não fechar ao clicar dentro do menu (exceto em links)
                menu.addEventListener('click', function(e) {
                    if (e.target.tagName === 'A' || e.target.closest('a')) {
                        // Se for um link, deixar o comportamento padrão
                        return;
                    }
                    e.stopPropagation();
                });
            });

            // ========================================
            // MENU MOBILE (HAMBÚRGUER)
            // ========================================
            const menuToggle = document.querySelector('.menu-toggle');
            const sidebar = document.querySelector('.sidebar-responsive');
            const sidebarBackdrop = document.querySelector('.sidebar-backdrop');
            
            if (menuToggle && sidebar) {
                console.log('Menu mobile configurado');
                
                let isMenuOpen = false;
                
                // Função para abrir menu
                const openMenu = () => {
                    isMenuOpen = true;
                    sidebar.classList.add('open');
                    sidebar.setAttribute('aria-hidden', 'false');
                    if (sidebarBackdrop) {
                        sidebarBackdrop.classList.add('show');
                    }
                    menuToggle.setAttribute('aria-expanded', 'true');
                    const hamburger = menuToggle.querySelector('.hamburger');
                    if (hamburger) hamburger.classList.add('active');
                    document.body.style.overflow = 'hidden'; // Prevenir scroll
                    console.log('Menu aberto');
                };
                
                // Função para fechar menu
                const closeMenu = () => {
                    isMenuOpen = false;
                    sidebar.classList.remove('open');
                    sidebar.setAttribute('aria-hidden', 'true');
                    if (sidebarBackdrop) {
                        sidebarBackdrop.classList.remove('show');
                    }
                    menuToggle.setAttribute('aria-expanded', 'false');
                    const hamburger = menuToggle.querySelector('.hamburger');
                    if (hamburger) hamburger.classList.remove('active');
                    document.body.style.overflow = ''; // Restaurar scroll
                    console.log('Menu fechado');
                };
                
                // Abrir/fechar menu ao clicar no hambúrguer
                menuToggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    console.log('Menu toggle clicado, isMenuOpen:', isMenuOpen);
                    
                    if (!isMenuOpen) {
                        openMenu();
                    } else {
                        closeMenu();
                    }
                });
                
                // Suporte para touch (mobile)
                menuToggle.addEventListener('touchend', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    e.stopImmediatePropagation();
                    
                    console.log('Menu toggle touched, isMenuOpen:', isMenuOpen);
                    
                    if (!isMenuOpen) {
                        openMenu();
                    } else {
                        closeMenu();
                    }
                }, { passive: false });
                
                // Prevenir fechamento ao clicar na própria sidebar
                sidebar.addEventListener('click', function(e) {
                    e.stopPropagation();
                });
                
                // Fechar menu ao clicar no backdrop
                if (sidebarBackdrop) {
                    sidebarBackdrop.addEventListener('click', function(e) {
                        e.stopPropagation();
                        closeMenu();
                    });
                }
                
                // Fechar menu ao clicar em qualquer link da sidebar
                const sidebarLinks = sidebar.querySelectorAll('a');
                sidebarLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        setTimeout(closeMenu, 200);
                    });
                });
                
                // Botão fechar dentro da sidebar
                const btnCloseSidebar = sidebar.querySelector('.btn-close-sidebar');
                if (btnCloseSidebar) {
                    btnCloseSidebar.addEventListener('click', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        closeMenu();
                    });
                }
            } else {
                console.warn('Menu mobile não encontrado');
            }

            // ========================================
            // LOGOUT FORMS
            // ========================================
            const logoutForms = ['logout-form', 'top-logout-form'];
            
            logoutForms.forEach(formId => {
                const form = document.getElementById(formId);
                if (form) {
                    // Adicionar evento de submit para debug
                    form.addEventListener('submit', function(e) {
                        console.log('Logout form submitted:', formId);
                    });
                }
            });
            
            // Logout links
            const logoutLinks = document.querySelectorAll('a[href*="logout"]');
            logoutLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    // Tentar encontrar o form correspondente
                    let formId;
                    if (this.getAttribute('onclick') && this.getAttribute('onclick').includes('logout-form')) {
                        formId = 'logout-form';
                    } else if (this.getAttribute('onclick') && this.getAttribute('onclick').includes('top-logout-form')) {
                        formId = 'top-logout-form';
                    }
                    
                    const form = document.getElementById(formId);
                    if (form) {
                        console.log('Submitting logout form:', formId);
                        form.submit();
                    } else {
                        console.log('Form not found, redirecting to logout URL');
                        // Fallback: redirecionar diretamente (usará a rota GET)
                        window.location.href = this.href;
                    }
                });
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>
