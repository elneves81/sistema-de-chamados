@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('ramais.index') }}">Ramais</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Editar Ramal</li>
                </ol>
            </nav>
            <h2><i class="bi bi-pencil"></i> Editar Ramal</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('ramais.update', $ramal) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="departamento" class="form-label">Departamento <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('departamento') is-invalid @enderror" 
                                   id="departamento" 
                                   name="departamento" 
                                   value="{{ old('departamento', $ramal->departamento) }}" 
                                   required>
                            @error('departamento')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="descricao" class="form-label">Descrição <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('descricao') is-invalid @enderror" 
                                   id="descricao" 
                                   name="descricao" 
                                   value="{{ old('descricao', $ramal->descricao) }}" 
                                   required>
                            @error('descricao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="ramal" class="form-label">Ramal <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('ramal') is-invalid @enderror" 
                                   id="ramal" 
                                   name="ramal" 
                                   value="{{ old('ramal', $ramal->ramal) }}" 
                                   required>
                            @error('ramal')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle"></i> Atualizar
                            </button>
                            <a href="{{ route('ramais.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
