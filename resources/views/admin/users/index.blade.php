@extends('layouts.app')

@section('title', 'Administração de Usuários')

@section('content')
<div class="container-fluid">
    <!-- Header com ações -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">👥 Administração de Usuários</h1>
                    <p class="text-muted">Gerencie usuários, funções e permissões</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Novo Usuário
                    </a>
                    <button type="button" class="btn btn-outline-primary dropdown-toggle dropdown-toggle-split" data-bs-toggle="dropdown">
                        <span class="visually-hidden">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#importModal">
                            <i class="bi bi-upload"></i> Importar CSV
                        </a></li>
                        <li><a class="dropdown-item" href="{{ route('admin.users.export') }}">
                            <i class="bi bi-download"></i> Exportar CSV
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Total de Usuários</h5>
                            <h2 class="mb-0">{{ $users->total() }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-people fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Administradores</h5>
                            <h2 class="mb-0">{{ \App\Models\User::where('role', 'admin')->count() }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-shield-check fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Técnicos</h5>
                            <h2 class="mb-0">{{ \App\Models\User::where('role', 'technician')->count() }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-gear fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title">Clientes</h5>
                            <h2 class="mb-0">{{ \App\Models\User::where('role', 'customer')->count() }}</h2>
                        </div>
                        <div class="align-self-center">
                            <i class="bi bi-person fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros e busca -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label for="search" class="form-label">Buscar</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Nome ou email...">
                </div>
                <div class="col-md-3">
                    <label for="role" class="form-label">Função</label>
                    <select class="form-select" id="role" name="role">
                        <option value="">Todas as funções</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Administrador</option>
                        <option value="technician" {{ request('role') === 'technician' ? 'selected' : '' }}>Técnico</option>
                        <option value="customer" {{ request('role') === 'customer' ? 'selected' : '' }}>Cliente</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativos</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="location" class="form-label">Localização</label>
                    <select class="form-select" id="location" name="location">
                        <option value="">Todas as UBSs</option>
                        <option value="null" {{ request('location') === 'null' ? 'selected' : '' }}>Sem localização</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ request('location') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search"></i> Buscar
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela de usuários -->
    <div class="card">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Lista de Usuários</h5>
                <div>
                    <button type="button" class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn" disabled>
                        <i class="bi bi-trash"></i> Excluir Selecionados
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-toggle="dropdown">
                        <i class="bi bi-gear"></i> Ações em Lote
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('change_role', 'admin')">
                            Tornar Administrador
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('change_role', 'technician')">
                            Tornar Técnico
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('change_role', 'customer')">
                            Tornar Cliente
                        </a></li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($users->count() > 0)
            <form id="bulkActionForm" method="POST" action="{{ route('admin.users.bulk-action') }}">
                @csrf
                <input type="hidden" name="action" id="bulkAction">
                <input type="hidden" name="role" id="bulkRole">
                
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th width="50">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>Usuário</th>
                                <th>Função</th>
                                <th>Localização</th>
                                <th>Contato</th>
                                <th>Tickets</th>
                                <th>Cadastro</th>
                                <th width="120">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" name="user_ids[]" value="{{ $user->id }}" 
                                           class="form-check-input user-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar me-3">
                                            <div class="avatar-circle">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </div>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">{{ $user->name }}</h6>
                                            <small class="text-muted">{{ $user->email }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : ($user->role === 'technician' ? 'warning' : 'info') }}">
                                        @if($user->role === 'admin')
                                            <i class="bi bi-shield-check"></i> Administrador
                                        @elseif($user->role === 'technician')
                                            <i class="bi bi-gear"></i> Técnico
                                        @else
                                            <i class="bi bi-person"></i> Cliente
                                        @endif
                                    </span>
                                </td>
                                <td>
                                    @if($user->location)
                                        <div class="d-flex align-items-center">
                                            <i class="bi bi-geo-alt text-primary me-2"></i>
                                            <div>
                                                <small class="fw-bold">{{ $user->location->short_name }}</small><br>
                                                <small class="text-muted">{{ $user->location->name }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <div class="text-center">
                                            <button type="button" class="btn btn-sm btn-outline-warning" 
                                                    onclick="assignLocation({{ $user->id }})" title="Atribuir Localização">
                                                <i class="bi bi-geo-alt"></i> Definir UBS
                                            </button>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        @if($user->phone)
                                            <small><i class="bi bi-telephone"></i> {{ $user->phone }}</small><br>
                                        @endif
                                        @if($user->department)
                                            <small><i class="bi bi-building"></i> {{ $user->department }}</small>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <small>
                                        <span class="badge bg-primary">{{ $user->tickets()->count() }}</span> Criados<br>
                                        <span class="badge bg-success">{{ $user->assignedTickets()->count() }}</span> Atribuídos
                                    </small>
                                </td>
                                <td>
                                    <small>{{ $user->created_at->format('d/m/Y') }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.users.show', $user) }}" 
                                           class="btn btn-outline-primary btn-sm" title="Ver">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" 
                                           class="btn btn-outline-secondary btn-sm" title="Editar">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        @if($user->id !== auth()->id())
                                        <button type="button" class="btn btn-outline-danger btn-sm" 
                                                onclick="deleteUser({{ $user->id }})" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>
            @else
            <div class="text-center py-5">
                <i class="bi bi-people fs-1 text-muted"></i>
                <h5 class="mt-3 text-muted">Nenhum usuário encontrado</h5>
                <p class="text-muted">Tente ajustar os filtros ou criar um novo usuário.</p>
            </div>
            @endif
        </div>
        
        @if($users->hasPages())
        <div class="card-footer">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal de Importação -->
<div class="modal fade" id="importModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="POST" action="{{ route('admin.users.import') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Importar Usuários</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="file" class="form-label">Arquivo CSV</label>
                        <input type="file" class="form-control" id="file" name="file" accept=".csv" required>
                        <div class="form-text">
                            O arquivo deve conter as colunas: name, email, role, password, phone, department
                        </div>
                    </div>
                    <div class="alert alert-info">
                        <h6>Formato esperado:</h6>
                        <code>name,email,role,password,phone,department</code><br>
                        <code>João Silva,joao@email.com,customer,123456,11999999999,TI</code>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Importar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Estilos personalizados -->
<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(45deg, #007bff, #0056b3);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 14px;
}

.table td {
    vertical-align: middle;
}
</style>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox
    const selectAll = document.getElementById('selectAll');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    // Verificar se os elementos existem antes de adicionar eventos
    if (selectAll) {
        selectAll.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkButtons();
        });
    }

    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkButtons);
    });

    function updateBulkButtons() {
        const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
        if (bulkDeleteBtn) {
            bulkDeleteBtn.disabled = checkedBoxes.length === 0;
        }
    }

    // Bulk delete
    if (bulkDeleteBtn) {
        bulkDeleteBtn.addEventListener('click', function() {
            if (confirm('Tem certeza que deseja excluir os usuários selecionados?')) {
                bulkAction('delete');
            }
        });
    }
});

function bulkAction(action, role = null) {
    const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Selecione pelo menos um usuário');
        return;
    }

    if (confirm(`Tem certeza que deseja executar esta ação em ${checkedBoxes.length} usuário(s)?`)) {
        const bulkActionElement = document.getElementById('bulkAction');
        const bulkRoleElement = document.getElementById('bulkRole');
        const bulkActionForm = document.getElementById('bulkActionForm');
        
        if (bulkActionElement) {
            bulkActionElement.value = action;
        }
        if (role && bulkRoleElement) {
            bulkRoleElement.value = role;
        }
        if (bulkActionForm) {
            bulkActionForm.submit();
        }
    }
}

function deleteUser(userId) {
    if (confirm('Tem certeza que deseja excluir este usuário?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/users/${userId}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

function assignLocation(userId) {
    const modal = new bootstrap.Modal(document.getElementById('assignLocationModal'));
    document.getElementById('assignLocationUserId').value = userId;
    modal.show();
}

function saveUserLocation() {
    const userId = document.getElementById('assignLocationUserId').value;
    const locationId = document.getElementById('assignLocationSelect').value;
    
    if (!locationId) {
        alert('Selecione uma UBS');
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = `/admin/users/${userId}/assign-location`;
    form.innerHTML = `
        @csrf
        <input type="hidden" name="location_id" value="${locationId}">
    `;
    document.body.appendChild(form);
    form.submit();
}
</script>

<!-- Modal para atribuir localização -->
<div class="modal fade" id="assignLocationModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-geo-alt"></i> Atribuir UBS ao Usuário
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="assignLocationForm">
                    <input type="hidden" id="assignLocationUserId">
                    <div class="mb-3">
                        <label for="assignLocationSelect" class="form-label">Selecione a UBS:</label>
                        <select class="form-select" id="assignLocationSelect" required>
                            <option value="">Escolha uma UBS...</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}">{{ $location->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="saveUserLocation()">
                    <i class="bi bi-save"></i> Salvar
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
