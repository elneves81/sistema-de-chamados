@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-tags"></i> Categorias
        <small class="text-muted">Gerenciar Categorias do Sistema</small>
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('categories.create') }}" class="btn btn-sm btn-primary">
                <i class="bi bi-plus"></i> Nova Categoria
            </a>
        </div>
    </div>
</div>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-primary">{{ $categories->total() }}</h5>
                <p class="card-text">Total de Categorias</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-success">{{ $categories->where('active', true)->count() }}</h5>
                <p class="card-text">Ativas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-warning">{{ $categories->where('active', false)->count() }}</h5>
                <p class="card-text">Inativas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h5 class="card-title text-info">{{ $categories->sum('tickets_count') }}</h5>
                <p class="card-text">Total de Chamados</p>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Categorias -->
<div class="card">
    <div class="card-body">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th width="80">Cor</th>
                            <th width="100">SLA (horas)</th>
                            <th width="100">Chamados</th>
                            <th width="80">Status</th>
                            <th width="120">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                        <tr>
                            <td>{{ $category->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <span class="badge me-2" style="background-color: {{ $category->color }}; color: white; width: 20px; height: 20px; border-radius: 50%;">&nbsp;</span>
                                    <strong>{{ $category->name }}</strong>
                                </div>
                                <small class="text-muted">{{ $category->slug }}</small>
                            </td>
                            <td>
                                <span title="{{ $category->description }}">
                                    {{ Str::limit($category->description, 60) }}
                                </span>
                            </td>
                            <td>
                                <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                                    {{ $category->color }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $category->sla_hours }}h</span>
                            </td>
                            <td class="text-center">
                                @if($category->tickets_count > 0)
                                <a href="{{ route('categories.show', $category) }}" class="btn btn-link btn-sm p-0">
                                    {{ $category->tickets_count }}
                                </a>
                                @else
                                <span class="text-muted">0</span>
                                @endif
                            </td>
                            <td>
                                @if($category->active)
                                    <span class="badge bg-success">Ativa</span>
                                @else
                                    <span class="badge bg-secondary">Inativa</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('categories.show', $category) }}" 
                                       class="btn btn-outline-primary btn-sm" 
                                       title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('categories.edit', $category) }}" 
                                       class="btn btn-outline-secondary btn-sm" 
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if($category->tickets_count == 0)
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm" 
                                            title="Excluir"
                                            onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @else
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm disabled" 
                                            title="Não é possível excluir: possui chamados associados">
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

            <!-- Paginação -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div>
                    <small class="text-muted">
                        Mostrando {{ $categories->firstItem() ?? 0 }} a {{ $categories->lastItem() ?? 0 }} 
                        de {{ $categories->total() }} categorias
                    </small>
                </div>
                <div>
                    {{ $categories->links() }}
                </div>
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-tags display-1 text-muted"></i>
                <h4 class="text-muted mt-3">Nenhuma categoria encontrada</h4>
                <p class="text-muted">Crie sua primeira categoria para organizar os chamados.</p>
                <a href="{{ route('categories.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus"></i> Criar Categoria
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir a categoria <strong id="categoryName"></strong>?</p>
                <p class="text-muted">Esta ação não pode ser desfeita.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function confirmDelete(categoryId, categoryName) {
    document.getElementById('categoryName').textContent = categoryName;
    document.getElementById('deleteForm').action = `/categories/${categoryId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Adicionar tooltip para descrições longas
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
