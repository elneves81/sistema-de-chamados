@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Usuários</h3>
                    <div class="card-tools">
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Novo Usuário
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="mb-3">
                        <form action="{{ route('users.index') }}" method="GET" class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group">
                                    <input type="text" name="search" class="form-control" placeholder="Buscar por nome ou email" value="{{ request('search') }}">
                                    <button class="btn btn-outline-secondary" type="submit">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="role" class="form-select" onchange="this.form.submit()">
                                    <option value="">Todos os perfis</option>
                                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Administrador</option>
                                    <option value="technician" {{ request('role') == 'technician' ? 'selected' : '' }}>Técnico</option>
                                    <option value="customer" {{ request('role') == 'customer' ? 'selected' : '' }}>Cliente</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-select" onchange="this.form.submit()">
                                    <option value="">Todos os status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Ativos</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inativos</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <a href="{{ route('users.index') }}" class="btn btn-secondary w-100">Limpar</a>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Perfil</th>
                                    <th>Departamento</th>
                                    <th>Status</th>
                                    <th>Último login</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @php
                                                $roleClass = [
                                                    'admin' => 'danger',
                                                    'technician' => 'primary',
                                                    'customer' => 'success'
                                                ][$user->role] ?? 'secondary';
                                                
                                                $roleText = [
                                                    'admin' => 'Administrador',
                                                    'technician' => 'Técnico',
                                                    'customer' => 'Cliente'
                                                ][$user->role] ?? 'Desconhecido';
                                            @endphp
                                            <span class="badge bg-{{ $roleClass }}">
                                                {{ $roleText }}
                                            </span>
                                        </td>
                                        <td>{{ $user->department ?? 'N/A' }}</td>
                                        <td>
                                            @if($user->trashed())
                                                <span class="badge bg-danger">Inativo</span>
                                            @else
                                                <span class="badge bg-success">Ativo</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}
                                        </td>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-info btn-sm">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja remover este usuário?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3">
                        {{ $users->withQueryString()->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
