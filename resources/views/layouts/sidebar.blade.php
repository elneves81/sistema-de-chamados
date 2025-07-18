<!-- Menu lateral -->
<div class="sidebar bg-dark text-white">
    <div class="sidebar-header p-3">
        <h5>Sistema de Chamados</h5>
    </div>
    <ul class="nav flex-column">
        <li class="nav-item">
            <a href="{{ route('dashboard') }}" class="nav-link text-white {{ Request::is('dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>

        <!-- Chamados -->
        <li class="nav-item">
            <a href="#ticketsSubmenu" data-bs-toggle="collapse" class="nav-link text-white">
                <i class="fas fa-ticket-alt"></i> Chamados
            </a>
            <ul class="collapse {{ Request::is('tickets*') ? 'show' : '' }} list-unstyled" id="ticketsSubmenu">
                <li>
                    <a href="{{ route('tickets.create') }}" class="nav-link text-white">
                        <i class="fas fa-plus"></i> Novo Chamado
                    </a>
                </li>
                <li>
                    <a href="{{ route('tickets.index') }}" class="nav-link text-white">
                        <i class="fas fa-list"></i> Listar Chamados
                    </a>
                </li>
                <li>
                    <a href="{{ route('categories.index') }}" class="nav-link text-white">
                        <i class="fas fa-tags"></i> Categorias
                    </a>
                </li>
            </ul>
        </li>

        <!-- Assets -->
        <li class="nav-item">
            <a href="#assetsSubmenu" data-bs-toggle="collapse" class="nav-link text-white">
                <i class="fas fa-desktop"></i> Ativos
            </a>
            <ul class="collapse list-unstyled" id="assetsSubmenu">
                <li>
                    <a href="{{ route('assets.create') }}" class="nav-link text-white">
                        <i class="fas fa-plus"></i> Novo Ativo
                    </a>
                </li>
                <li>
                    <a href="{{ route('assets.index') }}" class="nav-link text-white">
                        <i class="fas fa-list"></i> Listar Ativos
                    </a>
                </li>
                <li>
                    <a href="{{ route('manufacturers.index') }}" class="nav-link text-white">
                        <i class="fas fa-industry"></i> Fabricantes
                    </a>
                </li>
                <li>
                    <a href="{{ route('models.index') }}" class="nav-link text-white">
                        <i class="fas fa-copy"></i> Modelos
                    </a>
                </li>
            </ul>
        </li>

        <!-- Software -->
        <li class="nav-item">
            <a href="#softwareSubmenu" data-bs-toggle="collapse" class="nav-link text-white">
                <i class="fas fa-laptop-code"></i> Software
            </a>
            <ul class="collapse list-unstyled" id="softwareSubmenu">
                <li>
                    <a href="{{ route('software.index') }}" class="nav-link text-white">
                        <i class="fas fa-list"></i> Listar Software
                    </a>
                </li>
                <li>
                    <a href="{{ route('licenses.index') }}" class="nav-link text-white">
                        <i class="fas fa-key"></i> Licenças
                    </a>
                </li>
            </ul>
        </li>

        <!-- Administração -->
        <li class="nav-item">
            <a href="#adminSubmenu" data-bs-toggle="collapse" class="nav-link text-white">
                <i class="fas fa-cogs"></i> Administração
            </a>
            <ul class="collapse list-unstyled" id="adminSubmenu">
                <li>
                    <a href="{{ route('users.index') }}" class="nav-link text-white">
                        <i class="fas fa-users"></i> Usuários
                    </a>
                </li>
                <li>
                    <a href="{{ route('departments.index') }}" class="nav-link text-white">
                        <i class="fas fa-building"></i> Departamentos
                    </a>
                </li>
                <li>
                    <a href="{{ route('locations.index') }}" class="nav-link text-white">
                        <i class="fas fa-map-marker-alt"></i> Localizações
                    </a>
                </li>
                <li>
                    <a href="{{ route('sla.index') }}" class="nav-link text-white">
                        <i class="fas fa-clock"></i> SLAs
                    </a>
                </li>
                <li>
                    <a href="{{ route('ldap.index') }}" class="nav-link text-white">
                        <i class="fas fa-network-wired"></i> LDAP/AD
                    </a>
                </li>
            </ul>
        </li>

        <!-- Relatórios -->
        <li class="nav-item">
            <a href="{{ route('reports.index') }}" class="nav-link text-white">
                <i class="fas fa-chart-bar"></i> Relatórios
            </a>
        </li>

        <!-- Base de Conhecimento -->
        <li class="nav-item">
            <a href="{{ route('knowledge.index') }}" class="nav-link text-white">
                <i class="fas fa-book"></i> Base de Conhecimento
            </a>
        </li>
    </ul>
</div>

<style>
.sidebar {
    min-height: 100vh;
    width: 250px;
    position: fixed;
    top: 0;
    left: 0;
    z-index: 100;
    padding-top: 60px;
}

.sidebar .nav-link {
    padding: 0.75rem 1rem;
    font-size: 0.9rem;
}

.sidebar .nav-link:hover {
    background-color: rgba(255, 255, 255, 0.1);
}

.sidebar .nav-link.active {
    background-color: rgba(255, 255, 255, 0.2);
}

.sidebar ul ul .nav-link {
    padding-left: 2.5rem;
}

main {
    margin-left: 250px;
    padding: 2rem;
}
</style>
