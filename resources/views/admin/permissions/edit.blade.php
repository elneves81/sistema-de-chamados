@extends('layouts.app')

@section('title', 'Editar Permissões - ' . $user->name)

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="card-title">
                                <i class="bi bi-shield-lock"></i> Editar Permissões
                            </h4>
                            <p class="card-subtitle">
                                Usuário: <strong>{{ $user->name }}</strong> ({{ $user->email }}) - 
                                <span class="badge 
                                    @if($user->role == 'admin') bg-danger
                                    @elseif($user->role == 'technician') bg-warning
                                    @else bg-info
                                    @endif">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </p>
                        </div>
                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>
                
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.permissions.update', $user) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                @foreach($permissions as $module => $modulePermissions)
                                    <div class="card mb-3">
                                        <div class="card-header">
                                            <h5 class="mb-0">
                                                <i class="bi bi-{{ $module == 'tickets' ? 'ticket-perforated' : ($module == 'dashboard' ? 'speedometer2' : ($module == 'users' ? 'people' : ($module == 'board' ? 'display' : 'gear'))) }}"></i>
                                                {{ ucfirst($module) }}
                                            </h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($modulePermissions as $permission)
                                                    <div class="col-md-6 mb-3">
                                                        <div class="form-check">
                                                            <input type="hidden" name="permissions[{{ $permission->name }}]" value="0">
                                                            <input class="form-check-input" type="checkbox" 
                                                                   name="permissions[{{ $permission->name }}]" 
                                                                   value="1"
                                                                   id="permission_{{ $permission->id }}"
                                                                   {{ isset($userPermissions[$permission->name]) && $userPermissions[$permission->name] ? 'checked' : '' }}>
                                                            <label class="form-check-label" for="permission_{{ $permission->id }}">
                                                                <strong>{{ $permission->display_name }}</strong>
                                                                @if($permission->description)
                                                                    <br><small class="text-muted">{{ $permission->description }}</small>
                                                                @endif
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                <div class="d-flex justify-content-between">
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-lg"></i> Salvar Permissões
                                        </button>
                                        <a href="{{ route('admin.permissions.index') }}" class="btn btn-secondary">
                                            <i class="bi bi-x-lg"></i> Cancelar
                                        </a>
                                    </div>
                                    <div>
                                        <form method="POST" action="{{ route('admin.permissions.apply-default', $user) }}" 
                                              style="display: inline-block;" 
                                              onsubmit="return confirm('Aplicar permissões padrão baseadas na função do usuário? Isso substituirá todas as permissões atuais.')">
                                            @csrf
                                            <button type="submit" class="btn btn-warning">
                                                <i class="bi bi-arrow-clockwise"></i> Aplicar Permissões Padrão
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="mb-0">
                                            <i class="bi bi-info-circle"></i> Informações
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <h6>Ações Rápidas</h6>
                                        <div class="d-grid gap-2 mb-3">
                                            <button type="button" class="btn btn-outline-success btn-sm" onclick="checkAll()">
                                                <i class="bi bi-check2-all"></i> Marcar Todas
                                            </button>
                                            <button type="button" class="btn btn-outline-danger btn-sm" onclick="uncheckAll()">
                                                <i class="bi bi-x"></i> Desmarcar Todas
                                            </button>
                                        </div>

                                        <hr>

                                        <h6>Permissões Padrão por Função</h6>
                                        <div class="mb-2">
                                            <strong>Cliente:</strong>
                                            <ul class="small">
                                                <li>Ver próprios chamados</li>
                                                <li>Criar chamados</li>
                                                <li>Editar próprios chamados</li>
                                            </ul>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Técnico:</strong>
                                            <ul class="small">
                                                <li>Ver todos os chamados</li>
                                                <li>Gerenciar chamados</li>
                                                <li>Acessar dashboard</li>
                                            </ul>
                                        </div>
                                        <div class="mb-2">
                                            <strong>Administrador:</strong>
                                            <ul class="small">
                                                <li>Acesso completo ao sistema</li>
                                                <li>Gerenciar usuários</li>
                                                <li>Configurações avançadas</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function checkAll() {
    document.querySelectorAll('input[type="checkbox"][name^="permissions"]').forEach(function(checkbox) {
        checkbox.checked = true;
    });
}

function uncheckAll() {
    document.querySelectorAll('input[type="checkbox"][name^="permissions"]').forEach(function(checkbox) {
        checkbox.checked = false;
    });
}
</script>
@endsection
