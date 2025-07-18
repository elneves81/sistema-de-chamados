@extends('layouts.app')

@section('title', 'Detalhes do Modelo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-copy"></i>
                        {{ $model->name }}
                        @if($model->manufacturer)
                            <small class="text-muted">({{ $model->manufacturer->name }})</small>
                        @endif
                    </h3>
                    <div>
                        <a href="{{ route('models.edit', $model) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('models.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Informações do Modelo -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle"></i> Informações do Modelo
                            </h5>

                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%"><strong>ID:</strong></td>
                                    <td>{{ $model->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Nome:</strong></td>
                                    <td>{{ $model->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fabricante:</strong></td>
                                    <td>
                                        @if($model->manufacturer)
                                            <a href="{{ route('manufacturers.show', $model->manufacturer) }}" class="badge badge-info">
                                                {{ $model->manufacturer->name }}
                                            </a>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Criado em:</strong></td>
                                    <td>{{ $model->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Atualizado em:</strong></td>
                                    <td>{{ $model->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Estatísticas -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-chart-bar"></i> Estatísticas
                            </h5>

                            <div class="row">
                                <div class="col-6">
                                    <div class="small-box bg-info">
                                        <div class="inner">
                                            <h3>{{ $model->assets->count() }}</h3>
                                            <p>Total de Ativos</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-cube"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="small-box bg-success">
                                        <div class="inner">
                                            <h3>{{ $model->assets->whereNotNull('assigned_to')->count() }}</h3>
                                            <p>Em Uso</p>
                                        </div>
                                        <div class="icon">
                                            <i class="fas fa-user"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($model->assets->count() > 0)
                                @php
                                    $totalValue = $model->assets->sum('purchase_cost');
                                @endphp
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-dollar-sign"></i> Valor Total dos Ativos</h6>
                                    <h4 class="mb-0">R$ {{ number_format($totalValue, 2, ',', '.') }}</h4>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Especificações -->
                    @if($model->specifications)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-cogs"></i> Especificações Técnicas
                            </h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {!! nl2br(e($model->specifications)) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Lista de Ativos -->
                    @if($model->assets->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-list"></i> Ativos deste Modelo
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Nome</th>
                                            <th>Tag</th>
                                            <th>Serial</th>
                                            <th>Status</th>
                                            <th>Localização</th>
                                            <th>Atribuído a</th>
                                            <th>Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($model->assets as $asset)
                                        <tr>
                                            <td>
                                                <strong>{{ $asset->name }}</strong>
                                            </td>
                                            <td>
                                                <span class="badge badge-secondary">{{ $asset->asset_tag }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $asset->serial_number ?: 'N/A' }}</small>
                                            </td>
                                            <td>
                                                @if($asset->status)
                                                    <span class="badge" style="background-color: {{ $asset->status->color }}; color: white;">
                                                        {{ $asset->status->name }}
                                                    </span>
                                                @else
                                                    <span class="badge badge-secondary">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($asset->location)
                                                    {{ $asset->location->name }}
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($asset->assignedUser)
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://www.gravatar.com/avatar/{{ md5($asset->assignedUser->email) }}?s=24&d=identicon" 
                                                             class="rounded-circle mr-2" width="24" height="24">
                                                        <small>{{ $asset->assignedUser->name }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Não atribuído</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('assets.show', $asset) }}" class="btn btn-info btn-sm">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="alert alert-info text-center">
                                <i class="fas fa-info-circle fa-2x mb-2"></i>
                                <h5>Nenhum ativo associado</h5>
                                <p class="mb-0">Este modelo ainda não possui ativos associados.</p>
                                <a href="{{ route('assets.create', ['model_id' => $model->id]) }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-plus"></i> Criar Ativo
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-footer">
                    <a href="{{ route('models.edit', $model) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar Modelo
                    </a>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                    <a href="{{ route('models.index') }}" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
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
@endsection
