@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2>
                <i class="bi bi-plus-circle"></i> Nova Máquina
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('machines.index') }}">Inventário</a></li>
                    <li class="breadcrumb-item active">Nova Máquina</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('machines.store') }}" method="POST">
        @csrf
        
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-info-circle"></i> Informações Básicas
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Patrimônio <span class="text-danger">*</span></label>
                            <input type="text" name="patrimonio" class="form-control @error('patrimonio') is-invalid @enderror" 
                                   value="{{ old('patrimonio') }}" required>
                            @error('patrimonio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Número de Série <span class="text-danger">*</span></label>
                            <input type="text" name="numero_serie" class="form-control @error('numero_serie') is-invalid @enderror" 
                                   value="{{ old('numero_serie') }}" required>
                            @error('numero_serie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" class="form-select @error('tipo') is-invalid @enderror" required>
                                <option value="">Selecione</option>
                                <option value="desktop" {{ old('tipo') == 'desktop' ? 'selected' : '' }}>Desktop</option>
                                <option value="notebook" {{ old('tipo') == 'notebook' ? 'selected' : '' }}>Notebook</option>
                                <option value="servidor" {{ old('tipo') == 'servidor' ? 'selected' : '' }}>Servidor</option>
                                <option value="impressora" {{ old('tipo') == 'impressora' ? 'selected' : '' }}>Impressora</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Marca/Fabricante</label>
                            <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" 
                                   value="{{ old('marca') }}" placeholder="Ex: Dell, HP, Lenovo">
                            @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Modelo <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                                   value="{{ old('modelo') }}" required placeholder="Ex: OptiPlex 7090">
                            @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control @error('descricao') is-invalid @enderror" rows="2">{{ old('descricao') }}</textarea>
                    @error('descricao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <i class="bi bi-cpu"></i> Especificações Técnicas
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Processador</label>
                            <input type="text" name="processador" class="form-control" 
                                   value="{{ old('processador') }}" placeholder="Ex: Intel i5-11400">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Memória RAM</label>
                            <input type="text" name="memoria_ram" class="form-control" 
                                   value="{{ old('memoria_ram') }}" placeholder="Ex: 8GB DDR4">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Armazenamento</label>
                            <input type="text" name="armazenamento" class="form-control" 
                                   value="{{ old('armazenamento') }}" placeholder="Ex: 256GB SSD">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sistema Operacional</label>
                    <input type="text" name="sistema_operacional" class="form-control" 
                           value="{{ old('sistema_operacional') }}" placeholder="Ex: Windows 11 Pro">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <i class="bi bi-person-check"></i> Vinculação e Status
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Usuário Vinculado</label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Sem vínculo</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Usuário responsável por esta máquina</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="ativo" {{ old('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="inativo" {{ old('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                <option value="manutencao" {{ old('status') == 'manutencao' ? 'selected' : '' }}>Manutenção</option>
                                <option value="descartado" {{ old('status') == 'descartado' ? 'selected' : '' }}>Descartado</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-calendar-dollar"></i> Informações Financeiras
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Data de Aquisição</label>
                            <input type="date" name="data_aquisicao" class="form-control" value="{{ old('data_aquisicao') }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Valor de Aquisição (R$)</label>
                            <input type="number" name="valor_aquisicao" class="form-control" 
                                   value="{{ old('valor_aquisicao') }}" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-chat-left-text"></i> Observações
            </div>
            <div class="card-body">
                <textarea name="observacoes" class="form-control" rows="3" placeholder="Informações adicionais...">{{ old('observacoes') }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <a href="{{ route('machines.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Salvar Máquina
            </button>
        </div>
    </form>
</div>
@endsection
