@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $assetType->name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('asset-types.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('asset-types.edit', $assetType) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Informações Básicas -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-grid-3x3-gap text-primary"></i> Informações do Tipo
                </h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-2 text-center">
                        @if($assetType->icon)
                            <i class="{{ $assetType->icon }} text-primary" style="font-size: 4rem;"></i>
                        @else
                            <i class="bi bi-grid-3x3-gap text-primary" style="font-size: 4rem;"></i>
                        @endif
                    </div>
                    <div class="col-md-8">
                        <h3>{{ $assetType->name }}</h3>
                        @if($assetType->description)
                            <p class="text-muted mb-0">{{ $assetType->description }}</p>
                        @endif
                    </div>
                    <div class="col-md-2 text-end">
                        @if($assetType->is_active)
                            <span class="badge bg-success fs-6">Ativo</span>
                        @else
                            <span class="badge bg-danger fs-6">Inativo</span>
                        @endif
                    </div>
                </div>

                @if($assetType->icon)
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6><i class="bi bi-palette"></i> Configurações Visuais</h6>
                        <p><strong>Ícone:</strong> <code>{{ $assetType->icon }}</code></p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Ativos deste Tipo -->
        @if($assetType->assets && $assetType->assets->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-laptop text-success"></i> 
                    Ativos ({{ $assetType->assets->count() }})
                </h5>
                <a href="{{ route('assets.index', ['asset_type_id' => $assetType->id]) }}" class="btn btn-sm btn-outline-primary">
                    Ver Todos
                </a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tag</th>
                                <th>Nome</th>
                                <th>Modelo</th>
                                <th>Localização</th>
                                <th>Responsável</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($assetType->assets->take(10) as $asset)
                            <tr>
                                <td>
                                    <a href="{{ route('assets.show', $asset) }}" class="text-decoration-none">
                                        <strong>{{ $asset->asset_tag }}</strong>
                                    </a>
                                </td>
                                <td>{{ $asset->name }}</td>
                                <td>{{ $asset->model ?? '-' }}</td>
                                <td>
                                    @if($asset->location)
                                        <span class="badge bg-info">{{ $asset->location->short_name ?? $asset->location->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->assignedUser)
                                        {{ $asset->assignedUser->name }}
                                    @else
                                        <span class="text-muted">Não atribuído</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'inactive' => 'secondary',
                                            'maintenance' => 'warning',
                                            'retired' => 'dark',
                                            'lost' => 'danger',
                                            'stolen' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'active' => 'Ativo',
                                            'inactive' => 'Inativo',
                                            'maintenance' => 'Manutenção',
                                            'retired' => 'Descartado',
                                            'lost' => 'Perdido',
                                            'stolen' => 'Roubado'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$asset->status] ?? $asset->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-info btn-sm" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($assetType->assets->count() > 10)
                    <div class="text-center mt-3">
                        <a href="{{ route('assets.index', ['asset_type_id' => $assetType->id]) }}" class="btn btn-outline-primary">
                            Ver todos os {{ $assetType->assets->count() }} ativos
                        </a>
                    </div>
                @endif
            </div>
        </div>
        @else
        <div class="card mb-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-laptop" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Nenhum ativo deste tipo</h5>
                <p class="text-muted">Ainda não há ativos cadastrados para este tipo.</p>
                <a href="{{ route('assets.create', ['asset_type_id' => $assetType->id]) }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Ativo
                </a>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <!-- Estatísticas -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Estatísticas</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-12 mb-3">
                        <h3 class="text-primary">{{ $assetType->assets->count() }}</h3>
                        <small class="text-muted">Total de Ativos</small>
                    </div>
                </div>
                
                @if($assetType->assets->count() > 0)
                <hr>
                <div class="row text-center">
                    @php
                        $statusCounts = $assetType->assets->groupBy('status')->map(function($group) {
                            return $group->count();
                        });
                    @endphp
                    @if($statusCounts->get('active', 0) > 0)
                    <div class="col-6 mb-2">
                        <h5 class="text-success">{{ $statusCounts->get('active', 0) }}</h5>
                        <small class="text-muted">Ativos</small>
                    </div>
                    @endif
                    @if($statusCounts->get('maintenance', 0) > 0)
                    <div class="col-6 mb-2">
                        <h5 class="text-warning">{{ $statusCounts->get('maintenance', 0) }}</h5>
                        <small class="text-muted">Manutenção</small>
                    </div>
                    @endif
                    @if($statusCounts->get('inactive', 0) > 0)
                    <div class="col-6 mb-2">
                        <h5 class="text-secondary">{{ $statusCounts->get('inactive', 0) }}</h5>
                        <small class="text-muted">Inativos</small>
                    </div>
                    @endif
                    @if($statusCounts->get('retired', 0) > 0)
                    <div class="col-6 mb-2">
                        <h5 class="text-dark">{{ $statusCounts->get('retired', 0) }}</h5>
                        <small class="text-muted">Descartados</small>
                    </div>
                    @endif
                </div>
                @endif
            </div>
        </div>

        <!-- Informações do Sistema -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Informações do Sistema</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Slug:</strong><br>
                    <code>{{ $assetType->slug }}</code>
                </p>
                <p class="mb-2">
                    <strong>Criado em:</strong><br>
                    <small>{{ $assetType->created_at->format('d/m/Y \à\s H:i') }}</small>
                </p>
                <p class="mb-0">
                    <strong>Última atualização:</strong><br>
                    <small>{{ $assetType->updated_at->format('d/m/Y \à\s H:i') }}</small>
                </p>
            </div>
        </div>

        <!-- Ações -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Ações</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('asset-types.edit', $assetType) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar Tipo
                    </a>
                    @if($assetType->assets->count() > 0)
                        <a href="{{ route('assets.index', ['asset_type_id' => $assetType->id]) }}" class="btn btn-success">
                            <i class="bi bi-laptop"></i> Ver Ativos
                        </a>
                    @else
                        <a href="{{ route('assets.create', ['asset_type_id' => $assetType->id]) }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Cadastrar Ativo
                        </a>
                    @endif
                    
                    @if($assetType->assets->count() == 0)
                        <form action="{{ route('asset-types.destroy', $assetType) }}" method="POST" 
                              onsubmit="return confirm('⚠️ ATENÇÃO!\n\nTem certeza que deseja EXCLUIR este tipo de ativo?\n\nEsta ação não pode ser desfeita!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Excluir Tipo
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-danger" disabled title="Não é possível excluir: há ativos vinculados">
                            <i class="bi bi-trash"></i> Excluir Tipo
                        </button>
                        <small class="text-muted">
                            Para excluir, remova todos os ativos deste tipo primeiro.
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
