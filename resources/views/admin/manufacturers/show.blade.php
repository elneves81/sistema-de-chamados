@extends('layouts.app')

@section('title', 'Visualizar Fabricante')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-industry text-primary me-2"></i>
                {{ $manufacturer->name }}
            </h1>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.manufacturers.index') }}">Fabricantes</a></li>
                    <li class="breadcrumb-item active">{{ $manufacturer->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.manufacturers.edit', $manufacturer) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>
                Editar
            </a>
            <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash me-1"></i>
                Excluir
            </button>
            <a href="{{ route('admin.manufacturers.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informações do Fabricante -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Gerais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Nome</label>
                                <p class="form-control-plaintext">{{ $manufacturer->name }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Website</label>
                                <p class="form-control-plaintext">
                                    @if($manufacturer->website)
                                        <a href="{{ $manufacturer->website }}" target="_blank" class="text-primary">
                                            {{ $manufacturer->website }}
                                            <i class="fas fa-external-link-alt ms-1"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Telefone</label>
                                <p class="form-control-plaintext">
                                    {{ $manufacturer->phone ?: 'Não informado' }}
                                    @if($manufacturer->phone)
                                        <a href="tel:{{ $manufacturer->phone }}" class="ms-2 text-success">
                                            <i class="fas fa-phone"></i>
                                        </a>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">E-mail</label>
                                <p class="form-control-plaintext">
                                    @if($manufacturer->email)
                                        <a href="mailto:{{ $manufacturer->email }}" class="text-primary">
                                            {{ $manufacturer->email }}
                                            <i class="fas fa-envelope ms-1"></i>
                                        </a>
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($manufacturer->address)
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Endereço</label>
                        <p class="form-control-plaintext">{{ $manufacturer->address }}</p>
                    </div>
                    @endif

                    @if($manufacturer->notes)
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Observações</label>
                        <p class="form-control-plaintext">{{ $manufacturer->notes }}</p>
                    </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Criado em</label>
                                <p class="form-control-plaintext">
                                    {{ $manufacturer->created_at->format('d/m/Y H:i') }}
                                    <small class="text-muted">({{ $manufacturer->created_at->diffForHumans() }})</small>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Última atualização</label>
                                <p class="form-control-plaintext">
                                    {{ $manufacturer->updated_at->format('d/m/Y H:i') }}
                                    <small class="text-muted">({{ $manufacturer->updated_at->diffForHumans() }})</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-pie me-2"></i>
                        Estatísticas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-primary">{{ $manufacturer->assets->count() }}</h3>
                        <p class="text-muted mb-0">Total de Ativos</p>
                    </div>

                    <div class="progress mb-3">
                        @php
                            $total = $manufacturer->assets->count();
                            $active = $manufacturer->assets->where('status', 'ativo')->count();
                            $percentage = $total > 0 ? ($active / $total) * 100 : 0;
                        @endphp
                        <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="card bg-success text-white text-center">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">{{ $manufacturer->assets->where('status', 'ativo')->count() }}</h6>
                                    <small>Ativos</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-warning text-white text-center">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">{{ $manufacturer->assets->where('status', 'manutencao')->count() }}</h6>
                                    <small>Manutenção</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-info text-white text-center">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">{{ $manufacturer->assets->where('status', 'disponivel')->count() }}</h6>
                                    <small>Disponíveis</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="card bg-danger text-white text-center">
                                <div class="card-body py-2">
                                    <h6 class="mb-0">{{ $manufacturer->assets->where('status', 'descartado')->count() }}</h6>
                                    <small>Descartados</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>
                        Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.assets.create', ['manufacturer' => $manufacturer->id]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>
                            Novo Ativo
                        </a>
                        <a href="{{ route('admin.assets.index', ['manufacturer' => $manufacturer->id]) }}" class="btn btn-outline-info">
                            <i class="fas fa-list me-2"></i>
                            Ver Todos os Ativos
                        </a>
                        <a href="{{ route('admin.manufacturers.edit', $manufacturer) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-2"></i>
                            Editar Fabricante
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Ativos -->
    @if($manufacturer->assets->count() > 0)
    <div class="card shadow">
        <div class="card-header bg-dark text-white">
            <h5 class="card-title mb-0">
                <i class="fas fa-laptop me-2"></i>
                Ativos do Fabricante ({{ $manufacturer->assets->count() }})
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Patrimônio</th>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Status</th>
                            <th>Localização</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manufacturer->assets->take(10) as $asset)
                        <tr>
                            <td>
                                <code>{{ $asset->asset_tag }}</code>
                            </td>
                            <td>{{ $asset->name }}</td>
                            <td>
                                <span class="badge bg-secondary">{{ $asset->assetType->name }}</span>
                            </td>
                            <td>
                                @php
                                    $statusColors = [
                                        'ativo' => 'success',
                                        'disponivel' => 'info',
                                        'manutencao' => 'warning',
                                        'descartado' => 'danger'
                                    ];
                                @endphp
                                <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
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
                                <a href="{{ route('admin.assets.show', $asset) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($manufacturer->assets->count() > 10)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.assets.index', ['manufacturer' => $manufacturer->id]) }}" class="btn btn-outline-primary">
                            Ver todos os {{ $manufacturer->assets->count() }} ativos
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
    @else
    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-laptop fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Nenhum ativo encontrado</h5>
            <p class="text-muted">Este fabricante ainda não possui ativos cadastrados.</p>
            <a href="{{ route('admin.assets.create', ['manufacturer' => $manufacturer->id]) }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                Cadastrar Primeiro Ativo
            </a>
        </div>
    </div>
    @endif
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
                <p>Tem certeza que deseja excluir o fabricante <strong>{{ $manufacturer->name }}</strong>?</p>
                @if($manufacturer->assets->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Este fabricante possui {{ $manufacturer->assets->count() }} ativo(s) associado(s).
                        A exclusão não será permitida enquanto houver ativos vinculados.
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Esta ação não pode ser desfeita.
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancelar
                </button>
                @if($manufacturer->assets->count() == 0)
                    <form action="{{ route('admin.manufacturers.destroy', $manufacturer) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash me-1"></i>
                            Confirmar Exclusão
                        </button>
                    </form>
                @else
                    <button type="button" class="btn btn-danger" disabled>
                        <i class="fas fa-ban me-1"></i>
                        Não é possível excluir
                    </button>
                @endif
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

.form-control-plaintext {
    background: #f8f9fa;
    border: 1px solid #e3e6f0;
    border-radius: 5px;
    padding: 0.375rem 0.75rem;
    margin-bottom: 0;
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

.progress {
    height: 8px;
}

.btn {
    border-radius: 5px;
    font-weight: 500;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
}
</style>
@endsection
