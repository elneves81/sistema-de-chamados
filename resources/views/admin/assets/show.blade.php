@extends('layouts.app')

@section('title', 'Visualizar Ativo')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-laptop text-primary me-2"></i>
                {{ $asset->name }}
                <span class="badge bg-secondary ms-2">{{ $asset->asset_tag }}</span>
            </h1>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.assets.index') }}">Ativos</a></li>
                    <li class="breadcrumb-item active">{{ $asset->name }}</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.assets.edit', $asset) }}" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i>
                Editar
            </a>
            <div class="btn-group me-2">
                <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-tools me-1"></i>
                    Ações
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="generateQRCode()">
                        <i class="fas fa-qrcode me-2"></i>QR Code
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="printAsset()">
                        <i class="fas fa-print me-2"></i>Imprimir Etiqueta
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="#" onclick="duplicateAsset()">
                        <i class="fas fa-copy me-2"></i>Duplicar Ativo
                    </a></li>
                </ul>
            </div>
            <button type="button" class="btn btn-danger me-2" data-bs-toggle="modal" data-bs-target="#deleteModal">
                <i class="fas fa-trash me-1"></i>
                Excluir
            </button>
            <a href="{{ route('admin.assets.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Voltar
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Coluna Principal -->
        <div class="col-lg-8">
            <!-- Informações Principais -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Informações Gerais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            @if($asset->image)
                                <img src="{{ asset('storage/' . $asset->image) }}" 
                                     alt="{{ $asset->name }}" 
                                     class="img-fluid rounded border"
                                     style="max-height: 200px;">
                            @else
                                <div class="bg-light border rounded d-flex align-items-center justify-content-center" 
                                     style="height: 200px;">
                                    <i class="fas fa-laptop fa-3x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Patrimônio</label>
                                        <p class="form-control-plaintext">
                                            <code class="bg-light px-2 py-1 rounded">{{ $asset->asset_tag }}</code>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Nome</label>
                                        <p class="form-control-plaintext">{{ $asset->name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Tipo</label>
                                        <p class="form-control-plaintext">
                                            <span class="badge bg-secondary">{{ $asset->assetType->name }}</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Fabricante</label>
                                        <p class="form-control-plaintext">
                                            <a href="{{ route('admin.manufacturers.show', $asset->manufacturer) }}" class="text-decoration-none">
                                                {{ $asset->manufacturer->name }}
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Modelo</label>
                                        <p class="form-control-plaintext">{{ $asset->model ?: 'Não informado' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label fw-bold text-muted">Número de Série</label>
                                        <p class="form-control-plaintext">{{ $asset->serial_number ?: 'Não informado' }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status e Localização -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-map-marker-alt me-2"></i>
                        Status e Localização
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Status</label>
                                <p class="form-control-plaintext">
                                    @php
                                        $statusColors = [
                                            'ativo' => 'success',
                                            'disponivel' => 'info',
                                            'manutencao' => 'warning',
                                            'descartado' => 'danger'
                                        ];
                                        $statusIcons = [
                                            'ativo' => 'check-circle',
                                            'disponivel' => 'box',
                                            'manutencao' => 'tools',
                                            'descartado' => 'trash'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }} fs-6">
                                        <i class="fas fa-{{ $statusIcons[$asset->status] ?? 'question' }} me-1"></i>
                                        {{ ucfirst($asset->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Localização</label>
                                <p class="form-control-plaintext">
                                    @if($asset->location)
                                        <i class="fas fa-map-marker-alt text-primary me-1"></i>
                                        <a href="{{ route('admin.locations.show', $asset->location) }}" class="text-decoration-none">
                                            {{ $asset->location->name }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $asset->location->address }}</small>
                                    @else
                                        <span class="text-muted">Não definida</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label class="form-label fw-bold text-muted">Atribuído a</label>
                                <p class="form-control-plaintext">
                                    @if($asset->assignedUser)
                                        <div class="d-flex align-items-center">
                                            <div class="bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $asset->assignedUser->name }}</div>
                                                <small class="text-muted">{{ $asset->assignedUser->email }}</small>
                                                @if($asset->assignedUser->employee_id)
                                                    <br><small class="text-muted">ID: {{ $asset->assignedUser->employee_id }}</small>
                                                @endif
                                            </div>
                                        </div>
                                    @else
                                        <span class="text-muted">Não atribuído</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Especificações Técnicas -->
            @if($asset->specifications)
            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Especificações Técnicas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <p class="mb-0">{{ $asset->specifications }}</p>
                    </div>
                </div>
            </div>
            @endif

            <!-- Observações -->
            @if($asset->notes)
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-sticky-note me-2"></i>
                        Observações
                    </h5>
                </div>
                <div class="card-body">
                    <p class="mb-0">{{ $asset->notes }}</p>
                </div>
            </div>
            @endif

            <!-- Tickets Relacionados -->
            @if($asset->tickets && $asset->tickets->count() > 0)
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-ticket-alt me-2"></i>
                        Tickets Relacionados ({{ $asset->tickets->count() }})
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Título</th>
                                    <th>Status</th>
                                    <th>Prioridade</th>
                                    <th>Criado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($asset->tickets->take(5) as $ticket)
                                <tr>
                                    <td><code>#{{ $ticket->id }}</code></td>
                                    <td>{{ $ticket->title }}</td>
                                    <td>
                                        <span class="badge bg-{{ $ticket->status == 'aberto' ? 'danger' : ($ticket->status == 'em_andamento' ? 'warning' : 'success') }}">
                                            {{ ucfirst(str_replace('_', ' ', $ticket->status)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $ticket->priority == 'alta' ? 'danger' : ($ticket->priority == 'media' ? 'warning' : 'info') }}">
                                            {{ ucfirst($ticket->priority) }}
                                        </span>
                                    </td>
                                    <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                                    <td>
                                        <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        @if($asset->tickets->count() > 5)
                            <div class="text-center mt-3">
                                <a href="{{ route('tickets.index', ['asset_id' => $asset->id]) }}" class="btn btn-outline-primary">
                                    Ver todos os {{ $asset->tickets->count() }} tickets
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Coluna Lateral -->
        <div class="col-lg-4">
            <!-- Informações Financeiras -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-dollar-sign me-2"></i>
                        Informações Financeiras
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Data de Compra</label>
                        <p class="form-control-plaintext">
                            @if($asset->purchase_date)
                                {{ $asset->purchase_date->format('d/m/Y') }}
                                <br><small class="text-muted">{{ $asset->purchase_date->diffForHumans() }}</small>
                            @else
                                <span class="text-muted">Não informada</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Valor de Compra</label>
                        <p class="form-control-plaintext">
                            @if($asset->purchase_cost)
                                <span class="text-success fw-bold">R$ {{ number_format($asset->purchase_cost, 2, ',', '.') }}</span>
                            @else
                                <span class="text-muted">Não informado</span>
                            @endif
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Garantia</label>
                        <p class="form-control-plaintext">
                            @if($asset->warranty_end)
                                @if($asset->warranty_end->isPast())
                                    <span class="text-danger">Expirada em {{ $asset->warranty_end->format('d/m/Y') }}</span>
                                @else
                                    <span class="text-success">Válida até {{ $asset->warranty_end->format('d/m/Y') }}</span>
                                    <br><small class="text-muted">{{ $asset->warranty_end->diffForHumans() }}</small>
                                @endif
                            @else
                                <span class="text-muted">Não informada</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="card shadow mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-tools me-2"></i>
                        Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('tickets.create', ['asset_id' => $asset->id]) }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus me-2"></i>
                            Abrir Ticket
                        </a>
                        @if($asset->status != 'manutencao')
                            <button class="btn btn-outline-warning" onclick="changeStatus('manutencao')">
                                <i class="fas fa-tools me-2"></i>
                                Marcar para Manutenção
                            </button>
                        @endif
                        @if($asset->assigned_to)
                            <button class="btn btn-outline-info" onclick="changeAssignment()">
                                <i class="fas fa-user-times me-2"></i>
                                Reatribuir Ativo
                            </button>
                        @endif
                        <a href="{{ route('admin.assets.edit', $asset) }}" class="btn btn-outline-success">
                            <i class="fas fa-edit me-2"></i>
                            Editar Informações
                        </a>
                    </div>
                </div>
            </div>

            <!-- Auditoria -->
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h6 class="card-title mb-0 text-muted">
                        <i class="fas fa-history me-2"></i>
                        Auditoria
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Criado em</label>
                        <p class="form-control-plaintext">
                            {{ $asset->created_at->format('d/m/Y H:i') }}
                            <br><small class="text-muted">{{ $asset->created_at->diffForHumans() }}</small>
                        </p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted">Última atualização</label>
                        <p class="form-control-plaintext">
                            {{ $asset->updated_at->format('d/m/Y H:i') }}
                            <br><small class="text-muted">{{ $asset->updated_at->diffForHumans() }}</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                <p>Tem certeza que deseja excluir o ativo <strong>{{ $asset->name }}</strong> ({{ $asset->asset_tag }})?</p>
                @if($asset->tickets && $asset->tickets->count() > 0)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Este ativo possui {{ $asset->tickets->count() }} ticket(s) associado(s).
                        A exclusão afetará esses registros.
                    </div>
                @endif
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>
                    Cancelar
                </button>
                <form action="{{ route('admin.assets.destroy', $asset) }}" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash me-1"></i>
                        Confirmar Exclusão
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal QR Code -->
<div class="modal fade" id="qrModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">QR Code do Ativo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrcode"></div>
                <p class="mt-3">{{ $asset->name }} ({{ $asset->asset_tag }})</p>
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

.btn {
    border-radius: 5px;
    font-weight: 500;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/qrcode@1.5.3/build/qrcode.min.js"></script>
<script>
function generateQRCode() {
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    const qrContainer = document.getElementById('qrcode');
    
    // Limpar QR code anterior
    qrContainer.innerHTML = '';
    
    // Gerar QR code
    const assetUrl = window.location.href;
    QRCode.toCanvas(qrContainer, assetUrl, {
        width: 200,
        margin: 2,
        color: {
            dark: '#000000',
            light: '#FFFFFF'
        }
    });
    
    qrModal.show();
}

function printAsset() {
    // Implementar impressão de etiqueta
    window.print();
}

function duplicateAsset() {
    if (confirm('Deseja duplicar este ativo? Um novo ativo será criado com as mesmas informações (exceto patrimônio).')) {
        window.location.href = '{{ route("admin.assets.create") }}?duplicate={{ $asset->id }}';
    }
}

function changeStatus(newStatus) {
    if (confirm(`Deseja alterar o status do ativo para "${newStatus}"?`)) {
        // Implementar mudança de status via AJAX
        // Por enquanto, redirecionar para edição
        window.location.href = '{{ route("admin.assets.edit", $asset) }}';
    }
}

function changeAssignment() {
    if (confirm('Deseja reatribuir este ativo a outro usuário?')) {
        window.location.href = '{{ route("admin.assets.edit", $asset) }}';
    }
}
</script>
@endsection
