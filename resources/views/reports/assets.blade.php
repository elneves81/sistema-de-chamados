@extends('layouts.app')

@section('title', 'Relatório de Ativos')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Relatório de Ativos
                    </h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#filterModal">
                            <i class="fas fa-filter"></i> Filtros
                        </button>
                        <button type="button" class="btn btn-success" onclick="exportAssets()">
                            <i class="fas fa-file-excel"></i> Exportar
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Estatísticas Resumidas -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ $assetsCount }}</h4>
                                            <small>Total de Ativos</small>
                                        </div>
                                        <div class="text-right">
                                            <i class="fas fa-cube fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">R$ {{ number_format($totalValue, 2, ',', '.') }}</h4>
                                            <small>Valor Total</small>
                                        </div>
                                        <div class="text-right">
                                            <i class="fas fa-dollar-sign fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ $expiredWarrantyCount }}</h4>
                                            <small>Garantias Expiradas</small>
                                        </div>
                                        <div class="text-right">
                                            <i class="fas fa-exclamation-triangle fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-3 col-md-6">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h4 class="mb-0">{{ count($statusStats) }}</h4>
                                            <small>Status Diferentes</small>
                                        </div>
                                        <div class="text-right">
                                            <i class="fas fa-tags fa-2x"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Gráficos -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Ativos por Status</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="statusChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Ativos por Modelo</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="modelChart"></canvas>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="card-title">Ativos por Fabricante</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="manufacturerChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabela de Ativos -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Tag</th>
                                    <th>Modelo</th>
                                    <th>Fabricante</th>
                                    <th>Status</th>
                                    <th>Localização</th>
                                    <th>Valor</th>
                                    <th>Garantia</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($assets as $asset)
                                <tr>
                                    <td>
                                        <strong>{{ $asset->name }}</strong>
                                        @if($asset->serial_number)
                                            <br><small class="text-muted">SN: {{ $asset->serial_number }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $asset->asset_tag }}</span>
                                    </td>
                                    <td>
                                        @if($asset->assetModel)
                                            {{ $asset->assetModel->name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asset->manufacturer)
                                            {{ $asset->manufacturer->name }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
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
                                        @if($asset->purchase_cost)
                                            <span class="text-success">R$ {{ number_format($asset->purchase_cost, 2, ',', '.') }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($asset->warranty_expires)
                                            @if($asset->warranty_expires->isPast())
                                                <span class="badge badge-danger">Expirada</span>
                                                <br><small>{{ $asset->warranty_expires->format('d/m/Y') }}</small>
                                            @elseif($asset->warranty_expires->diffInDays() <= 30)
                                                <span class="badge badge-warning">Expira em breve</span>
                                                <br><small>{{ $asset->warranty_expires->format('d/m/Y') }}</small>
                                            @else
                                                <span class="badge badge-success">Válida</span>
                                                <br><small>{{ $asset->warranty_expires->format('d/m/Y') }}</small>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('assets.show', $asset) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            Nenhum ativo encontrado com os filtros aplicados.
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="row mt-3">
                        <div class="col-md-8">
                            {{ $assets->links() }}
                        </div>
                        <div class="col-md-4 text-right">
                            <small class="text-muted">
                                Mostrando {{ $assets->firstItem() ?? 0 }} a {{ $assets->lastItem() ?? 0 }} 
                                de {{ $assets->total() }} ativos
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Filtros -->
<div class="modal fade" id="filterModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Filtros de Relatório</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form method="GET" action="{{ route('reports.assets') }}">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Fabricante:</label>
                                <select name="manufacturer_id" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach($manufacturers as $manufacturer)
                                        <option value="{{ $manufacturer->id }}" {{ request('manufacturer_id') == $manufacturer->id ? 'selected' : '' }}>
                                            {{ $manufacturer->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Localização:</label>
                                <select name="location_id" class="form-control">
                                    <option value="">Todas</option>
                                    @foreach($locations as $location)
                                        <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Departamento:</label>
                                <select name="department_id" class="form-control">
                                    <option value="">Todos</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ request('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Status da Garantia:</label>
                                <select name="under_warranty" class="form-control">
                                    <option value="">Todos</option>
                                    <option value="1" {{ request('under_warranty') == '1' ? 'selected' : '' }}>Sob Garantia</option>
                                    <option value="0" {{ request('under_warranty') == '0' ? 'selected' : '' }}>Fora de Garantia</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data de Compra (De):</label>
                                <input type="date" name="purchase_date_from" class="form-control" value="{{ request('purchase_date_from') }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Data de Compra (Até):</label>
                                <input type="date" name="purchase_date_to" class="form-control" value="{{ request('purchase_date_to') }}">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                    <a href="{{ route('reports.assets') }}" class="btn btn-warning">Limpar</a>
                    <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Status
const statusCtx = document.getElementById('statusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_keys($statusStats)) !!},
        datasets: [{
            data: {!! json_encode(array_values($statusStats)) !!},
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#dc3545',
                '#6c757d',
                '#17a2b8',
                '#6f42c1'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            position: 'bottom'
        }
    }
});

// Gráfico de Modelos
const modelCtx = document.getElementById('modelChart').getContext('2d');
const modelChart = new Chart(modelCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_keys($typeStats)) !!},
        datasets: [{
            label: 'Quantidade',
            data: {!! json_encode(array_values($typeStats)) !!},
            backgroundColor: '#007bff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// Gráfico de Fabricantes
const manufacturerCtx = document.getElementById('manufacturerChart').getContext('2d');
const manufacturerChart = new Chart(manufacturerCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode(array_keys($manufacturerStats)) !!},
        datasets: [{
            data: {!! json_encode(array_values($manufacturerStats)) !!},
            backgroundColor: [
                '#28a745',
                '#ffc107',
                '#dc3545',
                '#6c757d',
                '#17a2b8',
                '#6f42c1'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        legend: {
            position: 'bottom'
        }
    }
});

// Função de exportação
function exportAssets() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'excel');
    window.location.href = '{{ route("reports.assets") }}?' + params.toString();
}
</script>
@endsection
