@extends('layouts.app')

@section('styles')
<style>
/* Reset e configurações base */
* {
    box-sizing: border-box;
}

body.login-page {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    min-height: 100vh;
    margin: 0;
    padding: 0;
    font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    overflow-x: hidden;
}

/* Container principal */
.login-container {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
    position: relative;
}

/* Wrapper para layout lado a lado */
.login-wrapper {
    width: 100%;
    max-width: 980px;
    display: flex;
    align-items: stretch;
    justify-content: center;
    gap: 24px;
    position: relative;
    z-index: 1; /* acima do background decorativo */
}

/* Efeito de fundo animado */
.login-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.1"/><circle cx="10" cy="60" r="0.5" fill="white" opacity="0.1"/><circle cx="90" cy="40" r="0.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
    pointer-events: none;
}

/* Card principal de login */
.login-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow: 
        0 20px 40px rgba(0, 0, 0, 0.1),
        0 0 0 1px rgba(255, 255, 255, 0.2);
    padding: 40px 35px;
    width: 100%;
    max-width: 420px;
    position: relative;
    animation: slideInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

/* Card de informações de contato (ao lado do login) */
.contact-info-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(20px);
    border-radius: 20px;
    box-shadow:
        0 20px 40px rgba(0, 0, 0, 0.08),
        0 0 0 1px rgba(255, 255, 255, 0.3);
    padding: 28px;
    width: 100%;
    max-width: 420px;
    border: 1px solid rgba(255, 255, 255, 0.35);
    animation: slideInUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) 0.05s both;
}
/* Header do card */
.login-header {
    text-align: center;
    margin-bottom: 35px;
}

.login-logo {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.login-logo i {
    font-size: 28px;
    color: white;
}

.login-logo img {
    width: 34px;
    height: 34px;
    display: block;
}

.login-title {
    font-size: 28px;
    font-weight: 800;
    color: #1a202c;
    margin: 0 0 8px 0;
    letter-spacing: -0.5px;
}

.login-subtitle {
    font-size: 16px;
    color: #718096;
    margin: 0;
    font-weight: 400;
}

/* Formulário */
.login-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.form-group {
    position: relative;
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 8px;
    font-size: 14px;
    display: block;
    letter-spacing: 0.3px;
}

.form-control {
    width: 100%;
    padding: 14px 16px;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 16px;
    background: #f7fafc;
    transition: all 0.3s ease;
    outline: none;
    font-family: inherit;
}

.form-control:focus {
    border-color: #667eea;
    background: white;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
    transform: translateY(-1px);
}

.form-control::placeholder {
    color: #a0aec0;
}

/* Checkbox personalizado */
.form-check {
    display: flex;
    align-items: center;
    gap: 10px;
    margin: 10px 0;
}

.form-check-input {
    width: 18px;
    height: 18px;
    border: 2px solid #e2e8f0;
    border-radius: 4px;
    background: white;
    cursor: pointer;
    position: relative;
    margin: 0;
}

.form-check-input:checked {
    background: #667eea;
    border-color: #667eea;
}

.form-check-input:checked::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.form-check-label {
    font-size: 14px;
    color: #4a5568;
    cursor: pointer;
    user-select: none;
}

/* Botão de login */
.btn-login {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 12px;
    padding: 16px 24px;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
    letter-spacing: 0.5px;
    text-transform: uppercase;
    position: relative;
    overflow: hidden;
}

.btn-login::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
    transition: left 0.5s;
}

.btn-login:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
}

.btn-login:hover::before {
    left: 100%;
}

.btn-login:active {
    transform: translateY(0);
}

/* Link esqueceu senha */
.forgot-password {
    text-align: center;
    margin-top: 25px;
}

.forgot-link {
    color: #667eea;
    text-decoration: none;
    font-size: 14px;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
}

.forgot-link::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 0;
    height: 2px;
    background: #667eea;
    transition: width 0.3s ease;
}

.forgot-link:hover {
    color: #5a67d8;
}

.forgot-link:hover::after {
    width: 100%;
}

/* Alertas */
.alert {
    padding: 12px 16px;
    border-radius: 10px;
    margin-bottom: 20px;
    font-size: 14px;
    border: none;
}

.alert-danger {
    background: rgba(254, 178, 178, 0.2);
    color: #c53030;
    border-left: 4px solid #e53e3e;
}

/* Animações */
@keyframes slideInUp {
    from {
        opacity: 0;
        transform: translateY(40px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Responsividade */
@media (max-width: 480px) {
    .login-container {
        padding: 15px;
    }
    
    .login-wrapper {
        gap: 16px;
    }
    
    .login-card {
        padding: 30px 25px;
        border-radius: 16px;
    }
    
    .login-title {
        font-size: 24px;
    }
    
    .login-subtitle {
        font-size: 14px;
    }
    
    .form-control {
        padding: 12px 14px;
        font-size: 16px; /* Evita zoom no iOS */
    }
    
    .btn-login {
        padding: 14px 20px;
    }
}

@media (max-width: 360px) {
    .login-card {
        padding: 25px 20px;
    }
}

/* Quebra para empilhar em telas menores */
@media (max-width: 900px) {
    .login-wrapper {
        flex-direction: column;
        max-width: 520px;
    }
    .contact-info-card {
        max-width: 100%;
    }
}

/* Estados de loading */
.btn-login:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none;
}

.btn-login.loading::after {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    margin: auto;
    border: 2px solid transparent;
    border-top-color: white;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Melhorias de acessibilidade */
.form-control:focus-visible {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

.btn-login:focus-visible {
    outline: 2px solid #667eea;
    outline-offset: 2px;
}

/* Efeito de vidro no card */
.login-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
}

/* Card de informações de contato - estilos detalhados */

.contact-header {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 15px;
    color: #667eea;
    margin-bottom: 12px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(102, 126, 234, 0.2);
}

.contact-header i {
    font-size: 18px;
}

.contact-items {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-size: 13px;
    line-height: 1.6;
    color: #4a5568;
}

.contact-item i {
    font-size: 16px;
    color: #667eea;
    margin-top: 2px;
    flex-shrink: 0;
}

.contact-item strong {
    color: #2d3748;
    font-weight: 600;
}

.contact-item a {
    color: #667eea;
    text-decoration: none;
    font-weight: 600;
}

.contact-item a:hover {
    text-decoration: underline;
}

@media (max-width: 480px) {
    .contact-info-card {
        padding: 14px;
    }
    
    .contact-item {
        font-size: 12px;
    }
    
    .contact-header {
        font-size: 14px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    document.body.classList.add('login-page');
    
    // Adiciona efeito de loading no botão
    const form = document.querySelector('.login-form');
    const submitBtn = document.querySelector('.btn-login');
    
    if (form && submitBtn) {
        form.addEventListener('submit', function() {
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Entrando...';
        });
    }
    
    // Adiciona validação visual em tempo real
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.style.borderColor = '#e53e3e';
            } else {
                this.style.borderColor = '#48bb78';
            }
        });
        
        input.addEventListener('focus', function() {
            this.style.borderColor = '#667eea';
        });
    });
});
</script>
@endsection

@section('content')
<div class="login-container">
    <div class="login-wrapper">
        <div class="login-card">
        <div class="login-header">
            <div class="login-logo">
                <img src="{{ asset('favicon.svg') }}" alt="Ícone Suporte+ Saúde" />
            </div>
            <h1 class="login-title">
                <span style="color:#10b981; font-weight:800; letter-spacing:0.2px;">Suporte+</span>
                <span style="color:#f59e0b; font-weight:800; letter-spacing:0.2px;">Saúde</span>
            </h1>
            <p class="login-subtitle">Saúde - Guarapuava PR</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle me-2"></i>
                @foreach($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="login-form">
            @csrf
            
            <div class="form-group">
                <label for="login" class="form-label">E-mail ou Usuário</label>
                <input 
                    type="text" 
                    class="form-control @error('login') is-invalid @enderror" 
                    id="login" 
                    name="login" 
                    value="{{ old('login') }}"
                    placeholder="Digite seu e-mail ou usuário"
                    required 
                    autofocus
                    autocomplete="username"
                >
            </div>

            <div class="form-group">
                <label for="password" class="form-label">Senha</label>
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password" 
                    name="password" 
                    placeholder="Digite sua senha"
                    required
                    autocomplete="current-password"
                >
            </div>

            <div class="form-check">
                <input 
                    type="checkbox" 
                    class="form-check-input" 
                    id="remember" 
                    name="remember"
                    {{ old('remember') ? 'checked' : '' }}
                >
                <label class="form-check-label" for="remember">
                    Manter-me conectado
                </label>
            </div>

            <button type="submit" class="btn-login">
                Entrar no Sistema
            </button>
        </form>

        <div class="text-center mt-4">
            <small class="text-muted" style="font-size: 0.875rem;">
                <strong style="color: #667eea;">Ditis-Saúde</strong>
            </small>
        </div>
        </div>

        <!-- Informações de Contato ao lado do login -->
        <div class="contact-info-card">
            <div class="contact-header">
                <i class="bi bi-headset"></i>
                <span>Precisa de Ajuda?</span>
            </div>
            <div class="contact-items">
                <div class="contact-item">
                    <i class="bi bi-telephone-fill"></i>
                    <div>
                        <strong>Suporte:</strong> <a href="tel:+554231421512" aria-label="Ligar para Suporte (42) 3142-1512">(42) 3142-1512</a><br>
                        <strong>Administrativo:</strong> <a href="tel:+554231421527" aria-label="Ligar para Administrativo (42) 3142-1527">(42) 3142-1527</a>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="bi bi-whatsapp"></i>
                    <div>
                        <strong>WhatsApp Suporte:</strong> <a href="https://wa.me/554231421512" target="_blank" rel="noopener" aria-label="Abrir WhatsApp Suporte (42) 3142-1512">(42) 3142-1512</a><br>
                        <strong>WhatsApp Admin:</strong> <a href="https://wa.me/554231421527" target="_blank" rel="noopener" aria-label="Abrir WhatsApp Admin (42) 3142-1527">(42) 3142-1527</a><br>
                        <strong>WhatsApp CNES:</strong> <a href="https://wa.me/5542991452300" target="_blank" rel="noopener" aria-label="Abrir WhatsApp CNES (42) 99145-2300">(42) 99145-2300</a><br>
                        <strong>Sobreaviso:</strong> <a href="https://wa.me/5542991235068" target="_blank" rel="noopener" aria-label="Abrir WhatsApp Sobreaviso (42) 99123-5068">(42) 99123-5068</a>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="bi bi-envelope-fill"></i>
                    <div>
                        <strong>E-mail:</strong> <a href="https://mail.google.com/mail/?view=cm&fs=1&to=dtisaude@guarapuava.pr.gov.br" target="_blank" rel="noopener" aria-label="Enviar e-mail para dtisaude@guarapuava.pr.gov.br no Gmail">dtisaude@guarapuava.pr.gov.br</a>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="bi bi-globe2"></i>
                    <div>
                        <strong>Site Oficial:</strong> <a href="https://suportesaudeguarapuava.com.br" target="_blank" rel="noopener" aria-label="Abrir site oficial suportesaudeguarapuava.com.br">suportesaudeguarapuava.com.br</a>
                    </div>
                </div>
                <div class="contact-item">
                    <i class="bi bi-clock-fill"></i>
                    <div>
                        <strong>Horário:</strong><br>
                        Segunda-Sexta: 08:00-12:00 e 13:00-17:00
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
