@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fas fa-key"></i>
                        {{ $license->software->name ?? 'N/A' }}
                        @if($license->version)
                            <small class="text-muted">v{{ $license->version }}</small>
                        @endif
                    </h3>
                    <div>
                        <a href="{{ route('licenses.edit', $license) }}" class="btn btn-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('licenses.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Status da Licença -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="alert alert-{{ $license->status == 'active' ? 'success' : ($license->status == 'expired' ? 'danger' : 'warning') }}">
                                <h5 class="alert-heading">
                                    <i class="fas fa-{{ $license->status == 'active' ? 'check-circle' : ($license->status == 'expired' ? 'times-circle' : 'exclamation-triangle') }}"></i>
                                    Status: {{ $license->status == 'active' ? 'Ativa' : ($license->status == 'expired' ? 'Expirada' : 'Inativa') }}
                                </h5>
                                @if($license->expires_at)
                                    @if($license->expires_at->isPast())
                                        <p class="mb-0">Esta licença expirou em {{ $license->expires_at->format('d/m/Y') }}.</p>
                                    @elseif($license->expires_at->diffInDays() <= 30)
                                        <p class="mb-0">Esta licença expira em {{ $license->expires_at->diffInDays() }} dias ({{ $license->expires_at->format('d/m/Y') }}).</p>
                                    @else
                                        <p class="mb-0">Esta licença expira em {{ $license->expires_at->format('d/m/Y') }}.</p>
                                    @endif
                                @else
                                    <p class="mb-0">Esta é uma licença perpétua (não expira).</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Informações da Licença -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-cube"></i> Informações da Licença
                            </h5>

                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%"><strong>Software:</strong></td>
                                    <td>{{ $license->software->name ?? 'N/A' }}</td>
                                </tr>
                                @if($license->software && $license->software->publisher)
                                <tr>
                                    <td><strong>Desenvolvedor:</strong></td>
                                    <td>{{ $license->software->publisher }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td><strong>Versão:</strong></td>
                                    <td>
                                        @if($license->version)
                                            <span class="badge badge-secondary">{{ $license->version }}</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $license->type == 'perpetual' ? 'success' : 'info' }}">
                                            {{ $license->type == 'perpetual' ? 'Perpétua' : 'Assinatura' }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Chave da Licença:</strong></td>
                                    <td>
                                        @if($license->license_key)
                                            <div class="d-flex align-items-center">
                                                <code id="licenseKey" style="cursor: pointer;">{{ Str::mask($license->license_key, '*', 4, -4) }}</code>
                                                <button type="button" class="btn btn-sm btn-outline-secondary ml-2" onclick="toggleLicenseKey()">
                                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-secondary ml-1" onclick="copyLicenseKey()">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $license->status == 'active' ? 'success' : ($license->status == 'expired' ? 'danger' : 'secondary') }}">
                                            {{ $license->status == 'active' ? 'Ativa' : ($license->status == 'expired' ? 'Expirada' : 'Inativa') }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <!-- Uso e Comercial -->
                        <div class="col-md-6">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-chart-bar"></i> Uso da Licença
                            </h5>

                            <div class="mb-4">
                                @php
                                    $usedSeats = $license->installations->count();
                                    $percentage = $license->seats > 0 ? ($usedSeats / $license->seats) * 100 : 0;
                                @endphp
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span><strong>Licenças em uso:</strong></span>
                                    <span class="badge badge-{{ $percentage > 90 ? 'danger' : ($percentage > 70 ? 'warning' : 'success') }}">
                                        {{ $usedSeats }} / {{ $license->seats }}
                                    </span>
                                </div>
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-{{ $percentage > 90 ? 'danger' : ($percentage > 70 ? 'warning' : 'success') }}" 
                                         style="width: {{ $percentage }}%">
                                        {{ number_format($percentage, 1) }}%
                                    </div>
                                </div>
                            </div>

                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-dollar-sign"></i> Informações Comerciais
                            </h5>

                            <table class="table table-borderless">
                                <tr>
                                    <td width="35%"><strong>Custo:</strong></td>
                                    <td>
                                        @if($license->cost)
                                            <span class="text-success font-weight-bold">
                                                R$ {{ number_format($license->cost, 2, ',', '.') }}
                                            </span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Data de Compra:</strong></td>
                                    <td>{{ $license->purchase_date ? $license->purchase_date->format('d/m/Y') : 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Expira em:</strong></td>
                                    <td>
                                        @if($license->expires_at)
                                            {{ $license->expires_at->format('d/m/Y') }}
                                            @if($license->expires_at->isPast())
                                                <span class="badge badge-danger ml-1">Expirada</span>
                                            @elseif($license->expires_at->diffInDays() <= 30)
                                                <span class="badge badge-warning ml-1">Expira em breve</span>
                                            @endif
                                        @else
                                            <span class="text-success">Perpétua</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Fornecedor:</strong></td>
                                    <td>{{ $license->vendor ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Suporte até:</strong></td>
                                    <td>
                                        @if($license->support_expires_at)
                                            {{ $license->support_expires_at->format('d/m/Y') }}
                                            @if($license->support_expires_at->isPast())
                                                <span class="badge badge-danger ml-1">Expirado</span>
                                            @elseif($license->support_expires_at->diffInDays() <= 30)
                                                <span class="badge badge-warning ml-1">Expira em breve</span>
                                            @endif
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Observações -->
                    @if($license->notes)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-sticky-note"></i> Observações
                            </h5>
                            <div class="card bg-light">
                                <div class="card-body">
                                    {{ $license->notes }}
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Instalações -->
                    @if($license->installations && $license->installations->count() > 0)
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5 class="text-primary border-bottom pb-2 mb-3">
                                <i class="fas fa-desktop"></i> Instalações Ativas
                            </h5>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Ativo</th>
                                            <th>Usuário</th>
                                            <th>Data de Instalação</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($license->installations as $installation)
                                        <tr>
                                            <td>
                                                @if($installation->asset)
                                                    <a href="{{ route('assets.show', $installation->asset) }}">
                                                        {{ $installation->asset->name }}
                                                    </a>
                                                    <br><small class="text-muted">{{ $installation->asset->asset_tag }}</small>
                                                @else
                                                    <span class="text-muted">Ativo não encontrado</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($installation->user)
                                                    <div class="d-flex align-items-center">
                                                        <img src="https://www.gravatar.com/avatar/{{ md5($installation->user->email) }}?s=24&d=identicon" 
                                                             class="rounded-circle mr-2" width="24" height="24">
                                                        {{ $installation->user->name }}
                                                    </div>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>{{ $installation->installed_at ? $installation->installed_at->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="badge badge-{{ $installation->status == 'active' ? 'success' : 'secondary' }}">
                                                    {{ $installation->status == 'active' ? 'Ativa' : 'Inativa' }}
                                                </span>
                                            </td>
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
                    <a href="{{ route('licenses.edit', $license) }}" class="btn btn-warning">
                        <i class="fas fa-edit"></i> Editar Licença
                    </a>
                    <button type="button" class="btn btn-danger" data-toggle="modal" data-target="#deleteModal">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                    <a href="{{ route('licenses.index') }}" class="btn btn-secondary float-right">
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
                Tem certeza que deseja excluir a licença do software 
                <strong>{{ $license->software->name ?? 'N/A' }}</strong>?
                <br><br>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Esta ação não pode ser desfeita e afetará {{ $license->installations->count() }} instalação(ões) ativa(s).
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form action="{{ route('licenses.destroy', $license) }}" method="POST" style="display: inline;">
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

@section('scripts')
<script>
let licenseKeyVisible = false;
const originalKey = '{{ $license->license_key }}';
const maskedKey = '{{ $license->license_key ? Str::mask($license->license_key, "*", 4, -4) : "" }}';

function toggleLicenseKey() {
    const keyElement = document.getElementById('licenseKey');
    const iconElement = document.getElementById('toggleIcon');
    
    if (licenseKeyVisible) {
        keyElement.textContent = maskedKey;
        iconElement.className = 'fas fa-eye';
        licenseKeyVisible = false;
    } else {
        keyElement.textContent = originalKey;
        iconElement.className = 'fas fa-eye-slash';
        licenseKeyVisible = true;
    }
}

function copyLicenseKey() {
    navigator.clipboard.writeText(originalKey).then(function() {
        // Mostrar feedback
        const button = event.target.closest('button');
        const originalIcon = button.innerHTML;
        button.innerHTML = '<i class="fas fa-check text-success"></i>';
        setTimeout(() => {
            button.innerHTML = originalIcon;
        }, 2000);
    });
}
</script>
@endsection
