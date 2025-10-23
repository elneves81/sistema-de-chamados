<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

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
    </style>
</head>
<body>
    <!-- Skip Link para Acessibilidade -->
    <a href="#main-content" class="skip-link">Pular para o conteúdo principal</a>
    
    <div id="app">
        @auth
        <div class="container-fluid">
            <div class="row">
                <!-- Botão Toggle Menu Mobile -->
                <button class="menu-toggle d-md-none" aria-label="Abrir menu de navegação" aria-expanded="false">
                    <div class="hamburger">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                </button>
                
                <!-- Backdrop para Sidebar Mobile -->
                <div class="sidebar-backdrop" aria-hidden="true"></div>
                
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar-responsive collapse px-0" 
                     role="navigation" 
                     aria-label="Menu principal"
                     aria-hidden="false">
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
                            @if(auth()->user()->hasPermission('tickets.view.own') || auth()->user()->hasPermission('tickets.view.all'))
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
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
                                                    $pendingCount = \App\Models\ContactMessage::where('status', 'pendente')->count();
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
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content-wrapper" 
                      id="main-content" 
                      role="main"
                      aria-label="Conteúdo principal">
                    <!-- Top Navigation Bar -->
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom mb-3">
                        <div>
                            <!-- Breadcrumb ou título da página pode ficar aqui -->
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <!-- Notificação de Mensagens -->
                            @include('components.messages-notification')
                            
                            <!-- Dropdown do usuário -->
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i> Perfil</a></li>
                                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i> Configurações</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('logout') }}"
                                           onclick="event.preventDefault(); document.getElementById('top-logout-form').submit();">
                                            <i class="bi bi-box-arrow-right"></i> Sair
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
                </main>
            </div>
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
            // Logout forms
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
