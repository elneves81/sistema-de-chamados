@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Fabricantes</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('manufacturers.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Fabricante
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('manufacturers.index') }}">
            <div class="row">
                <div class="col-md-5">
                    <div class="mb-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" name="search" id="search" 
                               value="{{ request('search') }}" placeholder="Nome, website, e-mail...">
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
                            <a href="{{ route('manufacturers.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Limpar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Fabricantes -->
@if($manufacturers->count() > 0)
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Fabricante</th>
                            <th>Website</th>
                            <th>Suporte</th>
                            <th>Ativos</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($manufacturers as $manufacturer)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0 me-3">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px; font-weight: bold;">
                                            {{ strtoupper(substr($manufacturer->name, 0, 2)) }}
                                        </div>
                                    </div>
                                    <div>
                                        <strong>{{ $manufacturer->name }}</strong>
                                        @if($manufacturer->comment)
                                            <br><small class="text-muted">{{ Str::limit($manufacturer->comment, 50) }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($manufacturer->website)
                                    <a href="{{ $manufacturer->website }}" target="_blank" class="text-decoration-none">
                                        <i class="bi bi-globe"></i> {{ $manufacturer->website }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($manufacturer->support_phone || $manufacturer->support_email)
                                    @if($manufacturer->support_phone)
                                        <div><i class="bi bi-telephone"></i> {{ $manufacturer->support_phone }}</div>
                                    @endif
                                    @if($manufacturer->support_email)
                                        <div><i class="bi bi-envelope"></i> {{ $manufacturer->support_email }}</div>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $manufacturer->assets_count }}</span>
                            </td>
                            <td>
                                @if($manufacturer->is_active)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('manufacturers.show', $manufacturer) }}" class="btn btn-outline-info" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('manufacturers.edit', $manufacturer) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button" class="btn btn-outline-danger" title="Excluir"
                                            onclick="confirmDelete('{{ $manufacturer->id }}', '{{ $manufacturer->name }}', {{ $manufacturer->assets_count }})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <div class="d-flex justify-content-center">
                {{ $manufacturers->withQueryString()->links() }}
            </div>
        </div>
    </div>
@else
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="bi bi-building" style="font-size: 3rem; color: #ccc;"></i>
            <h5 class="mt-3 text-muted">Nenhum fabricante encontrado</h5>
            <p class="text-muted">Comece criando seu primeiro fabricante.</p>
            <a href="{{ route('manufacturers.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Novo Fabricante
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
                <p>Tem certeza que deseja excluir o fabricante <strong id="manufacturerName"></strong>?</p>
                <div id="hasAssetsWarning" class="alert alert-warning d-none">
                    <i class="bi bi-exclamation-triangle"></i>
                    <strong>Atenção:</strong> Este fabricante possui <span id="assetsCount"></span> ativo(s) vinculado(s). 
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
    document.getElementById('manufacturerName').textContent = name;
    document.getElementById('assetsCount').textContent = assetsCount;
    document.getElementById('deleteForm').action = '{{ route("manufacturers.index") }}/' + id;
    
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
