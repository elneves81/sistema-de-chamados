@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-key"></i>
                        Gestão de Licenças
                    </h3>
                    <div>
                        <a href="{{ route('licenses.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nova Licença
                        </a>
                        <button class="btn btn-info" data-toggle="modal" data-target="#filterModal">
                            <i class="fas fa-filter"></i> Filtros
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Estatísticas Rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-info">
                                    <i class="fas fa-key"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total de Licenças</span>
                                    <span class="info-box-number">{{ $licenses->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-success">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Ativas</span>
                                    <span class="info-box-number">{{ $licenses->where('status', 'active')->count() }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Expirando em 30 dias</span>
                                    <span class="info-box-number">
                                        {{ $licenses->filter(function($license) {
                                            return $license->expires_at && 
                                                   $license->expires_at->diffInDays() <= 30 && 
                                                   $license->expires_at->isFuture();
                                        })->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="info-box">
                                <span class="info-box-icon bg-danger">
                                    <i class="fas fa-times-circle"></i>
                                </span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Expiradas</span>
                                    <span class="info-box-number">
                                        {{ $licenses->filter(function($license) {
                                            return $license->expires_at && $license->expires_at->isPast();
                                        })->count() }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Licenças -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-dark">
                                <tr>
                                    <th>Software</th>
                                    <th>Versão</th>
                                    <th>Tipo</th>
                                    <th>Licenças Total</th>
                                    <th>Em Uso</th>
                                    <th>Status</th>
                                    <th>Expira em</th>
                                    <th>Custo</th>
                                    <th width="120">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($licenses as $license)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($license->software && $license->software->icon)
                                                <img src="{{ $license->software->icon }}" width="24" height="24" class="mr-2">
                                            @else
                                                <i class="fas fa-cube mr-2 text-muted"></i>
                                            @endif
                                            <div>
                                                <strong>{{ $license->software->name ?? 'N/A' }}</strong><br>
                                                <small class="text-muted">{{ $license->license_key ? Str::mask($license->license_key, '*', 4, -4) : 'N/A' }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">
                                            {{ $license->version ?? 'N/A' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $license->type == 'perpetual' ? 'success' : 'info' }}">
                                            {{ $license->type == 'perpetual' ? 'Perpétua' : 'Assinatura' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">
                                            {{ $license->seats }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $usedSeats = $license->installations->count();
                                            $percentage = $license->seats > 0 ? ($usedSeats / $license->seats) * 100 : 0;
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <span class="mr-2">{{ $usedSeats }}</span>
                                            <div class="progress flex-grow-1" style="height: 8px;">
                                                <div class="progress-bar bg-{{ $percentage > 90 ? 'danger' : ($percentage > 70 ? 'warning' : 'success') }}" 
                                                     style="width: {{ $percentage }}%"></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($license->status == 'active')
                                            <span class="badge badge-success">Ativa</span>
                                        @elseif($license->status == 'expired')
                                            <span class="badge badge-danger">Expirada</span>
                                        @else
                                            <span class="badge badge-secondary">Inativa</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($license->expires_at)
                                            @if($license->expires_at->isPast())
                                                <span class="text-danger">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                    {{ $license->expires_at->format('d/m/Y') }}
                                                </span>
                                            @elseif($license->expires_at->diffInDays() <= 30)
                                                <span class="text-warning">
                                                    <i class="fas fa-clock"></i>
                                                    {{ $license->expires_at->format('d/m/Y') }}
                                                </span>
                                            @else
                                                <span class="text-success">
                                                    {{ $license->expires_at->format('d/m/Y') }}
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-muted">Perpétua</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($license->cost)
                                            <span class="text-success font-weight-bold">
                                                R$ {{ number_format($license->cost, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('licenses.show', $license) }}" 
                                               class="btn btn-outline-info" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('licenses.edit', $license) }}" 
                                               class="btn btn-outline-warning" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button type="button" class="btn btn-outline-danger" 
                                                    title="Excluir" data-toggle="modal" 
                                                    data-target="#deleteModal{{ $license->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal de Exclusão -->
                                <div class="modal fade" id="deleteModal{{ $license->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmar Exclusão</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                Tem certeza que deseja excluir a licença do software 
                                                <strong>{{ $license->software->name ?? 'N/A' }}</strong>?
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                                    Cancelar
                                                </button>
                                                <form action="{{ route('licenses.destroy', $license) }}" method="POST" style="display: inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">
                                                        Confirmar Exclusão
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                @empty
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="fas fa-key fa-3x mb-3"></i>
                                            <h5>Nenhuma licença cadastrada</h5>
                                            <p>Comece criando sua primeira licença de software.</p>
                                            <a href="{{ route('licenses.create') }}" class="btn btn-primary">
                                                <i class="fas fa-plus"></i> Nova Licença
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Filtros -->
<div class="modal fade" id="filterModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="GET" action="{{ route('licenses.index') }}">
                <div class="modal-header">
                    <h5 class="modal-title">Filtrar Licenças</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">Todos</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativa</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>Expirada</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativa</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="type">Tipo</label>
                        <select name="type" id="type" class="form-control">
                            <option value="">Todos</option>
                            <option value="perpetual" {{ request('type') == 'perpetual' ? 'selected' : '' }}>Perpétua</option>
                            <option value="subscription" {{ request('type') == 'subscription' ? 'selected' : '' }}>Assinatura</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="expires_soon">Expiração</label>
                        <select name="expires_soon" id="expires_soon" class="form-control">
                            <option value="">Todas</option>
                            <option value="1" {{ request('expires_soon') == '1' ? 'selected' : '' }}>Expira em 30 dias</option>
                            <option value="2" {{ request('expires_soon') == '2' ? 'selected' : '' }}>Já expiradas</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                    <a href="{{ route('licenses.index') }}" class="btn btn-outline-secondary">Limpar</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Tooltip para botões
    $('[title]').tooltip();
    
    // Auto-refresh da página a cada 5 minutos para atualizar status
    setTimeout(function() {
        location.reload();
    }, 300000); // 5 minutos
});
</script>
@endsection
