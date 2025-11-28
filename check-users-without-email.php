<?php

require __DIR__.'/vendor/autoload.php';

use Adldap\Adldap;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== USUÁRIOS LDAP SEM EMAIL ===\n\n";

// Configuração LDAP
$config = [
    'hosts' => ['10.0.0.200'],
    'base_dn' => 'DC=guarapuava,DC=pr,DC=gov,DC=br',
    'username' => 'elber.pmg@guarapuava.pr.gov.br',
    'password' => 'elber@2023',
    'port' => 389,
    'use_ssl' => false,
    'use_tls' => false,
    'timeout' => 5,
];

try {
    $ad = new Adldap();
    $ad->addProvider($config);
    $provider = $ad->connect();
    
    echo "✓ Conectado ao LDAP\n\n";
    
    // Buscar todos os usuários (com ou sem email)
    $search = $provider->search();
    
    $users = $search->where('objectClass', '=', 'user')
                    ->where('objectCategory', '=', 'person')
                    ->whereHas('sAMAccountName')
                    ->get();
    
    echo "Total de usuários no LDAP: " . count($users) . "\n\n";
    
    $usersWithoutEmail = [];
    $activeWithoutEmail = 0;
    $disabledWithoutEmail = 0;
    
    foreach ($users as $user) {
        $userAccountControl = $user->getUserAccountControl();
        $isDisabled = ($userAccountControl & 2) == 2;
        
        // Verificar se NÃO tem email
        $mail = $user->mail;
        $hasEmail = !empty($mail) && (is_array($mail) ? !empty($mail[0]) : true);
        
        if (!$hasEmail) {
            $username = $user->getAccountName();
            $displayName = $user->getDisplayName();
            $dn = $user->getDistinguishedName();
            
            $userData = [
                'username' => $username,
                'name' => $displayName ?: $username,
                'dn' => $dn,
                'status' => $isDisabled ? 'Desativado' : 'Ativo',
                'userAccountControl' => $userAccountControl,
            ];
            
            if ($isDisabled) {
                $disabledWithoutEmail++;
            } else {
                $activeWithoutEmail++;
                $usersWithoutEmail[] = $userData;
            }
        }
    }
    
    echo "Usuários SEM email:\n";
    echo "  - Ativos: $activeWithoutEmail\n";
    echo "  - Desativados: $disabledWithoutEmail\n";
    echo "  - TOTAL: " . ($activeWithoutEmail + $disabledWithoutEmail) . "\n\n";
    
    if (!empty($usersWithoutEmail)) {
        echo "USUÁRIOS ATIVOS SEM EMAIL:\n";
        echo str_repeat("=", 80) . "\n\n";
        
        foreach ($usersWithoutEmail as $i => $user) {
            echo ($i + 1) . ". {$user['name']} ({$user['username']})\n";
            echo "   DN: {$user['dn']}\n";
            echo "   Status: {$user['status']}\n\n";
        }
        
        // Salvar lista
        file_put_contents(
            __DIR__ . '/users-without-email.json',
            json_encode($usersWithoutEmail, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        
        echo "📄 Lista salva em: users-without-email.json\n\n";
        
        // Perguntar se quer importar
        echo str_repeat("=", 80) . "\n";
        echo "⚠️  ATENÇÃO: Esses usuários NÃO possuem email no Active Directory.\n";
        echo "   Para importá-los, será necessário:\n";
        echo "   1. Gerar emails automáticos (ex: username@guarapuava.pr.gov.br)\n";
        echo "   2. Ou cadastrar emails manualmente no AD primeiro\n";
        echo str_repeat("=", 80) . "\n";
    } else {
        echo "✅ Todos os usuários ativos possuem email cadastrado!\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIM ===\n";
