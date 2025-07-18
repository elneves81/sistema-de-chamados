@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">{{ $location->name }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <div class="btn-group me-2">
            <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
            <a href="{{ route('locations.edit', $location) }}" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Editar
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Informações Básicas -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">
                    <i class="bi bi-geo-alt text-primary"></i> Informações da Localização
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <h4>{{ $location->name }}</h4>
                        @if($location->short_name)
                            <p class="mb-2"><span class="badge bg-secondary fs-6">{{ $location->short_name }}</span></p>
                        @endif
                    </div>
                    <div class="col-md-4 text-end">
                        @if($location->is_active)
                            <span class="badge bg-success fs-6">Ativo</span>
                        @else
                            <span class="badge bg-danger fs-6">Inativo</span>
                        @endif
                    </div>
                </div>

                @if($location->address || $location->city || $location->state || $location->country)
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="bi bi-house"></i> Endereço</h6>
                        @if($location->address)
                            <p class="mb-1">{{ $location->address }}</p>
                        @endif
                        <p class="mb-0">
                            @if($location->city){{ $location->city }}@endif
                            @if($location->city && $location->state), @endif
                            @if($location->state){{ $location->state }}@endif
                            @if($location->postal_code) - {{ $location->postal_code }}@endif
                        </p>
                        @if($location->country)
                            <p class="text-muted mb-0">{{ $location->country }}</p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <h6><i class="bi bi-telephone"></i> Contato</h6>
                        @if($location->phone)
                            <p class="mb-1">
                                <i class="bi bi-telephone"></i> {{ $location->phone }}
                            </p>
                        @endif
                        @if($location->email)
                            <p class="mb-1">
                                <i class="bi bi-envelope"></i> 
                                <a href="mailto:{{ $location->email }}">{{ $location->email }}</a>
                            </p>
                        @endif
                        @if(!$location->phone && !$location->email)
                            <p class="text-muted">Nenhuma informação de contato</p>
                        @endif
                    </div>
                </div>
                @endif

                @if($location->comment)
                <hr>
                <div class="row">
                    <div class="col-12">
                        <h6><i class="bi bi-chat-text"></i> Comentários</h6>
                        <p class="mb-0">{{ $location->comment }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Usuários nesta Localização -->
        @if($location->users && $location->users->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-people text-info"></i> 
                    Usuários ({{ $location->users->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>E-mail</th>
                                <th>Departamento</th>
                                <th>Perfil</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($location->users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->department ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ ucfirst($user->role) }}</span>
                                </td>
                                <td>
                                    @if($user->is_active)
                                        <span class="badge bg-success">Ativo</span>
                                    @else
                                        <span class="badge bg-danger">Inativo</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif

        <!-- Ativos nesta Localização -->
        @if($location->assets && $location->assets->count() > 0)
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="bi bi-laptop text-success"></i> 
                    Ativos ({{ $location->assets->count() }})
                </h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Tag Patrimonial</th>
                                <th>Nome</th>
                                <th>Tipo</th>
                                <th>Responsável</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($location->assets as $asset)
                            <tr>
                                <td>
                                    <a href="{{ route('assets.show', $asset) }}" class="text-decoration-none">
                                        <strong>{{ $asset->asset_tag }}</strong>
                                    </a>
                                </td>
                                <td>{{ $asset->name }}</td>
                                <td>
                                    @if($asset->assetType)
                                        <span class="badge bg-info">{{ $asset->assetType->name }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($asset->assignedUser)
                                        {{ $asset->assignedUser->name }}
                                    @else
                                        <span class="text-muted">Não atribuído</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'active' => 'success',
                                            'inactive' => 'secondary',
                                            'maintenance' => 'warning',
                                            'retired' => 'dark',
                                            'lost' => 'danger',
                                            'stolen' => 'danger'
                                        ];
                                        $statusLabels = [
                                            'active' => 'Ativo',
                                            'inactive' => 'Inativo',
                                            'maintenance' => 'Manutenção',
                                            'retired' => 'Descartado',
                                            'lost' => 'Perdido',
                                            'stolen' => 'Roubado'
                                        ];
                                    @endphp
                                    <span class="badge bg-{{ $statusColors[$asset->status] ?? 'secondary' }}">
                                        {{ $statusLabels[$asset->status] ?? $asset->status }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        @endif
    </div>
    
    <div class="col-md-4">
        <!-- Estatísticas -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Estatísticas</h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <h4 class="text-info">{{ $location->users->count() }}</h4>
                            <small class="text-muted">Usuários</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <h4 class="text-success">{{ $location->assets->count() }}</h4>
                        <small class="text-muted">Ativos</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Informações do Sistema -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="card-title mb-0">Informações do Sistema</h6>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Criado em:</strong><br>
                    <small>{{ $location->created_at->format('d/m/Y \à\s H:i') }}</small>
                </p>
                <p class="mb-0">
                    <strong>Última atualização:</strong><br>
                    <small>{{ $location->updated_at->format('d/m/Y \à\s H:i') }}</small>
                </p>
            </div>
        </div>

        <!-- Ações -->
        <div class="card">
            <div class="card-header">
                <h6 class="card-title mb-0">Ações</h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('locations.edit', $location) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Editar Localização
                    </a>
                    @if($location->users->count() == 0 && $location->assets->count() == 0)
                        <form action="{{ route('locations.destroy', $location) }}" method="POST" 
                              onsubmit="return confirm('⚠️ ATENÇÃO!\n\nTem certeza que deseja EXCLUIR esta localização?\n\nEsta ação não pode ser desfeita!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Excluir Localização
                            </button>
                        </form>
                    @else
                        <button type="button" class="btn btn-danger" disabled title="Não é possível excluir: há usuários ou ativos vinculados">
                            <i class="bi bi-trash"></i> Excluir Localização
                        </button>
                        <small class="text-muted">
                            Para excluir, remova todos os usuários e ativos desta localização primeiro.
                        </small>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
