@extends('layouts.app')

@section('title', 'Cadastro de Máquina - Tablet')

@section('styles')
<style>
.tablet-container{max-width:960px;margin:0 auto;padding:18px}
.form-card{background:#fff;border-radius:18px;box-shadow:0 4px 18px rgba(0,0,0,.08);padding:26px;margin-bottom:22px}
.form-card h5{display:flex;align-items:center;gap:8px;border-bottom:3px solid #0d6efd;padding-bottom:10px;margin-bottom:18px;font-weight:600}
.form-control,.form-select{height:54px;font-size:17px;border-radius:12px}
textarea.form-control{min-height:120px}
.toggle-switch{position:relative;display:inline-block;width:64px;height:36px}
.toggle-switch input{opacity:0;width:0;height:0}
.slider{position:absolute;cursor:pointer;top:0;left:0;right:0;bottom:0;background:#adb5bd;transition:.35s;border-radius:36px}
.slider:before{position:absolute;content:"";height:28px;width:28px;left:4px;bottom:4px;background:#fff;transition:.35s;border-radius:50%;box-shadow:0 2px 4px rgba(0,0,0,.2)}
input:checked+.slider{background:#0d6efd}
input:checked+.slider:before{transform:translateX(28px)}
.signature-container{border:3px dashed #0d6efd;border-radius:14px;background:#f8f9fa;padding:14px}
#signatureCanvas{border:2px solid #dee2e6;border-radius:10px;background:#fff;touch-action:none;width:100%;height:260px;display:block;cursor:crosshair}
.signature-actions{display:flex;gap:12px;margin-top:14px;justify-content:space-between}
.signature-status{margin-top:12px;border-radius:10px;padding:12px;font-weight:600;text-align:center;font-size:15px}
.signature-empty{background:#fff3cd;color:#856404}
.signature-done{background:#d4edda;color:#155724}
.user-search-results{position:absolute;z-index:1050;background:#fff;border:2px solid #dee2e6;border-radius:10px;max-height:340px;overflow-y:auto;width:100%;margin-top:6px;box-shadow:0 6px 18px rgba(0,0,0,.18)}
.user-search-item{padding:14px;border-bottom:1px solid #f0f0f0;cursor:pointer;transition:.2s}
.user-search-item:hover{background:#f1f5f9}
.user-search-item:last-child{border-bottom:none}
.step-badge{display:inline-flex;align-items:center;gap:6px;background:#0d6efd;color:#fff;padding:6px 14px;border-radius:30px;font-size:14px;font-weight:600;box-shadow:0 2px 8px rgba(0,0,0,.12)}
@media (max-width: 640px){.form-control,.form-select{height:60px;font-size:18px}h2{font-size:1.65rem}}
</style>
@endsection

@section('content')
<div class="tablet-container">
<div class="text-center mb-4">
<div class="step-badge mb-3"><i class="bi bi-tablet"></i> Modo Tablet</div>
<h2 class="mb-1"><i class="bi bi-laptop text-primary"></i> Cadastro de Máquina</h2>
<p class="text-muted">Formulário otimizado para toque. Preencha os dados e colete a assinatura do recebedor.</p>
</div>
<form action="{{ route('machines.store') }}" method="POST" id="machineForm" novalidate>
@csrf
<div class="form-card">
<h5><i class="bi bi-pc-display-horizontal text-primary"></i> Dados da Máquina</h5>
<div class="row g-3">
<div class="col-md-6"><label class="form-label">Tipo *</label><select class="form-select" name="tipo" id="tipoEquipamento" required><option value="">Selecione...</option><option value="desktop">🖥️ Desktop</option><option value="notebook">💻 Notebook</option><option value="servidor">🖧 Servidor</option><option value="monitor">🖥 Monitor</option><option value="impressora">🖨️ Impressora</option><option value="nobreak">🔋 Nobreak</option><option value="estabilizador">⚡ Estabilizador</option><option value="switch">🔌 Switch</option><option value="teclado">⌨️ Teclado</option><option value="mouse">🖱️ Mouse</option></select></div>
<div class="col-md-6"><label class="form-label" id="labelPatrimonio">Patrimônio *</label><input class="form-control" name="patrimonio" id="inputPatrimonio" required placeholder="Ex: 123456"></div>
<div class="col-md-6"><label class="form-label" id="labelNumeroSerie">Número de Série *</label><input class="form-control" name="numero_serie" id="inputNumeroSerie" required placeholder="Ex: SN123ABC456"></div>
<div class="col-md-6"><label class="form-label">Marca</label><input class="form-control" name="marca" placeholder="Ex: Dell, HP, Lenovo"></div>
<div class="col-12"><label class="form-label">Modelo *</label><input class="form-control" name="modelo" required placeholder="Ex: OptiPlex 7090, ThinkPad T14"></div>
<div class="col-md-4"><label class="form-label">Processador</label><input class="form-control" name="processador" placeholder="Ex: Intel i5"></div>
<div class="col-md-4"><label class="form-label">Memória RAM</label><input class="form-control" name="memoria_ram" placeholder="Ex: 8GB DDR4"></div>
<div class="col-md-4"><label class="form-label">Armazenamento</label><input class="form-control" name="armazenamento" placeholder="Ex: SSD 256GB"></div>
<div class="col-12"><label class="form-label">Sistema Operacional</label><input class="form-control" name="sistema_operacional" placeholder="Ex: Windows 11 Pro"></div>
</div></div>
<div class="form-card">
<h5><i class="bi bi-file-earmark-text text-success"></i> Contrato / Licitação</h5>
<div class="row g-3">
<div class="col-md-8"><label class="form-label">Contrato/Licitação</label><input class="form-control" name="contrato_licitacao" placeholder="Ex: Pregão Eletrônico 001/2025"></div>
<div class="col-md-4"><label class="form-label">Número</label><input class="form-control" name="numero_licitacao" placeholder="Ex: 001/2025"></div>
<div class="col-md-6"><label class="form-label">Data de Aquisição</label><input type="date" class="form-control" name="data_aquisicao"></div>
<div class="col-md-6"><label class="form-label">Valor (R$)</label><input type="number" step="0.01" class="form-control" name="valor_aquisicao" placeholder="0,00"></div>
</div></div>
<div class="form-card">
<h5><i class="bi bi-arrow-left-right text-warning"></i> Troca de Equipamento</h5>
<div class="d-flex align-items-center gap-3 mb-3">
<label class="form-label mb-0">É uma troca?</label>
<label class="toggle-switch"><input type="checkbox" id="is_troca" name="is_troca" value="1"><span class="slider"></span></label>
</div>
<div id="camposTroca" style="display:none;">
<div class="row g-3">
<div class="col-md-12"><label class="form-label">Patrimônio Substituído</label><input class="form-control" name="patrimonio_substituido" placeholder="Ex: 654321"></div>
<div class="col-md-12"><label class="form-label">Motivo da Troca</label><textarea class="form-control" name="motivo_troca" rows="3" placeholder="Descreva o motivo..."></textarea></div>
</div></div></div>
<div class="form-card">
<h5><i class="bi bi-person-check text-info"></i> Recebedor</h5>
<div class="mb-3 position-relative">
<label class="form-label">Buscar Usuário *</label>
<input class="form-control" id="searchUser" placeholder="Digite o nome..." autocomplete="off">
<div id="userResults" class="user-search-results" style="display:none;"></div>
<input type="hidden" id="recebedor_id" name="recebedor_id" required>
<div id="selectedUser" style="display:none;" class="mt-3 p-3 bg-light rounded">
<strong>Selecionado:</strong>
<div id="selectedUserInfo" class="mt-2"></div>
<button type="button" class="btn btn-sm btn-outline-danger mt-2" id="clearUser"><i class="bi bi-x-circle"></i> Alterar</button>
</div></div></div>
<div class="form-card">
<h5><i class="bi bi-pen text-danger"></i> Assinatura do Recebedor</h5>
<div class="alert alert-info">
<div class="form-check form-switch">
<input class="form-check-input" type="checkbox" id="cadastroParcial" name="cadastro_parcial" value="1">
<label class="form-check-label" for="cadastroParcial">
<strong><i class="bi bi-save"></i> Cadastro Parcial</strong> - Salvar sem assinatura (coletar depois)
</label>
</div>
</div>
<div id="assinaturaFields">
<p class="text-muted mb-3"><i class="bi bi-info-circle"></i> Assine com o dedo ou caneta stylus abaixo.</p>
<div class="signature-container">
<canvas id="signatureCanvas"></canvas>
<div class="signature-actions">
<button type="button" class="btn btn-outline-danger" id="clearSignature"><i class="bi bi-trash"></i> Limpar</button>
<button type="button" class="btn btn-outline-primary" id="saveSignature"><i class="bi bi-check-circle"></i> Confirmar Assinatura</button>
</div>
<div id="signatureStatus" class="signature-status signature-empty"><i class="bi bi-exclamation-triangle"></i> Assinatura não coletada</div>
</div>
<input type="hidden" id="assinatura_digital" name="assinatura_digital">
</div>
</div>
<div class="form-card">
<h5><i class="bi bi-chat-left-text text-secondary"></i> Observações da Entrega</h5>
<textarea class="form-control" name="observacoes_entrega" rows="3" placeholder="Observações adicionais..."></textarea>
</div>
<div class="d-grid gap-3 mb-4">
<button type="submit" class="btn btn-primary btn-lg" id="submitBtn"><i class="bi bi-check-circle-fill"></i> Finalizar Cadastro</button>
<a href="{{ route('machines.index') }}" class="btn btn-outline-secondary btn-lg"><i class="bi bi-arrow-left"></i> Cancelar</a>
</div>
</form>

{{-- Modal de Validação de Login --}}
<div class="modal fade" id="validacaoLoginModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
<div class="modal-dialog modal-dialog-centered">
<div class="modal-content">
<div class="modal-header bg-primary text-white">
<h5 class="modal-title"><i class="bi bi-shield-check"></i> Validação de Assinatura</h5>
<button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
</div>
<div class="modal-body">
<div class="alert alert-warning">
<i class="bi bi-exclamation-triangle"></i> <strong>Atenção:</strong> Preferencialmente, o recebedor deve validar a assinatura com suas credenciais.
</div>

<div class="alert alert-info mb-3" id="info_recebedor" style="display:none;">
<strong>Recebedor da máquina:</strong> <span id="nome_recebedor_modal"></span>
</div>

<form id="validacaoForm">
<div class="form-check form-switch mb-3">
<input class="form-check-input" type="checkbox" id="validar_por_terceiro_tablet">
<label class="form-check-label" for="validar_por_terceiro_tablet">
<strong>Validar por outra pessoa</strong><br>
<small class="text-muted">Marque se o recebedor não estiver disponível e outra pessoa precisar validar</small>
</label>
</div>

<div class="mb-3">
<label class="form-label fw-bold">Login de Rede *</label>
<div class="position-relative">
<input type="text" class="form-control form-control-lg" id="login_validacao" placeholder="Digite para buscar..." autocomplete="off" required>
<div id="usuarios_dropdown_tablet" class="list-group position-absolute w-100" style="display: none; max-height: 200px; overflow-y: auto; z-index: 1000; box-shadow: 0 4px 12px rgba(0,0,0,0.15);"></div>
</div>
<small class="text-muted" id="login_hint_tablet">O recebedor deve usar seu próprio login de rede</small>
<div id="usuario_info_tablet" class="mt-2" style="display: none;">
<small class="text-success">
<i class="bi bi-person-check"></i> <span id="usuario_nome_tablet"></span>
</small>
</div>
</div>

<div class="mb-3">
<label class="form-label fw-bold">Senha *</label>
<input type="password" class="form-control form-control-lg" id="senha_validacao" placeholder="Digite sua senha" autocomplete="off" required>
</div>
<div id="validacao_erro" class="alert alert-danger" style="display:none;"></div>
<div id="validacao_sucesso" class="alert alert-success" style="display:none;">
<i class="bi bi-check-circle-fill"></i> <strong>Validado com sucesso!</strong><br>
<span id="texto_confirmacao"></span>
</div>
</form>
</div>
<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
<button type="button" class="btn btn-primary" id="btnValidarLogin">
<i class="bi bi-check-circle"></i> Validar e Confirmar
</button>
</div>
</div>
</div>
</div>

</div>
@endsection
@push('scripts')
<script>
(function() {
    // Toggle cadastro parcial
    const cadastroParcial = document.getElementById('cadastroParcial');
    const assinaturaFields = document.getElementById('assinaturaFields');
    const assinaturaInput = document.getElementById('assinatura_digital');
    
    cadastroParcial.addEventListener('change', function() {
        if (this.checked) {
            assinaturaFields.style.display = 'none';
            assinaturaInput.removeAttribute('required');
        } else {
            assinaturaFields.style.display = 'block';
        }
    });
    
    // Toggle patrimônio e número de série conforme tipo
    const tipoEquipamento = document.getElementById('tipoEquipamento');
    const inputPatrimonio = document.getElementById('inputPatrimonio');
    const labelPatrimonio = document.getElementById('labelPatrimonio');
    const campoPatrimonio = document.getElementById('campoPatrimonio');
    const inputNumeroSerie = document.getElementById('inputNumeroSerie');
    const labelNumeroSerie = document.getElementById('labelNumeroSerie');
    
    if (tipoEquipamento) {
        tipoEquipamento.addEventListener('change', function() {
            const tipo = this.value;
            const semPatrimonio = ['teclado', 'mouse'];
            
            // Patrimônio: opcional apenas para teclado e mouse
            if (semPatrimonio.includes(tipo)) {
                inputPatrimonio.removeAttribute('required');
                labelPatrimonio.textContent = 'Patrimônio (opcional)';
                inputPatrimonio.placeholder = 'Opcional para este tipo';
                campoPatrimonio.classList.add('text-muted');
            } else {
                inputPatrimonio.setAttribute('required', 'required');
                labelPatrimonio.innerHTML = 'Patrimônio <span class="text-danger">*</span>';
                inputPatrimonio.placeholder = 'Ex: 123456';
                campoPatrimonio.classList.remove('text-muted');
            }
            
            // Número de série: obrigatório apenas para desktop
            if (tipo === 'desktop') {
                inputNumeroSerie.setAttribute('required', 'required');
                labelNumeroSerie.innerHTML = 'Número de Série <span class="text-danger">*</span>';
            } else {
                inputNumeroSerie.removeAttribute('required');
                labelNumeroSerie.textContent = 'Número de Série (opcional)';
            }
        });
    }
    
    // Toggle troca
    const trocaCheckbox = document.getElementById('is_troca');
    const camposTroca = document.getElementById('camposTroca');
    if (trocaCheckbox) {
        trocaCheckbox.addEventListener('change', () => {
            camposTroca.style.display = trocaCheckbox.checked ? 'block' : 'none';
        });
    }
    
    // Signature canvas
    const canvas = document.getElementById('signatureCanvas');
    if (!canvas) {
        console.error('Canvas não encontrado');
        return;
    }
    
    const ctx = canvas.getContext('2d');
    if (!ctx) {
        console.error('Contexto 2D não disponível');
        return;
    }
    
    let drawing = false, hasSignature = false, signatureValidated = false;
    
    function resize() {
        const r = canvas.getBoundingClientRect();
        // Salva o conteúdo atual se houver
        const imageData = hasSignature ? ctx.getImageData(0, 0, canvas.width, canvas.height) : null;
        
        canvas.width = r.width;
        canvas.height = 260;
        
        // Restaura configurações do contexto
        ctx.lineWidth = 2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';
        
        // Restaura o conteúdo se havia assinatura
        if (imageData) {
            ctx.putImageData(imageData, 0, 0);
        }
    }
    
    // Aguarda o DOM estar pronto antes de redimensionar
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', resize);
    } else {
        resize();
    }
    window.addEventListener('resize', resize);
    
    ctx.lineWidth = 2;
    ctx.lineCap = 'round';
    ctx.strokeStyle = '#000';
    
    function pointer(e) {
        const r = canvas.getBoundingClientRect();
        const p = e.touches ? e.touches[0] : e;
        return { x: p.clientX - r.left, y: p.clientY - r.top };
    }
    
    function start(e) {
        drawing = true;
        const { x, y } = pointer(e);
        ctx.beginPath();
        ctx.moveTo(x, y);
    }
    
    function move(e) {
        if (!drawing) return;
        const { x, y } = pointer(e);
        ctx.lineTo(x, y);
        ctx.stroke();
        hasSignature = true;
    }
    
    function end() {
        drawing = false;
    }
    
    ['mousedown', 'touchstart'].forEach(ev => {
        canvas.addEventListener(ev, (e) => {
            e.preventDefault();
            start(e);
        });
    });
    
    ['mousemove', 'touchmove'].forEach(ev => {
        canvas.addEventListener(ev, (e) => {
            e.preventDefault();
            move(e);
        });
    });
    
    ['mouseup', 'mouseleave', 'touchend', 'touchcancel'].forEach(ev => {
        canvas.addEventListener(ev, end);
    });
    
    document.getElementById('clearSignature').addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSignature = false;
        signatureValidated = false;
        document.getElementById('assinatura_digital').value = '';
        updateSignatureStatus(false);
    });
    
    // Ao clicar em "Confirmar Assinatura", abre modal de validação
    document.getElementById('saveSignature').addEventListener('click', () => {
        if (!hasSignature) {
            alert('Faça a assinatura antes de confirmar.');
            return;
        }
        
        // Pré-preenche informações do recebedor no modal
        if (window.recebedorSelecionado) {
            document.getElementById('nome_recebedor_modal').textContent = window.recebedorSelecionado.name;
            document.getElementById('info_recebedor').style.display = 'block';
            document.getElementById('login_validacao').value = window.recebedorSelecionado.username || '';
        }
        
        // Abre modal de validação
        const modal = new bootstrap.Modal(document.getElementById('validacaoLoginModal'));
        modal.show();
    });
    
    // Toggle validar por terceiro no tablet
    document.getElementById('validar_por_terceiro_tablet').addEventListener('change', function() {
        const hint = document.getElementById('login_hint_tablet');
        if (this.checked) {
            hint.textContent = 'Digite o login de rede de qualquer usuário autorizado';
            hint.className = 'text-primary';
            document.getElementById('login_validacao').value = ''; // Limpa o campo
        } else {
            hint.textContent = 'O recebedor deve usar seu próprio login de rede';
            hint.className = 'text-muted';
            // Repreenche com o username do recebedor
            if (window.recebedorSelecionado && window.recebedorSelecionado.username) {
                document.getElementById('login_validacao').value = window.recebedorSelecionado.username;
            }
        }
    });
    
    // Busca de usuários no modal (similar ao da página show)
    let searchTimeout;
    const loginInputTablet = document.getElementById('login_validacao');
    const dropdownTablet = document.getElementById('usuarios_dropdown_tablet');
    const usuarioInfoTablet = document.getElementById('usuario_info_tablet');
    const usuarioNomeTablet = document.getElementById('usuario_nome_tablet');

    loginInputTablet.addEventListener('input', async function() {
        const search = this.value.trim();
        
        if (search.length < 2) {
            dropdownTablet.style.display = 'none';
            dropdownTablet.innerHTML = '';
            usuarioInfoTablet.style.display = 'none';
            return;
        }
        
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(async () => {
            try {
                const res = await fetch(`{{ route('machines.search-users') }}?q=${encodeURIComponent(search)}`);
                const users = await res.json();
                
                dropdownTablet.innerHTML = '';
                
                if (users.length === 0) {
                    dropdownTablet.innerHTML = '<div class="list-group-item text-muted">Nenhum usuário encontrado</div>';
                    dropdownTablet.style.display = 'block';
                    return;
                }
                
                users.forEach(user => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.innerHTML = `
                        <div class="d-flex w-100 justify-content-between">
                            <h6 class="mb-1">${user.name}</h6>
                            <small class="text-muted">${user.username}</small>
                        </div>
                        <small class="text-muted">${user.email}</small>
                    `;
                    item.onclick = (e) => {
                        e.preventDefault();
                        loginInputTablet.value = user.username;
                        usuarioNomeTablet.textContent = user.name;
                        usuarioInfoTablet.style.display = 'block';
                        dropdownTablet.style.display = 'none';
                    };
                    dropdownTablet.appendChild(item);
                });
                
                dropdownTablet.style.display = 'block';
            } catch (error) {
                console.error('Erro ao buscar usuários:', error);
            }
        }, 300);
    });

    // Fecha dropdown ao clicar fora
    document.addEventListener('click', function(e) {
        if (!loginInputTablet.contains(e.target) && !dropdownTablet.contains(e.target)) {
            dropdownTablet.style.display = 'none';
        }
    });
    
    // Validação de login
    document.getElementById('btnValidarLogin').addEventListener('click', async function() {
        const login = document.getElementById('login_validacao').value.trim();
        const senha = document.getElementById('senha_validacao').value;
        const validarPorTerceiro = document.getElementById('validar_por_terceiro_tablet').checked;
        const erroDiv = document.getElementById('validacao_erro');
        const sucessoDiv = document.getElementById('validacao_sucesso');
        
        if (!login || !senha) {
            erroDiv.textContent = 'Preencha login e senha.';
            erroDiv.style.display = 'block';
            return;
        }
        
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Validando...';
        erroDiv.style.display = 'none';
        sucessoDiv.style.display = 'none';
        
        try {
            const res = await fetch('{{ route("machines.validate-signature") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ login, senha, validar_por_terceiro: validarPorTerceiro })
            });
            
            const data = await res.json();
            
            if (data.success) {
                // Salva assinatura
                const signatureData = canvas.toDataURL('image/png');
                document.getElementById('assinatura_digital').value = signatureData;
                
                // Adiciona campo hidden com usuário validador
                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = 'assinatura_usuario_validador';
                hiddenInput.value = login;
                document.getElementById('machineForm').appendChild(hiddenInput);
                
                signatureValidated = true;
                
                // Mostra mensagem de sucesso
                document.getElementById('texto_confirmacao').innerHTML = 
                    `Assinatura validada por <strong>${data.user_name}</strong> (${login})<br>` +
                    `<small class="text-muted">Certificado digitalmente via login de rede</small>`;
                sucessoDiv.style.display = 'block';
                
                setTimeout(() => {
                    bootstrap.Modal.getInstance(document.getElementById('validacaoLoginModal')).hide();
                    updateSignatureStatus(true);
                }, 2000);
            } else {
                erroDiv.textContent = data.message || 'Login ou senha inválidos.';
                erroDiv.style.display = 'block';
            }
        } catch (error) {
            erroDiv.textContent = 'Erro ao validar credenciais. Tente novamente.';
            erroDiv.style.display = 'block';
        } finally {
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-check-circle"></i> Validar e Confirmar';
        }
    });
    
    function updateSignatureStatus(done) {
        const st = document.getElementById('signatureStatus');
        st.className = 'signature-status ' + (done ? 'signature-done' : 'signature-empty');
        st.innerHTML = done ? 
            '<i class="bi bi-check-circle"></i> Assinatura confirmada e validada!' : 
            '<i class="bi bi-exclamation-triangle"></i> Assinatura não coletada';
    }
    
    // User search
    const searchInput = document.getElementById('searchUser');
    const resultsDiv = document.getElementById('userResults');
    const recebedorId = document.getElementById('recebedor_id');
    const selectedUser = document.getElementById('selectedUser');
    const selectedInfo = document.getElementById('selectedUserInfo');
    let debounce;
    
    searchInput.addEventListener('input', () => {
        clearTimeout(debounce);
        const q = searchInput.value.trim();
        
        if (q.length < 2) {
            resultsDiv.style.display = 'none';
            resultsDiv.innerHTML = '';
            return;
        }
        
        debounce = setTimeout(async () => {
            try {
                const url = '{{ route("admin.users.search") }}?q=' + encodeURIComponent(q);
                const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                const users = await res.json();
                
                if (!users.length) {
                    resultsDiv.innerHTML = '<div class="p-3 text-muted">Nenhum usuário encontrado</div>';
                    resultsDiv.style.display = 'block';
                    return;
                }
                
                resultsDiv.innerHTML = users.map(u => {
                    const badge = u.role === 'admin' ? 'danger' : (u.role === 'technician' ? 'primary' : 'secondary');
                    const role = u.role === 'admin' ? 'Admin' : (u.role === 'technician' ? 'Técnico' : 'Usuário');
                    return `<div class="user-search-item" data-id="${u.id}" data-name="${u.name}" data-username="${u.username || ''}">
                        <div class="d-flex justify-content-between align-items-center">
                            <div><strong>${u.name}</strong><br><small class="text-muted">${u.email}</small></div>
                            <span class="badge bg-${badge}">${role}</span>
                        </div>
                    </div>`;
                }).join('');
                resultsDiv.style.display = 'block';
            } catch (e) {
                resultsDiv.innerHTML = '<div class="p-3 text-danger">Erro ao buscar usuários</div>';
                resultsDiv.style.display = 'block';
            }
        }, 350);
    });
    
    resultsDiv.addEventListener('click', (e) => {
        const item = e.target.closest('.user-search-item');
        if (!item) return;
        
        const id = item.getAttribute('data-id');
        const name = item.getAttribute('data-name');
        const username = item.getAttribute('data-username'); // Adicionar username
        
        recebedorId.value = id;
        searchInput.value = '';
        searchInput.disabled = true;
        resultsDiv.style.display = 'none';
        selectedInfo.innerHTML = `<div class='d-flex align-items-center gap-2'>
            <i class='bi bi-person-circle fs-3 text-primary'></i>
            <div><strong>${name}</strong><br><small class='text-muted'>ID: ${id}</small></div>
        </div>`;
        selectedUser.style.display = 'block';
        
        // Armazena dados do recebedor para usar no modal
        window.recebedorSelecionado = {
            id: id,
            name: name,
            username: username
        };
    });
    
    document.getElementById('clearUser').addEventListener('click', () => {
        recebedorId.value = '';
        searchInput.value = '';
        searchInput.disabled = false;
        searchInput.focus();
        selectedUser.style.display = 'none';
    });
    
    // Form submit
    document.getElementById('machineForm').addEventListener('submit', function(e) {
        const isParcial = cadastroParcial.checked;
        
        if (!isParcial) {
            if (!document.getElementById('assinatura_digital').value) {
                e.preventDefault();
                alert('Colete a assinatura do recebedor.');
                canvas.scrollIntoView({ behavior: 'smooth', block: 'center' });
                return;
            }
            
            if (!signatureValidated) {
                e.preventDefault();
                alert('A assinatura precisa ser validada com login e senha.');
                return;
            }
        }
        
        if (!recebedorId.value) {
            e.preventDefault();
            alert('Selecione o usuário recebedor.');
            searchInput.focus();
            return;
        }
        
        addHidden(this, 'data_entrega', new Date().toISOString());
        addHidden(this, 'entregue_por_id', '{{ auth()->id() }}');
        addHidden(this, 'status', 'ativo');
        
        const btn = document.getElementById('submitBtn');
        btn.disabled = true;
        btn.innerHTML = '<i class="bi bi-hourglass-split"></i> Salvando...';
    });
    
    function addHidden(form, name, val) {
        const i = document.createElement('input');
        i.type = 'hidden';
        i.name = name;
        i.value = val;
        form.appendChild(i);
    }
})();
</script>
@endpush
