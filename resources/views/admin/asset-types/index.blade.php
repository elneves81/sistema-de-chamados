@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Tipos de Ativos</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('asset-types.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Tipo
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('asset-types.index') }}">
            <div class="row">
                <div class="col-md-5">
                    <div class="mb-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" name="search" id="search" 
                               value="{{ request('search') }}" placeholder="Nome, descrição...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Status</label>
                        <select class="form-select" name="is_active" id="is_active">
                            <option value="">Todos</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <a href="{{ route('asset-types.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Tipos de Ativos -->
@if($assetTypes->count() > 0)
    <div class="row">
        @foreach($assetTypes as $assetType)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card h-100 {{ !$assetType->is_active ? 'border-secondary' : '' }}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="flex-grow-1">
                            @if($assetType->icon)
                                <i class="{{ $assetType->icon }} text-primary fs-3 mb-2"></i>
                            @else
                                <i class="bi bi-grid-3x3-gap text-primary fs-3 mb-2"></i>
                            @endif
                            <h5 class="card-title">{{ $assetType->name }}</h5>
                        </div>
                        <div class="text-end">
                            @if($assetType->is_active)
                                <span class="badge bg-success">Ativo</span>
                            @else
                                <span class="badge bg-secondary">Inativo</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($assetType->description)
                        <p class="card-text text-muted">{{ Str::limit($assetType->description, 100) }}</p>
                    @endif
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted">
                            <i class="bi bi-laptop"></i> {{ $assetType->assets_count }} ativo(s)
                        </small>
                        <div class="btn-group btn-group-sm" role="group">
                            <a href="{{ route('asset-types.show', $assetType) }}" class="btn btn-outline-info" title="Visualizar">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('asset-types.edit', $assetType) }}" class="btn btn-outline-warning" title="Editar">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button type="button" class="btn btn-outline-danger" title="Excluir"
                                    onclick="confirmDelete('{{ $assetType->id }}', '{{ $assetType->name }}', {{ $assetType->assets_count }})">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Paginação -->
    <div class="d-flex justify-content-center">
        {{ $assetTypes->withQueryString()->links() }}
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-grid-3x3-gap" style="font-size: 3rem; color: #ccc;"></i>
            <h5 class="mt-3 text-muted">Nenhum tipo de ativo encontrado</h5>
            <p class="text-muted">Comece criando seu primeiro tipo de ativo.</p>
            <a href="{{ route('asset-types.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Tipo de Ativo
            </a>
        </div>
    </div>
@endif

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir o tipo de ativo <strong id="assetTypeName"></strong>?</p>
                <div id="hasAssetsWarning" class="alert alert-warning d-none">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Atenção:</strong> Este tipo possui <span id="assetsCount"></span> ativo(s) vinculado(s). 
                    A exclusão não será permitida.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" id="confirmDeleteBtn">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id, name, assetsCount) {
    document.getElementById('assetTypeName').textContent = name;
    document.getElementById('assetsCount').textContent = assetsCount;
    document.getElementById('deleteForm').action = '{{ route("asset-types.index") }}/' + id;
    
    const hasAssetsWarning = document.getElementById('hasAssetsWarning');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
    
    if (assetsCount > 0) {
        hasAssetsWarning.classList.remove('d-none');
        confirmDeleteBtn.disabled = true;
        confirmDeleteBtn.textContent = 'Não é possível excluir';
    } else {
        hasAssetsWarning.classList.add('d-none');
        confirmDeleteBtn.disabled = false;
        confirmDeleteBtn.textContent = 'Excluir';
    }
    
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
@endpush
@endsection
