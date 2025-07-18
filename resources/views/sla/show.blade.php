@extends('layouts.app')

@section('title', 'Detalhes do SLA')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-eye me-2"></i>
                        Detalhes do SLA: {{ $sla->name }}
                    </h4>
                    <div>
                        <a href="{{ route('sla.edit', $sla) }}" class="btn btn-warning btn-sm me-2">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                        <a href="{{ route('sla.index') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Informações Básicas</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nome:</strong></td>
                                    <td>{{ $sla->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Prioridade:</strong></td>
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
                                </tr>
                                <tr>
                                    <td><strong>Status:</strong></td>
                                    <td>
                                        @if($sla->is_active)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check-circle me-1"></i>Ativo
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-pause-circle me-1"></i>Inativo
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Tempos de Atendimento</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Tempo de Resposta:</strong></td>
                                    <td>
                                        <i class="fas fa-reply text-info me-1"></i>
                                        {{ $sla->response_time }} horas
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Tempo de Resolução:</strong></td>
                                    <td>
                                        <i class="fas fa-check-circle text-success me-1"></i>
                                        {{ $sla->resolution_time }} horas
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Criado em:</strong></td>
                                    <td>{{ $sla->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Atualizado em:</strong></td>
                                    <td>{{ $sla->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($sla->description)
                        <div class="mt-4">
                            <h6 class="text-muted">Descrição</h6>
                            <div class="p-3 bg-light rounded">
                                {{ $sla->description }}
                            </div>
                        </div>
                    @endif

                    <!-- Estatísticas do SLA (se disponível) -->
                    <div class="mt-4">
                        <h6 class="text-muted">Estatísticas</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card border-primary text-center">
                                    <div class="card-body">
                                        <i class="fas fa-ticket-alt fa-2x text-primary mb-2"></i>
                                        <h5 class="card-title">{{ $sla->tickets->count() ?? 0 }}</h5>
                                        <p class="card-text text-muted">Chamados Vinculados</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-success text-center">
                                    <div class="card-body">
                                        <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                        <h5 class="card-title">95%</h5>
                                        <p class="card-text text-muted">Taxa de Cumprimento</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-warning text-center">
                                    <div class="card-body">
                                        <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                        <h5 class="card-title">2.3h</h5>
                                        <p class="card-text text-muted">Tempo Médio de Resposta</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card border-info text-center">
                                    <div class="card-body">
                                        <i class="fas fa-tools fa-2x text-info mb-2"></i>
                                        <h5 class="card-title">15.7h</h5>
                                        <p class="card-text text-muted">Tempo Médio de Resolução</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
