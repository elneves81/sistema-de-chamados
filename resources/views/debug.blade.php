@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header">
                    <h4>🔧 Página de Debug - Sistema de Chamados</h4>
                </div>

                <div class="card-body">
                    <h5>📧 Informações do Usuário Admin</h5>
                    
                    @if($admin)
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <tr>
                                    <th>ID</th>
                                    <td>{{ $admin->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nome</th>
                                    <td>{{ $admin->name }}</td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>{{ $admin->email }}</td>
                                </tr>
                                <tr>
                                    <th>Role</th>
                                    <td>{{ $admin->role }}</td>
                                </tr>
                                <tr>
                                    <th>Ativo</th>
                                    <td>{{ $admin->is_active ? '✅ Sim' : '❌ Não' }}</td>
                                </tr>
                                <tr>
                                    <th>Email Verificado</th>
                                    <td>{{ $admin->email_verified_at ? '✅ ' . $admin->email_verified_at : '❌ Não verificado' }}</td>
                                </tr>
                                <tr>
                                    <th>Último Login</th>
                                    <td>{{ $admin->last_login_at ?? 'Nunca' }}</td>
                                </tr>
                                <tr>
                                    <th>Criado em</th>
                                    <td>{{ $admin->created_at }}</td>
                                </tr>
                                <tr>
                                    <th>Hash da Senha</th>
                                    <td><code>{{ substr($admin->password, 0, 30) }}...</code></td>
                                </tr>
                            </table>
                        </div>

                        <div class="mt-4">
                            <h5>🔑 Teste de Credenciais</h5>
                            <div class="alert alert-info">
                                <strong>Email:</strong> admin@admin.com<br>
                                <strong>Senha:</strong> admin123<br>
                                <strong>Status do Hash:</strong> {{ $passwordCheck ? '✅ Correto' : '❌ Incorreto' }}
                            </div>
                        </div>

                        <div class="mt-4">
                            <h5>🌐 Links do Sistema</h5>
                            <div class="btn-group" role="group">
                                <a href="{{ route('login') }}" class="btn btn-primary">Página de Login</a>
                                <a href="{{ route('dashboard') }}" class="btn btn-success">Dashboard</a>
                                <a href="{{ route('assets.index') }}" class="btn btn-info">Ativos</a>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-danger">
                            ❌ Usuário admin não encontrado!
                        </div>
                    @endif

                    <div class="mt-4">
                        <h5>📊 Estatísticas do Sistema</h5>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body">
                                        <h6>Total de Usuários</h6>
                                        <h3>{{ $totalUsers }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body">
                                        <h6>Usuários Ativos</h6>
                                        <h3>{{ $activeUsers }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body">
                                        <h6>Administradores</h6>
                                        <h3>{{ $adminUsers }}</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body">
                                        <h6>Técnicos</h6>
                                        <h3>{{ $techUsers }}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
