@extends('layouts.app')

@section('title', 'Acesso Negado')

@section('content')
<div class="container py-5 text-center">
    <h1 class="display-1 text-danger"><i class="bi bi-shield-lock"></i> 403</h1>
    <h2 class="mb-4">Acesso negado</h2>
    <p class="lead">Você não tem permissão para acessar esta funcionalidade.</p>
    @guest
        <a href="{{ route('login') }}" class="btn btn-primary btn-lg mt-3">
            <i class="bi bi-box-arrow-in-right"></i> Fazer login
        </a>
    @else
        <a href="{{ route('dashboard') }}" class="btn btn-secondary btn-lg mt-3">
            <i class="bi bi-house"></i> Voltar para o início
        </a>
    @endguest
</div>
@endsection
