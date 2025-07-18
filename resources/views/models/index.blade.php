@extends('layouts.app')

@section('title', 'Modelos de Ativos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-copy"></i>
                        Modelos de Ativos
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('models.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Novo Modelo
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fas fa-check"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                            <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                        </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('models.index') }}">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" 
                                           placeholder="Buscar modelos..." 
                                           value="{{ request('search') }}">
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="submit">
                                            <i class="fas fa-search"></i>
                                        </button>
                                        @if(request('search'))
                                            <a href="{{ route('models.index') }}" class="btn btn-outline-danger">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="col-md-6 text-right">
                            <div class="btn-group">
                                <button type="button" class="btn btn-outline-secondary dropdown-toggle" data-toggle="dropdown">
                                    <i class="fas fa-filter"></i> Filtros
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="{{ route('models.index') }}">
                                        <i class="fas fa-list"></i> Todos
                                    </a>
                                    <div class="dropdown-divider"></div>
                                    @foreach($manufacturers as $manufacturer)
                                        <a class="dropdown-item" href="{{ route('models.index', ['manufacturer' => $manufacturer->id]) }}">
                                            <i class="fas fa-industry"></i> {{ $manufacturer->name }}
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th>Fabricante</th>
                                    <th>Especificações</th>
                                    <th>Ativos</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($models as $model)
                                <tr>
                                    <td>{{ $model->id }}</td>
                                    <td>
                                        <strong>{{ $model->name }}</strong>
                                    </td>
                                    <td>
                                        @if($model->manufacturer)
                                            <span class="badge badge-info">{{ $model->manufacturer->name }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($model->specifications)
                                            <small class="text-muted">{{ Str::limit($model->specifications, 50) }}</small>
                                        @else
                                            <span class="text-muted">Não informado</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ $model->assets_count ?? $model->assets->count() }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">{{ $model->created_at->format('d/m/Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('models.show', $model) }}" class="btn btn-info btn-sm" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('models.edit', $model) }}" class="btn btn-warning btn-sm" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-danger btn-sm" 
                                                    data-toggle="modal" 
                                                    data-target="#deleteModal{{ $model->id }}" 
                                                    title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <!-- Modal de Confirmação -->
                                        <div class="modal fade" id="deleteModal{{ $model->id }}" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar Exclusão</h5>
                                                        <button type="button" class="close" data-dismiss="modal">
                                                            <span>&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        Tem certeza que deseja excluir o modelo 
                                                        <strong>{{ $model->name }}</strong>?
                                                        @if($model->assets->count() > 0)
                                                            <br><br>
                                                            <div class="alert alert-warning">
                                                                <i class="fas fa-exclamation-triangle"></i>
                                                                Este modelo possui {{ $model->assets->count() }} ativo(s) associado(s).
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                                                        <form action="{{ route('models.destroy', $model) }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash"></i> Confirmar Exclusão
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Nenhum modelo encontrado.
                                            <a href="{{ route('models.create') }}" class="btn btn-primary btn-sm ml-2">
                                                <i class="fas fa-plus"></i> Criar Primeiro Modelo
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($models instanceof \Illuminate\Pagination\LengthAwarePaginator)
                        <div class="row mt-3">
                            <div class="col-md-8">
                                {{ $models->appends(request()->query())->links() }}
                            </div>
                            <div class="col-md-4 text-right">
                                <small class="text-muted">
                                    Mostrando {{ $models->firstItem() ?? 0 }} a {{ $models->lastItem() ?? 0 }} 
                                    de {{ $models->total() }} modelos
                                </small>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
});
</script>
@endsection
