@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('ramais.index') }}">Ramais</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $ramal->departamento }}</li>
                </ol>
            </nav>
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="bi bi-telephone"></i> Detalhes do Ramal</h2>
                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'technician')
                <div>
                    <a href="{{ route('ramais.edit', $ramal) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <form action="{{ route('ramais.destroy', $ramal) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este ramal?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-trash"></i> Excluir
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <dl class="row">
                        <dt class="col-sm-4">Departamento:</dt>
                        <dd class="col-sm-8">{{ $ramal->departamento }}</dd>

                        <dt class="col-sm-4">Descrição:</dt>
                        <dd class="col-sm-8">{{ $ramal->descricao }}</dd>

                        <dt class="col-sm-4">Ramal:</dt>
                        <dd class="col-sm-8">
                            <span class="badge bg-primary">
                                <i class="bi bi-telephone-fill"></i> {{ $ramal->ramal }}
                            </span>
                        </dd>

                        <dt class="col-sm-4">Cadastrado em:</dt>
                        <dd class="col-sm-8">{{ $ramal->created_at->format('d/m/Y H:i') }}</dd>

                        @if($ramal->updated_at && $ramal->updated_at != $ramal->created_at)
                        <dt class="col-sm-4">Última atualização:</dt>
                        <dd class="col-sm-8">{{ $ramal->updated_at->format('d/m/Y H:i') }}</dd>
                        @endif
                    </dl>

                    <div class="mt-4">
                        <a href="{{ route('ramais.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
