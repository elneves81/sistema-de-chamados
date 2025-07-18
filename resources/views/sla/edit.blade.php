@extends('layouts.app')

@section('title', 'Editar SLA')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-edit me-2"></i>
                        Editar SLA: {{ $sla->name }}
                    </h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('sla.update', $sla) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nome do SLA <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('name') is-invalid @enderror" 
                                       id="name" 
                                       name="name" 
                                       value="{{ old('name', $sla->name) }}" 
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="priority" class="form-label">Prioridade <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" 
                                        name="priority" 
                                        required>
                                    <option value="">Selecione uma prioridade</option>
                                    <option value="baixa" {{ old('priority', $sla->priority) == 'baixa' ? 'selected' : '' }}>Baixa</option>
                                    <option value="media" {{ old('priority', $sla->priority) == 'media' ? 'selected' : '' }}>Média</option>
                                    <option value="alta" {{ old('priority', $sla->priority) == 'alta' ? 'selected' : '' }}>Alta</option>
                                    <option value="critica" {{ old('priority', $sla->priority) == 'critica' ? 'selected' : '' }}>Crítica</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Descrição</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" 
                                      name="description" 
                                      rows="3">{{ old('description', $sla->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="response_time" class="form-label">Tempo de Resposta (horas) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('response_time') is-invalid @enderror" 
                                       id="response_time" 
                                       name="response_time" 
                                       value="{{ old('response_time', $sla->response_time) }}" 
                                       min="1"
                                       step="0.5"
                                       required>
                                @error('response_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Tempo máximo para primeira resposta ao chamado</small>
                            </div>
                            <div class="col-md-6">
                                <label for="resolution_time" class="form-label">Tempo de Resolução (horas) <span class="text-danger">*</span></label>
                                <input type="number" 
                                       class="form-control @error('resolution_time') is-invalid @enderror" 
                                       id="resolution_time" 
                                       name="resolution_time" 
                                       value="{{ old('resolution_time', $sla->resolution_time) }}" 
                                       min="1"
                                       step="0.5"
                                       required>
                                @error('resolution_time')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Tempo máximo para resolução completa do chamado</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="is_active" 
                                       name="is_active" 
                                       value="1" 
                                       {{ old('is_active', $sla->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_active">
                                    SLA ativo
                                </label>
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('sla.index') }}" class="btn btn-secondary me-md-2">
                                <i class="fas fa-arrow-left me-1"></i>
                                Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>
                                Atualizar SLA
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Validação do formulário
    $('#resolution_time').on('input', function() {
        const responseTime = parseFloat($('#response_time').val()) || 0;
        const resolutionTime = parseFloat($(this).val()) || 0;
        
        if (resolutionTime <= responseTime && resolutionTime > 0) {
            $(this).addClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
            $(this).after('<div class="invalid-feedback">O tempo de resolução deve ser maior que o tempo de resposta</div>');
        } else {
            $(this).removeClass('is-invalid');
            $(this).siblings('.invalid-feedback').remove();
        }
    });
    
    $('#response_time').on('input', function() {
        $('#resolution_time').trigger('input');
    });
});
</script>
@endsection
