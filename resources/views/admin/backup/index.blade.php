@extends('layouts.app')

@section('title', 'Gerenciamento de Backups')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-0">
                        <i class="bi bi-shield-check"></i> Gerenciamento de Backups
                    </h2>
                    <p class="text-muted mb-0">Proteja seus dados com backups automáticos e manuais</p>
                </div>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBackupModal">
                        <i class="bi bi-plus-circle"></i> Criar Backup
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Alertas -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> {{ session('warning') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Total de Backups</h6>
                            <h3 class="mb-0">{{ $stats['total_backups'] }}</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-archive"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Tamanho Total</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_size'] / 1024 / 1024, 2) }} MB</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-hdd"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Último Backup</h6>
                            <h6 class="mb-0">{{ $stats['last_backup'] }}</h6>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-subtitle mb-2 text-white-50">Espaço Livre</h6>
                            <h3 class="mb-0">{{ number_format($stats['disk_usage'] / 1024 / 1024 / 1024, 1) }} GB</h3>
                        </div>
                        <div class="fs-1 opacity-50">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Informações Importantes -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info">
                <h5 class="alert-heading"><i class="bi bi-info-circle"></i> Informações Importantes</h5>
                <hr>
                <ul class="mb-0">
                    <li><strong>Backup Automático:</strong> O sistema faz backup completo diariamente às 3:00 AM</li>
                    <li><strong>Backup do Banco:</strong> Executado automaticamente a cada 6 horas</li>
                    <li><strong>Retenção:</strong> Backups são mantidos por 7 dias automaticamente</li>
                    <li><strong>Localização:</strong> <code>storage/backups/</code></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Lista de Backups -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul"></i> Backups Disponíveis</h5>
                </div>
                <div class="card-body">
                    @if(count($backups) > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Tipo</th>
                                        <th>Nome do Arquivo</th>
                                        <th>Tamanho</th>
                                        <th>Data de Criação</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($backups as $backup)
                                        <tr>
                                            <td>
                                                <span class="badge bg-{{ $backup['color'] }}">
                                                    <i class="bi {{ $backup['icon'] }}"></i> {{ $backup['type_label'] }}
                                                </span>
                                            </td>
                                            <td>
                                                <code>{{ $backup['filename'] }}</code>
                                            </td>
                                            <td>{{ $backup['size_formatted'] }}</td>
                                            <td>
                                                <i class="bi bi-calendar3"></i> {{ $backup['date_formatted'] }}
                                            </td>
                                            <td class="text-end">
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('admin.backup.download', $backup['filename']) }}" 
                                                       class="btn btn-primary" 
                                                       title="Download">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                    <button type="button" 
                                                            class="btn btn-danger" 
                                                            onclick="deleteBackup('{{ $backup['filename'] }}')"
                                                            title="Excluir">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox display-1 text-muted"></i>
                            <p class="text-muted mt-3">Nenhum backup encontrado. Crie seu primeiro backup agora!</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createBackupModal">
                                <i class="bi bi-plus-circle"></i> Criar Primeiro Backup
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Documentação -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="bi bi-book"></i> Guia Rápido</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6><i class="bi bi-1-circle-fill text-primary"></i> Backup Completo</h6>
                            <p class="small text-muted">Inclui banco de dados e todos os arquivos importantes do sistema. Recomendado para restaurações completas.</p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="bi bi-2-circle-fill text-success"></i> Backup do Banco</h6>
                            <p class="small text-muted">Apenas o banco de dados. Mais rápido e ideal para backups frequentes dos dados.</p>
                        </div>
                        <div class="col-md-4">
                            <h6><i class="bi bi-3-circle-fill text-info"></i> Restauração</h6>
                            <p class="small text-muted">Para restaurar, use o terminal: <code>php artisan backup:restore</code></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Criação de Backup -->
<div class="modal fade" id="createBackupModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.backup.create') }}" method="POST" id="createBackupForm">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title"><i class="bi bi-plus-circle"></i> Criar Novo Backup</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tipo de Backup</label>
                        <div class="list-group">
                            <label class="list-group-item">
                                <input class="form-check-input me-2" type="radio" name="type" value="full" checked>
                                <div class="d-flex w-100 justify-content-between">
                                    <div>
                                        <strong><i class="bi bi-archive-fill text-primary"></i> Backup Completo</strong>
                                        <p class="mb-0 small text-muted">Banco de dados + arquivos (~1-2 MB)</p>
                                    </div>
                                </div>
                            </label>
                            <label class="list-group-item">
                                <input class="form-check-input me-2" type="radio" name="type" value="database">
                                <div class="d-flex w-100 justify-content-between">
                                    <div>
                                        <strong><i class="bi bi-database-fill text-success"></i> Apenas Banco de Dados</strong>
                                        <p class="mb-0 small text-muted">Mais rápido (~500 KB)</p>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Atenção:</strong> O processo pode levar alguns segundos. Aguarde a conclusão.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="btnCreateBackup">
                        <i class="bi bi-play-circle"></i> Criar Backup
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Form de Delete (hidden) -->
<form id="deleteBackupForm" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@push('scripts')
<script>
// Corrigir backdrop dos modais
document.addEventListener('DOMContentLoaded', function() {
    const createBackupModal = document.getElementById('createBackupModal');
    
    if (createBackupModal) {
        createBackupModal.addEventListener('show.bs.modal', function () {
            // Garantir que backdrop fique atrás do modal
            setTimeout(() => {
                const backdrop = document.querySelector('.modal-backdrop');
                if (backdrop) {
                    backdrop.style.zIndex = '1400';
                }
                createBackupModal.style.zIndex = '1500';
            }, 10);
        });
    }
});

// Criar backup com loading
document.getElementById('createBackupForm')?.addEventListener('submit', function() {
    const btn = document.getElementById('btnCreateBackup');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Criando...';
    }
});

// Deletar backup
function deleteBackup(filename) {
    if (confirm('Tem certeza que deseja excluir este backup?\n\nArquivo: ' + filename)) {
        const form = document.getElementById('deleteBackupForm');
        form.action = '{{ route("admin.backup.destroy", "") }}/' + filename;
        form.submit();
    }
}

// Auto-refresh das estatísticas (opcional)
setInterval(function() {
    // Você pode implementar AJAX para atualizar estatísticas em tempo real
}, 30000); // 30 segundos
</script>
@endpush

@push('styles')
<style>
/* Garantir que modais funcionem corretamente - PRIORIDADE MÁXIMA */
.modal {
    z-index: 1500 !important;
}

.modal-backdrop {
    z-index: 1400 !important;
}

.modal-open .modal {
    z-index: 1500 !important;
}

/* Garantir que o conteúdo do modal seja clicável */
.modal-dialog {
    z-index: 1501 !important;
    position: relative;
}

.modal-content {
    z-index: 1502 !important;
    position: relative;
}

.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: none;
    border-radius: 0.5rem;
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.list-group-item {
    cursor: pointer;
    transition: background-color 0.2s;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.list-group-item input[type="radio"]:checked ~ div {
    color: #0d6efd;
}

code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}
</style>
@endpush
