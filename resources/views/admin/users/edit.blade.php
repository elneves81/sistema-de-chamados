@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">✏️ Editar Usuário</h1>
                    <p class="text-muted">Altere as informações de {{ $user->name }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline-info">
                        <i class="bi bi-eye"></i> Visualizar
                    </a>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Voltar
                    </a>
                </div>
            </div>

            <!-- Formulário -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informações do Usuário</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.update', $user) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informações Básicas -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-person"></i> Informações Básicas
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('username') is-invalid @enderror" 
                                           id="username" name="username" value="{{ old('username', $user->username) }}" required>
                                    <div class="form-text">
                                        Usado no login. Você pode corrigir aqui se o valor do LDAP vier incorreto/incompleto.
                                    </div>
                                    @error('username')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Senha -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-lock"></i> Alterar Senha
                                    <small class="text-muted">(deixe em branco para manter a senha atual)</small>
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password" class="form-label">Nova Senha</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                               id="password" name="password">
                                        <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                    <div class="form-text">Mínimo de 8 caracteres (deixe vazio para não alterar)</div>
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation">
                                </div>
                            </div>
                        </div>

                        <!-- Função e Permissões -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-shield-check"></i> Função e Permissões
                                </h6>
                            </div>
                            
                            <div class="col-md-12">
                                <div class="mb-3">
                                    <label for="role" class="form-label">Função <span class="text-danger">*</span></label>
                                    <select class="form-select @error('role') is-invalid @enderror" 
                                            id="role" name="role" required 
                                            {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                        <option value="">Selecione a função</option>
                                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>
                                            👑 Administrador - Acesso total ao sistema
                                        </option>
                                        <option value="technician" {{ old('role', $user->role) === 'technician' ? 'selected' : '' }}>
                                            🛠️ Técnico - Pode gerenciar tickets e categorias
                                        </option>
                                        <option value="customer" {{ old('role', $user->role) === 'customer' ? 'selected' : '' }}>
                                            👤 Cliente - Pode criar e acompanhar seus tickets
                                        </option>
                                    </select>
                                    @if($user->id === auth()->id())
                                        <input type="hidden" name="role" value="{{ $user->role }}">
                                        <div class="form-text text-warning">
                                            <i class="bi bi-exclamation-triangle"></i> Você não pode alterar sua própria função
                                        </div>
                                    @endif
                                    @error('role')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Informações Adicionais -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-info-circle"></i> Informações Adicionais
                                </h6>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="phone" class="form-label">Telefone</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                           placeholder="(11) 99999-9999">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="department" class="form-label">Departamento</label>
                                    <input type="text" class="form-control @error('department') is-invalid @enderror" 
                                           id="department" name="department" value="{{ old('department', $user->department) }}" 
                                           placeholder="Ex: TI, Vendas, RH">
                                    @error('department')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Estatísticas do Usuário -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary mb-3">
                                    <i class="bi bi-graph-up"></i> Estatísticas
                                </h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="card bg-light text-center">
                                            <div class="card-body">
                                                <h5 class="text-primary mb-0">{{ $user->tickets()->count() }}</h5>
                                                <small class="text-muted">Tickets Criados</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light text-center">
                                            <div class="card-body">
                                                <h5 class="text-success mb-0">{{ $user->assignedTickets()->count() }}</h5>
                                                <small class="text-muted">Tickets Atribuídos</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light text-center">
                                            <div class="card-body">
                                                <h5 class="text-warning mb-0">{{ $user->assignedTickets()->where('status', 'open')->count() }}</h5>
                                                <small class="text-muted">Tickets Abertos</small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="card bg-light text-center">
                                            <div class="card-body">
                                                <h5 class="text-info mb-0">{{ $user->created_at->format('d/m/Y') }}</h5>
                                                <small class="text-muted">Cadastrado em</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview das Permissões -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card bg-light" id="permissionPreview">
                                    <div class="card-body">
                                        <h6 class="card-title">Permissões desta função:</h6>
                                        <ul class="mb-0" id="permissionList"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        @if($user->id !== auth()->id())
                                        <button type="button" class="btn btn-danger" onclick="deleteUser()">
                                            <i class="bi bi-trash"></i> Excluir Usuário
                                        </button>
                                        @endif
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                                            <i class="bi bi-x-circle"></i> Cancelar
                                        </a>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> Salvar Alterações
                                        </button>
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

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    
    togglePassword.addEventListener('click', function() {
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.className = type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    });

    // Show permissions based on role
    const roleSelect = document.getElementById('role');
    const permissionPreview = document.getElementById('permissionPreview');
    const permissionList = document.getElementById('permissionList');
    
    const permissions = {
        admin: [
            'Gerenciar todos os usuários',
            'Criar, editar e excluir tickets',
            'Gerenciar categorias',
            'Visualizar dashboard administrativo',
            'Exportar/importar dados',
            'Configurações do sistema'
        ],
        technician: [
            'Gerenciar tickets atribuídos',
            'Criar e editar categorias',
            'Visualizar dashboard técnico',
            'Comentar em tickets',
            'Alterar status de tickets'
        ],
        customer: [
            'Criar novos tickets',
            'Visualizar próprios tickets',
            'Comentar em próprios tickets',
            'Visualizar dashboard pessoal'
        ]
    };
    
    function updatePermissions() {
        const role = roleSelect.value;
        
        if (role && permissions[role]) {
            permissionList.innerHTML = permissions[role]
                .map(permission => `<li><i class="bi bi-check-circle text-success"></i> ${permission}</li>`)
                .join('');
            permissionPreview.style.display = 'block';
        } else {
            permissionPreview.style.display = 'none';
        }
    }
    
    roleSelect.addEventListener('change', updatePermissions);
    updatePermissions(); // Initialize

    // Phone mask
    const phoneField = document.getElementById('phone');
    phoneField.addEventListener('input', function() {
        let value = this.value.replace(/\D/g, '');
        if (value.length >= 11) {
            value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
        } else if (value.length >= 7) {
            value = value.replace(/(\d{2})(\d{4})(\d+)/, '($1) $2-$3');
        } else if (value.length >= 3) {
            value = value.replace(/(\d{2})(\d+)/, '($1) $2');
        }
        this.value = value;
    });
});

function deleteUser() {
    if (confirm('⚠️ ATENÇÃO!\n\nTem certeza que deseja excluir este usuário?\n\nEsta ação não pode ser desfeita e todos os dados relacionados serão removidos.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.users.destroy", $user) }}';
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}
</script>

<style>
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    border: 1px solid rgba(0, 0, 0, 0.125);
}

.text-primary {
    color: #007bff !important;
}

#permissionList li {
    margin-bottom: 0.5rem;
}

.form-control:focus, .form-select:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.card.bg-light {
    background-color: #f8f9fa !important;
}
</style>
@endsection
