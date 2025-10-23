@extends('layouts.app')

@section('title', 'Gerenciar Permissões de Usuários')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="bi bi-shield-lock"></i> Gerenciar Permissões de Usuários
                    </h4>
                    <p class="card-subtitle">Controle detalhado de acesso às funcionalidades do sistema</p>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Filtros -->
                    <form method="GET" class="row g-3 mb-4">
                        <div class="col-md-4">
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Buscar por nome ou email" 
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <select name="role" class="form-select">
                                <option value="">Todas as funções</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role }}" {{ request('role') == $role ? 'selected' : '' }}>
                                        {{ ucfirst($role) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Filtrar
                            </button>
                            <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Limpar
                            </a>
                        </div>
                    </form>

                    <!-- Lista de usuários -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Usuário</th>
                                    <th>Email</th>
                                    <th>Função</th>
                                    <th>Permissões Customizadas</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2">
                                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                                </div>
                                                <strong>{{ $user->name }}</strong>
                                            </div>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($user->role == 'admin') bg-danger
                                                @elseif($user->role == 'technician') bg-warning
                                                @else bg-info
                                                @endif">
                                                {{ ucfirst($user->role) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($user->permissions->count() > 0)
                                                <span class="badge bg-success">
                                                    {{ $user->permissions->count() }} permissões configuradas
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Permissões padrão</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->deleted_at == null)
                                                <span class="badge bg-success">Ativo</span>
                                            @else
                                                <span class="badge bg-danger">Inativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.permissions.edit', $user) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-shield-check"></i> Gerenciar
                                                </a>
                                                <form method="POST" action="{{ route('admin.permissions.apply-default', $user) }}" 
                                                      style="display: inline-block;">
                                                    @csrf
                                                    <button type="submit" class="btn btn-sm btn-outline-secondary"
                                                            onclick="return confirm('Aplicar permissões padrão? Isso substituirá as permissões atuais.')">
                                                        <i class="bi bi-arrow-clockwise"></i> Padrão
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-users fs-1 text-muted"></i>
                                            <p class="mt-2 text-muted">Nenhum usuário encontrado</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    {{ $users->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 14px;
    font-weight: bold;
}
</style>
@endsection
