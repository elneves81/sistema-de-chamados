@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Novo Tipo de Ativo</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('asset-types.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações do Tipo de Ativo</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('asset-types.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome do Tipo *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" id="name" value="{{ old('name') }}" required
                               placeholder="Ex: Notebook, Desktop, Impressora...">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" id="description" rows="3" 
                                  placeholder="Descreva este tipo de ativo e suas características">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="icon" class="form-label">Ícone</label>
                        <div class="input-group">
                            <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                   name="icon" id="icon" value="{{ old('icon') }}" 
                                   placeholder="Ex: fas fa-laptop, bi bi-printer...">
                            <button class="btn btn-outline-secondary" type="button" id="previewIcon">
                                <i id="iconPreview" class="bi bi-eye"></i> Visualizar
                            </button>
                        </div>
                        <small class="form-text text-muted">
                            Use classes do FontAwesome ou Bootstrap Icons. 
                            <a href="https://icons.getbootstrap.com/" target="_blank">Ver ícones disponíveis</a>
                        </small>
                        @error('icon')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Tipo ativo
                            </label>
                        </div>
                        <small class="form-text text-muted">Tipos inativos não aparecem em seleções de formulários.</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('asset-types.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Salvar Tipo de Ativo
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Pré-visualização</h6>
            </div>
            <div class="card-body text-center">
                <div id="typePreview" class="mb-3">
                    <i id="previewIconDisplay" class="bi bi-grid-3x3-gap text-primary" style="font-size: 3rem;"></i>
                    <h5 id="previewName" class="mt-2">Nome do Tipo</h5>
                    <p id="previewDescription" class="text-muted">Descrição do tipo de ativo</p>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Ícones Sugeridos</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-4 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="selectIcon('fas fa-laptop')">
                            <i class="fas fa-laptop"></i><br>
                            <small>Laptop</small>
                        </button>
                    </div>
                    <div class="col-4 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="selectIcon('fas fa-desktop')">
                            <i class="fas fa-desktop"></i><br>
                            <small>Desktop</small>
                        </button>
                    </div>
                    <div class="col-4 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="selectIcon('fas fa-print')">
                            <i class="fas fa-print"></i><br>
                            <small>Impressora</small>
                        </button>
                    </div>
                    <div class="col-4 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="selectIcon('fas fa-mobile-alt')">
                            <i class="fas fa-mobile-alt"></i><br>
                            <small>Mobile</small>
                        </button>
                    </div>
                    <div class="col-4 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="selectIcon('fas fa-server')">
                            <i class="fas fa-server"></i><br>
                            <small>Servidor</small>
                        </button>
                    </div>
                    <div class="col-4 text-center">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="selectIcon('fas fa-tv')">
                            <i class="fas fa-tv"></i><br>
                            <small>Monitor</small>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Ajuda</h6>
            </div>
            <div class="card-body">
                <h6><i class="bi bi-info-circle text-primary"></i> Dicas</h6>
                <ul class="small mb-0">
                    <li><strong>Nome:</strong> Use nomes claros e específicos</li>
                    <li><strong>Descrição:</strong> Detalhe as características do tipo</li>
                    <li><strong>Ícone:</strong> Escolha ícones que representem bem o tipo</li>
                    <li><strong>Status:</strong> Controle a visibilidade do tipo</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Função para selecionar ícone
function selectIcon(iconClass) {
    document.getElementById('icon').value = iconClass;
    updatePreview();
}

// Função para atualizar preview
function updatePreview() {
    const name = document.getElementById('name').value || 'Nome do Tipo';
    const description = document.getElementById('description').value || 'Descrição do tipo de ativo';
    const icon = document.getElementById('icon').value || 'bi bi-grid-3x3-gap';
    
    document.getElementById('previewName').textContent = name;
    document.getElementById('previewDescription').textContent = description;
    document.getElementById('previewIconDisplay').className = icon + ' text-primary';
    document.getElementById('iconPreview').className = icon;
}

// Event listeners
document.getElementById('name').addEventListener('input', updatePreview);
document.getElementById('description').addEventListener('input', updatePreview);
document.getElementById('icon').addEventListener('input', updatePreview);
document.getElementById('previewIcon').addEventListener('click', updatePreview);

// Inicializar preview
document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush
@endsection
