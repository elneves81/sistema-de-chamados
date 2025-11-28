<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use Adldap\Adldap as AdldapClient;

class ImportLdapUsers extends Command
{
    protected $signature = 'ldap:import-users 
                            {--limit=500 : Limite de usuários a importar}
                            {--batch-size=100 : Tamanho do lote}
                            {--filter= : Filtro de busca (opcional)}
                            {--dry-run : Simular sem salvar}';

    protected $description = 'Importa usuários do LDAP em lotes';

    private $stats = [
        'found' => 0,
        'imported' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => 0,
    ];

    public function handle()
    {
        $this->info('🔄 Importação LDAP iniciada...');
        
        // Usar configurações do .env
        $config = [
            'hosts' => [env('LDAP_HOSTS', '10.0.0.31')],
            'base_dn' => env('LDAP_BASE_DN', 'DC=guarapuava,DC=pr,DC=gov,DC=br'),
            'username' => env('LDAP_USERNAME'),
            'password' => env('LDAP_PASSWORD'),
            'port' => env('LDAP_PORT', 389),
            'use_ssl' => env('LDAP_USE_SSL', false),
            'use_tls' => env('LDAP_USE_TLS', false),
            'timeout' => env('LDAP_TIMEOUT', 10),
        ];

        $this->info("📡 Conectando a: {$config['hosts'][0]}:{$config['port']}");
        
        try {
            // Conectar ao LDAP
            $ad = new AdldapClient();
            $ad->addProvider($config);
            $provider = $ad->connect();
            
            $this->info('✅ Conectado ao LDAP com sucesso!');
            
            // Buscar usuários
            $this->info('🔍 Buscando usuários...');
            $users = $this->fetchUsers($provider);
            
            // Processar em lotes
            $this->processUsers($users);
            
            // Exibir relatório
            $this->showReport();
            
        } catch (\Exception $e) {
            $this->error('❌ Erro: ' . $e->getMessage());
            Log::error('LDAP Import Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return Command::FAILURE;
        }
        
        return Command::SUCCESS;
    }

    private function fetchUsers($provider): array
    {
        $limit = $this->option('limit');
        $filter = $this->option('filter');
        
        $search = $provider->search()->users()->select([
            'cn', 'samaccountname', 'mail', 'userprincipalname',
            'distinguishedname', 'displayname', 'useraccountcontrol',
            'givenname', 'sn', 'name', 'department', 'title'
        ]);

        if ($filter) {
            $search->whereContains('cn', $filter)
                   ->orWhereContains('samaccountname', $filter)
                   ->orWhereContains('displayname', $filter);
        }

        $ldapUsers = $search->limit($limit)->get();
        
        $this->stats['found'] = count($ldapUsers);
        $this->info("📊 Encontrados {$this->stats['found']} usuários");
        
        $users = [];
        foreach ($ldapUsers as $user) {
            $userData = $this->extractUserData($user);
            if ($userData) {
                $users[] = $userData;
            }
        }
        
        return $users;
    }

    private function extractUserData($user): ?array
    {
        try {
            $sam = $user->getFirstAttribute('samaccountname') ?? '';
            $mail = $user->getFirstAttribute('mail') ?? '';
            $upn = $user->getFirstAttribute('userprincipalname') ?? '';
            $dn = $user->getDn() ?? '';
            
            if (!$sam && !$upn) {
                return null;
            }
            
            $cn = $user->getFirstAttribute('cn') ?? '';
            $displayName = $user->getFirstAttribute('displayname') ?? $cn;
            $givenName = $user->getFirstAttribute('givenname') ?? '';
            $surname = $user->getFirstAttribute('sn') ?? '';
            
            $name = $displayName ?: $cn ?: ($givenName . ' ' . $surname) ?: $sam;
            
            return [
                'name' => trim($name),
                'email' => $mail ?: $upn ?: ($sam . '@guarapuava.pr.gov.br'),
                'samaccountname' => $sam,
                'ldap_dn' => $dn,
                'department' => $user->getFirstAttribute('department') ?? '',
                'title' => $user->getFirstAttribute('title') ?? '',
            ];
            
        } catch (\Exception $e) {
            $this->warn("⚠️  Erro ao processar usuário: " . $e->getMessage());
            return null;
        }
    }

    private function processUsers(array $users): void
    {
        $batchSize = $this->option('batch-size');
        $batches = array_chunk($users, $batchSize);
        
        $this->info("📦 Processando {$this->stats['found']} usuários em " . count($batches) . " lotes...");
        
        $progressBar = $this->output->createProgressBar(count($users));
        $progressBar->start();
        
        foreach ($batches as $batch) {
            if ($this->option('dry-run')) {
                $this->processBatchDryRun($batch, $progressBar);
            } else {
                $this->processBatch($batch, $progressBar);
            }
        }
        
        $progressBar->finish();
        $this->line('');
    }

    private function processBatchDryRun(array $users, $progressBar): void
    {
        foreach ($users as $userData) {
            $existing = User::where('email', $userData['email'])->first();
            
            if ($existing) {
                $this->line("📝 [DRY-RUN] Atualizaria: {$userData['name']}");
                $this->stats['updated']++;
            } else {
                $this->line("➕ [DRY-RUN] Criaria: {$userData['name']}");
                $this->stats['imported']++;
            }
            
            $progressBar->advance();
        }
    }

    private function processBatch(array $users, $progressBar): void
    {
        DB::beginTransaction();
        
        try {
            foreach ($users as $userData) {
                $this->processUser($userData);
                $progressBar->advance();
            }
            
            DB::commit();
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("❌ Erro no lote: " . $e->getMessage());
            $this->stats['errors']++;
        }
    }

    private function processUser(array $userData): void
    {
        try {
            $existing = User::where('email', $userData['email'])
                           ->orWhere('ldap_dn', $userData['ldap_dn'])
                           ->first();
            
            if ($existing) {
                $existing->update([
                    'name' => $userData['name'],
                    'department' => $userData['department'],
                ]);
                $this->stats['updated']++;
            } else {
                User::create([
                    'name' => $userData['name'],
                    'email' => $userData['email'],
                    'ldap_dn' => $userData['ldap_dn'],
                    'password' => bcrypt('Senha@123'),
                    'department' => $userData['department'],
                    'is_active' => true,
                    'role' => 'user',
                ]);
                $this->stats['imported']++;
            }
            
        } catch (\Exception $e) {
            $this->warn("⚠️  Erro ao processar {$userData['name']}: " . $e->getMessage());
            $this->stats['errors']++;
        }
    }

    private function showReport(): void
    {
        $this->line('');
        $this->info('📊 Relatório de Importação:');
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Encontrados no LDAP', $this->stats['found']],
                ['Importados (novos)', $this->stats['imported']],
                ['Atualizados', $this->stats['updated']],
                ['Ignorados', $this->stats['skipped']],
                ['Erros', $this->stats['errors']],
                ['Modo', $this->option('dry-run') ? 'DRY-RUN (simulação)' : 'REAL'],
            ]
        );
    }
}
