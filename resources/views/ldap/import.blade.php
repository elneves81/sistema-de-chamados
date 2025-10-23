@extends('layouts.app')

@section('styles')
<style>
.ldap-card {
    background: #fff;
    border-radius: 1.2rem;
    box-shadow: 0 4px 32px 0 rgba(0,0,0,0.10);
    padding: 2.5rem 2.2rem 2.2rem 2.2rem;
    max-width: 900px;
    margin: 2rem auto;
}
.ldap-title {
    font-size: 2rem;
    font-weight: 800;
    color: #1976f2;
    text-align: center;
    margin-bottom: 1.5rem;
    letter-spacing: 0.01em;
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
.ldap-btn:disabled {
    background: #ccc;
    cursor: not-allowed;
}
.ldap-preview {
    margin-top: 2rem;
    max-height: 600px;
    overflow-y: auto;
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
    font-size: 0.9rem;
}
.ldap-table th {
    background: #f3f6ff;
    color: #1976f2;
    font-weight: 700;
    position: sticky;
    top: 0;
    z-index: 10;
}
.user-row:hover {
    background-color: #f8f9fa;
}
.alert {
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 0.5rem;
}
.alert-success {
    background-color: #d4edda;
    border-color: #c3e6cb;
    color: #155724;
}
.alert-danger {
    background-color: #f8d7da;
    border-color: #f5c6cb;
    color: #721c24;
}
.alert-warning {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}
.import-summary {
    background: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
}
.checkbox-header {
    text-align: center;
    width: 50px;
}
.checkbox-cell {
    text-align: center;
    width: 50px;
}
.filters-section {
    background: #f8f9fa;
    border-radius: 0.5rem;
    padding: 1rem;
    margin-bottom: 1rem;
    border: 1px solid #dee2e6;
}
.filter-row {
    margin-bottom: 0.5rem;
}
</style>
@endsection

@section('content')
<div class="ldap-card">
    <div class="ldap-title">
        <i class="fas fa-users-cog me-2"></i>
        Importação LDAP / Active Directory
    </div>

    <!-- Exibir alertas -->
    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <strong>Erros encontrados:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Formulário de configuração LDAP -->
    <form class="ldap-form" method="POST" action="{{ route('admin.ldap.import.preview') }}" id="ldapForm">
        @csrf
        
        <div class="filters-section">
            <h6><i class="fas fa-cog me-2"></i>Configuração do Servidor LDAP</h6>
            <div class="row">
                <div class="col-md-6">
                    <label for="host">Servidor/Host *</label>
                    <input type="text" id="host" name="host" class="form-control" required 
                           placeholder="ex: ad.seudominio.local" value="{{ old('host') }}">
                </div>
                <div class="col-md-3">
                    <label for="port">Porta *</label>
                    <input type="number" id="port" name="port" class="form-control" 
                           value="{{ old('port', 389) }}" required min="1" max="65535">
                </div>
                <div class="col-md-3">
                    <label for="ssl">Usar SSL/TLS</label>
                    <select name="ssl" id="ssl" class="form-control">
                        <option value="0" {{ old('ssl') == '0' ? 'selected' : '' }}>Não</option>
                        <option value="1" {{ old('ssl') == '1' ? 'selected' : '' }}>Sim</option>
                    </select>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label for="username">Usuário (UPN / DOMÍNIO\usuário ou DN) *</label>
                    <input type="text" id="username" name="username" class="form-control" required 
                           placeholder="ex: usuario@seudominio.local ou DOMINIO\\usuario (ou DN completo)" 
                           value="{{ old('username') }}">
                </div>
                <div class="col-md-6">
                    <label for="password">Senha *</label>
                    <input type="password" id="password" name="password" class="form-control" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <label for="base_dn">Base DN *</label>
                    <input type="text" id="base_dn" name="base_dn" class="form-control" required 
                           placeholder="ex: DC=seudominio,DC=local" value="{{ old('base_dn') }}">
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <label for="search_filter">Filtro de Busca (opcional)</label>
                    <input type="text" id="search_filter" name="search_filter" class="form-control" 
                           placeholder="ex: (objectClass=user)" value="{{ old('search_filter') }}">
                    <small class="text-muted">Deixe vazio para usar filtro padrão</small>
                </div>
                <div class="col-md-3">
                    <label for="size">Limite de Resultados</label>
                    <input type="number" id="size" name="size" class="form-control" 
                           value="{{ old('size', 300) }}" min="1" max="1000">
                    <small class="text-muted">Máximo: 1000 usuários (recomendado: 300)</small>
                </div>
                <div class="col-md-3">
                    <label for="filter">Filtro por Nome</label>
                    <input type="text" id="filter" name="filter" class="form-control" 
                           placeholder="Digite parte do nome" value="{{ old('filter') }}">
                    <small class="text-muted">Filtrar por nome ou login</small>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="ldap-btn" id="previewBtn">
                <i class="fas fa-search me-2"></i>Pré-visualizar Usuários
            </button>
            <button type="button" class="ldap-btn" onclick="testConnection()" id="testBtn">
                <i class="fas fa-plug me-2"></i>Testar Conexão
            </button>
        </div>
    </form>

    <!-- Seção de Importação em Lotes -->
    <div class="bulk-import-section mt-4" style="border-top: 2px solid #e9ecef; padding-top: 2rem;">
        <h5 class="text-primary mb-3">
            <i class="fas fa-database me-2"></i>Importação em Lotes (Para Muitos Usuários)
        </h5>
        <div class="alert alert-info">
            <i class="fas fa-info-circle me-2"></i>
            <strong>Recomendado para importar milhares de usuários:</strong> Esta opção processa usuários em segundo plano usando filas, evitando timeouts.
        </div>
        
        <form id="bulkImportForm" class="ldap-form">
            @csrf
            <div class="row">
                <div class="col-md-4">
                    <label for="bulk_batch_size">Tamanho do Lote</label>
                    <select name="bulk_batch_size" id="bulk_batch_size" class="form-control">
                        <option value="50">50 usuários por lote</option>
                        <option value="100" selected>100 usuários por lote</option>
                        <option value="200">200 usuários por lote</option>
                        <option value="500">500 usuários por lote</option>
                    </select>
                    <small class="text-muted">Lotes menores são mais confiáveis</small>
                </div>
                <div class="col-md-4">
                    <label for="bulk_filter">Filtro de Nome (opcional)</label>
                    <input type="text" id="bulk_filter" name="bulk_filter" class="form-control" 
                           placeholder="ex: João, Silva, Departamento">
                    <small class="text-muted">Filtra usuários por nome/login</small>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="button" class="ldap-btn w-100" onclick="startBulkImport()" id="bulkImportBtn">
                        <i class="fas fa-rocket me-2"></i>Iniciar Importação em Lotes
                    </button>
                </div>
            </div>
        </form>

        <!-- Progress Section -->
        <div id="bulkProgressSection" class="mt-4" style="display: none;">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="fas fa-tasks me-2"></i>Progresso da Importação</h6>
                </div>
                <div class="card-body">
                    <div class="progress mb-3" style="height: 25px;">
                        <div id="bulkProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" 
                             role="progressbar" style="width: 0%;">0%</div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <small class="text-muted">Status:</small><br>
                            <span id="bulkStatus" class="badge bg-info">Iniciando...</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Lote Atual:</small><br>
                            <span id="bulkCurrentPage">0</span> / <span id="bulkTotalPages">0</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Importados:</small><br>
                            <span id="bulkImported" class="text-success fw-bold">0</span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Ignorados:</small><br>
                            <span id="bulkSkipped" class="text-warning fw-bold">0</span>
                        </div>
                    </div>
                    <div class="mt-3">
                        <small class="text-muted">Mensagem:</small><br>
                        <span id="bulkMessage">Preparando importação...</span>
                    </div>
                    <div class="mt-3">
                        <button type="button" class="btn btn-danger btn-sm" onclick="cancelBulkImport()" id="cancelBulkBtn">
                            <i class="fas fa-stop me-1"></i>Cancelar Importação
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pré-visualização dos usuários -->
    @if(isset($preview) && count($preview) > 0)
    <div class="ldap-preview">
        <div class="import-summary">
            <h6><i class="fas fa-info-circle me-2"></i>Resumo da Busca</h6>
            <div class="row">
                <div class="col-md-4">
                    <p class="mb-1"><strong>Total encontrados:</strong> {{ count($preview) }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1"><strong>Limite aplicado:</strong> {{ $limit ?? 500 }}</p>
                </div>
                <div class="col-md-4">
                    <p class="mb-1"><strong>Filtro:</strong> {{ $applied_filter ?: 'Nenhum' }}</p>
                </div>
            </div>
            <p class="mb-0 mt-2"><small class="text-muted">
                <i class="fas fa-lightbulb me-1"></i>
                Selecione os usuários para importar. Usuários já existentes serão atualizados.
                @if(count($preview) == ($limit ?? 500))
                    <strong>Atenção:</strong> Pode haver mais usuários disponíveis. Aumente o limite se necessário.
                @endif
            </small></p>
        </div>

        <form method="POST" action="{{ route('admin.ldap.import.process') }}" id="importForm">
            @csrf
            
            <!-- Reproduzir configurações LDAP para o processo de importação -->
            @foreach(request()->except(['_token', '_method']) as $key => $value)
                <input type="hidden" name="config[{{ $key }}]" value="{{ $value }}">
            @endforeach
            
            <!-- Filtro em tempo real na tabela -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tableSearch" class="form-label">
                        <i class="fas fa-search me-1"></i>Buscar na tabela atual:
                    </label>
                    <input type="text" id="tableSearch" class="form-control" 
                           placeholder="Digite para filtrar os resultados..." 
                           onkeyup="filterTable()">
                    <small class="text-muted">Busca em tempo real por nome, email, departamento ou cargo</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Ações em lote:</label>
                    <div>
                        <button type="button" class="btn btn-sm btn-outline-primary me-2" onclick="selectAll()">
                            <i class="fas fa-check-square me-1"></i>Selecionar Todos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary me-2" onclick="selectNone()">
                            <i class="fas fa-square me-1"></i>Desmarcar Todos
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-info" onclick="selectVisible()">
                            <i class="fas fa-eye me-1"></i>Selecionar Visíveis
                        </button>
                    </div>
                    <div class="mt-1">
                        <span class="text-muted" id="selectedCount">0 usuários selecionados</span>
                        <span class="text-muted ms-2" id="visibleCount">{{ count($preview) }} visíveis</span>
                    </div>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-12 text-end">
                    <button type="submit" class="ldap-btn" id="importBtn" disabled>
                        <i class="fas fa-download me-2"></i>Importar Selecionados
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="ldap-table">
                    <thead>
                        <tr>
                            <th class="checkbox-header">
                                <input type="checkbox" id="selectAllCheck" onchange="toggleAll(this)">
                            </th>
                            <th>Nome Completo</th>
                            <th>Login (SAM)</th>
                            <th>Email</th>
                            <th>Departamento</th>
                            <th>Título/Cargo</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($preview as $index => $user)
                        <tr class="user-row">
                            <td class="checkbox-cell">
                                <input type="checkbox" class="ldap-check" name="users[]" 
                                       value="{{ htmlspecialchars(json_encode($user), ENT_QUOTES, 'UTF-8') }}" 
                                       onchange="updateSelectedCount()" 
                                       {{ !empty($user['sAMAccountName'] ?? $user['login'] ?? '') && !empty($user['displayName'] ?? $user['name'] ?? '') ? 'checked' : '' }}>
                            </td>
                            <td>
                                <strong>{{ ($user['displayName'] ?? $user['name'] ?? '') ?: ($user['cn'] ?? '') ?: 'N/A' }}</strong>
                                @if(!empty($user['givenName'] ?? '') && !empty($user['surname'] ?? ''))
                                    <br><small class="text-muted">{{ $user['givenName'] }} {{ $user['surname'] }}</small>
                                @endif
                                @if(!empty($user['fullName'] ?? '') && ($user['fullName'] ?? '') !== ($user['displayName'] ?? $user['name'] ?? ''))
                                    <br><small class="text-muted">Nome completo: {{ $user['fullName'] }}</small>
                                @endif
                                @if(!empty($user['description'] ?? ''))
                                    <br><small class="text-info">{{ Str::limit($user['description'], 50) }}</small>
                                @endif
                            </td>
                            <td>
                                <code>{{ ($user['sAMAccountName'] ?? $user['login'] ?? '') ?: 'N/A' }}</code>
                                @if(!empty($user['userPrincipalName'] ?? $user['upn'] ?? '') && ($user['userPrincipalName'] ?? $user['upn'] ?? '') !== ($user['sAMAccountName'] ?? $user['login'] ?? ''))
                                    <br><small class="text-muted">UPN: {{ $user['userPrincipalName'] ?? $user['upn'] ?? 'N/A' }}</small>
                                @endif
                            </td>
                            <td>
                                {{ ($user['mail'] ?? $user['email'] ?? '') ?: 'Não informado' }}
                            </td>
                            <td>
                                {{ ($user['department'] ?? '') ?: 'N/A' }}
                                @if(!empty($user['company'] ?? ''))
                                    <br><small class="text-muted">{{ $user['company'] }}</small>
                                @endif
                            </td>
                            <td>
                                {{ ($user['title'] ?? '') ?: 'N/A' }}
                                @if(!empty($user['phone'] ?? '') || !empty($user['mobile'] ?? ''))
                                    <br>
                                    @if(!empty($user['phone'] ?? ''))
                                        <small class="text-muted"><i class="fas fa-phone fa-xs"></i> {{ $user['phone'] }}</small>
                                    @endif
                                    @if(!empty($user['mobile'] ?? ''))
                                        <br><small class="text-muted"><i class="fas fa-mobile fa-xs"></i> {{ $user['mobile'] }}</small>
                                    @endif
                                @endif
                            </td>
                            <td>
                                @php
                                    $uac = intval($user['userAccountControl'] ?? 0);
                                    $isDisabled = ($uac & 2) !== 0; // ADS_UF_ACCOUNTDISABLE
                                @endphp
                                @if($isDisabled)
                                    <span class="badge bg-danger">Inativo</span>
                                @else
                                    <span class="badge bg-success">Ativo</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
    @elseif(isset($preview))
        <div class="alert alert-warning mt-3">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Nenhum usuário foi encontrado com os critérios especificados.
        </div>
    @endif
</div>

<script>
function selectAll() {
    document.querySelectorAll('.ldap-check').forEach(checkbox => {
        checkbox.checked = true;
    });
    const selectAllCheck = document.getElementById('selectAllCheck');
    if (selectAllCheck) {
        selectAllCheck.checked = true;
    }
    updateSelectedCount();
}

function selectNone() {
    document.querySelectorAll('.ldap-check').forEach(checkbox => {
        checkbox.checked = false;
    });
    const selectAllCheck = document.getElementById('selectAllCheck');
    if (selectAllCheck) {
        selectAllCheck.checked = false;
    }
    updateSelectedCount();
}

function toggleAll(masterCheckbox) {
    document.querySelectorAll('.ldap-check').forEach(checkbox => {
        checkbox.checked = masterCheckbox.checked;
    });
    updateSelectedCount();
}

function updateSelectedCount() {
    const checkboxes = document.querySelectorAll('.ldap-check');
    const checkedBoxes = document.querySelectorAll('.ldap-check:checked');
    const count = checkedBoxes.length;
    
    // Verificar se os elementos existem antes de tentar atualizá-los
    const selectedCountElement = document.getElementById('selectedCount');
    const importBtnElement = document.getElementById('importBtn');
    const masterCheckbox = document.getElementById('selectAllCheck');
    
    if (selectedCountElement) {
        selectedCountElement.textContent = count + ' usuários selecionados';
    }
    
    if (importBtnElement) {
        importBtnElement.disabled = count === 0;
    }
    
    // Atualizar estado do checkbox master
    if (masterCheckbox) {
        if (count === 0) {
            masterCheckbox.indeterminate = false;
            masterCheckbox.checked = false;
        } else if (count === checkboxes.length) {
            masterCheckbox.indeterminate = false;
            masterCheckbox.checked = true;
        } else {
            masterCheckbox.indeterminate = true;
        }
    }
}

function testConnection() {
    const form = document.getElementById('ldapForm');
    const formData = new FormData(form);
    const testBtn = document.getElementById('testBtn');
    
    // Verificar se o botão existe
    if (!testBtn) {
        console.error('Botão de teste não encontrado');
        return;
    }
    
    // Verificar se o token CSRF está disponível
    const csrfToken = document.querySelector('meta[name="csrf-token"]');
    if (!csrfToken) {
        alert('❌ Erro: Token CSRF não encontrado. Recarregue a página e tente novamente.');
        return;
    }
    
    // Validação rápida do lado do cliente para evitar 422
    const requiredFields = ['host', 'base_dn', 'username', 'password'];
    const missing = [];
    requiredFields.forEach(name => {
        const input = form.querySelector(`[name="${name}"]`);
        if (!input || !String(input.value).trim()) {
            missing.push(name);
            if (input) input.classList.add('is-invalid');
        } else if (input) {
            input.classList.remove('is-invalid');
        }
    });
    if (missing.length) {
        alert('Preencha os campos obrigatórios antes de testar:\n- ' + missing.join('\n- '));
        return;
    }

    // Ajuste de porta quando SSL ativado
    const ssl = form.querySelector('[name="ssl"]').value === '1';
    const portInput = form.querySelector('[name="port"]');
    if (ssl && portInput && (!portInput.value || portInput.value === '389')) {
        portInput.value = '636';
    }

    testBtn.disabled = true;
    testBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Testando...';
    
    fetch('{{ route("admin.ldap.test-connection") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': csrfToken.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(err => Promise.reject(err));
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            alert('✅ Conexão realizada com sucesso!\n\nDetalhes:\n' + data.message);
        } else {
            alert('❌ Falha na conexão!\n\nErro: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro completo:', error);
        let errorMessage = 'Erro desconhecido';
        let details = '';
        let code = '';
        if (error && typeof error === 'object') {
            if (error.message) errorMessage = error.message;
            if (error.code) code = error.code;
            if (error.errors) {
                const flat = Object.values(error.errors).flat();
                details = '\n\nDetalhes:\n- ' + flat.join('\n- ');
            }
        } else if (typeof error === 'string') {
            errorMessage = error;
        }
        // Sugestões específicas
        if (code === 'invalid_credentials') {
            const baseDn = document.getElementById('base_dn')?.value || '';
            const userVal = document.getElementById('username')?.value || '';
            const domain = (baseDn.match(/DC=([^,]+)/gi) || []).map(dc => dc.split('=')[1]).join('.');
            const upnHint = (!userVal.includes('@') && domain) ? `\n\nDica: tente no formato UPN: ${userVal}@${domain}` : '';
            alert('❌ Credenciais inválidas.\n' + errorMessage + upnHint);
            return;
        }
        if (code === 'unreachable') {
            alert('❌ Não foi possível contatar o servidor LDAP.\n' + errorMessage + '\n\nVerifique host, porta (389/636), SSL e firewall.');
            return;
        }
        alert('❌ Erro ao testar conexão:\n' + errorMessage + details);
    })
    .finally(() => {
        testBtn.disabled = false;
        testBtn.innerHTML = '<i class="fas fa-plug me-2"></i>Testar Conexão';
    });
}

// Inicializar contadores quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
});

// Validação do formulário
const ldapForm = document.getElementById('ldapForm');
if (ldapForm) {
    ldapForm.addEventListener('submit', function(e) {
        const previewBtn = document.getElementById('previewBtn');
        if (previewBtn) {
            previewBtn.disabled = true;
            previewBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Carregando...';
        }
    });
}

// Variáveis globais para importação em lotes
let bulkImportJobId = null;
let bulkImportInterval = null;

// Função para iniciar importação em lotes
function startBulkImport() {
    const btn = document.getElementById('bulkImportBtn');
    const form = document.getElementById('ldapForm');
    
    if (!form) {
        alert('Configure primeiro as credenciais LDAP acima.');
        return;
    }
    
    // Validar campos obrigatórios
    const requiredFields = ['host', 'base_dn', 'username', 'password'];
    let missingFields = [];
    
    requiredFields.forEach(field => {
        const input = form.querySelector(`[name="${field}"]`);
        if (!input || !input.value.trim()) {
            missingFields.push(field);
        }
    });
    
    if (missingFields.length > 0) {
        alert('Preencha todos os campos obrigatórios da configuração LDAP:\n' + 
              missingFields.join(', '));
        return;
    }
    
    // Confirmar início da importação
    const batchSize = document.getElementById('bulk_batch_size').value;
    const filter = document.getElementById('bulk_filter').value;
    
    if (!confirm(`Iniciar importação em lotes de ${batchSize} usuários?\n\nEsta operação pode levar vários minutos e será executada em segundo plano.`)) {
        return;
    }
    
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Iniciando...';
    
    // Coletar dados do formulário principal
    const formData = new FormData();
    formData.append('_token', form.querySelector('[name="_token"]').value);
    formData.append('host', form.querySelector('[name="host"]').value);
    formData.append('base_dn', form.querySelector('[name="base_dn"]').value);
    formData.append('username', form.querySelector('[name="username"]').value);
    formData.append('password', form.querySelector('[name="password"]').value);
    formData.append('port', form.querySelector('[name="port"]').value || '389');
    formData.append('ssl', form.querySelector('[name="ssl"]').value || '0');
    formData.append('batch_size', batchSize);
    if (filter) formData.append('filter', filter);
    
    fetch('{{ route("admin.ldap.import.bulk") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bulkImportJobId = data.job_id;
            
            // Mostrar seção de progresso
            document.getElementById('bulkProgressSection').style.display = 'block';
            document.getElementById('bulkTotalPages').textContent = data.total_pages;
            
            // Iniciar monitoramento
            startProgressMonitoring();
            
            // Scroll para a seção de progresso
            document.getElementById('bulkProgressSection').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        } else {
            alert('Erro ao iniciar importação:\n' + data.message);
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-rocket me-2"></i>Iniciar Importação em Lotes';
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao iniciar importação. Verifique o console para detalhes.');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-rocket me-2"></i>Iniciar Importação em Lotes';
    });
}

// Função para monitorar progresso
function startProgressMonitoring() {
    if (!bulkImportJobId) return;
    
    bulkImportInterval = setInterval(() => {
        checkBulkProgress();
    }, 3000); // Verificar a cada 3 segundos
    
    // Primeira verificação imediata
    checkBulkProgress();
}

// Função para verificar progresso
function checkBulkProgress() {
    if (!bulkImportJobId) return;
    
    fetch(`{{ route("admin.ldap.import.progress", ":jobId") }}`.replace(':jobId', bulkImportJobId))
    .then(response => {
        if (response.status === 404) {
            // Job não encontrado ou finalizado - parar monitoramento
            console.log('Job finalizado ou não encontrado');
            clearInterval(bulkImportInterval);
            bulkImportInterval = null;
            
            // Reabilitar botão de importação
            const btn = document.getElementById('bulkImportBtn');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-rocket me-2"></i>Iniciar Importação em Lotes';
            
            // Mostrar status finalizado
            const statusElement = document.getElementById('bulkStatus');
            statusElement.className = 'badge bg-success';
            statusElement.textContent = 'Finalizado';
            
            return null;
        }
        return response.json();
    })
    .then(data => {
        if (!data) return; // Job finalizado
        
        if (data.success) {
            updateProgressUI(data.data);
            
            // Parar monitoramento se completado ou falhou
            if (data.data.status === 'completed' || data.data.status === 'failed' || data.data.status === 'cancelled') {
                clearInterval(bulkImportInterval);
                bulkImportInterval = null;
                
                // Reabilitar botão de importação
                const btn = document.getElementById('bulkImportBtn');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-rocket me-2"></i>Iniciar Importação em Lotes';
            }
        } else {
            console.error('Erro ao verificar progresso:', data.message);
            // Se erro persistir, parar monitoramento após algumas tentativas
            errorCount = (errorCount || 0) + 1;
            if (errorCount > 5) {
                clearInterval(bulkImportInterval);
                bulkImportInterval = null;
                const btn = document.getElementById('bulkImportBtn');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-rocket me-2"></i>Iniciar Importação em Lotes';
            }
        }
    })
    .catch(error => {
        console.error('Erro na requisição de progresso:', error);
        // Se erro persistir, parar monitoramento
        errorCount = (errorCount || 0) + 1;
        if (errorCount > 5) {
            clearInterval(bulkImportInterval);
            bulkImportInterval = null;
            const btn = document.getElementById('bulkImportBtn');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-rocket me-2"></i>Iniciar Importação em Lotes';
        }
    });
}

// Função para atualizar interface de progresso
function updateProgressUI(progress) {
    // Atualizar barra de progresso
    const percentage = progress.progress_percentage || 0;
    const progressBar = document.getElementById('bulkProgressBar');
    progressBar.style.width = percentage + '%';
    progressBar.textContent = percentage.toFixed(1) + '%';
    
    // Atualizar status
    const statusElement = document.getElementById('bulkStatus');
    let statusClass = 'bg-info';
    let statusText = progress.status || 'processando';
    
    switch (progress.status) {
        case 'queued':
            statusClass = 'bg-secondary';
            statusText = 'Na fila';
            break;
        case 'processing':
            statusClass = 'bg-primary';
            statusText = 'Processando';
            break;
        case 'completed':
            statusClass = 'bg-success';
            statusText = 'Concluído';
            break;
        case 'failed':
            statusClass = 'bg-danger';
            statusText = 'Falhou';
            break;
        case 'cancelled':
            statusClass = 'bg-warning';
            statusText = 'Cancelado';
            break;
    }
    
    statusElement.className = `badge ${statusClass}`;
    statusElement.textContent = statusText;
    
    // Atualizar contadores
    document.getElementById('bulkCurrentPage').textContent = progress.current_page || 0;
    document.getElementById('bulkImported').textContent = progress.imported || 0;
    document.getElementById('bulkSkipped').textContent = progress.skipped || 0;
    document.getElementById('bulkMessage').textContent = progress.message || 'Processando...';
    
    // Mostrar/ocultar botão de cancelar
    const cancelBtn = document.getElementById('cancelBulkBtn');
    if (progress.status === 'processing' || progress.status === 'queued') {
        cancelBtn.style.display = 'inline-block';
    } else {
        cancelBtn.style.display = 'none';
    }
}

// Função para cancelar importação
function cancelBulkImport() {
    if (!bulkImportJobId) return;
    
    if (!confirm('Tem certeza que deseja cancelar a importação em andamento?')) {
        return;
    }
    
    const cancelBtn = document.getElementById('cancelBulkBtn');
    cancelBtn.disabled = true;
    cancelBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Cancelando...';
    
    fetch(`{{ route("admin.ldap.import.cancel", ":jobId") }}`.replace(':jobId', bulkImportJobId), {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('[name="_token"]').value,
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Parar monitoramento
            if (bulkImportInterval) {
                clearInterval(bulkImportInterval);
                bulkImportInterval = null;
            }
            
            // Atualizar UI
            updateProgressUI({
                status: 'cancelled',
                message: 'Importação cancelada pelo usuário'
            });
            
            alert('Importação cancelada com sucesso.');
        } else {
            alert('Erro ao cancelar importação: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Erro ao cancelar:', error);
        alert('Erro ao cancelar importação.');
    })
    .finally(() => {
        cancelBtn.disabled = false;
        cancelBtn.innerHTML = '<i class="fas fa-stop me-1"></i>Cancelar Importação';
    });
}

// Funções para busca em tempo real na tabela
function filterTable() {
    const searchTerm = document.getElementById('tableSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.user-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const name = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
        const login = row.querySelector('td:nth-child(3)').textContent.toLowerCase();
        const email = row.querySelector('td:nth-child(4)').textContent.toLowerCase();
        const department = row.querySelector('td:nth-child(5)').textContent.toLowerCase();
        const title = row.querySelector('td:nth-child(6)').textContent.toLowerCase();
        
        const isVisible = name.includes(searchTerm) || 
                         login.includes(searchTerm) || 
                         email.includes(searchTerm) || 
                         department.includes(searchTerm) || 
                         title.includes(searchTerm);
        
        row.style.display = isVisible ? '' : 'none';
        if (isVisible) visibleCount++;
    });
    
    // Atualizar contador de visíveis
    document.getElementById('visibleCount').textContent = `${visibleCount} visíveis`;
    
    // Atualizar contador de selecionados
    updateSelectedCount();
}

function selectVisible() {
    const checkboxes = document.querySelectorAll('.ldap-check');
    checkboxes.forEach(checkbox => {
        const row = checkbox.closest('.user-row');
        if (row.style.display !== 'none') {
            checkbox.checked = true;
        }
    });
    updateSelectedCount();
}

function normalizeString(str) {
    return str.normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
}

// Busca aprimorada que normaliza acentos
function filterTableAdvanced() {
    const searchTerm = normalizeString(document.getElementById('tableSearch').value);
    const rows = document.querySelectorAll('.user-row');
    let visibleCount = 0;
    
    if (searchTerm.length === 0) {
        // Se não há termo de busca, mostrar todos
        rows.forEach(row => {
            row.style.display = '';
            visibleCount++;
        });
    } else {
        rows.forEach(row => {
            const name = normalizeString(row.querySelector('td:nth-child(2)').textContent);
            const login = normalizeString(row.querySelector('td:nth-child(3)').textContent);
            const email = normalizeString(row.querySelector('td:nth-child(4)').textContent);
            const department = normalizeString(row.querySelector('td:nth-child(5)').textContent);
            const title = normalizeString(row.querySelector('td:nth-child(6)').textContent);
            
            // Busca por termo completo ou por partes (palavras separadas)
            const searchWords = searchTerm.split(' ').filter(word => word.length > 1);
            let isVisible = false;
            
            if (searchWords.length === 0) {
                // Termo muito curto, buscar como está
                isVisible = name.includes(searchTerm) || 
                           login.includes(searchTerm) || 
                           email.includes(searchTerm) || 
                           department.includes(searchTerm) || 
                           title.includes(searchTerm);
            } else {
                // Buscar cada palavra separadamente
                isVisible = searchWords.every(word => 
                    name.includes(word) || 
                    login.includes(word) || 
                    email.includes(word) || 
                    department.includes(word) || 
                    title.includes(word)
                );
            }
            
            row.style.display = isVisible ? '' : 'none';
            if (isVisible) visibleCount++;
        });
    }
    
    // Atualizar contador de visíveis
    document.getElementById('visibleCount').textContent = `${visibleCount} visíveis`;
    
    // Atualizar contador de selecionados
    updateSelectedCount();
}

// Substituir a função filterTable pela versão avançada
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('tableSearch');
    if (searchInput) {
        // Usar a função avançada ao invés da básica
        searchInput.onkeyup = filterTableAdvanced;
        
        // Adicionar placeholder dinâmico
        const totalUsers = document.querySelectorAll('.user-row').length;
        searchInput.placeholder = `Buscar entre ${totalUsers} usuários...`;
    }
});
</script>
@endsection
