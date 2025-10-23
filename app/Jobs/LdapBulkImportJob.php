<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User;
use Adldap\Adldap as AdldapClient;
use Exception;

class LdapBulkImportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 3600; // 1 hora
    public $tries = 3; // Até 3 tentativas
    public $maxExceptions = 5; // Máximo de exceções

    protected $config;
    protected $batchSize;
    protected $currentPage;
    protected $totalPages;
    protected $jobId;
    protected $filter;

    public function __construct($config, $batchSize = 100, $currentPage = 1, $totalPages = 1, $jobId = null, $filter = '')
    {
        $this->config = $config;
        $this->batchSize = $batchSize;
        $this->currentPage = $currentPage;
        $this->totalPages = $totalPages;
        $this->jobId = $jobId ?? Str::uuid();
        $this->filter = $filter;
    }

    public function handle()
    {
        $progressKey = "ldap_import_progress_{$this->jobId}";
        
        try {
            // Definir configurações de timeout otimizadas
            set_time_limit(3600);
            ini_set('max_execution_time', 3600);
            ini_set('memory_limit', '512M');
            
            Log::info("LDAP Bulk Import - Iniciando lote {$this->currentPage}/{$this->totalPages}", [
                'job_id' => $this->jobId,
                'batch_size' => $this->batchSize,
                'page' => $this->currentPage
            ]);

            // Atualizar progresso
            $this->updateProgress($progressKey, [
                'status' => 'processing',
                'current_page' => $this->currentPage,
                'total_pages' => $this->totalPages,
                'message' => "Processando lote {$this->currentPage} de {$this->totalPages}..."
            ]);

            // Conectar ao LDAP
            $provider = $this->connectLdap();
            
            // Buscar usuários da página atual
            $users = $this->fetchUsersPage($provider);
            
            if (empty($users)) {
                Log::warning("LDAP Bulk Import - Nenhum usuário encontrado na página {$this->currentPage}");
                $this->updateProgress($progressKey, [
                    'status' => 'completed',
                    'message' => "Lote {$this->currentPage} concluído - nenhum usuário encontrado"
                ]);
                return;
            }

            // Processar usuários em lotes menores
            $processed = $this->processUsersBatch($users);
            
            Log::info("LDAP Bulk Import - Lote concluído", [
                'job_id' => $this->jobId,
                'page' => $this->currentPage,
                'processed' => $processed['imported'],
                'skipped' => $processed['skipped']
            ]);

            // Atualizar progresso final deste lote
            $this->updateProgress($progressKey, [
                'status' => $this->currentPage >= $this->totalPages ? 'completed' : 'processing',
                'current_page' => $this->currentPage,
                'total_pages' => $this->totalPages,
                'imported' => $processed['imported'],
                'skipped' => $processed['skipped'],
                'message' => "Lote {$this->currentPage} concluído: {$processed['imported']} importados, {$processed['skipped']} ignorados"
            ]);

            // Despachar próximo lote se necessário
            if ($this->currentPage < $this->totalPages) {
                self::dispatch(
                    $this->config,
                    $this->batchSize,
                    $this->currentPage + 1,
                    $this->totalPages,
                    $this->jobId,
                    $this->filter
                )->delay(now()->addSeconds(5)); // Pequeno delay entre lotes
            }

        } catch (Exception $e) {
            Log::error("LDAP Bulk Import - Erro no lote {$this->currentPage}", [
                'job_id' => $this->jobId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $this->updateProgress($progressKey, [
                'status' => 'error',
                'message' => "Erro no lote {$this->currentPage}: " . $e->getMessage()
            ]);

            throw $e;
        }
    }

    private function connectLdap()
    {
        $host = (string) ($this->config['host'] ?? '');
        $baseDn = (string) ($this->config['base_dn'] ?? '');
        $username = (string) ($this->config['username'] ?? '');
        $password = (string) ($this->config['password'] ?? '');
        $port = (int) ($this->config['port'] ?? 389);
        $useSsl = (bool) ($this->config['ssl'] ?? false);

        // Ajustar porta se SSL ativo
        if ($useSsl && ($port === 389 || $port === 0)) {
            $port = 636;
        }

        // Normalizar username caso venha sem domínio/DN
        if ($username && !str_contains($username, '@') && !str_contains($username, '\\') && !preg_match('/(CN=|DC=)/i', $username)) {
            $domain = $this->domainFromBaseDn($baseDn);
            if (!empty($domain)) {
                $username = $username . '@' . $domain;
            }
        }

        $ldapConfig = [
            "hosts"    => [$host],
            "base_dn"  => $baseDn,
            "username" => $username,
            "password" => $password,
            "port"     => $port,
            "use_ssl"  => $useSsl,
            "timeout"  => 60,
        ];

        $client = new AdldapClient();
        $client->addProvider($ldapConfig, "bulk_import");
        $provider = $client->connect("bulk_import");
        
        // Configurar opções LDAP após a conexão
        if ($provider) {
            $connection = $provider->getConnection();
            $connection->setOption(LDAP_OPT_REFERRALS, 0);
            $connection->setOption(LDAP_OPT_TIMELIMIT, 300);
            $connection->setOption(LDAP_OPT_NETWORK_TIMEOUT, 60);
            $connection->setOption(LDAP_OPT_PROTOCOL_VERSION, 3);
        }
        
        return $provider;
    }

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

    private function fetchUsersPage($provider)
    {
        $select = [
            "cn", 
            "samaccountname", 
            "mail", 
            "userprincipalname", 
            "distinguishedname",
            "displayname",
            "useraccountcontrol"
        ];
        
        $search = $provider->search()->users()->select($select);
        $search->in($this->config['base_dn']);

        // Aplicar filtro se fornecido
        if (!empty($this->filter)) {
            // Usar orWhere ao invés de closure para LDAP
            $search->whereContains("samaccountname", $this->filter)
                   ->orWhereContains("cn", $this->filter)
                   ->orWhereContains("displayname", $this->filter);
        }

        // REMOVIDO: Filtrar apenas usuários ativos - agora importa TODOS
        // $search->where('useraccountcontrol', '=', '512');

        // Paginar
        $users = $search->paginate($this->batchSize, $this->currentPage);
        
        $processedUsers = [];
        foreach ($users as $u) {
            $cn = method_exists($u, "getCommonName") ? $u->getCommonName() : ($u->getFirstAttribute("cn") ?? "");
            $sam = method_exists($u, "getAccountName") ? $u->getAccountName() : ($u->getFirstAttribute("samaccountname") ?? "");
            $mail = method_exists($u, "getEmail") ? $u->getEmail() : ($u->getFirstAttribute("mail") ?? "");
            $upn = $u->getFirstAttribute("userprincipalname") ?? "";
            $dn = $u->getDn() ?? ($u->getFirstAttribute("distinguishedname") ?? "");
            $displayName = $u->getFirstAttribute("displayname") ?? $cn;

            $processedUsers[] = [
                "dn" => $dn,
                "name" => $displayName ?: $cn ?: $sam ?: $upn ?: "(sem nome)",
                "login" => $sam ?: $upn,
                "email" => $mail ?: $upn,
            ];
        }

        return $processedUsers;
    }

    private function processUsersBatch($users)
    {
        $imported = 0;
        $skipped = 0;

        // Pré-carregar usuários existentes para otimização
        $existingUsers = $this->buildExistingUsersCache();

        DB::beginTransaction();
        try {
            foreach ($users as $userData) {
                $result = $this->processUser($userData, $existingUsers);
                if ($result === 'imported') {
                    $imported++;
                } elseif ($result === 'skipped') {
                    $skipped++;
                }
            }
            
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return ['imported' => $imported, 'skipped' => $skipped];
    }

    private function buildExistingUsersCache()
    {
        $cache = ['by_dn' => [], 'by_email' => []];
        
        User::select('id', 'email', 'ldap_dn', 'name')
            ->whereNotNull('ldap_dn')
            ->orWhereNotNull('email')
            ->chunk(1000, function ($users) use (&$cache) {
                foreach ($users as $user) {
                    if ($user->ldap_dn) {
                        $cache['by_dn'][$user->ldap_dn] = $user;
                    }
                    if ($user->email) {
                        $cache['by_email'][$user->email] = $user;
                    }
                }
            });

        return $cache;
    }

    private function processUser($userData, &$existingUsers)
    {
        $dn = trim($userData['dn']);
        $email = trim($userData['email']);
        $name = trim($userData['name']);
        $login = trim($userData['login']);

        // Validar dados essenciais - mais flexível
        if (empty($dn) && empty($email) && empty($login) && empty($name)) {
            return 'skipped'; // Só pula se não tiver NENHUM dado útil
        }

        // Verificar se usuário já existe
        $existingUser = null;
        if ($dn && isset($existingUsers['by_dn'][$dn])) {
            $existingUser = $existingUsers['by_dn'][$dn];
        } elseif ($email && isset($existingUsers['by_email'][$email])) {
            $existingUser = $existingUsers['by_email'][$email];
        }

        try {
            if ($existingUser) {
                // Atualizar usuário existente
                User::where('id', $existingUser->id)->update([
                    'name' => $name ?: $existingUser->name,
                    'email' => $email ?: $existingUser->email,
                    'ldap_dn' => $dn ?: $existingUser->ldap_dn,
                    'is_active' => true,
                ]);
                return 'imported';
            } else {
                // Criar novo usuário - mais flexível
                if ($email && isset($existingUsers['by_email'][$email])) {
                    return 'skipped'; // Email já existe
                }

                // Gerar email único se não tiver
                $finalEmail = $email;
                if (empty($finalEmail) && !empty($login)) {
                    $finalEmail = $login . '@ldap.local';
                }
                if (empty($finalEmail) && !empty($name)) {
                    $finalEmail = Str::slug($name) . '@ldap.local';
                }
                if (empty($finalEmail)) {
                    $finalEmail = 'user_' . Str::random(8) . '@ldap.local';
                }

                $newUser = User::create([
                    'name' => $name ?: ($login ?: 'Usuário LDAP'),
                    'email' => $finalEmail,
                    'password' => bcrypt(Str::random(40)),
                    'role' => 'customer',
                    'is_active' => true,
                    'ldap_dn' => $dn ?: null,
                    'auth_via_ldap' => true,
                ]);

                // Atualizar cache
                if ($dn) {
                    $existingUsers['by_dn'][$dn] = $newUser;
                }
                if ($email) {
                    $existingUsers['by_email'][$email] = $newUser;
                }

                return 'imported';
            }
        } catch (Exception $e) {
            Log::error("LDAP Bulk Import - Erro ao processar usuário", [
                'data' => $userData,
                'error' => $e->getMessage()
            ]);
            return 'skipped';
        }
    }

    private function updateProgress($key, $data)
    {
        $data['updated_at'] = now()->toISOString();
        Cache::put($key, $data, now()->addHours(2));
    }

    public function failed(Exception $exception)
    {
        Log::error("LDAP Bulk Import Job falhou", [
            'job_id' => $this->jobId,
            'page' => $this->currentPage,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);

        $progressKey = "ldap_import_progress_{$this->jobId}";
        $this->updateProgress($progressKey, [
            'status' => 'failed',
            'message' => 'Falha na importação: ' . $exception->getMessage()
        ]);
    }
}
