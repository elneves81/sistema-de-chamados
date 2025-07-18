@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-plus-circle"></i> Novo Chamado
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações do Chamado</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('tickets.store') }}">
                    @csrf

                    <div class="mb-3">
                    <div class="mb-3">
                        <label for="local" class="form-label">Local</label>
                        <input type="text" class="form-control @error('local') is-invalid @enderror" id="local" name="local" value="{{ old('local') }}" placeholder="Informe o local do chamado">
                        @error('local')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                        <label for="title" class="form-label">Título <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               placeholder="Descreva brevemente o problema"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                                <select class="form-select @error('category_id') is-invalid @enderror" 
                                        id="category_id" 
                                        name="category_id" 
                                        required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id') == $category->id ? 'selected' : '' }}
                                            data-color="{{ $category->color }}">
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="priority" class="form-label">Prioridade <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" 
                                        id="priority" 
                                        name="priority" 
                                        required>
                                    <option value="">Selecione a prioridade</option>
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>
                                        🟢 Baixa - Não é urgente
                                    </option>
                                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>
                                        🟡 Média - Pode aguardar alguns dias
                                    </option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                                        🟠 Alta - Precisa ser resolvido em breve
                                    </option>
                                    <option value="urgent" {{ old('priority') === 'urgent' ? 'selected' : '' }}>
                                        🔴 Urgente - Problema crítico
                                    </option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição <span class="text-danger">*</span></label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="6" 
                                  placeholder="Descreva detalhadamente o problema, incluindo:&#10;- O que você estava fazendo quando o problema ocorreu&#10;- Mensagens de erro (se houver)&#10;- Passos para reproduzir o problema&#10;- Qualquer informação adicional relevante"
                                  required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('tickets.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Criar Chamado
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Dicas -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb"></i> Dicas para um bom chamado
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Seja específico</strong> no título
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Descreva</strong> o problema detalhadamente
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Inclua</strong> mensagens de erro
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Informe</strong> quando o problema começou
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Escolha</strong> a prioridade correta
                    </li>
                </ul>
            </div>
        </div>

        <!-- Info sobre Prioridades -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-info-circle"></i> Sobre as Prioridades
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <span class="priority-badge priority-low">Baixa</span>
                    <small class="text-muted d-block">Problemas menores, melhorias</small>
                </div>
                <div class="mb-2">
                    <span class="priority-badge priority-medium">Média</span>
                    <small class="text-muted d-block">Problemas que afetam o trabalho</small>
                </div>
                <div class="mb-2">
                    <span class="priority-badge priority-high">Alta</span>
                    <small class="text-muted d-block">Problemas importantes</small>
                </div>
                <div class="mb-0">
                    <span class="priority-badge priority-urgent">Urgente</span>
                    <small class="text-muted d-block">Sistema parado, problema crítico</small>
                </div>
            </div>
        </div>

        <!-- Categorias Disponíveis -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-tags"></i> Categorias Disponíveis
                </h6>
            </div>
            <div class="card-body">
                @foreach($categories as $category)
                <div class="mb-2">
                    <span class="badge" style="background-color: {{ $category->color }}; color: white;">
                        {{ $category->name }}
                    </span>
                    @if($category->description)
                    <small class="text-muted d-block">{{ $category->description }}</small>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-resize textarea
    const textarea = document.getElementById('description');
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });

    // Preview da categoria selecionada
    const categorySelect = document.getElementById('category_id');
    categorySelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.dataset.color) {
            this.style.borderLeftColor = selectedOption.dataset.color;
            this.style.borderLeftWidth = '4px';
        } else {
            this.style.borderLeft = '';
        }
    });
});
</script>
@endpush
