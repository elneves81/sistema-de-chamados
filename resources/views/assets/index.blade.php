@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2>Gestão de Ativos</h2>
                <a href="{{ route('assets.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Novo Ativo
                </a>
            </div>
        </div>
    </div>

    <!-- Estatísticas Resumidas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h3>{{ $totalAssets }}</h3>
                    <p class="mb-0">Total de Ativos</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h3>{{ $activeAssets }}</h3>
                    <p class="mb-0">Em Uso</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h3>{{ $stockAssets }}</h3>
                    <p class="mb-0">Em Estoque</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h3>{{ $maintenanceAssets }}</h3>
                    <p class="mb-0">Em Manutenção</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">Filtros</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('assets.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="Buscar..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status_id" class="form-select">
                            <option value="">Todos os Status</option>
                            @foreach($statuses as $status)
                                <option value="{{ $status->id }}" {{ request('status_id') == $status->id ? 'selected' : '' }}>
                                    {{ $status->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="location_id" class="form-select">
                            <option value="">Todas as Localizações</option>
                            @foreach($locations as $location)
                                <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                    {{ $location->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="department_id" class="form-select">
                            <option value="">Todos os Departamentos</option>
                            @foreach($departments as $department)
                                <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <div class="btn-group" role="group">
                            <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">Limpar</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Ativos -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">Ativos</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Tag</th>
                            <th>Nome</th>
                            <th>Modelo</th>
                            <th>Status</th>
                            <th>Localização</th>
                            <th>Responsável</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td>{{ $asset->asset_tag }}</td>
                            <td>{{ $asset->name }}</td>
                            <td>
                                @if($asset->assetModel)
                                    {{ $asset->assetModel->name }}
                                    @if($asset->assetModel->manufacturer)
                                        <small class="text-muted">({{ $asset->assetModel->manufacturer->name }})</small>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @if($asset->status)
                                    <span class="badge" style="background-color: {{ $asset->status->color }}">
                                        {{ $asset->status->name }}
                                    </span>
                                @endif
                            </td>
                            <td>{{ $asset->location ? $asset->location->name : '-' }}</td>
                            <td>{{ $asset->assignedUser ? $asset->assignedUser->name : '-' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-primary">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('assets.destroy', $asset) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" 
                                                onclick="return confirm('Tem certeza que deseja excluir este ativo?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center">Nenhum ativo encontrado.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($assets->hasPages())
                <div class="mt-3">
                    {{ $assets->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
