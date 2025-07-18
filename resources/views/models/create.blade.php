@extends('layouts.app')

@section('title', 'Criar Modelo de Ativo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-plus"></i>
                        Criar Novo Modelo de Ativo
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('models.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <form action="{{ route('models.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        @if($errors->any())
                            <div class="alert alert-danger">
                                <h6><i class="fas fa-exclamation-triangle"></i> Corrija os erros abaixo:</h6>
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nome do Modelo <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name') }}" 
                                           placeholder="Ex: OptiPlex 7090, EliteBook 840, ThinkPad E14"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="manufacturer_id">Fabricante <span class="text-danger">*</span></label>
                                    <select class="form-control @error('manufacturer_id') is-invalid @enderror" 
                                            id="manufacturer_id" 
                                            name="manufacturer_id" 
                                            required>
                                        <option value="">Selecione um fabricante</option>
                                        @foreach($manufacturers as $manufacturer)
                                            <option value="{{ $manufacturer->id }}" 
                                                    {{ old('manufacturer_id') == $manufacturer->id ? 'selected' : '' }}>
                                                {{ $manufacturer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('manufacturer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        <a href="{{ route('manufacturers.create') }}" target="_blank">
                                            <i class="fas fa-plus"></i> Criar novo fabricante
                                        </a>
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="specifications">Especificações</label>
                                    <textarea class="form-control @error('specifications') is-invalid @enderror" 
                                              id="specifications" 
                                              name="specifications" 
                                              rows="4"
                                              placeholder="Descreva as especificações técnicas do modelo...">{{ old('specifications') }}</textarea>
                                    @error('specifications')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Ex: Processador Intel Core i7, 16GB RAM, SSD 512GB, Tela 15.6"
                                    </small>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle"></i> Dica:</h6>
                                    <p class="mb-0">
                                        Após criar o modelo, você poderá associá-lo aos ativos. 
                                        Mantenha as especificações detalhadas para facilitar a gestão dos ativos.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Modelo
                        </button>
                        <a href="{{ route('models.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Auto-focus no primeiro campo
    $('#name').focus();
    
    // Preview das especificações
    $('#specifications').on('input', function() {
        const text = $(this).val();
        if (text.length > 0) {
            $(this).next('.invalid-feedback').hide();
        }
    });
});
</script>
@endsection
