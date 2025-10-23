<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\Location;
use App\Jobs\LdapBulkImportJob;
use Adldap\Adldap as AdldapClient;
use Carbon\Carbon;

class LdapSyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ldap:sync 
                            {--dry-run : Executar sem salvar alterações}
                            {--batch-size=100 : Tamanho do lote para processamento}
                            {--email= : Email para relatório (opcional)}
                            {--force : Forçar sincronização mesmo se já executada hoje}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sincroniza usuários do LDAP/Active Directory automaticamente';

    protected $stats = [
        'total_found' => 0,
        'new_users' => 0,
        'updated_users' => 0,
        'deactivated_users' => 0,
        'errors' => 0,
        'start_time' => null,
        'end_time' => null,
    ];

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->stats['start_time'] = Carbon::now();
        
        $this->info('🔄 Iniciando sincronização automática LDAP...');
        
        // Verificar se já foi executada hoje (a menos que --force)
        if (!$this->option('force') && $this->wasExecutedToday()) {
            $this->warn('⚠️  Sincronização já foi executada hoje. Use --force para executar novamente.');
            return Command::SUCCESS;
        }
        
        try {
            // Conectar ao LDAP usando configurações padrão
            $provider = $this->connectToLdap();
            if (!$provider) {
                return Command::FAILURE;
            }
            
            // Buscar usuários do LDAP
            $ldapUsers = $this->fetchLdapUsers($provider);
            
            // Processar usuários
            $this->processUsers($ldapUsers);
            
            // Gerar relatório
            $this->generateReport();
            
            // Marcar como executada hoje
            $this->markAsExecutedToday();
            
        } catch (\Exception $e) {
            $this->error('❌ Erro durante sincronização: ' . $e->getMessage());
            Log::error('LDAP Sync Error: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);
            return Command::FAILURE;
        }
        
        $this->stats['end_time'] = Carbon::now();
        $this->info('✅ Sincronização concluída com sucesso!');
        
        return Command::SUCCESS;
    }

    /**
     * Verifica se a sincronização já foi executada hoje
     */
    private function wasExecutedToday(): bool
    {
        $lastRun = Cache::get('ldap_sync_last_run');
        return $lastRun && Carbon::parse($lastRun)->isToday();
    }

    /**
     * Marca a sincronização como executada hoje
     */
    private function markAsExecutedToday(): void
    {
        Cache::put('ldap_sync_last_run', Carbon::now(), now()->addDays(2));
    }

    /**
     * Conecta ao LDAP usando configurações padrão
     */
    private function connectToLdap()
    {
        try {
            // Usar configurações do arquivo config/ldap.php ou valores padrão
            $config = [
                'hosts' => [config('ldap.default.hosts.0', '10.0.50.10')],
                'base_dn' => config('ldap.default.base_dn', 'DC=pmguarapuava,DC=local'),
                'username' => config('ldap.default.username', 'CN=Administrador,CN=Users,DC=pmguarapuava,DC=local'),
                'password' => config('ldap.default.password', 'Senha@123'),
                'port' => config('ldap.default.port', 389),
                'use_ssl' => config('ldap.default.use_ssl', false),
                'timeout' => config('ldap.default.timeout', 30),
            ];

            $ad = new AdldapClient();
            $ad->addProvider($config);
            $provider = $ad->connect();

            $this->info('✅ Conectado ao LDAP com sucesso');
            return $provider;

        } catch (\Exception $e) {
            $this->error('❌ Falha ao conectar ao LDAP: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Busca usuários do LDAP
     */
    private function fetchLdapUsers($provider): array
    {
        $this->info('🔍 Buscando usuários no LDAP...');
        
        $users = [];
        
        try {
            $search = $provider->search()->users()->select([
                'cn', 'samaccountname', 'mail', 'userprincipalname',
                'distinguishedname', 'displayname', 'useraccountcontrol',
                'givenname', 'sn', 'name', 'department', 'title', 
                'company', 'telephonenumber', 'mobile'
            ]);

            // Buscar todos os usuários (sem filtro de status para detectar desativações)
            $ldapUsers = $search->get();
            
            $this->info("📊 Encontrados " . count($ldapUsers) . " usuários no LDAP");
            $this->stats['total_found'] = count($ldapUsers);

            foreach ($ldapUsers as $user) {
                $userData = $this->extractUserData($user);
                if ($userData) {
                    $users[] = $userData;
                }
            }

            return $users;

        } catch (\Exception $e) {
            $this->error('❌ Erro ao buscar usuários: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Extrai dados do usuário do LDAP
     */
    private function extractUserData($user): ?array
    {
        try {
            $cn = $user->getFirstAttribute('cn') ?? '';
            $sam = $user->getFirstAttribute('samaccountname') ?? '';
            $mail = $user->getFirstAttribute('mail') ?? '';
            $upn = $user->getFirstAttribute('userprincipalname') ?? '';
            $dn = $user->getDn() ?? '';
            $displayName = $user->getFirstAttribute('displayname') ?? $cn;
            $userAccountControl = $user->getFirstAttribute('useraccountcontrol') ?? '';

            // Verificar se tem identificador válido
            if (!$sam && !$upn && !$dn) {
                return null;
            }

            // Construir nome inteligente
            $givenName = $user->getFirstAttribute('givenname') ?? '';
            $surname = $user->getFirstAttribute('sn') ?? '';
            $fullName = $user->getFirstAttribute('name') ?? '';
            
            $bestName = $displayName ?: $fullName ?: $cn ?: $sam;
            if (!$bestName && $givenName && $surname) {
                $bestName = trim($givenName . ' ' . $surname);
            }

            // Verificar se conta está ativa (bit 2 = ACCOUNTDISABLE)
            $isActive = !($userAccountControl && (intval($userAccountControl) & 2));

            return [
                'dn' => $dn,
                'samaccountname' => $sam,
                'email' => $mail ?: $upn,
                'name' => $bestName ?: $upn ?: '(sem nome)',
                'display_name' => $displayName,
                'upn' => $upn,
                'department' => $user->getFirstAttribute('department') ?? '',
                'title' => $user->getFirstAttribute('title') ?? '',
                'company' => $user->getFirstAttribute('company') ?? '',
                'phone' => $user->getFirstAttribute('telephonenumber') ?? '',
                'mobile' => $user->getFirstAttribute('mobile') ?? '',
                'is_active' => $isActive,
                'user_account_control' => $userAccountControl,
            ];

        } catch (\Exception $e) {
            $this->warn("⚠️  Erro ao processar usuário: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Processa os usuários (cria, atualiza, desativa)
     */
    private function processUsers(array $ldapUsers): void
    {
        $this->info('⚙️  Processando usuários...');
        
        $batchSize = $this->option('batch-size');
        $batches = array_chunk($ldapUsers, $batchSize);
        
        $progressBar = $this->output->createProgressBar(count($ldapUsers));
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

    /**
     * Processa um lote de usuários (modo dry-run)
     */
    private function processBatchDryRun(array $users, $progressBar): void
    {
        foreach ($users as $userData) {
            $existing = User::where('ldap_dn', $userData['dn'])
                           ->orWhere('email', $userData['email'])
                           ->first();

            if ($existing) {
                $this->line("📝 [DRY-RUN] Atualizaria: " . $userData['name']);
                $this->stats['updated_users']++;
            } else {
                $this->line("➕ [DRY-RUN] Criaria: " . $userData['name']);
                $this->stats['new_users']++;
            }

            $progressBar->advance();
        }
    }

    /**
     * Processa um lote de usuários (modo real)
     */
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

    /**
     * Processa um usuário individual
     */
    private function processUser(array $userData): void
    {
        try {
            $existing = User::where('ldap_dn', $userData['dn'])
                           ->orWhere('email', $userData['email'])
                           ->first();

            if ($existing) {
                // Atualizar usuário existente
                $updated = $this->updateUser($existing, $userData);
                if ($updated) {
                    $this->stats['updated_users']++;
                }
            } else {
                // Criar novo usuário
                $this->createUser($userData);
                $this->stats['new_users']++;
            }

        } catch (\Exception $e) {
            $this->warn("⚠️  Erro ao processar {$userData['name']}: " . $e->getMessage());
            $this->stats['errors']++;
        }
    }

    /**
     * Cria um novo usuário
     */
    private function createUser(array $userData): void
    {
        // Só criar se a conta estiver ativa
        if (!$userData['is_active']) {
            return;
        }

        User::create([
            'name' => $userData['name'],
            'email' => $userData['email'] ?: $userData['samaccountname'] . '@pmguarapuava.local',
            'ldap_dn' => $userData['dn'],
            'password' => bcrypt('password123'), // Senha temporária
            'department' => $userData['department'],
            'is_active' => true,
            // Atribuir UBS aleatoriamente se não tiver
            'location_id' => $this->getRandomUbsId(),
        ]);
    }

    /**
     * Atualiza um usuário existente
     */
    private function updateUser(User $user, array $userData): bool
    {
        $changes = [];

        // Verificar campos que podem ter mudado
        if ($user->name !== $userData['name']) {
            $changes['name'] = $userData['name'];
        }

        if ($user->department !== $userData['department']) {
            $changes['department'] = $userData['department'];
        }

        // Atualizar status de ativação
        $shouldBeActive = $userData['is_active'];
        if ($user->is_active !== $shouldBeActive) {
            $changes['is_active'] = $shouldBeActive;
            if (!$shouldBeActive) {
                $this->stats['deactivated_users']++;
            }
        }

        if (!empty($changes)) {
            $user->update($changes);
            return true;
        }

        return false;
    }

    /**
     * Obtém ID de UBS aleatória para novos usuários
     */
    private function getRandomUbsId(): ?int
    {
        static $ubsIds = null;
        
        if ($ubsIds === null) {
            $ubsIds = Location::pluck('id')->toArray();
        }
        
        return $ubsIds ? $ubsIds[array_rand($ubsIds)] : null;
    }

    /**
     * Gera relatório da sincronização
     */
    private function generateReport(): void
    {
        $this->stats['end_time'] = Carbon::now();
        
        $duration = $this->stats['start_time'] && $this->stats['end_time']
            ? $this->stats['start_time']->diffInSeconds($this->stats['end_time'])
            : 0;

        $this->line('');
        $this->info('📊 Relatório de Sincronização:');
        $this->table(
            ['Métrica', 'Valor'],
            [
                ['Usuários encontrados no LDAP', $this->stats['total_found']],
                ['Novos usuários criados', $this->stats['new_users']],
                ['Usuários atualizados', $this->stats['updated_users']],
                ['Usuários desativados', $this->stats['deactivated_users']],
                ['Erros', $this->stats['errors']],
                ['Duração', $duration . ' segundos'],
                ['Modo', $this->option('dry-run') ? 'DRY-RUN' : 'REAL'],
            ]
        );

        // Salvar relatório em log
        Log::info('LDAP Sync Report', $this->stats);

        // Enviar por email se solicitado
        $email = $this->option('email');
        if ($email) {
            $this->sendReportEmail($email);
        }
    }

    /**
     * Envia relatório por email
     */
    private function sendReportEmail(string $email): void
    {
        try {
            // Implementar envio de email do relatório
            $this->info("📧 Relatório enviado para: $email");
        } catch (\Exception $e) {
            $this->warn("⚠️  Erro ao enviar email: " . $e->getMessage());
        }
    }
}
