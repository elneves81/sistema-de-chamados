@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Localizações</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Nova Localização
        </a>
    </div>
</div>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.locations.index') }}">
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="search" class="form-label">Buscar</label>
                        <input type="text" class="form-control" name="search" id="search" 
                               value="{{ request('search') }}" placeholder="Nome, cidade, endereço...">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="country" class="form-label">País</label>
                        <select class="form-select" name="country" id="country">
                            <option value="">Todos</option>
                            <option value="Brasil" {{ request('country') == 'Brasil' ? 'selected' : '' }}>Brasil</option>
                            <option value="Estados Unidos" {{ request('country') == 'Estados Unidos' ? 'selected' : '' }}>Estados Unidos</option>
                            <option value="Canadá" {{ request('country') == 'Canadá' ? 'selected' : '' }}>Canadá</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="is_active" class="form-label">Status</label>
                        <select class="form-select" name="is_active" id="is_active">
                            <option value="">Todos</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Ativo</option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="bi bi-search"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Localizações -->
<div class="card">
    <div class="card-body">
        @if($locations->count() > 0)
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Nome</th>
                            <th>Código</th>
                            <th>Cidade/Estado</th>
                            <th>País</th>
                            <th>Contato</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($locations as $location)
                        <tr>
                            <td>
                                <strong>{{ $location->name }}</strong>
                                @if($location->address)
                                    <br><small class="text-muted">{{ Str::limit($location->address, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($location->short_name)
                                    <span class="badge bg-secondary">{{ $location->short_name }}</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                {{ $location->city }}
                                @if($location->state)
                                    <br><small class="text-muted">{{ $location->state }}</small>
                                @endif
                            </td>
                            <td>{{ $location->country ?? '-' }}</td>
                            <td>
                                @if($location->phone)
                                    <i class="bi bi-telephone"></i> {{ $location->phone }}<br>
                                @endif
                                @if($location->email)
                                    <i class="bi bi-envelope"></i> {{ $location->email }}
                                @endif
                                @if(!$location->phone && !$location->email)
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($location->is_active)
                                    <span class="badge bg-success">Ativo</span>
                                @else
                                    <span class="badge bg-danger">Inativo</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a href="{{ route('admin.locations.show', $location) }}" class="btn btn-outline-info" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.locations.edit', $location) }}" class="btn btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('admin.locations.destroy', $location) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Excluir"
                                                onclick="return confirm('Tem certeza que deseja excluir esta localização?')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Paginação -->
            <div class="d-flex justify-content-center">
                {{ $locations->withQueryString()->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-geo-alt" style="font-size: 3rem; color: #ccc;"></i>
                <h5 class="mt-3 text-muted">Nenhuma localização encontrada</h5>
                <p class="text-muted">Comece criando sua primeira localização.</p>
                <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Nova Localização
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
