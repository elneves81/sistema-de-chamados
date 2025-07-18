@extends('layouts.app')

@section('title', 'Ativos')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-laptop text-primary me-2"></i>
                Gerenciamento de Ativos
            </h1>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item active">Ativos</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.assets.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i>
                Novo Ativo
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card shadow mb-4">
        <div class="card-header bg-light">
            <h6 class="card-title mb-0">
                <i class="fas fa-filter me-2"></i>
                Filtros
                <button class="btn btn-sm btn-outline-secondary ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#filtersCollapse">
                    <i class="fas fa-chevron-down"></i>
                </button>
            </h6>
        </div>
        <div class="collapse show" id="filtersCollapse">
            <div class="card-body">
                <form method="GET" id="filterForm">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="search" class="form-label">Buscar</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" 
                                   placeholder="Nome, patrimônio, modelo...">
                        </div>
                        <div class="col-md-2">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">Todos</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativo</option>
                                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Disponível</option>
                                <option value="maintenance" {{ request('status') == 'maintenance' ? 'selected' : '' }}>Manutenção</option>
                                <option value="retired" {{ request('status') == 'retired' ? 'selected' : '' }}>Descartado</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="asset_type_id" class="form-label">Tipo</label>
                            <select class="form-select" id="asset_type_id" name="asset_type_id">
                                <option value="">Todos</option>
                                @foreach($assetTypes as $type)
                                    <option value="{{ $type->id }}" {{ request('asset_type_id') == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="manufacturer_id" class="form-label">Fabricante</label>
                            <select class="form-select" id="manufacturer_id" name="manufacturer_id">
                                <option value="">Todos</option>
                                @foreach($manufacturers as $manufacturer)
                                    <option value="{{ $manufacturer->id }}" {{ request('manufacturer_id') == $manufacturer->id ? 'selected' : '' }}>
                                        {{ $manufacturer->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="location_id" class="form-label">Localização</label>
                            <select class="form-select" id="location_id" name="location_id">
                                <option value="">Todas</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">&nbsp;</label>
                            <div class="d-flex gap-1">
                                <button type="submit" class="btn btn-primary btn-sm">
                                    <i class="fas fa-search"></i>
                                </button>
                                <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Total de Ativos</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $totalAssets }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-laptop fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Ativos em Uso</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $activeAssets }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Disponíveis</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $availableAssets }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-uppercase mb-1">Em Manutenção</div>
                            <div class="h5 mb-0 font-weight-bold">{{ $maintenanceAssets }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabela de Ativos -->
    <div class="card shadow">
        <div class="card-header bg-white py-3">
            <div class="row align-items-center">
                <div class="col">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Lista de Ativos 
                        <span class="badge bg-secondary">{{ $assets->total() }}</span>
                    </h6>
                </div>
                <div class="col-auto">
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-download me-1"></i>
                            Exportar
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{ route('admin.assets.export', ['format' => 'csv'] + request()->all()) }}">
                                <i class="fas fa-file-csv me-2"></i>CSV
                            </a></li>
                            <li><a class="dropdown-item" href="{{ route('admin.assets.export', ['format' => 'xlsx'] + request()->all()) }}">
                                <i class="fas fa-file-excel me-2"></i>Excel
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            @if($assets->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th>Patrimônio</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Fabricante</th>
                                <th>Status</th>
                                <th>Localização</th>
                                <th>Usuário</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assets as $asset)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $asset->asset_tag }}</code>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($asset->image)
                                            <img src="{{ asset('storage/' . $asset->image) }}" 
                                                 class="rounded me-2" 
                                                 width="32" height="32" 
                                                 alt="{{ $asset->name }}">
                                        @else
                                            <div class="bg-secondary rounded me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 32px; height: 32px;">
                                                <i class="fas fa-laptop text-white" style="font-size: 14px;"></i>
                                            </div>
                                        @endif
                                        <div>
                                            <div class="fw-bold">{{ $asset->name }}</div>
                                            @if($asset->model)
                                                <small class="text-muted">{{ $asset->model }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $asset->assetType->name }}</span>
                                </td>
                                <td>{{ $asset->manufacturer->name }}</td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'ativo' => 'success',
                                            'disponivel' => 'info',
                                            'manutencao' => 'warning',
                                            'descartado' => 'danger'
                                        ];
                                        $statusIcons = [
                                            'ativo' => 'check-circle',
                                            'disponivel' => 'box',
                                            'manutencao' => 'tools',
                                            'descartado' => 'trash'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                        <i class="fas fa-{{ $statusIcons[$asset->status] ?? 'question' }} me-1"></i>
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </td>
                                <td>
                                    @if($asset->location)
                                        <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                        {{ $asset->location->name }}
                                    @else
                                        <span class="text-muted">Não definida</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->assignedUser)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-2 d-flex align-items-center justify-content-center" 
                                                 style="width: 24px; height: 24px;">
                                                <i class="fas fa-user text-white" style="font-size: 10px;"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold" style="font-size: 0.875rem;">{{ $asset->assignedUser->name }}</div>
                                                <small class="text-muted">{{ $asset->assignedUser->email }}</small>
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Não atribuído</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.assets.show', $asset) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.assets.edit', $asset) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
                                                title="Excluir"
                                                onclick="confirmDelete({{ $asset->id }}, '{{ $asset->name }}')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Paginação -->
                <div class="card-footer bg-white">
                    <div class="row align-items-center">
                        <div class="col">
                            <small class="text-muted">
                                Mostrando {{ $assets->firstItem() }} a {{ $assets->lastItem() }} de {{ $assets->total() }} resultados
                            </small>
                        </div>
                        <div class="col-auto">
                            {{ $assets->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-laptop fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhum ativo encontrado</h5>
                    <p class="text-muted">
                        @if(request()->hasAny(['search', 'status', 'asset_type_id', 'manufacturer_id', 'location_id']))
                            Não há ativos que correspondem aos filtros selecionados.
                        @else
                            Ainda não há ativos cadastrados no sistema.
                        @endif
                    </p>
                    @if(!request()->hasAny(['search', 'status', 'asset_type_id', 'manufacturer_id', 'location_id']))
                        <a href="{{ route('admin.assets.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>
                            Cadastrar Primeiro Ativo
                        </a>
                    @else
                        <a href="{{ route('admin.assets.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-2"></i>
                            Limpar Filtros
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Exclusão
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o ativo <strong id="assetName"></strong>?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancelar
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    border: none;
}

.table td {
    vertical-align: middle;
    border-color: #e3e6f0;
}

.btn {
    border-radius: 5px;
}

.badge {
    font-size: 0.75em;
}

.breadcrumb {
    background: none;
    padding: 0;
}

.breadcrumb-item a {
    color: #6c757d;
    text-decoration: none;
}

.breadcrumb-item a:hover {
    color: #4e73df;
}
</style>

<script>
function confirmDelete(assetId, assetName) {
    document.getElementById('assetName').textContent = assetName;
    document.getElementById('deleteForm').action = `/admin/assets/${assetId}`;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit do formulário de filtros
    const filterInputs = document.querySelectorAll('#filterForm select');
    filterInputs.forEach(input => {
        input.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });

    // Busca com delay
    const searchInput = document.getElementById('search');
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            document.getElementById('filterForm').submit();
        }, 500);
    });
});
</script>
@endsection
