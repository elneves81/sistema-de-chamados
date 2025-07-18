@extends('layouts.app')

@section('title', 'Gerenciar SLAs')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Gerenciar SLAs (Service Level Agreements)
                    </h4>
                    <a href="{{ route('sla.create') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-plus me-1"></i> Novo SLA
                    </a>
                </div>
                <div class="card-body">
                    @if($slas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th>Prioridade</th>
                                        <th>Tempo de Resposta</th>
                                        <th>Tempo de Resolução</th>
                                        <th>Status</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($slas as $sla)
                                        <tr>
                                            <td>
                                                <strong>{{ $sla->name }}</strong>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ Str::limit($sla->description, 50) }}</small>
                                            </td>
                                            <td>
                                                @switch($sla->priority)
                                                    @case('baixa')
                                                        <span class="badge bg-success">Baixa</span>
                                                        @break
                                                    @case('media')
                                                        <span class="badge bg-warning">Média</span>
                                                        @break
                                                    @case('alta')
                                                        <span class="badge bg-danger">Alta</span>
                                                        @break
                                                    @case('critica')
                                                        <span class="badge bg-dark">Crítica</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ ucfirst($sla->priority) }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                <i class="fas fa-reply text-info me-1"></i>
                                                {{ $sla->response_time }} horas
                                            </td>
                                            <td>
                                                <i class="fas fa-check-circle text-success me-1"></i>
                                                {{ $sla->resolution_time }} horas
                                            </td>
                                            <td>
                                                @if($sla->is_active)
                                                    <span class="badge bg-success">Ativo</span>
                                                @else
                                                    <span class="badge bg-secondary">Inativo</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('sla.show', $sla) }}" 
                                                       class="btn btn-sm btn-outline-info" 
                                                       title="Visualizar">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('sla.edit', $sla) }}" 
                                                       class="btn btn-sm btn-outline-warning" 
                                                       title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('sla.destroy', $sla) }}" 
                                                          method="POST" 
                                                          style="display: inline-block;"
                                                          onsubmit="return confirm('Tem certeza que deseja excluir este SLA?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                class="btn btn-sm btn-outline-danger" 
                                                                title="Excluir">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Nenhum SLA cadastrado</h5>
                            <p class="text-muted mb-3">
                                Comece criando seu primeiro Service Level Agreement para definir
                                os tempos de resposta e resolução dos chamados.
                            </p>
                            <a href="{{ route('sla.create') }}" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>
                                Criar Primeiro SLA
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Cards informativos -->
    @if($slas->count() > 0)
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card border-success">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                        <h5 class="card-title">{{ $slas->where('is_active', true)->count() }}</h5>
                        <p class="card-text text-muted">SLAs Ativos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-danger">
                    <div class="card-body text-center">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger mb-2"></i>
                        <h5 class="card-title">{{ $slas->where('priority', 'critica')->count() }}</h5>
                        <p class="card-text text-muted">SLAs Críticos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-warning">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                        <h5 class="card-title">{{ number_format($slas->avg('response_time'), 1) }}h</h5>
                        <p class="card-text text-muted">Tempo Médio de Resposta</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card border-info">
                    <div class="card-body text-center">
                        <i class="fas fa-tools fa-2x text-info mb-2"></i>
                        <h5 class="card-title">{{ number_format($slas->avg('resolution_time'), 1) }}h</h5>
                        <p class="card-text text-muted">Tempo Médio de Resolução</p>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Inicializar DataTable se houver dados
    @if($slas->count() > 0)
        $('.table').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/pt-BR.json'
            },
            responsive: true,
            order: [[0, 'asc']],
            pageLength: 25,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'excel',
                    text: '<i class="fas fa-file-excel"></i> Excel',
                    className: 'btn btn-success btn-sm'
                },
                {
                    extend: 'pdf',
                    text: '<i class="fas fa-file-pdf"></i> PDF',
                    className: 'btn btn-danger btn-sm'
                },
                {
                    extend: 'print',
                    text: '<i class="fas fa-print"></i> Imprimir',
                    className: 'btn btn-info btn-sm'
                }
            ]
        });
    @endif
});
</script>
@endsection
