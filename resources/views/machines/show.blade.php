@extends('layouts.app')

@section('content')
<style>
:root {
    --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    --info-gradient: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
    --success-gradient: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    --warning-gradient: linear-gradient(135deg, #ffc107 0%, #e0a800 100%);
    --card-shadow: 0 5px 20px rgba(0,0,0,0.1);
    --card-hover-shadow: 0 10px 40px rgba(0,0,0,0.2);
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideInRight {
    from {
        opacity: 0;
        transform: translateX(50px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.05); }
}

.machine-header {
    background: var(--primary-gradient);
    color: white;
    padding: 2.5rem;
    border-radius: 20px;
    margin-bottom: 2.5rem;
    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
    position: relative;
    overflow: hidden;
    animation: fadeInUp 0.6s ease-out;
}

.machine-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 3s ease-in-out infinite;
}

.machine-header h2 {
    font-weight: 700;
    text-shadow: 2px 2px 4px rgba(0,0,0,0.2);
    margin: 0;
    position: relative;
    z-index: 1;
}

.breadcrumb {
    background: transparent !important;
    padding: 0 !important;
    margin: 0 !important;
}

.breadcrumb-item a {
    color: rgba(255,255,255,0.9) !important;
    text-decoration: none;
    transition: all 0.3s;
    font-weight: 500;
    position: relative;
    z-index: 1;
}

.breadcrumb-item a:hover {
    color: white !important;
    transform: translateX(5px);
}

.breadcrumb-item.active {
    position: relative;
    z-index: 1;
}

.nav-tabs {
    border: none;
    gap: 10px;
}

.nav-tabs .nav-link {
    border: none;
    background: #f8f9fa;
    color: #6c757d;
    border-radius: 12px;
    padding: 12px 24px;
    font-weight: 600;
    transition: all 0.3s;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.nav-tabs .nav-link:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.nav-tabs .nav-link.active {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.detail-card {
    border: none;
    border-radius: 20px;
    box-shadow: var(--card-shadow);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    margin-bottom: 1.5rem;
    overflow: hidden;
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
    background: white;
}

.detail-card:nth-child(1) { animation-delay: 0.1s; }
.detail-card:nth-child(2) { animation-delay: 0.2s; }
.detail-card:nth-child(3) { animation-delay: 0.3s; }
.detail-card:nth-child(4) { animation-delay: 0.4s; }

.detail-card:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: var(--card-hover-shadow);
}

.detail-card .card-header {
    font-weight: 700;
    border: none;
    padding: 1.25rem 1.5rem;
    position: relative;
    overflow: hidden;
}

.detail-card .card-header::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
}

.detail-card .card-header h5 {
    margin: 0;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.detail-card .card-header i {
    font-size: 1.3rem;
}

.bg-primary {
    background: var(--primary-gradient) !important;
}

.bg-info {
    background: var(--info-gradient) !important;
}

.bg-success {
    background: var(--success-gradient) !important;
}

.bg-warning {
    background: var(--warning-gradient) !important;
}

.info-item {
    padding: 1.25rem;
    border-left: 4px solid transparent;
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
    border-radius: 12px;
    margin-bottom: 1rem;
    transition: all 0.3s;
    position: relative;
    overflow: hidden;
}

.info-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
    transition: width 0.3s;
}

.info-item:hover {
    transform: translateX(5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.info-item:hover::before {
    width: 100%;
    opacity: 0.1;
}

.info-label {
    font-weight: 700;
    color: #495057;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 6px;
}

.info-label i {
    color: #667eea;
    font-size: 1rem;
}

.info-value {
    font-size: 1.15rem;
    color: #212529;
    font-weight: 600;
}

.info-value code {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
}

.btn {
    border-radius: 12px;
    padding: 10px 24px;
    font-weight: 600;
    transition: all 0.3s;
    border: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.25);
}

.btn-light {
    background: white;
    color: #667eea;
}

.btn-light:hover {
    background: #667eea;
    color: white;
}

.btn-warning {
    background: var(--warning-gradient);
    color: #000;
}

.btn-danger {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

.btn-success {
    background: var(--success-gradient);
    color: white;
}

.btn-secondary {
    background: linear-gradient(135deg, #6c757d 0%, #545b62 100%);
    color: white;
}

.btn-info {
    background: var(--info-gradient);
    color: white;
}

.btn-outline-success {
    border: 2px solid #28a745;
    color: #28a745;
    background: transparent;
    box-shadow: none;
}

.btn-outline-success:hover {
    background: var(--success-gradient);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.badge {
    padding: 6px 14px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.85rem;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.bi-person-circle {
    background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.alert {
    border: none;
    border-radius: 15px;
    padding: 1.25rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    border-left: 5px solid;
}

.alert-warning {
    background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
    border-left-color: #ffc107;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
    border-left-color: #17a2b8;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
    border-left-color: #28a745;
}

img.border {
    border: 3px solid #667eea !important;
    border-radius: 12px;
    box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
    transition: all 0.3s;
}

img.border:hover {
    transform: scale(1.05);
    box-shadow: 0 8px 30px rgba(102, 126, 234, 0.5);
}

.col-md-4 .detail-card {
    animation: slideInRight 0.6s ease-out;
    animation-fill-mode: both;
}

.tab-content {
    animation: fadeInUp 0.4s ease-out;
}

@media print {
    .no-print { display: none !important; }
    .machine-header, .detail-card .card-header { 
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .detail-card { 
        box-shadow: none;
        page-break-inside: avoid;
    }
}

@media (max-width: 768px) {
    .machine-header {
        padding: 1.5rem;
        border-radius: 15px;
    }
    
    .machine-header h2 {
        font-size: 1.5rem;
    }
    
    .btn {
        width: 100%;
        margin-bottom: 10px;
    }
}
</style>

<div class="container">
    <!-- Cabeçalho -->
    <div class="machine-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2">
                    <i class="bi bi-pc-display-horizontal"></i> Detalhes da Máquina
                </h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0" style="background: transparent;">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-white text-decoration-none">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('machines.index') }}" class="text-white text-decoration-none">Inventário</a></li>
                        <li class="breadcrumb-item active text-white">{{ $machine->patrimonio }}</li>
                    </ol>
                </nav>
            </div>
            <div class="no-print">
                <button onclick="window.print()" class="btn btn-light me-2">
                    <i class="bi bi-printer"></i> Imprimir
                </button>
                @can('machines.edit')
                    <a href="{{ route('machines.edit', $machine) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                @endcan
                @can('machines.delete')
                    <form action="{{ route('machines.destroy', $machine) }}" method="POST" class="d-inline" 
                          onsubmit="return confirm('Tem certeza que deseja excluir esta máquina?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Excluir
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-4" id="machineTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button">
                <i class="bi bi-info-circle"></i> Detalhes
            </button>
        </li>
        @if($oldMachine)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="old-machine-tab" data-bs-toggle="tab" data-bs-target="#old-machine" type="button">
                    <i class="bi bi-arrow-left-right"></i> Máquina Substituída
                </button>
            </li>
        @endif
        @if($deliveredTogether->count() > 0)
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="delivered-tab" data-bs-toggle="tab" data-bs-target="#delivered" type="button">
                    <i class="bi bi-box-seam"></i> Entregues Juntas ({{ $deliveredTogether->count() }})
                </button>
            </li>
        @endif
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Tab Detalhes -->
        <div class="tab-pane fade show active" id="details">
            <div class="row">
                <!-- Coluna Esquerda -->
                <div class="col-md-8">
                    <!-- Informações Básicas -->
                    <div class="detail-card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informações Básicas</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-tag"></i> Patrimônio</div>
                                        <div class="info-value text-primary fw-bold">{{ $machine->patrimonio }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-upc-scan"></i> Número de Série</div>
                                        <div class="info-value"><code>{{ $machine->numero_serie }}</code></div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-gear"></i> Status</div>
                                        <div class="info-value">{!! $machine->status_badge !!}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-pc-display"></i> Tipo</div>
                                        <div class="info-value">{!! $machine->tipo_badge !!}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-building"></i> Marca</div>
                                        <div class="info-value">{{ $machine->marca ?: 'Não informado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-box"></i> Modelo</div>
                                        <div class="info-value">{{ $machine->modelo }}</div>
                                    </div>
                                </div>
                                @if($machine->descricao)
                                    <div class="col-12">
                                        <div class="info-item">
                                            <div class="info-label"><i class="bi bi-card-text"></i> Descrição</div>
                                            <div class="info-value">{{ $machine->descricao }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Especificações Técnicas -->
                    <div class="detail-card">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-cpu"></i> Especificações Técnicas</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-cpu-fill"></i> Processador</div>
                                        <div class="info-value">{{ $machine->processador ?: 'Não informado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-memory"></i> RAM</div>
                                        <div class="info-value">{{ $machine->memoria_ram ?: 'Não informado' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-device-hdd"></i> Armazenamento</div>
                                        <div class="info-value">{{ $machine->armazenamento ?: 'Não informado' }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-windows"></i> Sistema Operacional</div>
                                        <div class="info-value">{{ $machine->sistema_operacional ?: 'Não informado' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($machine->observacoes)
                        <div class="detail-card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Observações</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $machine->observacoes }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Coluna Direita -->
                <div class="col-md-4">
                    <!-- Usuário Vinculado -->
                    <div class="detail-card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-person-check"></i> Usuário Vinculado</h5>
                        </div>
                        <div class="card-body text-center">
                            @if($machine->user)
                                <i class="bi bi-person-circle fs-1 text-success mb-3 d-block"></i>
                                <h5>{{ $machine->user->name }}</h5>
                                <p class="text-muted mb-2">
                                    <i class="bi bi-envelope"></i> {{ $machine->user->email }}
                                </p>
                                @if($machine->user->department)
                                    <p class="text-muted mb-3">
                                        <i class="bi bi-building"></i> {{ $machine->user->department->name }}
                                    </p>
                                @endif
                                <a href="{{ route('admin.users.show', $machine->user) }}" class="btn btn-sm btn-outline-success">
                                    Ver Perfil <i class="bi bi-arrow-right"></i>
                                </a>
                            @else
                                <i class="bi bi-person-x fs-1 text-muted mb-3 d-block"></i>
                                <p class="text-muted">Nenhum usuário vinculado</p>
                            @endif
                        </div>
                    </div>

                    <!-- Informações Financeiras -->
                    <div class="detail-card">
                        <div class="card-header bg-warning text-dark">
                            <h5 class="mb-0"><i class="bi bi-cash-coin"></i> Informações Financeiras</h5>
                        </div>
                        <div class="card-body">
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-calendar-event"></i> Data de Aquisição</div>
                                <div class="info-value">{{ $machine->data_aquisicao ? $machine->data_aquisicao->format('d/m/Y') : 'Não informado' }}</div>
                            </div>
                            
                            <div class="info-item">
                                <div class="info-label"><i class="bi bi-cash"></i> Valor</div>
                                <div class="info-value">
                                    @if($machine->valor_aquisicao)
                                        <span class="text-success fw-bold">R$ {{ number_format($machine->valor_aquisicao, 2, ',', '.') }}</span>
                                    @else
                                        <span class="text-muted">Não informado</span>
                                    @endif
                                </div>
                            </div>

                            @if($machine->contrato_licitacao)
                                <div class="info-item">
                                    <div class="info-label"><i class="bi bi-file-earmark-text"></i> Contrato/Licitação</div>
                                    <div class="info-value">{{ $machine->contrato_licitacao }}</div>
                                </div>
                            @endif
                            
                            @if($machine->numero_licitacao)
                                <div class="info-item">
                                    <div class="info-label"><i class="bi bi-hash"></i> Número</div>
                                    <div class="info-value">{{ $machine->numero_licitacao }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Troca de Equipamento -->
                    @if($machine->is_troca)
                        <div class="detail-card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-arrow-left-right"></i> Troca de Equipamento</h5>
                            </div>
                            <div class="card-body">
                                <div class="info-item">
                                    <div class="info-label"><i class="bi bi-tag-fill"></i> Patrimônio Substituído</div>
                                    <div class="info-value"><code>{{ $machine->patrimonio_substituido }}</code></div>
                                </div>
                                @if($machine->motivo_troca)
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-chat-text"></i> Motivo</div>
                                        <div class="info-value">{{ $machine->motivo_troca }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Informações de Entrega -->
                    @if($machine->recebedor || $machine->data_entrega || $machine->assinatura_digital || $machine->assinatura_status)
                        <div class="detail-card">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="bi bi-truck"></i> Informações de Entrega</h5>
                            </div>
                            <div class="card-body">
                                @if($machine->recebedor)
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-person"></i> Recebedor</div>
                                        <div class="info-value">
                                            <a href="{{ route('admin.users.show', $machine->recebedor) }}">{{ $machine->recebedor->name }}</a>
                                        </div>
                                    </div>
                                @endif
                                
                                @if($machine->data_entrega)
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-calendar-check"></i> Data</div>
                                        <div class="info-value">{{ $machine->data_entrega->format('d/m/Y H:i') }}</div>
                                    </div>
                                @endif
                                
                                @if($machine->entregador)
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-person-badge"></i> Entregador</div>
                                        <div class="info-value">
                                            <a href="{{ route('admin.users.show', $machine->entregador) }}">{{ $machine->entregador->name }}</a>
                                        </div>
                                    </div>
                                @endif

                                {{-- Seção de Assinatura Digital - Sempre Exibir --}}
                                <hr>
                                <div class="text-center mt-3">
                                    <div class="info-label mb-3"><i class="bi bi-pen"></i> ASSINATURA DIGITAL</div>
                                    
                                    @if($machine->assinatura_digital)
                                        <div class="signature-box mb-3" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                            <div style="min-height: 150px; max-height: 200px; display: flex; align-items: center; justify-content: center; background: white; border: 2px solid #dee2e6; border-radius: 8px; padding: 10px;">
                                                <img src="{{ route('machines.signature', $machine) }}" 
                                                     alt="Assinatura Digital" 
                                                     style="max-width: 100%; max-height: 180px; object-fit: contain;" 
                                                     loading="eager"
                                                     onerror="console.error('Erro ao carregar assinatura da máquina #{{ $machine->id }}'); this.style.display='none'; this.parentElement.innerHTML='<div class=\'alert alert-danger m-0\'><i class=\'bi bi-x-circle\'></i> Erro ao carregar imagem da assinatura</div>'">
                                            </div>
                                            <small class="text-muted d-block mt-2">
                                                <i class="bi bi-info-circle"></i> Assinatura validada em {{ $machine->assinatura_validada_em ? $machine->assinatura_validada_em->format('d/m/Y H:i') : 'N/A' }}
                                            </small>
                                        </div>
                                        
                                        @if($machine->nome_legivel_assinatura)
                                            <div class="mb-3 p-2 bg-light rounded">
                                                <strong>Nome do Recebedor:</strong><br>
                                                <a href="{{ route('admin.users.show', $machine->recebedor) }}" class="fw-bold text-primary" style="font-size: 1.1em;">
                                                    {{ $machine->nome_legivel_assinatura }}
                                                </a>
                                            </div>
                                        @endif
                                    @else
                                        <div class="alert alert-info">
                                            <i class="bi bi-info-circle"></i> Assinatura não coletada (cadastro parcial)
                                        </div>
                                        @if($machine->recebedor)
                                            <button type="button" class="btn btn-primary btn-sm" onclick="abrirModalColetarAssinatura({{ $machine->id }}, '{{ $machine->recebedor->name }}', '{{ $machine->patrimonio }}')">
                                                <i class="bi bi-pen"></i> Coletar Assinatura Agora
                                            </button>
                                        @endif
                                    @endif
                                    
                                    {{-- Status da Assinatura --}}
                                    @if($machine->assinatura_status && $machine->assinatura_status !== 'nao_requerida')
                                        <div class="mb-3">
                                            <strong><i class="bi bi-shield-check"></i> Status da Assinatura:</strong><br>
                                            <div class="mt-2">
                                                {!! $machine->assinatura_status_badge !!}
                                            </div>
                                        </div>
                                        
                                        @if($machine->assinatura_status === 'validada' && $machine->assinatura_validada_em)
                                            <div class="alert alert-success mt-2 mb-0">
                                                <i class="bi bi-check-circle-fill"></i> <strong>Assinatura Certificada</strong><br>
                                                <small>
                                                    Validado por: <strong>{{ $machine->validadorAssinatura->name ?? $machine->assinatura_usuario_validador }}</strong>
                                                    @if($machine->assinatura_validada_por_terceiro && $machine->recebedor)
                                                        <span class="badge bg-info ms-1" title="Validado por outra pessoa em nome do recebedor">
                                                            <i class="bi bi-person-check"></i> Validação por Terceiro
                                                        </span>
                                                        <br>Recebedor: <strong>{{ $machine->recebedor->name }}</strong>
                                                    @endif
                                                    <br>Em: {{ $machine->assinatura_validada_em->format('d/m/Y H:i') }}<br>
                                                    <i class="bi bi-lock-fill"></i> Certificado digitalmente via login de rede
                                                </small>
                                            </div>
                                        @elseif($machine->assinatura_status === 'pendente')
                                            <div class="alert alert-warning mt-2 mb-2">
                                                <i class="bi bi-exclamation-triangle-fill"></i> <strong>Aguardando Validação</strong><br>
                                                <small>Esta assinatura ainda não foi validada com credenciais do usuário.</small>
                                                <hr class="my-2">
                                                <button type="button" class="btn btn-sm btn-success w-100" onclick="abrirModalValidacao({{ $machine->id }}, '{{ $machine->patrimonio }}')">
                                                    <i class="bi bi-shield-check"></i> Validar Assinatura Agora
                                                </button>
                                            </div>
                                        @endif
                                    @endif
                                </div>

                                @if($machine->observacoes_entrega)
                                    <div class="info-item">
                                        <div class="info-label"><i class="bi bi-chat-square-text"></i> Observações</div>
                                        <div class="info-value">{{ $machine->observacoes_entrega }}</div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Registro -->
                    <div class="detail-card no-print">
                        <div class="card-header">
                            <h6 class="mb-0"><i class="bi bi-clock-history"></i> Registro</h6>
                        </div>
                        <div class="card-body">
                            <small>
                                <strong>Criado em:</strong><br>
                                {{ $machine->created_at->format('d/m/Y H:i') }}
                            </small>
                            <hr>
                            <small>
                                <strong>Última atualização:</strong><br>
                                {{ $machine->updated_at->format('d/m/Y H:i') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tab Máquina Substituída -->
        @if($oldMachine)
        <div class="tab-pane fade" id="old-machine">
            <div class="alert alert-warning">
                <i class="bi bi-info-circle"></i> Esta máquina <strong>{{ $machine->patrimonio }}</strong> foi entregue como substituição da máquina abaixo:
            </div>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="detail-card">
                        <div class="card-header bg-secondary text-white">
                            <h5 class="mb-0"><i class="bi bi-pc-display-horizontal"></i> Máquina Antiga</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <strong><i class="bi bi-tag"></i> Patrimônio:</strong><br>
                                    <span class="fs-5 text-secondary">{{ $oldMachine->patrimonio }}</span>
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="bi bi-upc-scan"></i> Número de Série:</strong><br>
                                    <code>{{ $oldMachine->numero_serie }}</code>
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="bi bi-gear"></i> Status:</strong><br>
                                    {!! $oldMachine->status_badge !!}
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="bi bi-pc-display"></i> Tipo:</strong><br>
                                    {!! $oldMachine->tipo_badge !!}
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="bi bi-building"></i> Marca:</strong><br>
                                    {{ $oldMachine->marca ?: 'Não informado' }}
                                </div>
                                <div class="col-md-4">
                                    <strong><i class="bi bi-box"></i> Modelo:</strong><br>
                                    {{ $oldMachine->modelo }}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($machine->motivo_troca)
                        <div class="detail-card">
                            <div class="card-header bg-warning text-dark">
                                <h5 class="mb-0"><i class="bi bi-chat-left-text"></i> Motivo da Troca</h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">{{ $machine->motivo_troca }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-md-4">
                    <div class="detail-card">
                        <div class="card-body text-center">
                            <a href="{{ route('machines.show', $oldMachine) }}" class="btn btn-secondary">
                                <i class="bi bi-eye"></i> Ver Detalhes Completos
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Tab Entregues Juntas -->
        @if($deliveredTogether->count() > 0)
        <div class="tab-pane fade" id="delivered">
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> Máquinas entregues junto com <strong>{{ $machine->patrimonio }}</strong>:
            </div>

            <div class="row">
                @foreach($deliveredTogether as $delivered)
                    <div class="col-md-6 mb-3">
                        <div class="detail-card h-100">
                            <div class="card-header bg-info text-white">
                                <h6 class="mb-0"><i class="bi bi-laptop"></i> {{ $delivered->patrimonio }}</h6>
                            </div>
                            <div class="card-body">
                                <p class="mb-2"><strong>Tipo:</strong> {!! $delivered->tipo_badge !!}</p>
                                <p class="mb-2"><strong>Modelo:</strong> {{ $delivered->modelo }}</p>
                                <p class="mb-2"><strong>Número de Série:</strong> <code>{{ $delivered->numero_serie }}</code></p>
                                @if($delivered->user)
                                    <p class="mb-2"><strong>Usuário:</strong> 
                                        <a href="{{ route('admin.users.show', $delivered->user) }}">{{ $delivered->user->name }}</a>
                                    </p>
                                @endif
                                <a href="{{ route('machines.show', $delivered) }}" class="btn btn-sm btn-info w-100 mt-2">
                                    <i class="bi bi-eye"></i> Ver Detalhes
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div class="mt-4 no-print">
        <a href="{{ route('machines.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar para Inventário
        </a>
    </div>
</div>

{{-- Modal de Validação (mantido igual) --}}
<div class="modal fade" id="validacaoAssinaturaModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-shield-check"></i> Validar Assinatura</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> <strong>Atenção:</strong> Preferencialmente, o recebedor deve validar com suas credenciais.
                </div>
                
                <p class="mb-3">
                    <strong>Recebedor:</strong> <span id="modal_recebedor_nome"></span><br>
                    <small class="text-muted" id="modal_recebedor_email"></small>
                </p>
                
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="validar_por_terceiro">
                    <label class="form-check-label" for="validar_por_terceiro">
                        <strong>Validar por outra pessoa</strong><br>
                        <small class="text-muted">Marque se o recebedor não estiver disponível</small>
                    </label>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Login de Rede *</label>
                    <div class="position-relative">
                        <input type="text" class="form-control" id="validacao_login" placeholder="Digite para buscar usuário..." autocomplete="off">
                    </div>
                    <small class="text-muted" id="login_hint">O recebedor deve digitar seu próprio login de rede</small>
                    <div id="usuario_info" class="mt-2" style="display: none;">
                        <small class="text-success">
                            <i class="bi bi-person-check"></i> <span id="usuario_nome"></span>
                        </small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Senha de Rede *</label>
                    <input type="password" class="form-control" id="validacao_senha" placeholder="Senha da rede">
                </div>
                
                <div id="validacao_erro" class="alert alert-danger" style="display: none;"></div>
                <div id="validacao_sucesso" class="alert alert-success" style="display: none;">
                    <i class="bi bi-check-circle-fill"></i> <strong>Assinatura validada com sucesso!</strong><br>
                    <span id="validacao_texto_confirmacao"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btn_validar_assinatura_pendente">
                    <i class="bi bi-check-circle"></i> Validar Assinatura
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let machineIdAtual = null;

function solicitarValidacao(machineId, nomeRecebedor, emailRecebedor) {
    machineIdAtual = machineId;
    
    document.getElementById('modal_recebedor_nome').textContent = nomeRecebedor;
    document.getElementById('modal_recebedor_email').textContent = emailRecebedor;
    document.getElementById('validacao_login').value = '';
    document.getElementById('validacao_senha').value = '';
    document.getElementById('validacao_erro').style.display = 'none';
    document.getElementById('validacao_sucesso').style.display = 'none';
    document.getElementById('validar_por_terceiro').checked = false;
    
    const modal = new bootstrap.Modal(document.getElementById('validacaoAssinaturaModal'));
    modal.show();
}

// Atualiza hint quando checkbox muda
document.getElementById('validar_por_terceiro')?.addEventListener('change', function() {
    const hint = document.getElementById('login_hint');
    if (this.checked) {
        hint.textContent = 'Digite o login de rede de qualquer usuário autorizado';
        hint.className = 'text-primary';
    } else {
        hint.textContent = 'O recebedor deve digitar seu próprio login';
        hint.className = 'text-muted';
    }
});

// Busca de usuários em tempo real
let searchTimeout;
const loginInput = document.getElementById('validacao_login');
const usuarioInfo = document.getElementById('usuario_info');
const usuarioNome = document.getElementById('usuario_nome');

loginInput?.addEventListener('input', async function() {
    const search = this.value.trim();
    
    if (search.length < 2) {
        if (usuarioInfo) usuarioInfo.style.display = 'none';
        return;
    }
    
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(async () => {
        try {
            const res = await fetch(`{{ route('machines.search-users') }}?q=${encodeURIComponent(search)}`);
            const users = await res.json();
            
            if (users.length > 0 && usuarioNome) {
                usuarioNome.textContent = users[0].name;
                usuarioInfo.style.display = 'block';
            }
        } catch (error) {
            console.error('Erro ao buscar usuários:', error);
        }
    }, 300);
});

document.getElementById('btn_validar_assinatura_pendente')?.addEventListener('click', async function() {
    const login = document.getElementById('validacao_login').value.trim();
    const senha = document.getElementById('validacao_senha').value;
    const validarPorTerceiro = document.getElementById('validar_por_terceiro').checked;
    const erroDiv = document.getElementById('validacao_erro');
    const sucessoDiv = document.getElementById('validacao_sucesso');
    
    if (!login || !senha) {
        erroDiv.textContent = 'Preencha login e senha.';
        erroDiv.style.display = 'block';
        return;
    }
    
    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Validando...';
    erroDiv.style.display = 'none';
    sucessoDiv.style.display = 'none';
    
    try {
        const res = await fetch('{{ route("machines.validate-signature") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ login, senha, machine_id: machineIdAtual, validar_por_terceiro: validarPorTerceiro })
        });
        
        // Verifica se é erro 419 (CSRF token expirado)
        if (res.status === 419) {
            alert('Sua sessão expirou. A página será recarregada.');
            location.reload();
            return;
        }
        
        if (!res.ok) {
            const text = await res.text();
            console.error('Erro na resposta:', text);
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const data = await res.json();
        
        if (data.success) {
            // Atualiza status da assinatura no banco
            const updateRes = await fetch(`/machines/${machineIdAtual}/validate-signature-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ 
                    usuario_validador: login,
                    user_id: data.user_id,
                    validado_por_terceiro: validarPorTerceiro
                })
            });
            
            if (updateRes.ok) {
                document.getElementById('validacao_texto_confirmacao').innerHTML = 
                    `Validado por <strong>${data.user_name}</strong> (${login})<br>` +
                    `<small class="text-muted">Certificado digitalmente via login de rede</small>`;
                sucessoDiv.style.display = 'block';
                
                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                erroDiv.textContent = 'Erro ao atualizar o status da assinatura.';
                erroDiv.style.display = 'block';
            }
        } else {
            if (data.session_expired) {
                alert('Sua sessão expirou. Você será redirecionado para fazer login novamente.');
                window.location.href = '/login';
                return;
            }
            
            erroDiv.textContent = data.message || 'Login ou senha inválidos.';
            erroDiv.style.display = 'block';
        }
    } catch (error) {
        erroDiv.textContent = 'Erro ao validar credenciais. Tente novamente.';
        erroDiv.style.display = 'block';
    } finally {
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-check-circle"></i> Validar Assinatura';
    }
});

// Renova o CSRF token a cada 10 minutos
setInterval(async () => {
    try {
        const response = await fetch('/sanctum/csrf-cookie', { credentials: 'same-origin' });
        if (response.ok) {
            console.log('CSRF token renovado');
        }
    } catch (error) {
        console.error('Erro ao renovar CSRF token:', error);
    }
}, 10 * 60 * 1000);

// Função para abrir modal de validação
function abrirModalValidacao(machineId, patrimonio) {
    document.getElementById('validate-machine-id-show').value = machineId;
    document.getElementById('modal-patrimonio-show').textContent = patrimonio;
    
    // Limpar campos
    document.getElementById('validateSignatureFormShow').reset();
    document.getElementById('validate-error-message-show').classList.add('d-none');
    document.getElementById('validate-success-message-show').classList.add('d-none');
    
    // Resetar filtro de busca
    const searchInput = document.getElementById('search-user-show');
    if (searchInput) {
        searchInput.value = '';
        // Mostrar todas as opções
        const options = document.getElementById('validate-user-select-show').querySelectorAll('option');
        options.forEach(opt => opt.style.display = '');
    }
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('validateSignatureModalShow'));
    modal.show();
}

// Filtro de busca de usuários no modal de validação
document.addEventListener('DOMContentLoaded', function() {
    const searchUserInput = document.getElementById('search-user-show');
    const userSelect = document.getElementById('validate-user-select-show');
    
    if (searchUserInput && userSelect) {
        searchUserInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = userSelect.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') return; // Pular opção vazia
                
                const name = option.dataset.name || '';
                const username = option.dataset.username || '';
                
                if (name.includes(searchTerm) || username.includes(searchTerm)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Auto-selecionar se houver apenas uma opção visível
            const visibleOptions = Array.from(options).filter(opt => opt.style.display !== 'none' && opt.value !== '');
            if (visibleOptions.length === 1) {
                userSelect.value = visibleOptions[0].value;
                visibleOptions[0].selected = true;
            }
        });
        
        // Garantir que clique na opção a selecione
        userSelect.addEventListener('change', function(e) {
            console.log('Usuário selecionado:', this.value);
        });
        
        userSelect.addEventListener('click', function(e) {
            if (e.target.tagName === 'OPTION' && e.target.value) {
                this.value = e.target.value;
                e.target.selected = true;
                this.dispatchEvent(new Event('change'));
            }
        });
        
        // Permitir Enter para selecionar
        searchUserInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const visibleOptions = Array.from(userSelect.querySelectorAll('option')).filter(
                    opt => opt.style.display !== 'none' && opt.value !== ''
                );
                if (visibleOptions.length > 0) {
                    userSelect.value = visibleOptions[0].value;
                    visibleOptions[0].selected = true;
                    document.getElementById('validate-password-show').focus();
                }
            }
        });
    }
});

// Validar assinatura no modal da página show
document.getElementById('confirmValidateBtnShow').addEventListener('click', async function() {
    const errorDiv = document.getElementById('validate-error-message-show');
    const successDiv = document.getElementById('validate-success-message-show');
    
    errorDiv.classList.add('d-none');
    successDiv.classList.add('d-none');

    const machineId = document.getElementById('validate-machine-id-show').value;
    const username = document.getElementById('validate-user-select-show').value;
    const password = document.getElementById('validate-password-show').value;
    const isTerceiro = document.getElementById('validate-third-party-show').checked;

    if (!username || !password) {
        errorDiv.textContent = 'Por favor, selecione o usuário e digite a senha.';
        errorDiv.classList.remove('d-none');
        return;
    }

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Validando...';

    try {
        const response = await fetch('/machines/validate-signature', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                login: username,
                senha: password,
                machine_id: machineId,
                validar_por_terceiro: isTerceiro
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Credenciais inválidas ou erro ao validar assinatura');
        }

        successDiv.textContent = 'Assinatura validada com sucesso!';
        successDiv.classList.remove('d-none');

        setTimeout(() => {
            location.reload();
        }, 1500);

    } catch (error) {
        errorDiv.textContent = error.message;
        errorDiv.classList.remove('d-none');
    } finally {
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-check-circle"></i> Validar Assinatura';
    }
});

// Canvas de assinatura
let canvas, ctx, isDrawing = false;

function abrirModalColetarAssinatura(machineId, recebedorNome, patrimonio) {
    document.getElementById('coletar-machine-id').value = machineId;
    document.getElementById('modal-recebedor-nome').textContent = recebedorNome;
    document.getElementById('modal-patrimonio-coletar').textContent = patrimonio;
    document.getElementById('nome-legivel').value = recebedorNome;
    
    // Limpar mensagens
    document.getElementById('coletar-error-message').classList.add('d-none');
    document.getElementById('coletar-success-message').classList.add('d-none');
    
    // Mostrar modal
    const modal = new bootstrap.Modal(document.getElementById('coletarAssinaturaModal'));
    modal.show();
    
    // Inicializar canvas após modal abrir
    setTimeout(() => {
        inicializarCanvas();
    }, 300);
}

function inicializarCanvas() {
    canvas = document.getElementById('signatureCanvas');
    if (!canvas) return;
    
    ctx = canvas.getContext('2d');
    ctx.strokeStyle = '#000';
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    
    // Mouse events
    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseout', stopDrawing);
    
    // Touch events
    canvas.addEventListener('touchstart', handleTouch);
    canvas.addEventListener('touchmove', handleTouch);
    canvas.addEventListener('touchend', stopDrawing);
}

function startDrawing(e) {
    isDrawing = true;
    const rect = canvas.getBoundingClientRect();
    ctx.beginPath();
    ctx.moveTo(e.clientX - rect.left, e.clientY - rect.top);
}

function draw(e) {
    if (!isDrawing) return;
    const rect = canvas.getBoundingClientRect();
    ctx.lineTo(e.clientX - rect.left, e.clientY - rect.top);
    ctx.stroke();
}

function stopDrawing() {
    isDrawing = false;
}

function handleTouch(e) {
    e.preventDefault();
    const touch = e.touches[0];
    const mouseEvent = new MouseEvent(e.type === 'touchstart' ? 'mousedown' : 'mousemove', {
        clientX: touch.clientX,
        clientY: touch.clientY
    });
    canvas.dispatchEvent(mouseEvent);
}

function limparAssinatura() {
    if (ctx && canvas) {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
    }
}

// Salvar assinatura
document.getElementById('salvarAssinaturaBtn').addEventListener('click', async function() {
    const errorDiv = document.getElementById('coletar-error-message');
    const successDiv = document.getElementById('coletar-success-message');
    
    errorDiv.classList.add('d-none');
    successDiv.classList.add('d-none');

    const machineId = document.getElementById('coletar-machine-id').value;
    const nomeLegivel = document.getElementById('nome-legivel').value;

    if (!nomeLegivel) {
        errorDiv.textContent = 'Por favor, digite o nome completo.';
        errorDiv.classList.remove('d-none');
        return;
    }

    // Verificar se há assinatura no canvas
    const canvasData = canvas.toDataURL();
    const emptyCanvas = document.createElement('canvas');
    emptyCanvas.width = canvas.width;
    emptyCanvas.height = canvas.height;
    
    if (canvasData === emptyCanvas.toDataURL()) {
        errorDiv.textContent = 'Por favor, assine no quadro acima.';
        errorDiv.classList.remove('d-none');
        return;
    }

    this.disabled = true;
    this.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Salvando...';

    try {
        const response = await fetch(`/machines/${machineId}/save-signature`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                assinatura_digital: canvasData,
                nome_legivel_assinatura: nomeLegivel,
                assinatura_status: 'pendente'
            })
        });

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || 'Erro ao salvar assinatura');
        }

        successDiv.textContent = 'Assinatura salva com sucesso!';
        successDiv.classList.remove('d-none');

        setTimeout(() => {
            location.reload();
        }, 1500);

    } catch (error) {
        console.error('Erro ao salvar assinatura:', error);
        errorDiv.textContent = error.message || 'Erro ao salvar assinatura';
        errorDiv.classList.remove('d-none');
    } finally {
        this.disabled = false;
        this.innerHTML = '<i class="bi bi-check-circle"></i> Salvar Assinatura';
    }
});
</script>
@endpush

<!-- Modal de Validação de Assinatura -->
<div class="modal fade" id="validateSignatureModalShow" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-check"></i> Validar Assinatura Digital
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Validando assinatura para máquina <strong id="modal-patrimonio-show"></strong>
                </div>
                
                <form id="validateSignatureFormShow">
                    @csrf
                    <input type="hidden" id="validate-machine-id-show" name="machine_id">
                    
                    <div class="mb-3">
                        <label for="validate-user-select-show" class="form-label">
                            <i class="bi bi-person-badge"></i> Selecionar Usuário
                        </label>
                        <input type="text" class="form-control mb-2" id="search-user-show" 
                               placeholder="Digite para buscar usuário..." autocomplete="off">
                        <select class="form-select" id="validate-user-select-show" required>
                            <option value="">Selecione o usuário...</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                <option value="{{ $user->username }}" data-name="{{ strtolower($user->name) }}" data-username="{{ strtolower($user->username) }}">
                                    {{ $user->name }} ({{ $user->username }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Selecione o usuário que receberá a máquina</small>
                    </div>

                    <div class="mb-3">
                        <label for="validate-password-show" class="form-label">
                            <i class="bi bi-key"></i> Senha LDAP do Usuário
                        </label>
                        <input type="password" class="form-control" id="validate-password-show" 
                               name="password" required placeholder="Digite a senha de rede do usuário" autocomplete="off">
                        <small class="text-muted">Digite a senha de rede (LDAP) do usuário selecionado</small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="validate-third-party-show" name="validacao_terceiro">
                        <label class="form-check-label" for="validate-third-party-show">
                            Sou terceiro validando esta assinatura
                        </label>
                    </div>

                    <div id="validate-error-message-show" class="alert alert-danger d-none"></div>
                    <div id="validate-success-message-show" class="alert alert-success d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="confirmValidateBtnShow">
                    <i class="bi bi-check-circle"></i> Validar Assinatura
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Coleta de Assinatura -->
<div class="modal fade" id="coletarAssinaturaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-pen"></i> Coletar Assinatura Digital
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Coletando assinatura de <strong id="modal-recebedor-nome"></strong> para máquina <strong id="modal-patrimonio-coletar"></strong>
                </div>
                
                <form id="coletarAssinaturaForm">
                    @csrf
                    <input type="hidden" id="coletar-machine-id" name="machine_id">
                    
                    <div class="mb-3">
                        <label class="form-label">
                            <i class="bi bi-pen"></i> Assine no quadro abaixo
                        </label>
                        <div class="border rounded p-2" style="background: white;">
                            <canvas id="signatureCanvas" width="600" height="200" style="border: 2px dashed #ccc; cursor: crosshair; width: 100%; max-width: 600px;"></canvas>
                        </div>
                        <div class="mt-2">
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="limparAssinatura()">
                                <i class="bi bi-eraser"></i> Limpar
                            </button>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="nome-legivel" class="form-label">
                            <i class="bi bi-person"></i> Nome Completo (legível)
                        </label>
                        <input type="text" class="form-control" id="nome-legivel" 
                               name="nome_legivel" required placeholder="Digite seu nome completo">
                    </div>

                    <div id="coletar-error-message" class="alert alert-danger d-none"></div>
                    <div id="coletar-success-message" class="alert alert-success d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="salvarAssinaturaBtn">
                    <i class="bi bi-check-circle"></i> Salvar Assinatura
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
