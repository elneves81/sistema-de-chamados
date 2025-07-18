@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-desktop"></i>
                        {{ $asset->name }}
                        <small class="text-muted">({{ $asset->asset_tag }})</small>
                    </h3>
                    <div>
                        <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('assets.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row">
                        <!-- Informações Básicas -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-info-circle"></i> Informações Básicas
                            </h5>

                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Nome:</strong></td>
                                    <td>{{ $asset->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Tag do Ativo:</strong></td>
                                    <td><span class="badge badge-primary">{{ $asset->asset_tag }}</span></td>
                                </tr>
                                <tr>
                                    <td><strong>Número de Série:</strong></td>
                                    <td>{{ $asset->serial_number ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Modelo:</strong></td>
                                    <td>{{ $asset->assetModel->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Fabricante:</strong></td>
                                    <td>{{ $asset->assetModel->manufacturer->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $asset->status->color ?? 'secondary' }}">
                                            {{ $asset->status->name ?? 'N/A' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Criado em:</strong></td>
                                    <td>{{ $asset->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Atualizado em:</strong></td>
                                    <td>{{ $asset->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>

                        <!-- Localização e Atribuição -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-map-marker-alt"></i> Localização e Atribuição
                            </h5>

                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Localização:</strong></td>
                                    <td>{{ $asset->location->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Departamento:</strong></td>
                                    <td>{{ $asset->department->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Atribuído a:</strong></td>
                                    <td>
                                        @if($asset->assignedUser)
                                            <div class="d-flex align-items-center">
                                                <img src="https://www.gravatar.com/avatar/{{ md5($asset->assignedUser->email) }}?s=32&d=identicon" 
                                                     class="rounded-circle mr-2" width="32" height="32">
                                                <div>
                                                    {{ $asset->assignedUser->name }}<br>
                                                    <small class="text-muted">{{ $asset->assignedUser->email }}</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted">Não atribuído</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>

                            <!-- Informações Financeiras -->
                            <h5 class="text-primary border-bottom pb-2 mb-3 mt-4">
                                <i class="fas fa-dollar-sign"></i> Informações Financeiras
                            </h5>

                            <table class="table table-borderless">
                                <tr>
                                    <td width="30%"><strong>Data de Compra:</strong></td>
                                    <td>{{ $asset->purchase_date ? $asset->purchase_date->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Custo de Compra:</strong></td>
                                    <td>{{ $asset->purchase_cost ? 'R$ ' . number_format($asset->purchase_cost, 2, ',', '.') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Garantia Expira:</strong></td>
                                    <td>
                                        @if($asset->warranty_expires)
                                            {{ $asset->warranty_expires->format('d/m/Y') }}
                                            @if($asset->warranty_expires->isPast())
                                                <span class="badge badge-danger ml-1">Expirada</span>
                                            @elseif($asset->warranty_expires->diffInDays() <= 30)
                                                <span class="badge badge-warning ml-1">Expira em breve</span>
                                            @else
                                                <span class="badge badge-success ml-1">Válida</span>
                                            @endif
                                        @else
                                            N/A
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Observações -->
                    @if($asset->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-sticky-note"></i> Observações
                            </h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $asset->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Histórico de Manutenções -->
                    @if($asset->maintenances && $asset->maintenances->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-wrench"></i> Histórico de Manutenções
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Título</th>
                                            <th>Tipo</th>
                                            <th>Responsável</th>
                                            <th>Custo</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($asset->maintenances as $maintenance)
                                        <tr>
                                            <td>{{ $maintenance->maintenance_date->format('d/m/Y') }}</td>
                                            <td>{{ $maintenance->title }}</td>
                                            <td>
                                                <span class="badge badge-{{ $maintenance->type == 'preventive' ? 'success' : ($maintenance->type == 'corrective' ? 'warning' : 'info') }}">
                                                    {{ ucfirst($maintenance->type) }}
                                                </span>
                                            </td>
                                            <td>{{ $maintenance->user->name }}</td>
                                            <td>{{ $maintenance->cost ? 'R$ ' . number_format($maintenance->cost, 2, ',', '.') : 'N/A' }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                <div class="card-footer">
                    <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar Ativo
                    </a>
                    <a href="{{ route('assets.create', ['duplicate' => $asset->id]) }}" class="btn btn-info">
                        <i class="fas fa-copy"></i> Duplicar
                    </a>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                    <a href="{{ route('assets.index') }}" class="btn btn-secondary float-right">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Tem certeza que deseja excluir o ativo <strong>{{ $asset->name }}</strong>?
                <br><br>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form action="{{ route('assets.destroy', $asset) }}" method="POST" style="display: inline;">
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
