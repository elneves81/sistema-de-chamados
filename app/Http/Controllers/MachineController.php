<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class MachineController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Machine::with('user');

        // Filtros
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('patrimonio', 'like', "%{$search}%")
                  ->orWhere('numero_serie', 'like', "%{$search}%")
                  ->orWhere('modelo', 'like', "%{$search}%")
                  ->orWhere('marca', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('assinatura_status')) {
            $query->where('assinatura_status', $request->assinatura_status);
        }

        $machines = $query->orderBy('created_at', 'desc')->paginate(15);

        // Estatísticas
        $stats = [
            'total' => Machine::count(),
            'ativas' => Machine::where('status', 'ativo')->count(),
            'manutencao' => Machine::where('status', 'manutencao')->count(),
            'vinculadas' => Machine::whereNotNull('user_id')->count(),
            'assinaturas_pendentes' => Machine::where('assinatura_status', 'pendente')->count(),
            'assinaturas_validadas' => Machine::where('assinatura_status', 'validada')->count(),
        ];

        return view('machines.index', compact('machines', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $users = User::orderBy('name')->get();
        return view('machines.create', compact('users'));
    }

    /**
     * Show the tablet-optimized form for creating a new resource.
     */
    public function createTablet()
    {
        return view('machines.create-tablet');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Patrimônio é opcional para teclado e mouse
        $patrimonioRule = in_array($request->input('tipo'), ['teclado', 'mouse']) 
            ? 'nullable|string|unique:machines,patrimonio' 
            : 'required|string|unique:machines,patrimonio';
        
        // Número de série é obrigatório apenas para desktop
        $numeroSerieRule = $request->input('tipo') === 'desktop'
            ? 'required|string|unique:machines,numero_serie'
            : 'nullable|string|unique:machines,numero_serie';
        
        $validated = $request->validate([
            'patrimonio' => $patrimonioRule,
            'numero_serie' => $numeroSerieRule,
            'modelo' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'tipo' => 'required|in:desktop,notebook,servidor,monitor,impressora,nobreak,estabilizador,switch,teclado,mouse',
            'descricao' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'processador' => 'nullable|string|max:255',
            'memoria_ram' => 'nullable|string|max:255',
            'armazenamento' => 'nullable|string|max:255',
            'sistema_operacional' => 'nullable|string|max:255',
            'data_aquisicao' => 'nullable|date',
            'valor_aquisicao' => 'nullable|numeric|min:0',
            'status' => 'required|in:ativo,inativo,manutencao,descartado',
            'observacoes' => 'nullable|string',
            // Campos adicionais do cadastro via tablet
            'contrato_licitacao' => 'nullable|string|max:255',
            'numero_licitacao' => 'nullable|string|max:255',
            'is_troca' => 'nullable|boolean',
            'patrimonio_substituido' => 'nullable|string|max:255',
            'motivo_troca' => 'nullable|string',
            'recebedor_id' => 'nullable|exists:users,id',
            'data_entrega' => 'nullable|date',
            'assinatura_digital' => 'nullable|string',
            'nome_legivel_assinatura' => 'nullable|string|max:255',
            'entregue_por_id' => 'nullable|exists:users,id',
            'observacoes_entrega' => 'nullable|string',
            // Campos de validação de assinatura
            'cadastro_parcial' => 'nullable|boolean',
            'assinatura_usuario_validador' => 'nullable|string|max:255',
        ]);

        // Normalizações e defaults
        // Se não informado user_id mas houver recebedor_id, vincula a máquina ao recebedor
        if (empty($validated['user_id']) && !empty($validated['recebedor_id'])) {
            $validated['user_id'] = $validated['recebedor_id'];
        }
        
        // Marca IP da entrega caso assinatura/recebedor tenham sido informados
        if (!empty($validated['assinatura_digital']) || !empty($validated['recebedor_id'])) {
            $validated['ip_entrega'] = $request->ip();
        }
        
        // Normaliza boolean de troca e cadastro parcial
        $validated['is_troca'] = $request->boolean('is_troca');
        $cadastroParcial = $request->boolean('cadastro_parcial');
        
        // Define status da assinatura
        if ($cadastroParcial) {
            // Cadastro parcial: assinatura pendente
            $validated['assinatura_status'] = 'pendente';
        } elseif (!empty($validated['assinatura_digital']) && !empty($validated['assinatura_usuario_validador'])) {
            // Assinatura coletada e validada
            $validated['assinatura_status'] = 'validada';
            $validated['assinatura_validada_em'] = now();
            
            // Busca o ID do usuário validador
            $validador = User::where('username', $validated['assinatura_usuario_validador'])
                            ->orWhere('email', $validated['assinatura_usuario_validador'])
                            ->first();
            
            if ($validador) {
                $validated['assinatura_validada_por'] = $validador->id;
            }
        } else {
            // Sem necessidade de assinatura
            $validated['assinatura_status'] = 'nao_requerida';
        }
        
        // Remove campos auxiliares
        unset($validated['cadastro_parcial']);

        Machine::create($validated);

        return redirect()->route('machines.index')
            ->with('success', 'Máquina cadastrada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Machine $machine)
    {
        $machine->load('user', 'recebedor', 'entregador');
        
        // Buscar máquina antiga se for troca
        $oldMachine = null;
        if ($machine->is_troca && $machine->patrimonio_substituido) {
            $oldMachine = Machine::where('patrimonio', $machine->patrimonio_substituido)->first();
        }
        
        // Buscar máquinas entregues juntas (mesmo técnico, mesma data/hora, mesma pessoa)
        $deliveredTogether = collect();
        if ($machine->data_entrega && $machine->entregue_por_id && $machine->recebedor_id) {
            $deliveredTogether = Machine::where('id', '!=', $machine->id)
                ->where('recebedor_id', $machine->recebedor_id)
                ->where('entregue_por_id', $machine->entregue_por_id)
                ->whereDate('data_entrega', $machine->data_entrega->format('Y-m-d'))
                ->where('is_troca', false)
                ->with('user')
                ->get();
        }
        
        return view('machines.show', compact('machine', 'oldMachine', 'deliveredTogether'));
    }
    
    /**
     * Retorna a assinatura como imagem PNG
     */
    public function getSignature(Machine $machine)
    {
        if (!$machine->assinatura_digital) {
            abort(404, 'Assinatura não encontrada');
        }
        
        // Remove o prefixo data:image/png;base64, se existir
        $imageData = $machine->assinatura_digital;
        if (strpos($imageData, 'data:image') === 0) {
            $imageData = substr($imageData, strpos($imageData, ',') + 1);
        }
        
        // Decodifica base64
        $image = base64_decode($imageData);
        
        return response($image)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'public, max-age=31536000');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Machine $machine)
    {
        $users = User::orderBy('name')->get();
        return view('machines.edit', compact('machine', 'users'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Machine $machine)
    {
        // Patrimônio é opcional para teclado e mouse
        $patrimonioRule = in_array($request->input('tipo'), ['teclado', 'mouse']) 
            ? 'nullable|string|unique:machines,patrimonio,' . $machine->id 
            : 'required|string|unique:machines,patrimonio,' . $machine->id;
        
        // Número de série é obrigatório apenas para desktop
        $numeroSerieRule = $request->input('tipo') === 'desktop'
            ? 'required|string|unique:machines,numero_serie,' . $machine->id
            : 'nullable|string|unique:machines,numero_serie,' . $machine->id;
        
        $validated = $request->validate([
            'patrimonio' => $patrimonioRule,
            'numero_serie' => $numeroSerieRule,
            'modelo' => 'required|string|max:255',
            'marca' => 'nullable|string|max:255',
            'tipo' => 'required|in:desktop,notebook,servidor,monitor,impressora,nobreak,estabilizador,switch,teclado,mouse',
            'descricao' => 'nullable|string',
            'user_id' => 'nullable|exists:users,id',
            'processador' => 'nullable|string|max:255',
            'memoria_ram' => 'nullable|string|max:255',
            'armazenamento' => 'nullable|string|max:255',
            'sistema_operacional' => 'nullable|string|max:255',
            'data_aquisicao' => 'nullable|date',
            'valor_aquisicao' => 'nullable|numeric|min:0',
            'status' => 'required|in:ativo,inativo,manutencao,descartado',
            'observacoes' => 'nullable|string',
            // Campos adicionais
            'contrato_licitacao' => 'nullable|string|max:255',
            'numero_licitacao' => 'nullable|string|max:255',
            'is_troca' => 'nullable|boolean',
            'patrimonio_substituido' => 'nullable|string|max:255',
            'motivo_troca' => 'nullable|string',
            'recebedor_id' => 'nullable|exists:users,id',
            'data_entrega' => 'nullable|date',
            'assinatura_digital' => 'nullable|string',
            'nome_legivel_assinatura' => 'nullable|string|max:255',
            'entregue_por_id' => 'nullable|exists:users,id',
            'observacoes_entrega' => 'nullable|string',
            'solicitar_nova_assinatura' => 'nullable|boolean',
        ]);

        // Normaliza boolean de troca
        $validated['is_troca'] = $request->boolean('is_troca');
        
        // Protege campos de assinatura - não permite sobrescrever com null
        // a menos que seja uma solicitação explícita de nova assinatura
        if (!$request->boolean('solicitar_nova_assinatura')) {
            // Remove campos de assinatura do validated para não sobrescrever
            unset($validated['assinatura_digital']);
            unset($validated['nome_legivel_assinatura']);
            // Mantém campos de validação se estiverem vazios na request
            if (empty($validated['assinatura_status'])) {
                unset($validated['assinatura_status']);
            }
        }
        
        // Se usuário com permissão machines.edit solicitar nova assinatura, atualiza campos de entrega
        if ($request->boolean('solicitar_nova_assinatura') && auth()->user()->can('machines.edit')) {
            $validated['data_entrega'] = now();
            $validated['entregue_por_id'] = auth()->id();
            $validated['ip_entrega'] = $request->ip();
            // Limpa assinatura anterior
            $validated['assinatura_digital'] = null;
            $validated['nome_legivel_assinatura'] = null;
            $validated['assinatura_status'] = 'pendente';
            $validated['assinatura_validada_em'] = null;
            $validated['assinatura_validada_por'] = null;
            $validated['assinatura_usuario_validador'] = null;
            $validated['assinatura_validada_por_terceiro'] = false;
        }
        
        // Remove o campo solicitar_nova_assinatura antes de salvar
        unset($validated['solicitar_nova_assinatura']);

        $machine->update($validated);

        return redirect()->route('machines.index')
            ->with('success', 'Máquina atualizada com sucesso!');
    }

    /**
     * Valida credenciais LDAP do usuário para certificar assinatura.
     */
    public function validateSignature(Request $request)
    {
        // Verifica se há uma sessão ativa
        if (!Auth::check()) {
            Log::warning('Tentativa de validação sem sessão ativa', [
                'ip' => $request->ip(),
                'login_tentado' => $request->input('login')
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Sua sessão expirou. Por favor, faça login novamente.',
                'session_expired' => true
            ], 401);
        }
        
        $request->validate([
            'login' => 'required|string',
            'senha' => 'required|string',
            'machine_id' => 'sometimes|integer|exists:machines,id',
            'validar_por_terceiro' => 'sometimes|boolean', // Permite validação por terceiro
        ]);

        $login = $request->input('login');
        $senha = $request->input('senha');
        $machineId = $request->input('machine_id');
        $validarPorTerceiro = $request->input('validar_por_terceiro', false);

        Log::info('Tentativa de validação de assinatura', [
            'usuario_logado' => Auth::user()->username,
            'usuario_logado_id' => Auth::id(),
            'login_para_validar' => $login,
            'senha_length' => strlen($senha),
            'machine_id' => $machineId ?? 'N/A (criação)',
            'validar_por_terceiro' => $validarPorTerceiro,
            'ip' => $request->ip()
        ]);

        // Busca a máquina para verificar quem é o recebedor (se machine_id fornecido)
        $machine = null;
        if ($machineId) {
            $machine = Machine::find($machineId);
            
            if (!$machine) {
                Log::warning('Máquina não encontrada', ['machine_id' => $machineId]);
                return response()->json([
                    'success' => false,
                    'message' => 'Máquina não encontrada.',
                ], 404);
            }
        }

        // Tenta autenticar via LDAP
        try {
            // Primeiro verifica se o usuário existe no banco (case-insensitive)
            $user = User::whereRaw('LOWER(username) = ?', [strtolower($login)])
                       ->orWhereRaw('LOWER(email) = ?', [strtolower($login)])
                       ->first();

            if (!$user) {
                Log::warning('Usuário não encontrado', ['login' => $login]);
                return response()->json([
                    'success' => false,
                    'message' => 'Usuário não encontrado no sistema.',
                ], 401);
            }
            
            // VALIDAÇÃO: Verifica se o usuário que está validando é o recebedor OU se tem permissão para validar por terceiro
            // Apenas valida se há uma máquina (não é criação)
            if ($machine) {
                $usuarioLogado = Auth::user();
                $isRecebedor = ($user->id === $machine->user_id);
                $podeValidarPorTerceiro = $validarPorTerceiro && (
                    $usuarioLogado->role === 'admin' || 
                    $usuarioLogado->role === 'technician' ||
                    $usuarioLogado->hasPermissionTo('machines.manage')
                );
                
                if (!$isRecebedor && !$podeValidarPorTerceiro) {
                    Log::warning('Tentativa de validação por usuário não autorizado', [
                        'user_tentando' => $user->id,
                        'user_recebedor' => $machine->user_id,
                        'machine_id' => $machineId,
                        'validar_por_terceiro' => $validarPorTerceiro,
                        'pode_validar_terceiro' => $podeValidarPorTerceiro
                    ]);
                    return response()->json([
                        'success' => false,
                        'message' => 'Apenas o recebedor da máquina pode validar a assinatura. Se ele não estiver disponível, marque a opção "Validar por terceiro".',
                    ], 403);
                }
                
                // Se está validando por terceiro, registra essa informação
                if (!$isRecebedor && $podeValidarPorTerceiro) {
                    Log::info('Validação sendo feita por terceiro autorizado', [
                        'recebedor_id' => $machine->user_id,
                        'validador_id' => $user->id,
                        'usuario_logado_id' => $usuarioLogado->id,
                        'machine_id' => $machineId
                    ]);
                }
            }

            Log::info('Usuário encontrado', [
                'user_id' => $user->id,
                'username' => $user->username,
                'auth_via_ldap' => $user->auth_via_ldap
            ]);

            // Verifica se LDAP está configurado e usuário tem autenticação LDAP habilitada
            if (config('ldap.connections.default') && $user->auth_via_ldap) {
                Log::info('Iniciando autenticação LDAP');
                
                // Usa provider LDAP se configurado
                try {
                    $ldapConfig = config('ldap.connections.default');
                    
                    // Remove configurações incompatíveis com Adldap2 v10
                    $settings = $ldapConfig['settings'] ?? $ldapConfig;
                    unset($ldapConfig['auto_connect']);
                    unset($ldapConfig['connection']);
                    
                    Log::info('Configuração LDAP', [
                        'hosts' => $settings['hosts'] ?? 'N/A',
                        'base_dn' => $settings['base_dn'] ?? 'N/A'
                    ]);
                    
                    // Configura provider sem conectar automaticamente
                    $ad = new \Adldap\Adldap();
                    $ad->addProvider($settings, 'default');
                    $provider = $ad->getProvider('default');
                    
                    Log::info('Tentando autenticação LDAP', [
                        'username' => $user->username,
                        'bind_as_user' => true
                    ]);
                    
                    // Tenta diferentes formatos de username para LDAP
                    // IMPORTANTE: Ordem importa! Primeiro formato exato do banco (funcionando para elber.pmg)
                    $formatos = [
                        $user->username . '@guarapuava.pr.gov.br', // UPN format com case exato do banco (PRINCIPAL)
                        $user->username, // Username simples com case exato
                        strtoupper($user->username) . '@guarapuava.pr.gov.br', // UPN uppercase (caso o AD exija)
                        strtolower($user->username) . '@guarapuava.pr.gov.br', // UPN lowercase
                        ucfirst(strtolower($user->username)) . '@guarapuava.pr.gov.br', // Primeira letra maiúscula
                        'GUARAPUAVA\\' . $user->username, // Domain\username
                    ];
                    
                    $autenticado = false;
                    $ultimoErro = null;
                    
                    foreach ($formatos as $formato) {
                        try {
                            Log::debug('Tentando formato LDAP', ['formato' => $formato]);
                            
                            if ($provider->auth()->attempt($formato, $senha, $bindAsUser = true)) {
                                $autenticado = true;
                                Log::info('Autenticação LDAP bem-sucedida', [
                                    'user_id' => $user->id,
                                    'username' => $user->username,
                                    'formato_usado' => $formato
                                ]);
                                break;
                            }
                        } catch (\Exception $e) {
                            // Continua tentando outros formatos
                            $ultimoErro = $e->getMessage();
                            Log::debug('Formato LDAP falhou', [
                                'formato' => $formato,
                                'erro' => $e->getMessage()
                            ]);
                        }
                    }
                    
                    // Tenta autenticar diretamente com as credenciais do usuário
                    // Isso fará bind com as credenciais fornecidas sem precisar de admin
                    if ($autenticado) {
                        // Autenticação LDAP bem-sucedida
                        Log::info('Autenticação LDAP validada com sucesso', ['user_id' => $user->id]);
                        
                        // Se machine_id foi fornecido, atualiza o status da assinatura
                        if ($machine) {
                            $machine->assinatura_status = 'validada';
                            $machine->assinatura_validada_em = now();
                            $machine->assinatura_validada_por = $user->id; // ID do usuário que validou
                            $machine->assinatura_usuario_validador = $user->username;
                            $machine->assinatura_validada_por_terceiro = $validarPorTerceiro;
                            $machine->save();
                            
                            Log::info('Status da assinatura atualizado', [
                                'machine_id' => $machine->id,
                                'validado_por' => $user->id,
                                'usuario_validador' => $user->username
                            ]);
                        }
                        
                        return response()->json([
                            'success' => true,
                            'message' => 'Credenciais validadas com sucesso.',
                            'user_name' => $user->name,
                            'user_id' => $user->id,
                        ]);
                    } else {
                        Log::warning('LDAP authentication failed for user', [
                            'username' => $user->username,
                            'ultimo_erro' => $ultimoErro,
                            'tentando_fallback' => 'autenticação local'
                        ]);
                        
                        // Fallback: tenta autenticação local se LDAP falhou
                        Log::info('Tentando autenticação local como fallback');
                        
                        // Verifica senha usando Hash::check (não faz login)
                        if (Hash::check($senha, $user->password)) {
                            Log::info('Autenticação local bem-sucedida (fallback)', ['user_id' => $user->id]);
                            
                            // Se machine_id foi fornecido, atualiza o status da assinatura
                            if ($machine) {
                                $machine->assinatura_status = 'validada';
                                $machine->assinatura_validada_em = now();
                                $machine->assinatura_validada_por = $user->id; // ID do usuário que validou
                                $machine->assinatura_usuario_validador = $user->username;
                                $machine->assinatura_validada_por_terceiro = $validarPorTerceiro;
                                $machine->save();
                                
                                Log::info('Status da assinatura atualizado (fallback)', [
                                    'machine_id' => $machine->id,
                                    'validado_por' => $user->id
                                ]);
                            }
                            
                            return response()->json([
                                'success' => true,
                                'message' => 'Credenciais validadas com sucesso.',
                                'user_name' => $user->name,
                                'user_id' => $user->id,
                            ]);
                        }
                        
                        Log::warning('Todas as tentativas de autenticação falharam', [
                            'user_id' => $user->id,
                            'username' => $user->username
                        ]);
                        
                        return response()->json([
                            'success' => false,
                            'message' => 'Login ou senha inválidos.',
                        ], 401);
                    }
                } catch (\Exception $e) {
                    Log::error('Erro ao validar credenciais LDAP: ' . $e->getMessage());
                    Log::error('Stack trace: ' . $e->getTraceAsString());
                    
                    return response()->json([
                        'success' => false,
                        'message' => 'Erro ao validar credenciais LDAP. Tente novamente.',
                    ], 500);
                }
            } else {
                // Sem LDAP ou usuário sem LDAP habilitado - usa autenticação local
                Log::info('Usando autenticação local (sem LDAP)');
                
                // Verifica senha sem fazer login (usando Hash::check)
                if (Hash::check($senha, $user->password)) {
                    Log::info('Autenticação local bem-sucedida via hash', ['user_id' => $user->id]);
                    
                    // Se machine_id foi fornecido, atualiza o status da assinatura
                    if ($machine) {
                        $machine->assinatura_status = 'validada';
                        $machine->assinatura_validada_em = now();
                        $machine->assinatura_validada_por = $user->id; // ID do usuário que validou
                        $machine->assinatura_usuario_validador = $user->username;
                        $machine->assinatura_validada_por_terceiro = $validarPorTerceiro;
                        $machine->save();
                        
                        Log::info('Status da assinatura atualizado (autenticação local)', [
                            'machine_id' => $machine->id,
                            'validado_por' => $user->id
                        ]);
                    }
                    
                    return response()->json([
                        'success' => true,
                        'message' => 'Credenciais validadas com sucesso.',
                        'user_name' => $user->name,
                        'user_id' => $user->id,
                    ]);
                }
                
                Log::warning('Autenticação local falhou', [
                    'user_id' => $user->id,
                    'username' => $user->username
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'Login ou senha inválidos.',
                ], 401);
            }

        } catch (\Exception $e) {
            Log::error('Erro na validação de assinatura: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao validar credenciais. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Salvar assinatura digital
     */
    public function saveSignature(Request $request, Machine $machine)
    {
        try {
            $validated = $request->validate([
                'assinatura_digital' => 'required|string',
                'nome_legivel_assinatura' => 'required|string|max:255',
                'assinatura_status' => 'required|in:nao_requerida,pendente,validada',
            ]);

            $machine->update([
                'assinatura_digital' => $validated['assinatura_digital'],
                'nome_legivel_assinatura' => $validated['nome_legivel_assinatura'],
                'assinatura_status' => $validated['assinatura_status'],
            ]);

            Log::info('Assinatura digital salva', [
                'machine_id' => $machine->id,
                'patrimonio' => $machine->patrimonio,
                'nome_legivel' => $validated['nome_legivel_assinatura'],
                'status' => $validated['assinatura_status'],
                'usuario_logado' => Auth::id()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Assinatura salva com sucesso.',
                'machine_id' => $machine->id
            ]);

        } catch (\Exception $e) {
            Log::error('Erro ao salvar assinatura: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao salvar assinatura. Tente novamente.',
            ], 500);
        }
    }

    /**
     * Atualiza o status da assinatura para validada
     */
    public function updateSignatureStatus(Request $request, Machine $machine)
    {
        // Verifica se há uma sessão ativa
        if (!Auth::check()) {
            Log::warning('Tentativa de atualizar status sem sessão ativa', [
                'ip' => $request->ip(),
                'machine_id' => $machine->id
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Sua sessão expirou. Por favor, faça login novamente.',
                'session_expired' => true
            ], 401);
        }
        
        try {
            $validated = $request->validate([
                'usuario_validador' => 'required|string',
                'user_id' => 'required|integer|exists:users,id',
                'validado_por_terceiro' => 'sometimes|boolean',
            ]);
            
            $validadoPorTerceiro = $validated['validado_por_terceiro'] ?? false;
            
            $machine->update([
                'assinatura_status' => 'validada',
                'assinatura_validada_em' => now(),
                'assinatura_validada_por' => $validated['user_id'],
                'assinatura_usuario_validador' => $validated['usuario_validador'],
                'assinatura_validada_por_terceiro' => $validadoPorTerceiro,
            ]);
            
            Log::info('Assinatura validada', [
                'machine_id' => $machine->id,
                'validado_por' => $validated['user_id'],
                'usuario_validador' => $validated['usuario_validador'],
                'usuario_logado' => Auth::id(),
                'recebedor_id' => $machine->user_id,
                'validado_por_terceiro' => $validadoPorTerceiro
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Assinatura validada com sucesso!'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Erro ao atualizar status da assinatura: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao atualizar status. Tente novamente.'
            ], 500);
        }
    }

    /**
     * Busca usuários para validação de assinatura
     */
    public function searchUsers(Request $request)
    {
        $search = $request->input('q', '');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }
        
        $users = User::where(function($query) use ($search) {
                $query->whereRaw('LOWER(username) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(name) LIKE ?', ['%' . strtolower($search) . '%'])
                      ->orWhereRaw('LOWER(email) LIKE ?', ['%' . strtolower($search) . '%']);
            })
            ->select('id', 'username', 'name', 'email')
            ->limit(10)
            ->get();
        
        return response()->json($users);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Machine $machine)
    {
        $machine->delete();

        return redirect()->route('machines.index')
            ->with('success', 'Máquina removida com sucesso!');
    }
}
