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

    @yield('styles')

    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            margin: 0.25rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
        }
        .sidebar .nav-link i {
            margin-right: 0.5rem;
            width: 1.2rem;
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
    <div id="app">
        @auth
        <div class="container-fluid">
            <div class="row">
                <!-- Sidebar -->
                <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse px-0">
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
                                <a class="nav-link {{ request()->routeIs('tickets.boardTvEnhanced') ? 'active' : '' }}" href="{{ route('tickets.boardTvEnhanced') }}">
                                    <i class="bi bi-tv"></i> 
                                    Painel TV
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
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 content-wrapper">
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

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
