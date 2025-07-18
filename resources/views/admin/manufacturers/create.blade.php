@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Novo Fabricante</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Informações do Fabricante</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('manufacturers.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label for="name" class="form-label">Nome do Fabricante *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror" 
                               name="name" id="name" value="{{ old('name') }}" required
                               placeholder="Ex: Dell Technologies, HP Inc., Lenovo...">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="website" class="form-label">Website</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-globe"></i></span>
                            <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                   name="website" id="website" value="{{ old('website') }}" 
                                   placeholder="https://www.fabricante.com">
                        </div>
                        @error('website')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="support_phone" class="form-label">Telefone de Suporte</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                                    <input type="text" class="form-control @error('support_phone') is-invalid @enderror" 
                                           name="support_phone" id="support_phone" value="{{ old('support_phone') }}" 
                                           placeholder="Ex: 0800-123-4567">
                                </div>
                                @error('support_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="support_email" class="form-label">E-mail de Suporte</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email" class="form-control @error('support_email') is-invalid @enderror" 
                                           name="support_email" id="support_email" value="{{ old('support_email') }}" 
                                           placeholder="suporte@fabricante.com">
                                </div>
                                @error('support_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="comment" class="form-label">Comentários</label>
                        <textarea class="form-control @error('comment') is-invalid @enderror" 
                                  name="comment" id="comment" rows="3" 
                                  placeholder="Observações sobre este fabricante, produtos principais, etc.">{{ old('comment') }}</textarea>
                        @error('comment')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active" 
                                   value="1" {{ old('is_active', '1') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                Fabricante ativo
                            </label>
                        </div>
                        <small class="form-text text-muted">Fabricantes inativos não aparecem em seleções de formulários.</small>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('manufacturers.index') }}" class="btn btn-secondary me-md-2">Cancelar</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i> Salvar Fabricante
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
                <div id="manufacturerPreview" class="mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" 
                         style="width: 60px; height: 60px; font-weight: bold; font-size: 1.5rem;" id="previewInitials">
                        ??
                    </div>
                    <h5 id="previewName">Nome do Fabricante</h5>
                    <p id="previewWebsite" class="text-muted small">Website</p>
                    <p id="previewComment" class="text-muted">Comentários sobre o fabricante</p>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h6 class="card-title mb-0">Fabricantes Populares</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="fillManufacturer('Dell Technologies', 'https://www.dell.com', '0800-123-4567', 'suporte@dell.com')">
                            <strong>Dell</strong><br>
                            <small>Computadores</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="fillManufacturer('HP Inc.', 'https://www.hp.com', '0800-765-4321', 'support@hp.com')">
                            <strong>HP</strong><br>
                            <small>Impressoras</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="fillManufacturer('Lenovo', 'https://www.lenovo.com', '0800-111-2222', 'suporte@lenovo.com')">
                            <strong>Lenovo</strong><br>
                            <small>ThinkPad</small>
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-outline-secondary btn-sm w-100" onclick="fillManufacturer('Apple', 'https://www.apple.com', '0800-555-6666', 'support@apple.com')">
                            <strong>Apple</strong><br>
                            <small>Mac/iOS</small>
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
                    <li><strong>Nome:</strong> Use o nome oficial da empresa</li>
                    <li><strong>Website:</strong> URL completa do site oficial</li>
                    <li><strong>Suporte:</strong> Canais de atendimento técnico</li>
                    <li><strong>Comentários:</strong> Informações relevantes sobre produtos</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Função para preencher dados de fabricante
function fillManufacturer(name, website, phone, email) {
    document.getElementById('name').value = name;
    document.getElementById('website').value = website;
    document.getElementById('support_phone').value = phone;
    document.getElementById('support_email').value = email;
    updatePreview();
}

// Função para atualizar preview
function updatePreview() {
    const name = document.getElementById('name').value || 'Nome do Fabricante';
    const website = document.getElementById('website').value || 'Website';
    const comment = document.getElementById('comment').value || 'Comentários sobre o fabricante';
    
    // Iniciais
    const initials = name.split(' ').map(word => word.charAt(0)).join('').substring(0, 2).toUpperCase() || '??';
    
    document.getElementById('previewName').textContent = name;
    document.getElementById('previewWebsite').textContent = website;
    document.getElementById('previewComment').textContent = comment;
    document.getElementById('previewInitials').textContent = initials;
}

// Event listeners
document.getElementById('name').addEventListener('input', updatePreview);
document.getElementById('website').addEventListener('input', updatePreview);
document.getElementById('comment').addEventListener('input', updatePreview);

// Inicializar preview
document.addEventListener('DOMContentLoaded', updatePreview);
</script>
@endpush
@endsection
