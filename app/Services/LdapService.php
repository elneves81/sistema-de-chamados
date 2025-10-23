<?php

namespace App\Services;

use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use Exception;

class LdapService
{
    protected $config;
    protected $connection;
    protected $connected = false;

    public function __construct()
    {
        $this->config = config('ldap_custom');
    }

    /**
     * Testa a conexão LDAP
     */
    public function testConnection(): array
    {
        try {
            if (!$this->config['enabled']) {
                return [
                    'success' => false,
                    'message' => 'LDAP está desabilitado na configuração',
                    'details' => null
                ];
            }

            // Verifica se a extensão LDAP está disponível
            if (!extension_loaded('ldap')) {
                return [
                    'success' => false,
                    'message' => 'Extensão LDAP não está carregada no PHP',
                    'details' => 'Execute: sudo apt-get install php-ldap (Linux) ou habilite no php.ini (Windows)'
                ];
            }

            if ($this->connect()) {
                return [
                    'success' => true,
                    'message' => 'Conexão LDAP estabelecida com sucesso',
                    'details' => [
                        'server' => $this->config['connections']['default']['hosts'][0],
                        'port' => $this->config['connections']['default']['port'],
                        'base_dn' => $this->config['connections']['default']['base_dn']
                    ]
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Falha ao conectar com o servidor LDAP',
                    'details' => 'Verifique host, porta e credenciais'
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Erro ao testar conexão LDAP',
                'details' => $e->getMessage()
            ];
        }
    }

    /**
     * Conecta ao servidor LDAP
     */
    protected function connect(): bool
    {
        if ($this->connected && $this->connection) {
            return true;
        }

        try {
            $config = $this->config['connections']['default'];
            
            // Conecta ao servidor LDAP
            $this->connection = ldap_connect($config['hosts'][0], $config['port']);
            
            if (!$this->connection) {
                $this->log('Falha ao conectar ao servidor LDAP');
                return false;
            }

            // Define opções do LDAP
            ldap_set_option($this->connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($this->connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($this->connection, LDAP_OPT_NETWORK_TIMEOUT, $config['timeout']);

            if ($config['use_tls']) {
                if (!ldap_start_tls($this->connection)) {
                    $this->log('Falha ao iniciar TLS');
                    return false;
                }
            }

            // Autentica com credenciais administrativas se fornecidas
            if ($config['username'] && $config['password']) {
                $bind = ldap_bind($this->connection, $config['username'], $config['password']);
                if (!$bind) {
                    $this->log('Falha na autenticação administrativa: ' . ldap_error($this->connection));
                    return false;
                }
            }

            $this->connected = true;
            $this->log('Conexão LDAP estabelecida com sucesso');
            
            return true;
        } catch (Exception $e) {
            $this->log('Erro ao conectar LDAP: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Autentica um usuário via LDAP
     */
    public function authenticate(string $username, string $password): array
    {
        try {
            if (!$this->config['authentication']['enabled']) {
                return [
                    'success' => false,
                    'message' => 'Autenticação LDAP está desabilitada'
                ];
            }

            // Busca o usuário no LDAP
            $userData = $this->getUserData($username);
            if (!$userData) {
                return [
                    'success' => false,
                    'message' => 'Usuário não encontrado no Active Directory'
                ];
            }

            // Tenta autenticar com as credenciais fornecidas
            $userDn = $userData['dn'];
            $tempConnection = ldap_connect($this->config['connections']['default']['hosts'][0]);
            
            if (ldap_bind($tempConnection, $userDn, $password)) {
                ldap_close($tempConnection);
                
                return [
                    'success' => true,
                    'message' => 'Autenticação realizada com sucesso',
                    'user_data' => $userData
                ];
            } else {
                ldap_close($tempConnection);
                
                return [
                    'success' => false,
                    'message' => 'Credenciais inválidas'
                ];
            }
        } catch (Exception $e) {
            $this->log('Erro na autenticação LDAP: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Erro interno na autenticação'
            ];
        }
    }

    /**
     * Busca dados de um usuário no LDAP
     */
    public function getUserData(string $username): ?array
    {
        if (!$this->connect()) {
            return null;
        }

        try {
            $config = $this->config['connections']['default'];
            $syncConfig = $this->config['sync'];
            
            $filter = "(&({$syncConfig['attributes']['username']}={$username}){$syncConfig['user_filter']})";
            $searchBase = $syncConfig['search_base'] ?? $config['base_dn'];
            
            $search = ldap_search($this->connection, $searchBase, $filter);
            if (!$search) {
                return null;
            }

            $entries = ldap_get_entries($this->connection, $search);
            if ($entries['count'] === 0) {
                return null;
            }

            $entry = $entries[0];
            
            // Mapeia os atributos do LDAP
            $userData = [
                'dn' => $entry['dn'],
                'username' => $this->getLdapAttribute($entry, $syncConfig['attributes']['username']),
                'name' => $this->getLdapAttribute($entry, $syncConfig['attributes']['name']),
                'first_name' => $this->getLdapAttribute($entry, $syncConfig['attributes']['first_name']),
                'last_name' => $this->getLdapAttribute($entry, $syncConfig['attributes']['last_name']),
                'email' => $this->getLdapAttribute($entry, $syncConfig['attributes']['email']),
                'phone' => $this->getLdapAttribute($entry, $syncConfig['attributes']['phone']),
                'department' => $this->getLdapAttribute($entry, $syncConfig['attributes']['department']),
                'title' => $this->getLdapAttribute($entry, $syncConfig['attributes']['title']),
                'office' => $this->getLdapAttribute($entry, $syncConfig['attributes']['office']),
                'employee_id' => $this->getLdapAttribute($entry, $syncConfig['attributes']['employee_id']),
            ];

            return $userData;
        } catch (Exception $e) {
            $this->log('Erro ao buscar dados do usuário: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Extrai um atributo do LDAP
     */
    protected function getLdapAttribute(array $entry, string $attribute): ?string
    {
        if (!isset($entry[$attribute]) || !isset($entry[$attribute][0])) {
            return null;
        }
        
        return $entry[$attribute][0];
    }

    /**
     * Sincroniza usuários do LDAP
     */
    public function syncUsers(): array
    {
        $results = [
            'success' => true,
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
            'messages' => []
        ];

        try {
            if (!$this->config['sync']['enabled']) {
                $results['success'] = false;
                $results['messages'][] = 'Sincronização LDAP está desabilitada';
                return $results;
            }

            $ldapUsers = $this->getAllUsers();
            
            foreach ($ldapUsers as $ldapUser) {
                try {
                    $syncResult = $this->syncUser($ldapUser);
                    
                    if ($syncResult['action'] === 'created') {
                        $results['created']++;
                    } elseif ($syncResult['action'] === 'updated') {
                        $results['updated']++;
                    }
                    
                    $results['messages'][] = $syncResult['message'];
                } catch (Exception $e) {
                    $results['errors']++;
                    $results['messages'][] = "Erro ao sincronizar {$ldapUser['username']}: " . $e->getMessage();
                }
            }

            $this->log("Sincronização concluída: {$results['created']} criados, {$results['updated']} atualizados, {$results['errors']} erros");
        } catch (Exception $e) {
            $results['success'] = false;
            $results['messages'][] = 'Erro geral na sincronização: ' . $e->getMessage();
            $this->log('Erro na sincronização: ' . $e->getMessage());
        }

        return $results;
    }

    /**
     * Busca todos os usuários do LDAP
     */
    protected function getAllUsers(): array
    {
        if (!$this->connect()) {
            return [];
        }

        $config = $this->config['connections']['default'];
        $syncConfig = $this->config['sync'];
        
        $filter = $syncConfig['user_filter'];
        $searchBase = $syncConfig['search_base'] ?? $config['base_dn'];
        
        $search = ldap_search($this->connection, $searchBase, $filter);
        if (!$search) {
            return [];
        }

        $entries = ldap_get_entries($this->connection, $search);
        $users = [];

        for ($i = 0; $i < $entries['count']; $i++) {
            $entry = $entries[$i];
            
            $userData = [
                'dn' => $entry['dn'],
                'username' => $this->getLdapAttribute($entry, $syncConfig['attributes']['username']),
                'name' => $this->getLdapAttribute($entry, $syncConfig['attributes']['name']),
                'first_name' => $this->getLdapAttribute($entry, $syncConfig['attributes']['first_name']),
                'last_name' => $this->getLdapAttribute($entry, $syncConfig['attributes']['last_name']),
                'email' => $this->getLdapAttribute($entry, $syncConfig['attributes']['email']),
                'phone' => $this->getLdapAttribute($entry, $syncConfig['attributes']['phone']),
                'department' => $this->getLdapAttribute($entry, $syncConfig['attributes']['department']),
                'title' => $this->getLdapAttribute($entry, $syncConfig['attributes']['title']),
                'office' => $this->getLdapAttribute($entry, $syncConfig['attributes']['office']),
                'employee_id' => $this->getLdapAttribute($entry, $syncConfig['attributes']['employee_id']),
            ];

            if ($userData['username']) {
                $users[] = $userData;
            }
        }

        return $users;
    }

    /**
     * Sincroniza um usuário específico
     */
    protected function syncUser(array $ldapUser): array
    {
        $user = User::where('username', $ldapUser['username'])->first();
        
        $userData = [
            'username' => $ldapUser['username'],
            'name' => $ldapUser['name'] ?? $ldapUser['username'],
            'email' => $ldapUser['email'] ?? $ldapUser['username'] . '@empresa.local',
            'phone' => $ldapUser['phone'],
            'employee_id' => $ldapUser['employee_id'],
            'title' => $ldapUser['title'],
            'office' => $ldapUser['office'],
            'role' => $this->mapUserRole($ldapUser),
            'department_id' => $this->getDepartmentId($ldapUser['department']),
            'is_active' => true,
        ];

        if ($user) {
            // Atualiza usuário existente se configurado
            if ($this->config['sync']['auto_update']) {
                $user->update($userData);
                return [
                    'action' => 'updated',
                    'message' => "Usuário {$ldapUser['username']} atualizado com sucesso"
                ];
            } else {
                return [
                    'action' => 'skipped',
                    'message' => "Usuário {$ldapUser['username']} encontrado mas não atualizado (auto_update desabilitado)"
                ];
            }
        } else {
            // Cria novo usuário se configurado
            if ($this->config['sync']['auto_import']) {
                $userData['password'] = Hash::make('senha_temporaria_' . uniqid());
                User::create($userData);
                
                return [
                    'action' => 'created',
                    'message' => "Usuário {$ldapUser['username']} criado com sucesso"
                ];
            } else {
                return [
                    'action' => 'skipped',
                    'message' => "Usuário {$ldapUser['username']} não encontrado mas não criado (auto_import desabilitado)"
                ];
            }
        }
    }

    /**
     * Mapeia o papel do usuário baseado nos grupos do LDAP
     */
    protected function mapUserRole(array $ldapUser): string
    {
        // Implementação básica - pode ser expandida para verificar grupos
        return $this->config['sync']['default_role'];
    }

    /**
     * Obtém o ID do departamento baseado no nome
     */
    protected function getDepartmentId(?string $departmentName): ?int
    {
        if (!$departmentName) {
            $defaultDept = $this->config['sync']['default_department'];
            if ($defaultDept) {
                $department = Department::firstOrCreate(['name' => $defaultDept]);
                return $department->id;
            }
            return null;
        }

        $department = Department::firstOrCreate(['name' => $departmentName]);
        return $department->id;
    }

    /**
     * Limpa o cache LDAP
     */
    public function clearCache(): bool
    {
        if ($this->config['cache']['enabled']) {
            Cache::flush();
            return true;
        }
        
        return false;
    }

    /**
     * Log das operações LDAP
     */
    protected function log(string $message, string $level = 'info'): void
    {
        if ($this->config['logging']['enabled']) {
            Log::{$level}("[LDAP] {$message}");
        }
    }

    /**
     * Destrutor para fechar a conexão
     */
    /**
     * Busca usuários no LDAP com configuração customizada
     */
    public function searchUsers(array $customConfig, string $filter = null, int $limit = 100): array
    {
        $connection = null;
        $users = [];

        try {
            // Criar conexão com configuração customizada
            $connection = ldap_connect($customConfig['hosts'][0], $customConfig['port']);
            
            if (!$connection) {
                throw new Exception('Não foi possível conectar ao servidor LDAP');
            }

            // Configurar opções
            ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($connection, LDAP_OPT_REFERRALS, 0);
            ldap_set_option($connection, LDAP_OPT_NETWORK_TIMEOUT, 10);

            if ($customConfig['use_ssl']) {
                ldap_start_tls($connection);
            }

            // Autenticar
            $bind = ldap_bind($connection, $customConfig['username'], $customConfig['password']);
            if (!$bind) {
                throw new Exception('Falha na autenticação LDAP: ' . ldap_error($connection));
            }

            // Definir filtro padrão se não fornecido
            if (!$filter) {
                $filter = '(&(objectClass=user)(!(objectClass=computer)))';
            }

            // Atributos a buscar
            $attributes = [
                'dn',
                'cn', 
                'displayName',
                'sAMAccountName',
                'userPrincipalName',
                'mail',
                'department',
                'title',
                'userAccountControl'
            ];

            // Buscar usuários
            $search = ldap_search($connection, $customConfig['base_dn'], $filter, $attributes, 0, $limit);
            
            if (!$search) {
                throw new Exception('Erro na busca LDAP: ' . ldap_error($connection));
            }

            $entries = ldap_get_entries($connection, $search);

            // Processar resultados
            for ($i = 0; $i < $entries['count']; $i++) {
                $entry = $entries[$i];
                
                $users[] = [
                    'dn' => $entry['dn'] ?? '',
                    'cn' => isset($entry['cn'][0]) ? $entry['cn'][0] : '',
                    'displayName' => isset($entry['displayname'][0]) ? $entry['displayname'][0] : '',
                    'sAMAccountName' => isset($entry['samaccountname'][0]) ? $entry['samaccountname'][0] : '',
                    'userPrincipalName' => isset($entry['userprincipalname'][0]) ? $entry['userprincipalname'][0] : '',
                    'mail' => isset($entry['mail'][0]) ? $entry['mail'][0] : '',
                    'department' => isset($entry['department'][0]) ? $entry['department'][0] : '',
                    'title' => isset($entry['title'][0]) ? $entry['title'][0] : '',
                    'userAccountControl' => isset($entry['useraccountcontrol'][0]) ? intval($entry['useraccountcontrol'][0]) : 0
                ];
            }

        } catch (Exception $e) {
            throw new Exception('Erro na busca LDAP: ' . $e->getMessage());
        } finally {
            if ($connection) {
                ldap_close($connection);
            }
        }

        return $users;
    }

    public function __destruct()
    {
        if ($this->connection) {
            ldap_close($this->connection);
        }
    }
}
