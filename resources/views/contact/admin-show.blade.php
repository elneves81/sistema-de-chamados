@extends('layouts.app')

@section('title', 'Mensagem de Contato #' . $contactMessage->id)

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="mb-1">
                        <i class="bi bi-envelope-open"></i> 
                        Mensagem de Contato #{{ $contactMessage->id }}
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.contact.list') }}">Mensagens de Contato</a></li>
                            <li class="breadcrumb-item active">Mensagem #{{ $contactMessage->id }}</li>
                        </ol>
                    </nav>
                </div>
                <a href="{{ route('admin.contact.list') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Coluna Principal -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header {{ $contactMessage->status === 'pendente' ? 'bg-warning' : ($contactMessage->status === 'em_andamento' ? 'bg-info' : 'bg-success') }} text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-left-text"></i> {{ $contactMessage->subject }}
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-person"></i> Nome:</strong><br>
                                {{ $contactMessage->name }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2">
                                <strong><i class="bi bi-envelope"></i> Email:</strong><br>
                                <a href="mailto:{{ $contactMessage->email }}">{{ $contactMessage->email }}</a>
                            </p>
                        </div>
                    </div>

                    @if($contactMessage->phone)
                    <div class="mb-4">
                        <p class="mb-2">
                            <strong><i class="bi bi-telephone"></i> Telefone:</strong><br>
                            <a href="tel:{{ $contactMessage->phone }}">{{ $contactMessage->phone }}</a>
                        </p>
                    </div>
                    @endif

                    <div class="message-content">
                        <h6 class="fw-bold mb-3"><i class="bi bi-chat-dots"></i> Mensagem:</h6>
                        <div class="message-text p-3 bg-light rounded">
                            {{ $contactMessage->message }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Resposta do Admin -->
            @if($contactMessage->admin_response)
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-reply-fill"></i> Resposta do Sistema
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <small class="text-muted">
                            <i class="bi bi-person"></i> Respondido por: {{ $contactMessage->respondedBy->name ?? 'Sistema' }}
                            <br>
                            <i class="bi bi-calendar"></i> {{ $contactMessage->responded_at ? $contactMessage->responded_at->format('d/m/Y H:i') : 'N/A' }}
                        </small>
                    </div>
                    <div class="response-text p-3 bg-light rounded mt-2">
                        {!! nl2br(e($contactMessage->admin_response)) !!}
                    </div>
                </div>
            </div>
            @endif

            <!-- Formulário de Resposta -->
            @if($contactMessage->status !== 'resolvido')
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-send"></i> Enviar Resposta
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.contact.respond', $contactMessage) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="admin_response" class="form-label fw-bold">
                                Sua Resposta <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control @error('admin_response') is-invalid @enderror" 
                                      id="admin_response" 
                                      name="admin_response" 
                                      rows="6" 
                                      required 
                                      placeholder="Digite sua resposta aqui...">{{ old('admin_response') }}</textarea>
                            @error('admin_response')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle"></i> A resposta será enviada por email para {{ $contactMessage->email }}
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="status" class="form-label fw-bold">
                                Atualizar Status <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('status') is-invalid @enderror" id="status" name="status" required>
                                <option value="em_andamento" {{ $contactMessage->status === 'em_andamento' ? 'selected' : '' }}>
                                    Em Andamento
                                </option>
                                <option value="resolvido">Resolvido</option>
                            </select>
                            @error('status')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-send-fill"></i> Enviar Resposta e Atualizar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @endif
        </div>

        <!-- Coluna Lateral -->
        <div class="col-lg-4">
            <!-- Card de Status -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-info-circle"></i> Informações</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Status Atual:</strong><br>
                        @if($contactMessage->status === 'pendente')
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="bi bi-clock"></i> Pendente
                            </span>
                        @elseif($contactMessage->status === 'em_andamento')
                            <span class="badge bg-info fs-6">
                                <i class="bi bi-hourglass-split"></i> Em Andamento
                            </span>
                        @elseif($contactMessage->status === 'resolvido')
                            <span class="badge bg-success fs-6">
                                <i class="bi bi-check-circle"></i> Resolvido
                            </span>
                        @else
                            <span class="badge bg-secondary fs-6">
                                <i class="bi bi-archive"></i> Arquivado
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong><i class="bi bi-calendar-plus"></i> Recebida em:</strong><br>
                        {{ $contactMessage->created_at->format('d/m/Y H:i') }}<br>
                        <small class="text-muted">{{ $contactMessage->created_at->diffForHumans() }}</small>
                    </div>

                    @if($contactMessage->responded_at)
                    <div class="mb-3">
                        <strong><i class="bi bi-calendar-check"></i> Respondida em:</strong><br>
                        {{ $contactMessage->responded_at->format('d/m/Y H:i') }}<br>
                        <small class="text-muted">{{ $contactMessage->responded_at->diffForHumans() }}</small>
                    </div>
                    @endif

                    @if($contactMessage->respondedBy)
                    <div class="mb-3">
                        <strong><i class="bi bi-person-check"></i> Respondida por:</strong><br>
                        {{ $contactMessage->respondedBy->name }}
                    </div>
                    @endif
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="card shadow-sm">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0"><i class="bi bi-lightning"></i> Ações Rápidas</h6>
                </div>
                <div class="card-body">
                    @if($contactMessage->status !== 'resolvido')
                    <form action="{{ route('admin.contact.updateStatus', $contactMessage) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="resolvido">
                        <button type="submit" class="btn btn-success w-100 mb-2" 
                                onclick="return confirm('Marcar como resolvida?')">
                            <i class="bi bi-check-circle"></i> Marcar como Resolvida
                        </button>
                    </form>
                    @endif

                    <a href="mailto:{{ $contactMessage->email }}?subject=Re: {{ $contactMessage->subject }}" 
                       class="btn btn-outline-primary w-100 mb-2">
                        <i class="bi bi-envelope"></i> Responder por Email
                    </a>

                    @if($contactMessage->phone)
                    <a href="tel:{{ $contactMessage->phone }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-telephone"></i> Ligar
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.message-text,
.response-text {
    font-size: 1rem;
    line-height: 1.6;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.card {
    border: none;
}

.badge.fs-6 {
    font-size: 1rem !important;
    padding: 0.5rem 0.75rem;
}
</style>
@endpush
@endsection
