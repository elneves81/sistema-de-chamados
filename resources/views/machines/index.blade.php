@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-pc-display-horizontal"></i> Inventário de Máquinas</h2>
                    <p class="text-muted">Gestão completa do parque tecnológico</p>
                </div>
                <div>
                    @if(auth()->user()->hasPermission('machines.create') || auth()->user()->role === 'admin' || auth()->user()->role === 'technician')
                    <a href="{{ route('machines.create') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-plus-circle"></i> Nova Máquina
                    </a>
                    <a href="{{ route('machines.create.tablet') }}" class="btn btn-success btn-lg ms-2">
                        <i class="bi bi-tablet"></i> Cadastro Tablet
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-primary">{{ $stats['total'] }}</h3>
                    <p class="text-muted mb-0">Total de Máquinas</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-success">{{ $stats['ativas'] }}</h3>
                    <p class="text-muted mb-0">Ativas</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-warning">{{ $stats['manutencao'] }}</h3>
                    <p class="text-muted mb-0">Em Manutenção</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center">
                <div class="card-body">
                    <h3 class="text-info">{{ $stats['vinculadas'] }}</h3>
                    <p class="text-muted mb-0">Vinculadas</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h3 class="text-warning">
                        <i class="bi bi-clock-history"></i> {{ $stats['assinaturas_pendentes'] }}
                    </h3>
                    <p class="text-muted mb-0">Assinaturas Pendentes</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h3 class="text-success">
                        <i class="bi bi-check-circle-fill"></i> {{ $stats['assinaturas_validadas'] }}
                    </h3>
                    <p class="text-muted mb-0">Assinaturas Validadas</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('machines.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Pesquisar</label>
                    <input type="text" name="search" class="form-control" placeholder="Patrimônio, série, modelo..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Tipo</label>
                    <select name="tipo" class="form-select">
                        <option value="">Todos</option>
                        <option value="desktop" {{ request('tipo') == 'desktop' ? 'selected' : '' }}>Desktop</option>
                        <option value="notebook" {{ request('tipo') == 'notebook' ? 'selected' : '' }}>Notebook</option>
                        <option value="servidor" {{ request('tipo') == 'servidor' ? 'selected' : '' }}>Servidor</option>
                        <option value="monitor" {{ request('tipo') == 'monitor' ? 'selected' : '' }}>Monitor</option>
                        <option value="impressora" {{ request('tipo') == 'impressora' ? 'selected' : '' }}>Impressora</option>
                        <option value="nobreak" {{ request('tipo') == 'nobreak' ? 'selected' : '' }}>Nobreak</option>
                        <option value="estabilizador" {{ request('tipo') == 'estabilizador' ? 'selected' : '' }}>Estabilizador</option>
                        <option value="switch" {{ request('tipo') == 'switch' ? 'selected' : '' }}>Switch</option>
                        <option value="teclado" {{ request('tipo') == 'teclado' ? 'selected' : '' }}>Teclado</option>
                        <option value="mouse" {{ request('tipo') == 'mouse' ? 'selected' : '' }}>Mouse</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="ativo" {{ request('status') == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ request('status') == 'inativo' ? 'selected' : '' }}>Inativo</option>
                        <option value="manutencao" {{ request('status') == 'manutencao' ? 'selected' : '' }}>Manutenção</option>
                        <option value="descartado" {{ request('status') == 'descartado' ? 'selected' : '' }}>Descartado</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Status Assinatura</label>
                    <select name="assinatura_status" class="form-select">
                        <option value="">Todos</option>
                        <option value="nao_requerida" {{ request('assinatura_status') == 'nao_requerida' ? 'selected' : '' }}>Não Requerida</option>
                        <option value="pendente" {{ request('assinatura_status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="validada" {{ request('assinatura_status') == 'validada' ? 'selected' : '' }}>Validada</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Filtrar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabela -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Patrimônio</th>
                            <th>Modelo</th>
                            <th>Tipo</th>
                            <th>Nº Série</th>
                            <th>Usuário Vinculado</th>
                            <th>Status</th>
                            <th>Assinatura</th>
                            <th width="150">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($machines as $machine)
                        <tr>
                            <td><strong>{{ $machine->patrimonio }}</strong></td>
                            <td>
                                {{ $machine->marca }} {{ $machine->modelo }}<br>
                                <small class="text-muted">{{ $machine->sistema_operacional }}</small>
                            </td>
                            <td>{!! $machine->tipo_badge !!}</td>
                            <td><code>{{ $machine->numero_serie }}</code></td>
                            <td>
                                @if($machine->user)
                                    <i class="bi bi-person-circle"></i> {{ $machine->user->name }}
                                @else
                                    <span class="text-muted">Não vinculada</span>
                                @endif
                            </td>
                            <td>{!! $machine->status_badge !!}</td>
                            <td>
                                {!! $machine->assinatura_status_badge !!}
                                @if($machine->assinatura_status === 'pendente')
                                <button class="btn btn-sm btn-outline-primary mt-1 validate-signature-btn" 
                                        data-machine-id="{{ $machine->id }}" 
                                        data-machine-patrimonio="{{ $machine->patrimonio }}"
                                        title="Validar Assinatura">
                                    <i class="bi bi-shield-check"></i> Validar
                                </button>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('machines.show', $machine) }}" class="btn btn-info" title="Visualizar">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @can('machines.edit')
                                    <a href="{{ route('machines.edit', $machine) }}" class="btn btn-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @endcan
                                    @can('machines.delete')
                                    <form action="{{ route('machines.destroy', $machine) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Excluir">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="bi bi-inbox" style="font-size: 3rem; color: #ccc;"></i>
                                <p class="text-muted mt-2">Nenhuma máquina encontrada</p>
                                @can('machines.create')
                                <a href="{{ route('machines.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Cadastrar Primeira Máquina
                                </a>
                                @endcan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $machines->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Modal de Validação de Assinatura -->
<div class="modal fade" id="validateSignatureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-shield-check"></i> Validar Assinatura Digital
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i>
                    Validando assinatura para máquina <strong id="modal-patrimonio"></strong>
                </div>
                
                <form id="validateSignatureForm">
                    @csrf
                    <input type="hidden" id="validate-machine-id" name="machine_id">
                    
                    <div class="mb-3">
                        <label for="validate-user-select" class="form-label">
                            <i class="bi bi-person-badge"></i> Selecionar Usuário
                        </label>
                        <input type="text" class="form-control mb-2" id="search-user-index" 
                               placeholder="Digite para buscar usuário..." autocomplete="off">
                        <select class="form-select" id="validate-user-select" required>
                            <option value="">Selecione o usuário...</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $user)
                                <option value="{{ $user->username }}" data-name="{{ strtolower($user->name) }}" data-username="{{ strtolower($user->username) }}">
                                    {{ $user->name }} ({{ $user->username }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Selecione o usuário que receberá a máquina</small>
                    </div>

                    <div class="mb-3">
                        <label for="validate-password" class="form-label">
                            <i class="bi bi-key"></i> Senha LDAP do Usuário
                        </label>
                        <input type="password" class="form-control" id="validate-password" 
                               name="password" required placeholder="Digite a senha de rede do usuário" autocomplete="off">
                        <small class="text-muted">Digite a senha de rede (LDAP) do usuário selecionado</small>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="validate-third-party" name="validacao_terceiro">
                        <label class="form-check-label" for="validate-third-party">
                            Sou terceiro validando esta assinatura
                        </label>
                    </div>

                    <div id="validate-error-message" class="alert alert-danger d-none"></div>
                    <div id="validate-success-message" class="alert alert-success d-none"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Cancelar
                </button>
                <button type="button" class="btn btn-success" id="confirmValidateBtn">
                    <i class="bi bi-check-circle"></i> Validar Assinatura
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Forçar alertas abaixo do modal */
.alert {
    z-index: 100 !important;
}

/* Modal na frente de tudo */
#validateSignatureModal {
    z-index: 99999 !important;
}

#validateSignatureModal .modal-dialog {
    z-index: 100000 !important;
}

#validateSignatureModal .modal-content {
    z-index: 100001 !important;
}

.modal-backdrop {
    z-index: 99998 !important;
    pointer-events: none !important;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔧 Inicializando modal de validação...');
    
    const modalElement = document.getElementById('validateSignatureModal');
    let validateModal = null;
    
    try {
        validateModal = new bootstrap.Modal(modalElement);
    } catch(e) {
        console.error('❌ Erro ao criar modal:', e);
    }
    
    const validateForm = document.getElementById('validateSignatureForm');
    const confirmBtn = document.getElementById('confirmValidateBtn');
    const errorDiv = document.getElementById('validate-error-message');
    const successDiv = document.getElementById('validate-success-message');

    // Filtro de busca de usuários
    const searchUserInput = document.getElementById('search-user-index');
    const userSelect = document.getElementById('validate-user-select');
    
    if (searchUserInput && userSelect) {
        searchUserInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = userSelect.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') return; // Pular opção vazia
                
                const name = option.dataset.name || '';
                const username = option.dataset.username || '';
                
                if (name.includes(searchTerm) || username.includes(searchTerm)) {
                    option.style.display = '';
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Auto-selecionar se houver apenas uma opção visível
            const visibleOptions = Array.from(options).filter(opt => opt.style.display !== 'none' && opt.value !== '');
            if (visibleOptions.length === 1) {
                userSelect.value = visibleOptions[0].value;
                visibleOptions[0].selected = true;
            }
        });
        
        // Garantir que clique na opção a selecione
        userSelect.addEventListener('change', function(e) {
            console.log('Usuário selecionado:', this.value);
        });
        
        userSelect.addEventListener('click', function(e) {
            if (e.target.tagName === 'OPTION' && e.target.value) {
                this.value = e.target.value;
                e.target.selected = true;
                this.dispatchEvent(new Event('change'));
            }
        });
        
        // Permitir Enter para selecionar
        searchUserInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                const visibleOptions = Array.from(userSelect.querySelectorAll('option')).filter(
                    opt => opt.style.display !== 'none' && opt.value !== ''
                );
                if (visibleOptions.length > 0) {
                    userSelect.value = visibleOptions[0].value;
                    visibleOptions[0].selected = true;
                    document.getElementById('validate-password').focus();
                }
            }
        });
    }

    // Abrir modal ao clicar em validar
    document.querySelectorAll('.validate-signature-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            console.log('🖱️ Botão validar clicado');
            
            const machineId = this.dataset.machineId;
            const patrimonio = this.dataset.machinePatrimonio;
            
            document.getElementById('validate-machine-id').value = machineId;
            document.getElementById('modal-patrimonio').textContent = patrimonio;
            
            // Limpar campos
            validateForm.reset();
            errorDiv.classList.add('d-none');
            successDiv.classList.add('d-none');
            
            // Mostrar modal
            if (validateModal) {
                validateModal.show();
                console.log('✅ Modal aberto');
            }
        });
    });
    
    // Debug: verificar se inputs estão acessíveis após modal aberto
    modalElement.addEventListener('shown.bs.modal', function() {
        console.log('📋 Modal totalmente visível');
        
        // Forçar remoção de qualquer bloqueio
        const backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.style.pointerEvents = 'none';
            console.log('🛡️ Backdrop desabilitado para cliques');
        }
        
        // Garantir que o modal-dialog aceite cliques
        const modalDialog = modalElement.querySelector('.modal-dialog');
        if (modalDialog) {
            modalDialog.style.pointerEvents = 'auto';
            modalDialog.style.zIndex = '9999';
        }
        
        // Garantir que todos os inputs aceitem cliques
        const allInputs = modalElement.querySelectorAll('input, button, select, textarea');
        allInputs.forEach(input => {
            input.style.pointerEvents = 'auto';
            input.removeAttribute('disabled');
            input.removeAttribute('readonly');
        });
        
        const userSelect = document.getElementById('validate-user-select');
        if (userSelect) {
            setTimeout(() => {
                userSelect.focus();
                console.log('🎯 Foco no select de usuário');
            }, 100);
        }
    });

    // Validar assinatura
    confirmBtn.addEventListener('click', async function() {
        errorDiv.classList.add('d-none');
        successDiv.classList.add('d-none');

        const machineId = document.getElementById('validate-machine-id').value;
        const username = document.getElementById('validate-user-select').value;
        const password = document.getElementById('validate-password').value;
        const isTerceiro = document.getElementById('validate-third-party').checked;

        console.log('📤 Enviando validação:', { machineId, username, isTerceiro });

        if (!username || !password) {
            errorDiv.textContent = 'Por favor, selecione o usuário e digite a senha.';
            errorDiv.classList.remove('d-none');
            return;
        }

        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Validando...';

        try {
            // Validar credenciais LDAP e atualizar status da assinatura
            const response = await fetch('/machines/validate-signature', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    login: username,
                    senha: password,
                    machine_id: machineId,
                    validar_por_terceiro: isTerceiro
                })
            });

            console.log('📥 Status da resposta:', response.status);
            const data = await response.json();
            console.log('📥 Dados recebidos:', data);

            if (!response.ok || !data.success) {
                throw new Error(data.message || 'Credenciais inválidas ou erro ao validar assinatura');
            }

            successDiv.textContent = 'Assinatura validada com sucesso!';
            successDiv.classList.remove('d-none');
            console.log('✅ Assinatura validada com sucesso');

            setTimeout(() => {
                if (validateModal) {
                    validateModal.hide();
                }
                location.reload(); // Recarregar para atualizar badge
            }, 1500);

        } catch (error) {
            console.error('❌ Erro na validação:', error);
            errorDiv.textContent = error.message;
            errorDiv.classList.remove('d-none');
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="bi bi-check-circle"></i> Validar Assinatura';
        }
    });
});
</script>
@endpush

@endsection
