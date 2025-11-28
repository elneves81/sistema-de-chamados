@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-12">
            <h2>
                <i class="bi bi-pencil-square"></i> Editar Máquina
            </h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('machines.index') }}">Inventário</a></li>
                    <li class="breadcrumb-item active">Editar</li>
                </ol>
            </nav>
        </div>
    </div>

    <form action="{{ route('machines.update', $machine) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                <i class="bi bi-info-circle"></i> Informações Básicas
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" id="labelPatrimonioEdit">Patrimônio <span class="text-danger">*</span></label>
                            <input type="text" name="patrimonio" id="inputPatrimonioEdit" class="form-control @error('patrimonio') is-invalid @enderror" 
                                   value="{{ old('patrimonio', $machine->patrimonio) }}" required>
                            @error('patrimonio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label" id="labelNumeroSerieEdit">Número de Série <span class="text-danger">*</span></label>
                            <input type="text" name="numero_serie" id="inputNumeroSerieEdit" class="form-control @error('numero_serie') is-invalid @enderror" 
                                   value="{{ old('numero_serie', $machine->numero_serie) }}" required>
                            @error('numero_serie')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Tipo <span class="text-danger">*</span></label>
                            <select name="tipo" id="tipoEquipamentoEdit" class="form-select @error('tipo') is-invalid @enderror" required>
                                <option value="">Selecione</option>
                                <option value="desktop" {{ old('tipo', $machine->tipo) == 'desktop' ? 'selected' : '' }}>Desktop</option>
                                <option value="notebook" {{ old('tipo', $machine->tipo) == 'notebook' ? 'selected' : '' }}>Notebook</option>
                                <option value="servidor" {{ old('tipo', $machine->tipo) == 'servidor' ? 'selected' : '' }}>Servidor</option>
                                <option value="monitor" {{ old('tipo', $machine->tipo) == 'monitor' ? 'selected' : '' }}>Monitor</option>
                                <option value="impressora" {{ old('tipo', $machine->tipo) == 'impressora' ? 'selected' : '' }}>Impressora</option>
                                <option value="nobreak" {{ old('tipo', $machine->tipo) == 'nobreak' ? 'selected' : '' }}>Nobreak</option>
                                <option value="estabilizador" {{ old('tipo', $machine->tipo) == 'estabilizador' ? 'selected' : '' }}>Estabilizador</option>
                                <option value="switch" {{ old('tipo', $machine->tipo) == 'switch' ? 'selected' : '' }}>Switch</option>
                                <option value="teclado" {{ old('tipo', $machine->tipo) == 'teclado' ? 'selected' : '' }}>Teclado</option>
                                <option value="mouse" {{ old('tipo', $machine->tipo) == 'mouse' ? 'selected' : '' }}>Mouse</option>
                            </select>
                            @error('tipo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Marca/Fabricante</label>
                            <input type="text" name="marca" class="form-control @error('marca') is-invalid @enderror" 
                                   value="{{ old('marca', $machine->marca) }}" placeholder="Ex: Dell, HP, Lenovo">
                            @error('marca')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Modelo <span class="text-danger">*</span></label>
                            <input type="text" name="modelo" class="form-control @error('modelo') is-invalid @enderror" 
                                   value="{{ old('modelo', $machine->modelo) }}" required placeholder="Ex: OptiPlex 7090">
                            @error('modelo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Descrição</label>
                    <textarea name="descricao" class="form-control @error('descricao') is-invalid @enderror" rows="2">{{ old('descricao', $machine->descricao) }}</textarea>
                    @error('descricao')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <i class="bi bi-cpu"></i> Especificações Técnicas
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Processador</label>
                            <input type="text" name="processador" class="form-control" 
                                   value="{{ old('processador', $machine->processador) }}" placeholder="Ex: Intel i5-11400">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Memória RAM</label>
                            <input type="text" name="memoria_ram" class="form-control" 
                                   value="{{ old('memoria_ram', $machine->memoria_ram) }}" placeholder="Ex: 8GB DDR4">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-3">
                            <label class="form-label">Armazenamento</label>
                            <input type="text" name="armazenamento" class="form-control" 
                                   value="{{ old('armazenamento', $machine->armazenamento) }}" placeholder="Ex: 256GB SSD">
                        </div>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sistema Operacional</label>
                    <input type="text" name="sistema_operacional" class="form-control" 
                           value="{{ old('sistema_operacional', $machine->sistema_operacional) }}" placeholder="Ex: Windows 11 Pro">
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-arrow-left-right"></i> Troca de Equipamento
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_troca" name="is_troca" value="1" 
                               {{ old('is_troca', $machine->is_troca) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_troca">
                            <strong>É uma troca de equipamento?</strong>
                        </label>
                    </div>
                    <small class="text-muted">Marque se este equipamento está substituindo outro</small>
                </div>

                <div id="camposTroca" style="display: {{ old('is_troca', $machine->is_troca) ? 'block' : 'none' }};">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Patrimônio do Equipamento Substituído</label>
                                <input type="text" name="patrimonio_substituido" class="form-control @error('patrimonio_substituido') is-invalid @enderror" 
                                       value="{{ old('patrimonio_substituido', $machine->patrimonio_substituido) }}" 
                                       placeholder="Ex: 654321">
                                @error('patrimonio_substituido')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Patrimônio da máquina antiga</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Motivo da Troca</label>
                                <textarea name="motivo_troca" class="form-control @error('motivo_troca') is-invalid @enderror" 
                                          rows="3" placeholder="Descreva o motivo da troca...">{{ old('motivo_troca', $machine->motivo_troca) }}</textarea>
                                @error('motivo_troca')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-success text-white">
                <i class="bi bi-person-check"></i> Vinculação e Status
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Usuário Vinculado</label>
                            <select name="user_id" class="form-select @error('user_id') is-invalid @enderror">
                                <option value="">Sem vínculo</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}" {{ old('user_id', $machine->user_id) == $user->id ? 'selected' : '' }}>
                                        {{ $user->name }} ({{ $user->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('user_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Usuário responsável por esta máquina</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Status <span class="text-danger">*</span></label>
                            <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                                <option value="ativo" {{ old('status', $machine->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                                <option value="inativo" {{ old('status', $machine->status) == 'inativo' ? 'selected' : '' }}>Inativo</option>
                                <option value="manutencao" {{ old('status', $machine->status) == 'manutencao' ? 'selected' : '' }}>Manutenção</option>
                                <option value="descartado" {{ old('status', $machine->status) == 'descartado' ? 'selected' : '' }}>Descartado</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-warning text-dark">
                <i class="bi bi-calendar-dollar"></i> Informações Financeiras
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Data de Aquisição</label>
                            <input type="date" name="data_aquisicao" class="form-control" 
                                   value="{{ old('data_aquisicao', $machine->data_aquisicao?->format('Y-m-d')) }}">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Valor de Aquisição (R$)</label>
                            <input type="number" name="valor_aquisicao" class="form-control" 
                                   value="{{ old('valor_aquisicao', $machine->valor_aquisicao) }}" step="0.01" min="0" placeholder="0.00">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3">
            <div class="card-header bg-secondary text-white">
                <i class="bi bi-chat-left-text"></i> Observações
            </div>
            <div class="card-body">
                <textarea name="observacoes" class="form-control" rows="3" placeholder="Informações adicionais...">{{ old('observacoes', $machine->observacoes) }}</textarea>
            </div>
        </div>

        @can('machines.edit')
        <div class="card mb-3 border-info">
            <div class="card-header bg-info text-white">
                <i class="bi bi-pen"></i> Solicitar Nova Assinatura (Admin/Técnico)
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> <strong>Somente Administrador:</strong> 
                    Marque a opção abaixo para solicitar nova assinatura ao alterar o recebedor ou revincular a máquina.
                </div>
                
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="solicitar_nova_assinatura" name="solicitar_nova_assinatura" value="1">
                        <label class="form-check-label" for="solicitar_nova_assinatura">
                            <strong>Solicitar nova assinatura do recebedor</strong>
                        </label>
                    </div>
                    <small class="text-muted">Ao marcar, os campos abaixo ficam disponíveis para nova coleta</small>
                </div>

                <div id="novaAssinaturaFields" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Novo Recebedor</label>
                        <select name="recebedor_id" class="form-select" id="novo_recebedor">
                            <option value="">Selecione um usuário</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('recebedor_id', $machine->recebedor_id) == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }} ({{ $user->email }})
                                </option>
                            @endforeach
                        </select>
                        <small class="text-muted">Quem receberá a máquina</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nome Legível (como aparece no documento)</label>
                        <input type="text" class="form-control" name="nome_legivel_assinatura" id="nome_legivel_edit" 
                               value="{{ old('nome_legivel_assinatura', $machine->nome_legivel_assinatura) }}"
                               placeholder="Digite o nome completo do recebedor">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Assinatura Digital</label>
                        <div class="signature-container" style="border: 3px dashed #0dcaf0; border-radius: 14px; background: #f8f9fa; padding: 14px;">
                            <canvas id="signatureCanvasEdit" style="border: 2px solid #dee2e6; border-radius: 10px; background: #fff; touch-action: none; width: 100%; height: 200px;"></canvas>
                            <div class="mt-3 d-flex gap-2">
                                <button type="button" class="btn btn-outline-danger btn-sm" id="clearSignatureEdit">
                                    <i class="bi bi-trash"></i> Limpar
                                </button>
                                <button type="button" class="btn btn-outline-success btn-sm" id="saveSignatureEdit">
                                    <i class="bi bi-check-circle"></i> Confirmar
                                </button>
                            </div>
                            <div id="signatureStatusEdit" class="mt-2 p-2 rounded text-center bg-warning text-dark">
                                <i class="bi bi-exclamation-triangle"></i> Assinatura não coletada
                            </div>
                        </div>
                        <input type="hidden" id="assinatura_digital_edit" name="assinatura_digital">
                        @if($machine->assinatura_digital)
                            <div class="mt-2 p-2 bg-light border rounded">
                                <strong>Assinatura Atual:</strong><br>
                                <img src="{{ $machine->assinatura_digital }}" alt="Assinatura Atual" class="img-fluid mt-2" style="max-height: 100px;">
                                @if($machine->nome_legivel_assinatura)
                                    <p class="mb-0 mt-2"><strong>Nome:</strong> {{ $machine->nome_legivel_assinatura }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <div class="d-flex justify-content-between">
            <a href="{{ route('machines.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Cancelar
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save"></i> Atualizar Máquina
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle patrimônio e número de série conforme tipo
    const tipoEquipamentoEdit = document.getElementById('tipoEquipamentoEdit');
    const inputPatrimonioEdit = document.getElementById('inputPatrimonioEdit');
    const labelPatrimonioEdit = document.getElementById('labelPatrimonioEdit');
    const inputNumeroSerieEdit = document.getElementById('inputNumeroSerieEdit');
    const labelNumeroSerieEdit = document.getElementById('labelNumeroSerieEdit');
    
    if (tipoEquipamentoEdit) {
        // Função para atualizar campos
        function atualizarCampos() {
            const tipo = tipoEquipamentoEdit.value;
            const semPatrimonio = ['teclado', 'mouse'];
            
            // Patrimônio: opcional apenas para teclado e mouse
            if (semPatrimonio.includes(tipo)) {
                inputPatrimonioEdit.removeAttribute('required');
                labelPatrimonioEdit.innerHTML = 'Patrimônio (opcional)';
                inputPatrimonioEdit.placeholder = 'Opcional para este tipo';
            } else {
                inputPatrimonioEdit.setAttribute('required', 'required');
                labelPatrimonioEdit.innerHTML = 'Patrimônio <span class="text-danger">*</span>';
                inputPatrimonioEdit.placeholder = '';
            }
            
            // Número de série: obrigatório apenas para desktop
            if (tipo === 'desktop') {
                inputNumeroSerieEdit.setAttribute('required', 'required');
                labelNumeroSerieEdit.innerHTML = 'Número de Série <span class="text-danger">*</span>';
            } else {
                inputNumeroSerieEdit.removeAttribute('required');
                labelNumeroSerieEdit.innerHTML = 'Número de Série (opcional)';
            }
        }
        
        // Executa ao carregar (caso já tenha um tipo selecionado)
        atualizarCampos();
        
        // Executa ao mudar o tipo
        tipoEquipamentoEdit.addEventListener('change', atualizarCampos);
    }
    
    // Toggle campos de troca
    const trocaCheckbox = document.getElementById('is_troca');
    const camposTroca = document.getElementById('camposTroca');
    
    if (trocaCheckbox) {
        trocaCheckbox.addEventListener('change', function() {
            camposTroca.style.display = this.checked ? 'block' : 'none';
        });
    }
    
    const checkbox = document.getElementById('solicitar_nova_assinatura');
    const fields = document.getElementById('novaAssinaturaFields');
    const canvas = document.getElementById('signatureCanvasEdit');
    
    if (!checkbox || !canvas) return;
    
    const ctx = canvas.getContext('2d');
    let drawing = false, hasSignature = false;
    
    // Toggle fields visibility
    checkbox.addEventListener('change', function() {
        fields.style.display = this.checked ? 'block' : 'none';
        if (this.checked) {
            resizeCanvas();
        }
    });
    
    // Canvas resize
    function resizeCanvas() {
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width;
        canvas.height = 200;
    }
    
    // Drawing setup
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000';
    
    function getPointer(e) {
        const rect = canvas.getBoundingClientRect();
        const point = e.touches ? e.touches[0] : e;
        return {
            x: point.clientX - rect.left,
            y: point.clientY - rect.top
        };
    }
    
    function startDrawing(e) {
        drawing = true;
        const {x, y} = getPointer(e);
        ctx.beginPath();
        ctx.moveTo(x, y);
    }
    
    function draw(e) {
        if (!drawing) return;
        const {x, y} = getPointer(e);
        ctx.lineTo(x, y);
        ctx.stroke();
        hasSignature = true;
    }
    
    function stopDrawing() {
        drawing = false;
    }
    
    // Event listeners
    ['mousedown', 'touchstart'].forEach(ev => {
        canvas.addEventListener(ev, (e) => {
            e.preventDefault();
            startDrawing(e);
        });
    });
    
    ['mousemove', 'touchmove'].forEach(ev => {
        canvas.addEventListener(ev, (e) => {
            e.preventDefault();
            draw(e);
        });
    });
    
    ['mouseup', 'mouseleave', 'touchend', 'touchcancel'].forEach(ev => {
        canvas.addEventListener(ev, stopDrawing);
    });
    
    // Clear button
    document.getElementById('clearSignatureEdit').addEventListener('click', function() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSignature = false;
        document.getElementById('assinatura_digital_edit').value = '';
        updateStatus(false);
    });
    
    // Save button
    document.getElementById('saveSignatureEdit').addEventListener('click', function() {
        if (!hasSignature) {
            alert('Faça a assinatura antes de confirmar.');
            return;
        }
        const dataURL = canvas.toDataURL('image/png');
        document.getElementById('assinatura_digital_edit').value = dataURL;
        updateStatus(true);
    });
    
    function updateStatus(confirmed) {
        const status = document.getElementById('signatureStatusEdit');
        if (confirmed) {
            status.className = 'mt-2 p-2 rounded text-center bg-success text-white';
            status.innerHTML = '<i class="bi bi-check-circle"></i> Assinatura confirmada!';
        } else {
            status.className = 'mt-2 p-2 rounded text-center bg-warning text-dark';
            status.innerHTML = '<i class="bi bi-exclamation-triangle"></i> Assinatura não coletada';
        }
    }
});
</script>
@endpush
