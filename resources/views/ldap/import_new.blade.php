@extends('layouts.app')

@section('styles')
<style>
.ldap-card {
    background: #fff;
    border-radius: 1.2rem;
    box-shadow: 0 4px 32px 0 rgba(0,0,0,0.10);
    padding: 2.5rem 2.2rem 2.2rem 2.2rem;
    margin: 2rem auto;
}
.ldap-title {
    font-size: 2rem;
    font-weight: 800;
    color: #1976f2;
    text-align: center;
    margin-bottom: 1.5rem;
    letter-spacing: 0.01em;
    text-shadow: 0 2px 8px #6366f122;
}
.ldap-form label {
    font-weight: 600;
    color: #444;
    margin-bottom: 0.2rem;
    display: block;
}
.ldap-form input, .ldap-form select {
    border-radius: 8px;
    border: 1.2px solid #e0e7ef;
    font-size: 1.01rem;
    padding: 0.6rem 0.9rem;
    background: #f7f9fc;
    margin-bottom: 1rem;
    width: 100%;
}
.ldap-btn {
    background: linear-gradient(90deg, #1976f2 0%, #6366f1 100%);
    color: #fff;
    border: none;
    border-radius: 8px;
    font-weight: 700;
    font-size: 1.05rem;
    padding: 0.65rem 1.5rem;
    margin-right: 0.5rem;
    box-shadow: 0 2px 8px rgba(25,118,242,0.10);
    transition: background 0.2s;
    cursor: pointer;
}
.ldap-btn:hover {
    background: linear-gradient(90deg, #6366f1 0%, #1976f2 100%);
}
.ldap-btn-secondary {
    background: #6c757d;
    color: #fff;
}
.ldap-btn-secondary:hover {
    background: #5a6268;
}
.ldap-preview {
    background: #f8f9fa;
    border-radius: 8px;
    padding: 1.5rem;
    margin-top: 2rem;
}
.ldap-preview table {
    width: 100%;
    border-collapse: collapse;
}
.ldap-preview th, .ldap-preview td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}
.ldap-preview th {
    background-color: #e9ecef;
    font-weight: 600;
}
.ldap-preview tbody tr:hover {
    background-color: #f5f5f5;
}
.form-row {
    display: flex;
    gap: 1rem;
    margin-bottom: 1rem;
}
.form-row .form-group {
    flex: 1;
}
.form-group {
    margin-bottom: 1rem;
}
.checkbox-cell {
    text-align: center;
    width: 50px;
}
.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 8px;
    border: 1px solid transparent;
}
.alert-success {
    color: #155724;
    background-color: #d4edda;
    border-color: #c3e6cb;
}
.alert-danger {
    color: #721c24;
    background-color: #f8d7da;
    border-color: #f5c6cb;
}
.alert-warning {
    color: #856404;
    background-color: #fff3cd;
    border-color: #ffeaa7;
}
.actions-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
    padding: 1rem;
    background-color: #f8f9fa;
    border-radius: 8px;
}
</style>
@endsection

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            {{-- Exibir mensagens de feedback --}}
            @if(session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> {{ session('warning') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="ldap-card">
                <div class="ldap-title">
                    <i class="fas fa-users"></i> Importação LDAP / Active Directory
                </div>
                
                {{-- Formulário de configuração LDAP --}}
                <form class="ldap-form" method="POST" action="{{ route('admin.ldap.import.preview') }}">
                    @csrf
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="host">Servidor LDAP</label>
                            <input type="text" id="host" name="host" class="form-control" 
                                   value="{{ old('host') }}" required 
                                   placeholder="ex: ldap.empresa.com ou 192.168.1.10">
                        </div>
                        <div class="form-group">
                            <label for="port">Porta</label>
                            <input type="number" id="port" name="port" class="form-control" 
                                   value="{{ old('port', 389) }}" min="1" max="65535"
                                   placeholder="389 (LDAP) ou 636 (LDAPS)">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="base_dn">Base DN</label>
                        <input type="text" id="base_dn" name="base_dn" class="form-control" 
                               value="{{ old('base_dn') }}" required 
                               placeholder="ex: DC=seudominio,DC=local">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="username">Usuário de Conexão</label>
                            <input type="text" id="username" name="username" class="form-control" 
                                   value="{{ old('username') }}" required
                                   placeholder="ex: administrador@empresa.com">
                        </div>
                        <div class="form-group">
                            <label for="password">Senha</label>
                            <input type="password" id="password" name="password" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="filter">Filtro LDAP (Opcional)</label>
                            <input type="text" id="filter" name="filter" class="form-control" 
                                   value="{{ old('filter', '(&(objectClass=user)(!(objectClass=computer)))') }}"
                                   placeholder="ex: (&(objectClass=user)(department=TI))">
                        </div>
                        <div class="form-group">
                            <label for="size">Limite de Usuários</label>
                            <input type="number" id="size" name="size" class="form-control" 
                                   value="{{ old('size', 100) }}" min="1" max="1000"
                                   placeholder="Máximo de usuários a buscar">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="use_ssl" value="1" {{ old('use_ssl') ? 'checked' : '' }}>
                            Usar SSL/TLS (LDAPS)
                        </label>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="ldap-btn">
                            <i class="fas fa-search"></i> Buscar Usuários
                        </button>
                    </div>
                </form>

                {{-- Preview dos usuários encontrados --}}
                @if(isset($preview) && !empty($preview))
                <div class="ldap-preview">
                    <h4><i class="fas fa-eye"></i> Preview dos Usuários ({{ count($preview) }} encontrados)</h4>
                    
                    <form method="POST" action="{{ route('admin.ldap.import.process') }}">
                        @csrf
                        
                        <div class="actions-bar">
                            <div>
                                <button type="button" id="selectAll" class="ldap-btn ldap-btn-secondary">
                                    <i class="fas fa-check-square"></i> Selecionar Todos
                                </button>
                                <button type="button" id="selectNone" class="ldap-btn ldap-btn-secondary">
                                    <i class="fas fa-square"></i> Desmarcar Todos
                                </button>
                            </div>
                            <div>
                                <button type="submit" class="ldap-btn">
                                    <i class="fas fa-download"></i> Importar Selecionados
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="checkbox-cell">
                                            <input type="checkbox" id="masterCheckbox">
                                        </th>
                                        <th>Nome</th>
                                        <th>Login</th>
                                        <th>Email</th>
                                        <th>Departamento</th>
                                        <th>Cargo</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($preview as $i => $user)
                                    <tr class="{{ !$user['enabled'] ? 'text-muted' : '' }}">
                                        <td class="checkbox-cell">
                                            @if($user['enabled'])
                                                <input type="checkbox" name="users[{{ $i }}][dn]" 
                                                       value="{{ $user['dn'] }}" class="user-checkbox">
                                                
                                                {{-- Campos hidden com dados do usuário --}}
                                                <input type="hidden" name="users[{{ $i }}][upn]" value="{{ $user['upn'] }}">
                                                <input type="hidden" name="users[{{ $i }}][name]" value="{{ $user['name'] }}">
                                                <input type="hidden" name="users[{{ $i }}][login]" value="{{ $user['login'] }}">
                                                <input type="hidden" name="users[{{ $i }}][email]" value="{{ $user['email'] }}">
                                                <input type="hidden" name="users[{{ $i }}][department]" value="{{ $user['department'] }}">
                                                <input type="hidden" name="users[{{ $i }}][title]" value="{{ $user['title'] }}">
                                            @endif
                                        </td>
                                        <td>
                                            {{ $user['name'] }}
                                            @if(!$user['enabled'])
                                                <span class="badge badge-secondary">Desabilitado</span>
                                            @endif
                                        </td>
                                        <td>{{ $user['login'] }}</td>
                                        <td>{{ $user['email'] }}</td>
                                        <td>{{ $user['department'] }}</td>
                                        <td>{{ $user['title'] }}</td>
                                        <td>
                                            <span class="badge badge-{{ $user['enabled'] ? 'success' : 'secondary' }}">
                                                {{ $user['enabled'] ? 'Ativo' : 'Inativo' }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Checkbox master para selecionar/desmarcar todos
    const masterCheckbox = document.getElementById('masterCheckbox');
    const userCheckboxes = document.querySelectorAll('.user-checkbox');
    const selectAllBtn = document.getElementById('selectAll');
    const selectNoneBtn = document.getElementById('selectNone');

    if (masterCheckbox) {
        masterCheckbox.addEventListener('change', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    if (selectAllBtn) {
        selectAllBtn.addEventListener('click', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = true;
            });
            if (masterCheckbox) masterCheckbox.checked = true;
        });
    }

    if (selectNoneBtn) {
        selectNoneBtn.addEventListener('click', function() {
            userCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });
            if (masterCheckbox) masterCheckbox.checked = false;
        });
    }

    // Atualizar master checkbox baseado nos individuais
    userCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedBoxes = document.querySelectorAll('.user-checkbox:checked');
            if (masterCheckbox) {
                masterCheckbox.checked = checkedBoxes.length === userCheckboxes.length;
                masterCheckbox.indeterminate = checkedBoxes.length > 0 && checkedBoxes.length < userCheckboxes.length;
            }
        });
    });
});
</script>
@endsection
