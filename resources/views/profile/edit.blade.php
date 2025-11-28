@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-person-circle"></i> Meu Perfil</h2>
                    <p class="text-muted mb-0">Gerencie suas informações pessoais e preferências</p>
                </div>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="row">
                <!-- Informações Pessoais -->
                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="bi bi-person-badge"></i> Informações Pessoais</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label for="name" class="form-label">Nome Completo</label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $user->name) }}" 
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="email" class="form-label">E-mail</label>
                                    <input type="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email', $user->email) }}" 
                                           required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-3">
                                    <label for="username" class="form-label">Nome de Usuário</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           value="{{ $user->username }}" 
                                           disabled>
                                    <small class="text-muted">O nome de usuário não pode ser alterado</small>
                                </div>

                                <hr class="my-4">

                                <h6 class="mb-3"><i class="bi bi-key"></i> Alterar Senha</h6>
                                <p class="text-muted small">Deixe em branco se não quiser alterar a senha</p>

                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Senha Atual</label>
                                    <input type="password" 
                                           class="form-control @error('current_password') is-invalid @enderror" 
                                           id="current_password" 
                                           name="current_password">
                                    @error('current_password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="password" class="form-label">Nova Senha</label>
                                        <input type="password" 
                                               class="form-control @error('password') is-invalid @enderror" 
                                               id="password" 
                                               name="password"
                                               minlength="6">
                                        @error('password')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="password_confirmation" class="form-label">Confirmar Nova Senha</label>
                                        <input type="password" 
                                               class="form-control" 
                                               id="password_confirmation" 
                                               name="password_confirmation"
                                               minlength="6">
                                    </div>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-save"></i> Salvar Alterações
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Informações do Sistema -->
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-info text-white">
                            <h5 class="mb-0"><i class="bi bi-info-circle"></i> Informações da Conta</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="text-muted small">Função</label>
                                <div class="fw-bold">
                                    @if($user->role === 'admin')
                                        <span class="badge bg-danger">Administrador</span>
                                    @elseif($user->role === 'technician')
                                        <span class="badge bg-primary">Técnico</span>
                                    @else
                                        <span class="badge bg-secondary">Usuário</span>
                                    @endif
                                </div>
                            </div>

                            @if($user->location)
                            <div class="mb-3">
                                <label class="text-muted small">UBS Vinculada</label>
                                <div class="fw-bold">
                                    <i class="bi bi-geo-alt-fill text-primary"></i> {{ $user->location->name }}
                                </div>
                            </div>
                            @endif

                            <div class="mb-3">
                                <label class="text-muted small">Membro desde</label>
                                <div>{{ $user->created_at->format('d/m/Y') }}</div>
                            </div>

                            <div class="mb-3">
                                <label class="text-muted small">Último acesso</label>
                                <div>{{ $user->updated_at->diffForHumans() }}</div>
                            </div>

                            @if($user->auth_via_ldap)
                            <div class="alert alert-warning mt-3" role="alert">
                                <small>
                                    <i class="bi bi-shield-lock"></i> 
                                    Conta autenticada via LDAP
                                </small>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Estatísticas rápidas -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="bi bi-graph-up"></i> Suas Estatísticas</h5>
                        </div>
                        <div class="card-body">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Chamados Criados</span>
                                <strong>{{ $user->tickets()->count() }}</strong>
                            </div>
                            @if($user->role === 'technician' || $user->role === 'admin')
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Atribuídos a Mim</span>
                                <strong>{{ $user->assignedTickets()->count() }}</strong>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Validação de senha
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const confirm = document.getElementById('password_confirmation');
    
    if (password.length > 0 && password.length < 6) {
        this.setCustomValidity('A senha deve ter no mínimo 6 caracteres');
    } else {
        this.setCustomValidity('');
    }
});

document.getElementById('password_confirmation').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirm = this.value;
    
    if (confirm !== password) {
        this.setCustomValidity('As senhas não coincidem');
    } else {
        this.setCustomValidity('');
    }
});
</script>
@endpush
