<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;
use App\Jobs\LdapBulkImportJob;
use Adldap\Adldap as AdldapClient;

class LdapImportController extends Controller
{
    public function showImportForm()
    {
        return view("ldap.import");
    }

    public function preview(Request $request)
    {
        // Aumentar limite de tempo para consultas LDAP grandes
        set_time_limit(300); // 5 minutos
        ini_set('max_execution_time', 300);
        
        $validator = Validator::make($request->all(), [
            "host"     => "required|string",
            "base_dn"  => "required|string", 
            "username" => "required|string",
            "password" => "required|string",
            "port"     => "nullable|integer|min:1|max:65535",
            "ssl"      => "nullable|boolean",
            "filter"   => "nullable|string|max:80",
            "size"     => "nullable|integer|min:1|max:2000", // Aumentar limite máximo
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $size = $request->integer("size", 500); // Aumentar padrão para 500
        $filter = trim((string) $request->input("filter", ""));
        $preview = [];

        try {
            $provider = $this->connect($request);
            
            // Campos essenciais incluindo nomes separados para busca melhorada
            $select = [
                "cn", 
                "samaccountname", 
                "mail", 
                "userprincipalname", 
                "distinguishedname",
                "displayname",
                "useraccountcontrol",
                "givenname",        // Primeiro nome
                "sn",               // Sobrenome (surname)
                "name",             // Nome completo
                "description",      // Descrição do usuário
                "title",            // Cargo/título
                "department",       // Departamento
                "company",          // Empresa
                "telephonenumber",  // Telefone
                "mobile"            // Celular
            ];
            
            $search = $provider->search()->users()->select($select);

            if ($request->filled("base_dn")) {
                $search->in($request->base_dn);
            }

            // Busca simples e eficaz por nome (compatível com Adldap)
            if ($filter !== "") {
                // Normalizar o termo de busca
                $normalizedFilter = $this->normalizeSearchTerm($filter);
                
                // Busca nos campos principais (sem closures complexas)
                $search->whereContains("samaccountname", $filter)
                       ->orWhereContains("cn", $filter)
                       ->orWhereContains("displayname", $filter)
                       ->orWhereContains("userprincipalname", $filter)
                       ->orWhereContains("mail", $filter)
                       ->orWhereContains("givenname", $filter)
                       ->orWhereContains("sn", $filter)
                       ->orWhereContains("name", $filter);
                
                // Se o filtro é diferente do normalizado, adicionar busca normalizada
                if ($normalizedFilter !== $filter && strlen($normalizedFilter) >= 2) {
                    $search->orWhereContains("cn", $normalizedFilter)
                           ->orWhereContains("displayname", $normalizedFilter);
                }
            }

            // REMOVIDO: Filtro para usuários ativos apenas - agora importa TODOS
            // $search->where('useraccountcontrol', '=', '512'); // Conta normal ativa
            
            Log::info("LDAP Preview - Iniciando busca", [
                'size' => $size,
                'filter' => $filter,
                'base_dn' => $request->base_dn
            ]);

            $users = $search->paginate($size, 1); // Página 1, com limite
            
            Log::info("LDAP Preview - Usuários encontrados", ['count' => count($users)]);

            foreach ($users as $u) {
                $cn = method_exists($u, "getCommonName") ? $u->getCommonName() : ($u->getFirstAttribute("cn") ?? "");
                $sam = method_exists($u, "getAccountName") ? $u->getAccountName() : ($u->getFirstAttribute("samaccountname") ?? "");
                $mail = method_exists($u, "getEmail") ? $u->getEmail() : ($u->getFirstAttribute("mail") ?? "");
                $upn = $u->getFirstAttribute("userprincipalname") ?? "";
                $dn = $u->getDn() ?? ($u->getFirstAttribute("distinguishedname") ?? "");
                $displayName = $u->getFirstAttribute("displayname") ?? $cn;
                $userAccountControl = $u->getFirstAttribute("useraccountcontrol") ?? "";
                
                // Campos adicionais para busca aprimorada
                $givenName = $u->getFirstAttribute("givenname") ?? "";
                $surname = $u->getFirstAttribute("sn") ?? "";
                $fullName = $u->getFirstAttribute("name") ?? "";
                $description = $u->getFirstAttribute("description") ?? "";
                $title = $u->getFirstAttribute("title") ?? "";
                $department = $u->getFirstAttribute("department") ?? "";
                $company = $u->getFirstAttribute("company") ?? "";
                $phone = $u->getFirstAttribute("telephonenumber") ?? "";
                $mobile = $u->getFirstAttribute("mobile") ?? "";
                
                // Construir nome mais inteligente
                $bestName = $displayName ?: $fullName ?: $cn ?: $sam;
                if (!$bestName && $givenName && $surname) {
                    $bestName = trim($givenName . ' ' . $surname);
                } elseif (!$bestName && $givenName) {
                    $bestName = $givenName;
                } elseif (!$bestName && $surname) {
                    $bestName = $surname;
                }

                // Simplificar grupo - apenas primeiro ou vazio
                $group = "";
                if (method_exists($u, "getGroups")) {
                    $first = $u->getGroups()->first();
                    $group = $first ? ($first->getCommonName() ?? "") : "";
                }

                $preview[] = [
                    "dn" => $dn,
                    "cn" => $cn,
                    "sAMAccountName" => $sam,
                    "displayName" => $displayName ?: $cn ?: $sam,
                    "userPrincipalName" => $upn,
                    "mail" => $mail,
                    "department" => $department,
                    "title" => $title,
                    "company" => $company,
                    "phone" => $phone,
                    "mobile" => $mobile,
                    "givenName" => $givenName,
                    "surname" => $surname,
                    "fullName" => $fullName,
                    "description" => $description,
                    "userAccountControl" => $userAccountControl,
                    "distinguishedName" => $dn,
                    "group" => $group,
                    
                    // Manter compatibilidade com código antigo
                    "name" => $bestName ?: $upn ?: "(sem nome)",
                    "login" => $sam ?: $upn,
                    "email" => $mail ?: $upn,
                ];
            }
            
            Log::info("LDAP Preview - Processamento concluído", ['preview_count' => count($preview)]);
        } catch (\Exception $e) {
            Log::error("Erro LDAP preview: ".$e->getMessage(), ["trace" => $e->getTraceAsString()]);
            return back()->with("error", "Erro ao consultar o LDAP: " . $e->getMessage())->withInput();
        }

        usort($preview, fn($a, $b) => strcmp($a["name"], $b["name"]));

        return view("ldap.import", [
            "preview" => $preview,
            "applied_filter" => $filter,
            "limit" => $size,
        ]);
    }

    public function import(Request $request)
    {
        // Aumentar limite de tempo para importações grandes
        set_time_limit(600); // 10 minutos
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '256M'); // Aumentar memória também
        
        Log::info('LDAP Import - Dados recebidos', [
            'users_count' => count($request->input("users", [])),
            'users_sample' => array_slice($request->input("users", []), 0, 2)
        ]);

        $payload = $request->input("users");

        if (empty($payload) || !is_array($payload)) {
            Log::warning('LDAP Import - Nenhum usuário selecionado');
            return redirect()->route("admin.ldap.import.form")->with("warning", "Nenhum usuário selecionado.");
        }

        $imported = 0;
        $skipped = 0;
        $processed = 0;

        // Cache de usuários existentes para otimizar consultas
        $existingUsersByDn = [];
        $existingUsersByEmail = [];

        DB::beginTransaction();
        try {
            // Pré-carregar usuários existentes para otimizar verificações
            $allUsers = User::select('id', 'email', 'ldap_dn')->get();
            foreach ($allUsers as $user) {
                if ($user->ldap_dn) {
                    $existingUsersByDn[$user->ldap_dn] = $user;
                }
                if ($user->email) {
                    $existingUsersByEmail[$user->email] = $user;
                }
            }
            Log::info("LDAP Import - Cache preparado", [
                'users_by_dn' => count($existingUsersByDn),
                'users_by_email' => count($existingUsersByEmail)
            ]);

            foreach ($payload as $index => $row) {
                $processed++;
                Log::info("LDAP Import - Processando usuário $index ($processed/" . count($payload) . ")", ['data' => is_string($row) ? Str::limit($row, 100) : $row]);
                
                // 1) Se vier como string JSON escapado (&quot;), conserta
                if (is_string($row)) {
                    // Remove entidades HTML e tenta decodificar
                    $raw = html_entity_decode($row, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    
                    $decoded = json_decode($raw, true);
                    if (json_last_error() !== JSON_ERROR_NONE || !is_array($decoded)) {
                        Log::error("LDAP Import - Erro ao decodificar JSON do usuário $index: " . json_last_error_msg(), [
                            'sample' => Str::limit($raw, 300)
                        ]);
                        continue;
                    }
                    $row = $decoded;
                }

                // 2) Se não for array até aqui, ignora
                if (!is_array($row)) {
                    Log::error("LDAP Import - Formato inválido no usuário $index", ['row_type' => gettype($row)]);
                    continue;
                }

                // Compatibilidade com estrutura nova e antiga
                $dn = trim((string)($row["dn"] ?? $row["distinguishedName"] ?? ""));
                $upn = trim((string)($row["upn"] ?? $row["userPrincipalName"] ?? ""));
                $name = trim((string)($row["name"] ?? $row["displayName"] ?? $row["cn"] ?? ""));
                $login = trim((string)($row["login"] ?? $row["sAMAccountName"] ?? ""));
                $email = trim((string)($row["email"] ?? $row["mail"] ?? ""));

                $identity = $dn ?: $upn ?: $email ?: $login ?: $name;
                if ($identity === "") {
                    Log::warning("LDAP Import - Usuário $index sem identidade válida (totalmente vazio)", [
                        'dn' => $dn,
                        'upn' => $upn, 
                        'email' => $email,
                        'login' => $login,
                        'name' => $name,
                        'raw_data' => array_keys($row)
                    ]);
                    continue;
                }

                if ($email === "" && str_contains($upn, "@")) {
                    $email = $upn;
                }

                $randomPass = Str::random(40);

                // Verificar usuário existente usando cache (muito mais rápido)
                $user = null;
                if ($dn && isset($existingUsersByDn[$dn])) {
                    $user = $existingUsersByDn[$dn];
                    Log::info("LDAP Import - Usuário encontrado por DN no cache", ['user_id' => $user->id]);
                } elseif ($email && isset($existingUsersByEmail[$email])) {
                    $user = $existingUsersByEmail[$email];
                    Log::info("LDAP Import - Usuário encontrado por email no cache", ['user_id' => $user->id]);
                } else {
                    Log::info("LDAP Import - Usuário não encontrado no cache, será criado novo");
                }

                if ($user) {
                    Log::info("LDAP Import - Atualizando usuário existente", ['user_id' => $user->id]);
                    
                    $user->update([
                        "name"       => $name ?: ($login ?: $email ?: "Usuário LDAP"),
                        "email"      => $email ?: ($user->email ?? null),
                        "ldap_dn"    => $dn ?: ($user->ldap_dn ?? null),
                        "ldap_upn"   => $upn ?: ($user->ldap_upn ?? null),
                        "is_active"  => true,
                        "role"       => $user->role ?? "customer", // Usar 'customer' como padrão
                    ]);
                    
                    Log::info("LDAP Import - Usuário atualizado", ['user_id' => $user->id]);
                    $imported++; // Contabilizar usuário atualizado
                } else {
                    // Verificação adicional de duplicata por email usando cache
                    if ($email && isset($existingUsersByEmail[$email])) {
                        Log::warning("LDAP Import - Email já existe no cache, pulando usuário $index", ['email' => $email]);
                        $skipped++;
                        continue;
                    }
                    
                    Log::info("LDAP Import - Criando novo usuário", [
                        'name' => $name ?: ($login ?: $email ?: "Usuário LDAP"),
                        'email' => $email,
                        'ldap_dn' => $dn,
                        'ldap_upn' => $upn
                    ]);
                    
                    try {
                        // Gerar email único se não tiver
                        $finalEmail = $email;
                        if (empty($finalEmail) && !empty($login)) {
                            $finalEmail = $login . '@ldap.local';
                        }
                        if (empty($finalEmail) && !empty($upn)) {
                            $finalEmail = $upn;
                        }
                        if (empty($finalEmail) && !empty($name)) {
                            $finalEmail = Str::slug($name) . '@ldap.local';
                        }
                        if (empty($finalEmail)) {
                            $finalEmail = 'user_' . Str::random(8) . '@ldap.local';
                        }
                        
                        $newUser = User::create([
                            "name"          => $name ?: ($login ?: ($finalEmail ?: "Usuário LDAP")),
                            "email"         => $finalEmail,
                            "password"      => bcrypt($randomPass),
                            "role"          => "customer", // Usar 'customer' em vez de 'user'
                            "is_active"     => true,
                            "ldap_dn"       => $dn ?: null,
                            "ldap_upn"      => $upn ?: null,
                            "auth_via_ldap" => true,
                        ]);
                        
                        // Atualizar cache com novo usuário
                        if ($dn) {
                            $existingUsersByDn[$dn] = $newUser;
                        }
                        if ($email) {
                            $existingUsersByEmail[$email] = $newUser;
                        }
                        
                        Log::info("LDAP Import - Novo usuário criado", ['user_id' => $newUser->id]);
                        $imported++; // Contabilizar usuário criado
                    } catch (\Exception $createException) {
                        Log::error("LDAP Import - Erro ao criar usuário", [
                            'error' => $createException->getMessage(),
                            'data' => [
                                'name' => $name,
                                'email' => $email,
                                'ldap_dn' => $dn,
                                'ldap_upn' => $upn
                            ]
                        ]);
                        $skipped++;
                        continue; // Pula este usuário e continua com o próximo
                    }
                }
            }

            DB::commit();
            Log::info("LDAP Import - Importação concluída", [
                'total_imported' => $imported,
                'total_skipped' => $skipped,
                'total_processed' => $processed
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Erro ao importar usuários LDAP: ".$e->getMessage(), ["trace" => $e->getTraceAsString()]);
            return redirect()->route("admin.ldap.import.form")->with("error", "Falha ao importar usuários: " . $e->getMessage());
        }

        $message = "$imported usuário(s) importado(s) com sucesso!";
        if ($skipped > 0) {
            $message .= " ($skipped usuários ignorados por já existirem)";
        }
        
        return redirect()->route("admin.ldap.import.form")->with("success", $message);
    }

    public function testConnection(Request $request)
    {
        Log::info('Teste de conexão LDAP iniciado', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'data' => $request->except(['password'])
        ]);

        $validator = Validator::make($request->all(), [
            "host"     => "required|string",
            "base_dn"  => "required|string",
            "username" => "required|string",
            "password" => "required|string",
            "port"     => "nullable|integer|min:1|max:65535",
            "ssl"      => "nullable|boolean",
        ]);

        if ($validator->fails()) {
            Log::warning('Validação falhou no teste de conexão LDAP', [
                'errors' => $validator->errors()->all()
            ]);
            return response()->json([
                "success" => false,
                "message" => "Dados inválidos: " . implode(", ", $validator->errors()->all()),
                "errors" => $validator->errors()
            ], 422);
        }

        try {
            $provider = $this->connect($request);
            $users = $provider->search()->users()->select(["cn"])->limit(1)->get();

            $message = "Conexão estabelecida com sucesso!";
            if (count($users) > 0) {
                $message .= " 1 usuário de exemplo encontrado.";
            } else {
                $message .= " Servidor acessível, mas nenhum usuário retornou com o filtro padrão.";
            }

            Log::info('Teste de conexão LDAP bem-sucedido', [
                'host' => $request->host,
                'users_found' => count($users)
            ]);

            return response()->json(["success" => true, "message" => $message]);
        } catch (\Exception $e) {
            // Classificar erro para melhorar o feedback e evitar 500 desnecessário
            $msg = $e->getMessage() ?? '';
            $normalized = strtolower($msg);

            // Evitar vazar credenciais em logs
            if (str_contains($normalized, 'invalid credentials')) {
                Log::warning('LDAP: credenciais inválidas no teste de conexão', [
                    'host' => $request->host,
                    'username_hint' => $this->maskUsername($request->username ?? '')
                ]);
                return response()->json([
                    'success' => false,
                    'code' => 'invalid_credentials',
                    'message' => 'Credenciais inválidas. Tente no formato usuario@dominio (UPN) ou DOMINIO\\usuario.'
                ], 401);
            }

            if (str_contains($normalized, "can't contact ldap server") || str_contains($normalized, 'can\'t contact ldap server')) {
                Log::error('LDAP: servidor inacessível no teste de conexão', [
                    'host' => $request->host
                ]);
                return response()->json([
                    'success' => false,
                    'code' => 'unreachable',
                    'message' => 'Não foi possível contatar o servidor LDAP. Verifique host, porta e firewall.'
                ], 502);
            }

            Log::error('LDAP: erro inesperado no teste de conexão', [
                'host' => $request->host,
                'error' => $msg
            ]);
            return response()->json([
                'success' => false,
                'code' => 'unexpected',
                'message' => 'Falha na conexão ao LDAP: ' . $msg
            ], 500);
        }
    }

    private function connect(Request $request)
    {
        $host = (string) $request->host;
        $baseDn = (string) $request->base_dn;
        $username = (string) $request->username;
        $password = (string) $request->password;
        $port = $request->integer('port', 389);
        $useSsl = (bool) $request->boolean('ssl');

        // Se SSL estiver marcado e porta padrão 389, ajustar para 636 automaticamente no backend
        if ($useSsl && ($port === 389 || $port === 0)) {
            $port = 636;
        }

        // Se o username vier sem domínio / DN, tentar construir UPN a partir do base_dn
        if ($username && !str_contains($username, '@') && !str_contains($username, '\\') && !preg_match('/(CN=|DC=)/i', $username)) {
            $domain = $this->domainFromBaseDn($baseDn);
            if (!empty($domain)) {
                $username = $username . '@' . $domain;
            }
        }

        $config = [
            'hosts'    => [$host],
            'base_dn'  => $baseDn,
            'username' => $username,
            'password' => $password,
            'port'     => $port,
            'use_ssl'  => $useSsl,
            'timeout'  => 30, // Aumentar timeout para 30 segundos
        ];

        $client = new AdldapClient();
        $client->addProvider($config, "custom");
        
        // Configurar opções LDAP após a conexão
        $provider = $client->connect("custom");
        
        // Configurar opções LDAP diretamente na conexão
        if ($provider) {
            $connection = $provider->getConnection();
            $connection->setOption(LDAP_OPT_REFERRALS, 0);
            $connection->setOption(LDAP_OPT_TIMELIMIT, 120);
            $connection->setOption(LDAP_OPT_NETWORK_TIMEOUT, 30);
            $connection->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        }
        
        return $provider;
    }

    // Utilitário: extrai dominio FQDN do base_dn (ex.: DC=guarapuava,DC=pr,DC=gov,DC=br -> guarapuava.pr.gov.br)
    private function domainFromBaseDn(?string $baseDn): string
    {
        if (empty($baseDn)) {
            return '';
        }
        if (preg_match_all('/DC=([^,]+)/i', $baseDn, $m) && !empty($m[1])) {
            return strtolower(implode('.', $m[1]));
        }
        return '';
    }

    // Utilitário: mascara username para logs (preserva início e domínio)
    private function maskUsername(string $username): string
    {
        if ($username === '') return '';
        $visible = 2;
        $atPos = strpos($username, '@');
        if ($atPos !== false) {
            $name = substr($username, 0, $atPos);
            $domain = substr($username, $atPos);
            $maskedName = substr($name, 0, min($visible, strlen($name))) . str_repeat('*', max(0, strlen($name) - $visible));
            return $maskedName . $domain;
        }
        return substr($username, 0, min($visible, strlen($username))) . str_repeat('*', max(0, strlen($username) - $visible));
    }

    /**
     * Iniciar importação em lotes para muitos usuários
     */
    public function bulkImport(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "host"     => "required|string",
            "base_dn"  => "required|string", 
            "username" => "required|string",
            "password" => "required|string",
            "port"     => "integer|min:1|max:65535",
            "ssl"      => "boolean",
            "batch_size" => "integer|min:50|max:500",
            "filter"   => "string|nullable"
        ]);

        if ($validator->fails()) {
            return response()->json([
                "success" => false,
                "message" => "Dados inválidos",
                "errors"  => $validator->errors()
            ], 422);
        }

        try {
            // Conectar ao LDAP para calcular total de usuários
            $provider = $this->connect($request);
            
            $search = $provider->search()->users();
            $search->in($request->base_dn);
            
            // Aplicar filtro se fornecido
            if ($request->filled('filter')) {
                $filter = $request->filter;
                // Usar orWhere ao invés de closure para LDAP
                $search->whereContains("samaccountname", $filter)
                       ->orWhereContains("cn", $filter)
                       ->orWhereContains("displayname", $filter);
            }

            // REMOVIDO: Filtrar apenas usuários ativos - agora importa TODOS
            // $search->where('useraccountcontrol', '=', '512');
            
            // Fazer busca limitada para estimar total (o método count() não existe)
            $testResults = $search->paginate(1000, 1); // Pegar até 1000 usuários na primeira página
            $totalUsers = count($testResults);
            
            // Se temos exatamente 1000, provavelmente há mais usuários
            if ($totalUsers == 1000) {
                // Estimar baseado em algumas páginas
                $totalUsers = 2000; // Estimativa conservadora, será ajustada durante o processamento
            }
            
            $batchSize = $request->integer('batch_size', 100);
            $totalPages = max(1, ceil($totalUsers / $batchSize));
            
            // Gerar ID único para este trabalho
            $jobId = Str::uuid();
            
            // Configuração para o job (normalizada)
            $jobHost = (string) $request->host;
            $jobBaseDn = (string) $request->base_dn;
            $jobUsername = (string) $request->username;
            $jobPassword = (string) $request->password;
            $jobPort = $request->integer('port', 389);
            $jobSsl = $request->boolean('ssl', false);

            // Ajustar porta quando SSL
            if ($jobSsl && ($jobPort === 389 || $jobPort === 0)) {
                $jobPort = 636;
            }

            // Normalizar username para UPN se possível
            if ($jobUsername && !str_contains($jobUsername, '@') && !str_contains($jobUsername, '\\') && !preg_match('/(CN=|DC=)/i', $jobUsername)) {
                $domain = $this->domainFromBaseDn($jobBaseDn);
                if (!empty($domain)) {
                    $jobUsername = $jobUsername . '@' . $domain;
                }
            }

            $config = [
                'host' => $jobHost,
                'base_dn' => $jobBaseDn,
                'username' => $jobUsername,
                'password' => $jobPassword,
                'port' => $jobPort,
                'ssl' => $jobSsl,
            ];

            // Inicializar progresso
            $progressKey = "ldap_import_progress_{$jobId}";
            Cache::put($progressKey, [
                'status' => 'queued',
                'total_users' => $totalUsers,
                'total_pages' => $totalPages,
                'batch_size' => $batchSize,
                'current_page' => 0,
                'imported' => 0,
                'skipped' => 0,
                'message' => 'Importação em fila...',
                'started_at' => now()->toISOString()
            ], now()->addHours(2));

            // Despachar primeiro job
            LdapBulkImportJob::dispatch(
                $config,
                $batchSize,
                1,
                $totalPages,
                $jobId,
                $request->filter ?? ''
            )->onQueue('ldap');

            Log::info("LDAP Bulk Import iniciado", [
                'job_id' => $jobId,
                'total_users' => $totalUsers,
                'total_pages' => $totalPages,
                'batch_size' => $batchSize
            ]);

            return response()->json([
                "success" => true,
                "message" => "Importação em lotes iniciada com sucesso",
                "job_id" => $jobId,
                "total_users" => $totalUsers,
                "total_pages" => $totalPages,
                "batch_size" => $batchSize
            ]);

        } catch (\Exception $e) {
            Log::error("Erro ao iniciar importação em lotes", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                "success" => false,
                "message" => "Erro ao iniciar importação: " . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verificar progresso da importação em lotes
     */
    public function checkProgress($jobId)
    {
        $progressKey = "ldap_import_progress_{$jobId}";
        $progress = Cache::get($progressKey);

        if (!$progress) {
            return response()->json([
                "success" => false,
                "message" => "Job não encontrado ou finalizado"
            ], 404);
        }

        // Calcular estatísticas com verificações seguras
        $progress['progress_percentage'] = 0;
        if (isset($progress['total_pages']) && isset($progress['current_page']) && $progress['total_pages'] > 0) {
            $progress['progress_percentage'] = round(($progress['current_page'] / $progress['total_pages']) * 100, 1);
        }

        // Garantir que todas as chaves necessárias existam
        $progress['total_pages'] = $progress['total_pages'] ?? 0;
        $progress['current_page'] = $progress['current_page'] ?? 0;
        $progress['processed'] = $progress['processed'] ?? 0;
        $progress['total_estimated'] = $progress['total_estimated'] ?? 0;
        $progress['status'] = $progress['status'] ?? 'unknown';

        return response()->json([
            "success" => true,
            "data" => $progress
        ]);
    }

    /**
     * Cancelar importação em lotes
     */
    public function cancelImport($jobId)
    {
        $progressKey = "ldap_import_progress_{$jobId}";
        $progress = Cache::get($progressKey);

        if (!$progress) {
            return response()->json([
                "success" => false,
                "message" => "Job não encontrado"
            ], 404);
        }

        // Marcar como cancelado
        $progress['status'] = 'cancelled';
        $progress['message'] = 'Importação cancelada pelo usuário';
        Cache::put($progressKey, $progress, now()->addHours(2));

        return response()->json([
            "success" => true,
            "message" => "Importação cancelada"
        ]);
    }

    /**
     * Normaliza um termo de busca removendo acentos e caracteres especiais
     */
    private function normalizeSearchTerm($term)
    {
        // Converter para minúsculas
        $term = strtolower($term);
        
        // Remover acentos e caracteres especiais
        $unwanted = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n',
            'Á' => 'A', 'À' => 'A', 'Ã' => 'A', 'Â' => 'A', 'Ä' => 'A',
            'É' => 'E', 'È' => 'E', 'Ê' => 'E', 'Ë' => 'E',
            'Í' => 'I', 'Ì' => 'I', 'Î' => 'I', 'Ï' => 'I',
            'Ó' => 'O', 'Ò' => 'O', 'Õ' => 'O', 'Ô' => 'O', 'Ö' => 'O',
            'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U',
            'Ç' => 'C', 'Ñ' => 'N'
        ];
        
        $normalized = strtr($term, $unwanted);
        
        // Remover espaços extras
        $normalized = preg_replace('/\s+/', ' ', trim($normalized));
        
        return $normalized;
    }

    /**
     * Executa sincronização manual do LDAP
     */
    public function sync(Request $request)
    {
        try {
            $exitCode = Artisan::call('ldap:sync');
            $output = Artisan::output();

            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Sincronização LDAP executada com sucesso',
                    'output' => $output
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro na sincronização LDAP',
                    'output' => $output
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Erro na sincronização LDAP manual: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno na sincronização: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Executa dry-run da sincronização LDAP
     */
    public function dryRun(Request $request)
    {
        try {
            $exitCode = Artisan::call('ldap:sync', ['--dry-run' => true]);
            $output = Artisan::output();

            if ($exitCode === 0) {
                return response()->json([
                    'success' => true,
                    'message' => 'Dry-run executado com sucesso',
                    'output' => $output
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Erro no dry-run',
                    'output' => $output
                ], 500);
            }
        } catch (\Exception $e) {
            Log::error('Erro no dry-run LDAP: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro interno no dry-run: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verifica status da última sincronização
     */
    public function syncStatus(Request $request)
    {
        try {
            // Buscar informações da última sincronização
            $lastSync = Cache::get('ldap_last_sync_info', [
                'status' => 'never',
                'last_run' => null,
                'users_processed' => 0,
                'users_created' => 0,
                'users_updated' => 0,
                'errors' => 0
            ]);

            // Buscar estatísticas dos usuários LDAP
            $totalLdapUsers = User::whereNotNull('ldap_user_account_control')->count();
            $activeLdapUsers = User::where('ldap_is_active', true)->count();
            $inactiveLdapUsers = User::where('ldap_is_active', false)->count();
            
            // Usuários sincronizados recentemente (último mês)
            $recentSynced = User::where('ldap_last_sync', '>=', now()->subMonth())->count();

            return response()->json([
                'success' => true,
                'last_sync' => $lastSync,
                'statistics' => [
                    'total_ldap_users' => $totalLdapUsers,
                    'active_ldap_users' => $activeLdapUsers,
                    'inactive_ldap_users' => $inactiveLdapUsers,
                    'recent_synced' => $recentSynced
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Erro ao buscar status da sincronização: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erro ao buscar status: ' . $e->getMessage()
            ], 500);
        }
    }
}
