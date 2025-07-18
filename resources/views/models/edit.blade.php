@extends('layouts.app')

@section('title', 'Editar Modelo')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-edit"></i>
                        Editar Modelo: {{ $model->name }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('models.show', $model) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Visualizar
                        </a>
                        <a href="{{ route('models.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <form action="{{ route('models.update', $model) }}" method="POST">
                    @csrf
                    @method('PUT')
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
                                           value="{{ old('name', $model->name) }}" 
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
                                                    {{ old('manufacturer_id', $model->manufacturer_id) == $manufacturer->id ? 'selected' : '' }}>
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
                                              rows="6"
                                              placeholder="Descreva as especificações técnicas do modelo...">{{ old('specifications', $model->specifications) }}</textarea>
                                    @error('specifications')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Ex: Processador Intel Core i7, 16GB RAM, SSD 512GB, Tela 15.6"
                                    </small>
                                </div>
                            </div>
                        </div>

                        @if($model->assets->count() > 0)
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <h6><i class="fas fa-exclamation-triangle"></i> Atenção:</h6>
                                    <p class="mb-0">
                                        Este modelo possui <strong>{{ $model->assets->count() }} ativo(s)</strong> associado(s). 
                                        As alterações serão refletidas em todos os ativos que utilizam este modelo.
                                    </p>
                                </div>
                            </div>
                        </div>
                        @endif

                        <div class="row">
                            <div class="col-12">
                                <div class="card bg-light">
                                    <div class="card-header">
                                        <h6 class="mb-0"><i class="fas fa-history"></i> Histórico</h6>
                                    </div>
                                    <div class="card-body">
                                        <small class="text-muted">
                                            <strong>Criado em:</strong> {{ $model->created_at->format('d/m/Y H:i') }}<br>
                                            <strong>Última atualização:</strong> {{ $model->updated_at->format('d/m/Y H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Alterações
                        </button>
                        <a href="{{ route('models.show', $model) }}" class="btn btn-info">
                            <i class="fas fa-eye"></i> Visualizar
                        </a>
                        <a href="{{ route('models.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                        <button type="button" class="btn btn-danger float-right" data-toggle="modal" data-target="#deleteModal">
                            <i class="fas fa-trash"></i> Excluir Modelo
                        </button>
                    </div>
                </form>
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
                Tem certeza que deseja excluir o modelo 
                <strong>{{ $model->name }}</strong>?
                @if($model->assets->count() > 0)
                    <br><br>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Este modelo possui {{ $model->assets->count() }} ativo(s) associado(s).
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <form action="{{ route('models.destroy', $model) }}" method="POST" style="display: inline;">
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
$(document).ready(function() {
    // Auto-focus no primeiro campo
    $('#name').focus();
    
    // Contador de caracteres para especificações
    $('#specifications').on('input', function() {
        const text = $(this).val();
        const length = text.length;
        let help = $(this).siblings('.form-text');
        
        if (length > 500) {
            help.addClass('text-warning').text('Especificações muito longas (' + length + ' caracteres)');
        } else {
            help.removeClass('text-warning').text('Ex: Processador Intel Core i7, 16GB RAM, SSD 512GB, Tela 15.6"');
        }
    });
});
</script>
@endsection
