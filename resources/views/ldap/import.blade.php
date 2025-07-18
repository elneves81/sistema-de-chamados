@extends('layouts.app')

@section('styles')
<style>
.ldap-card {
    background: #fff;
    border-radius: 1.2rem;
    box-shadow: 0 4px 32px 0 rgba(0,0,0,0.10);
    padding: 2.5rem 2.2rem 2.2rem 2.2rem;
    max-width: 600px;
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
}
.ldap-form input, .ldap-form select {
    border-radius: 8px;
    border: 1.2px solid #e0e7ef;
    font-size: 1.01rem;
    padding: 0.6rem 0.9rem;
    background: #f7f9fc;
    margin-bottom: 0.6rem;
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
}
.ldap-btn:hover {
    background: linear-gradient(90deg, #6366f1 0%, #1976f2 100%);
}
.ldap-preview {
    margin-top: 2rem;
}
.ldap-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 1rem;
}
.ldap-table th, .ldap-table td {
    border: 1px solid #e0e7ef;
    padding: 0.5rem 0.7rem;
    text-align: left;
}
.ldap-table th {
    background: #f3f6ff;
    color: #1976f2;
    font-weight: 700;
}
</style>
@endsection

@section('content')
<div class="ldap-card">
    <div class="ldap-title">Importação LDAP / Active Directory</div>
    <form class="ldap-form" method="POST" action="{{ route('admin.ldap.import.preview') }}">
        @csrf
        <div class="row">
            <div class="col-md-6">
                <label>Servidor/Host</label>
                <input type="text" name="host" class="form-control" required placeholder="ex: ad.seudominio.local">
            </div>
            <div class="col-md-3">
                <label>Porta</label>
                <input type="number" name="port" class="form-control" value="389" required>
            </div>
            <div class="col-md-3">
                <label>SSL</label>
                <select name="ssl" class="form-control">
                    <option value="0">Não</option>
                    <option value="1">Sim</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <label>Usuário (DN)</label>
                <input type="text" name="username" class="form-control" required placeholder="ex: CN=Administrador,CN=Users,DC=seudominio,DC=local">
            </div>
            <div class="col-md-6">
                <label>Senha</label>
                <input type="password" name="password" class="form-control" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <label>Base DN</label>
                <input type="text" name="base_dn" class="form-control" required placeholder="ex: DC=seudominio,DC=local">
            </div>
        </div>
        <div class="text-center mt-3">
            <button type="submit" class="ldap-btn">Pré-visualizar Usuários</button>
            <button type="button" class="ldap-btn" onclick="alert('Função de teste de conexão em breve!')">Testar Conexão</button>
        </div>
    </form>
    @if(isset($preview))
    <div class="ldap-preview">
        <h5>Usuários encontrados:</h5>
        <form method="POST" action="{{ route('admin.ldap.import.process') }}">
            @csrf
            <table class="ldap-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="document.querySelectorAll('.ldap-check').forEach(e=>e.checked=this.checked)"></th>
                        <th>Nome</th>
                        <th>Login</th>
                        <th>Email</th>
                        <th>Grupo</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($preview as $user)
                    <tr>
                        <td><input type="checkbox" class="ldap-check" name="users[]" value="{{ $user['login'] }}" checked></td>
                        <td>{{ $user['name'] }}</td>
                        <td>{{ $user['login'] }}</td>
                        <td>{{ $user['email'] }}</td>
                        <td>{{ $user['group'] }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="text-center mt-3">
                <button type="submit" class="ldap-btn">Importar Selecionados</button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
