<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Sistema de Chamados') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-8 col-lg-6 col-xl-5">
                    <div class="card shadow-lg border-0" style="border-radius: 20px; backdrop-filter: blur(20px); background: rgba(255, 255, 255, 0.95);">
                        <div class="card-body p-5">
                            <!-- Logo/Header -->
                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="bi bi-shield-lock" style="font-size: 4rem; color: #667eea;"></i>
                                </div>
                                <h1 class="h3 fw-bold text-dark mb-2">Sistema de Chamados</h1>
                                <p class="text-muted">Faça login para acessar o sistema</p>
                            </div>

                            <!-- Login Form -->
                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label text-dark fw-semibold">E-mail</label>
                                    <input type="email" 
                                           class="form-control form-control-lg @error('email') is-invalid @enderror" 
                                           id="email" 
                                           name="email" 
                                           value="{{ old('email') }}"
                                           placeholder="Digite seu e-mail"
                                           required 
                                           autofocus
                                           style="border-radius: 10px; border: 2px solid #e9ecef; padding: 12px 16px;">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <label for="password" class="form-label text-dark fw-semibold">Senha</label>
                                    <input type="password" 
                                           class="form-control form-control-lg @error('password') is-invalid @enderror" 
                                           id="password" 
                                           name="password" 
                                           placeholder="Digite sua senha"
                                           required
                                           style="border-radius: 10px; border: 2px solid #e9ecef; padding: 12px 16px;">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="mb-4">
                                    <div class="form-check">
                                        <input type="checkbox" 
                                               class="form-check-input" 
                                               id="remember" 
                                               name="remember"
                                               {{ old('remember') ? 'checked' : '' }}>
                                        <label class="form-check-label text-muted" for="remember">
                                            Manter-me conectado
                                        </label>
                                    </div>
                                </div>

                                <button type="submit" 
                                        class="btn btn-lg w-100 text-white fw-semibold"
                                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 10px; padding: 12px; transition: all 0.3s;">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>
                                    Entrar no Sistema
                                </button>
                            </form>

                            @if($errors->any())
                                <div class="alert alert-danger mt-3" style="border-radius: 10px;">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    @foreach($errors->all() as $error)
                                        {{ $error }}
                                    @endforeach
                                </div>
                            @endif

                            @if(session('error'))
                                <div class="alert alert-danger mt-3" style="border-radius: 10px;">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    {{ session('error') }}
                                </div>
                            @endif

                            <!-- Footer -->
                            <div class="text-center mt-4">
                                @if (Route::has('password.request'))
                                    <a href="{{ route('password.request') }}" class="text-muted text-decoration-none">
                                        <small>Esqueceu sua senha?</small>
                                    </a>
                                @endif
                            </div>

                            <!-- Contact Link -->
                            <div class="text-center mt-3">
                                <a href="{{ route('contact.index') }}" class="btn btn-outline-secondary btn-sm">
                                    <i class="bi bi-headset me-1"></i>
                                    Fale Conosco
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

