@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">
        <i class="bi bi-plus-circle"></i> Nova Categoria
    </h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('categories.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações da Categoria</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('categories.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label for="name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('name') is-invalid @enderror" 
                               id="name" 
                               name="name" 
                               value="{{ old('name') }}" 
                               placeholder="Nome da categoria"
                               required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">O slug será gerado automaticamente baseado no nome</div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  id="description" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Descreva o tipo de chamados que pertencem a esta categoria">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="color" class="form-label">Cor da Categoria</label>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color @error('color') is-invalid @enderror" 
                                           id="color" 
                                           name="color" 
                                           value="{{ old('color', '#6c757d') }}" 
                                           title="Escolha uma cor">
                                    <input type="text" 
                                           class="form-control" 
                                           id="colorHex" 
                                           value="{{ old('color', '#6c757d') }}" 
                                           readonly>
                                </div>
                                @error('color')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">A cor será usada para identificar a categoria nos chamados</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sla_hours" class="form-label">SLA (horas)</label>
                                <input type="number" 
                                       class="form-control @error('sla_hours') is-invalid @enderror" 
                                       id="sla_hours" 
                                       name="sla_hours" 
                                       value="{{ old('sla_hours', 24) }}" 
                                       min="1" 
                                       max="720">
                                @error('sla_hours')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">Prazo padrão para resolução em horas</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" 
                                   type="checkbox" 
                                   value="1" 
                                   id="active" 
                                   name="active" 
                                   {{ old('active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="active">
                                Categoria ativa
                            </label>
                        </div>
                        <div class="form-text">Categorias inativas não aparecem na criação de novos chamados</div>
                    </div>

                    <!-- Preview da Categoria -->
                    <div class="mb-4">
                        <label class="form-label">Preview:</label>
                        <div class="border rounded p-3 bg-light">
                            <span id="categoryPreview" class="badge" style="background-color: #6c757d; color: white;">
                                Nome da Categoria
                            </span>
                            <div class="mt-2">
                                <small class="text-muted" id="descriptionPreview">Descrição da categoria aparecerá aqui</small>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="bi bi-x-circle"></i> Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle"></i> Criar Categoria
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
                    <i class="bi bi-lightbulb"></i> Dicas para uma boa categoria
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Use nomes claros</strong> e específicos
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Descreva</strong> que tipos de problemas se encaixam
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Escolha cores</strong> que facilitem identificação
                    </li>
                    <li class="mb-2">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Defina SLA</strong> realista para a categoria
                    </li>
                    <li class="mb-0">
                        <i class="bi bi-check-circle text-success me-2"></i>
                        <strong>Evite</strong> categorias muito genéricas
                    </li>
                </ul>
            </div>
        </div>

        <!-- Cores Sugeridas -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-palette"></i> Cores Sugeridas
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-2">
                        <button type="button" class="btn btn-sm w-100 color-suggestion" 
                                style="background-color: #dc3545; color: white;" 
                                data-color="#dc3545">
                            Urgente
                        </button>
                    </div>
                    <div class="col-6 mb-2">
                        <button type="button" class="btn btn-sm w-100 color-suggestion" 
                                style="background-color: #fd7e14; color: white;" 
                                data-color="#fd7e14">
                            Hardware
                        </button>
                    </div>
                    <div class="col-6 mb-2">
                        <button type="button" class="btn btn-sm w-100 color-suggestion" 
                                style="background-color: #ffc107; color: black;" 
                                data-color="#ffc107">
                            Software
                        </button>
                    </div>
                    <div class="col-6 mb-2">
                        <button type="button" class="btn btn-sm w-100 color-suggestion" 
                                style="background-color: #198754; color: white;" 
                                data-color="#198754">
                            Rede
                        </button>
                    </div>
                    <div class="col-6 mb-2">
                        <button type="button" class="btn btn-sm w-100 color-suggestion" 
                                style="background-color: #0dcaf0; color: black;" 
                                data-color="#0dcaf0">
                            E-mail
                        </button>
                    </div>
                    <div class="col-6 mb-2">
                        <button type="button" class="btn btn-sm w-100 color-suggestion" 
                                style="background-color: #6f42c1; color: white;" 
                                data-color="#6f42c1">
                            Sistema
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- SLA Sugerido -->
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clock"></i> SLA Sugerido
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <strong>Crítico:</strong> 4-8 horas
                    <button type="button" class="btn btn-sm btn-outline-primary float-end" onclick="setSLA(4)">4h</button>
                </div>
                <div class="mb-2">
                    <strong>Alto:</strong> 8-24 horas
                    <button type="button" class="btn btn-sm btn-outline-primary float-end" onclick="setSLA(24)">24h</button>
                </div>
                <div class="mb-2">
                    <strong>Médio:</strong> 48-72 horas
                    <button type="button" class="btn btn-sm btn-outline-primary float-end" onclick="setSLA(48)">48h</button>
                </div>
                <div class="mb-0">
                    <strong>Baixo:</strong> 5-7 dias
                    <button type="button" class="btn btn-sm btn-outline-primary float-end" onclick="setSLA(120)">120h</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const colorInput = document.getElementById('color');
    const colorHexInput = document.getElementById('colorHex');
    const categoryPreview = document.getElementById('categoryPreview');
    const descriptionPreview = document.getElementById('descriptionPreview');

    // Atualizar preview em tempo real
    function updatePreview() {
        const name = nameInput.value || 'Nome da Categoria';
        const description = descriptionInput.value || 'Descrição da categoria aparecerá aqui';
        const color = colorInput.value;

        categoryPreview.textContent = name;
        categoryPreview.style.backgroundColor = color;
        categoryPreview.style.color = getContrastColor(color);
        descriptionPreview.textContent = description;
    }

    // Sincronizar color picker com input text
    colorInput.addEventListener('input', function() {
        colorHexInput.value = this.value;
        updatePreview();
    });

    nameInput.addEventListener('input', updatePreview);
    descriptionInput.addEventListener('input', updatePreview);

    // Cores sugeridas
    document.querySelectorAll('.color-suggestion').forEach(button => {
        button.addEventListener('click', function() {
            const color = this.dataset.color;
            colorInput.value = color;
            colorHexInput.value = color;
            updatePreview();
        });
    });

    // Função para determinar cor do texto baseada no background
    function getContrastColor(hexcolor) {
        // Remove o # se presente
        hexcolor = hexcolor.replace('#', '');
        
        // Converte para RGB
        const r = parseInt(hexcolor.substr(0, 2), 16);
        const g = parseInt(hexcolor.substr(2, 2), 16);
        const b = parseInt(hexcolor.substr(4, 2), 16);
        
        // Calcula a luminância
        const luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;
        
        return luminance > 0.5 ? '#000000' : '#ffffff';
    }

    // Inicializar preview
    updatePreview();
});

function setSLA(hours) {
    document.getElementById('sla_hours').value = hours;
}
</script>
@endpush
