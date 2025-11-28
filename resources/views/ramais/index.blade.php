@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-telephone"></i> Ramais</h2>
                    <p class="text-muted">Lista de ramais telefônicos</p>
                </div>
                <div>
                    @if(auth()->user()->role === 'admin' || auth()->user()->role === 'technician')
                    <a href="{{ route('ramais.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle"></i> Novo Ramal
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0">Total de Ramais</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('ramais.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Pesquisar</label>
                    <input type="text" name="search" class="form-control" placeholder="Departamento, descrição, ramal..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Departamento</label>
                    <input type="text" name="departamento" class="form-control" placeholder="Filtrar por departamento" value="{{ request('departamento') }}">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                    <a href="{{ route('ramais.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-circle"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Mensagens de sucesso/erro -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <!-- Tabela de Ramais -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Departamento</th>
                            <th>Descrição</th>
                            <th>Ramal</th>
                            <th class="text-end">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ramais as $ramal)
                        <tr>
                            <td>
                                <strong>{{ $ramal->departamento }}</strong>
                            </td>
                            <td>{{ $ramal->descricao }}</td>
                            <td>
                                <span class="badge bg-primary">
                                    <i class="bi bi-telephone-fill"></i> {{ $ramal->ramal }}
                                </span>
                            </td>
                            <td class="text-end">
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'technician')
                                <a href="{{ route('ramais.edit', $ramal) }}" class="btn btn-sm btn-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('ramais.destroy', $ramal) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este ramal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @else
                                <span class="text-muted">Sem permissão</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                                <p class="mt-2">Nenhum ramal encontrado.</p>
                                @if(auth()->user()->role === 'admin' || auth()->user()->role === 'technician')
                                <a href="{{ route('ramais.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Cadastrar Primeiro Ramal
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Paginação -->
            @if($ramais->hasPages())
            <div class="mt-4">
                {{ $ramais->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
