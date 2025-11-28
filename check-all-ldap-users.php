<?php

require __DIR__.'/vendor/autoload.php';

use Adldap\Adldap;
use Illuminate\Support\Facades\DB;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICAÇÃO COMPLETA DE USUÁRIOS LDAP ===\n\n";

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
    
    // Buscar todos os usuários ativos
    $search = $provider->search();
    
    // Filtro: usuários com email e conta ativa
    $users = $search->where('objectClass', '=', 'user')
                    ->where('objectCategory', '=', 'person')
                    ->whereHas('mail')
                    ->whereHas('sAMAccountName')
                    ->get();
    
    echo "Total de usuários encontrados no LDAP: " . count($users) . "\n\n";
    
    $usersData = [];
    $activeUsers = 0;
    
    foreach ($users as $user) {
        $userAccountControl = $user->getUserAccountControl();
        
        // Verificar se a conta está ativa (bit 2 não está setado)
        $isDisabled = ($userAccountControl & 2) == 2;
        
        if (!$isDisabled) {
            $activeUsers++;
            
            $username = $user->getAccountName();
            $email = is_array($user->mail) ? $user->mail[0] : $user->mail;
            $displayName = $user->getDisplayName();
            
            $usersData[] = [
                'username' => $username,
                'email' => $email,
                'name' => $displayName,
                'dn' => $user->getDistinguishedName(),
            ];
        }
    }
    
    echo "Usuários ativos no LDAP: $activeUsers\n\n";
    
    // Buscar usuários que JÁ estão no banco
    $existingUsernames = DB::table('users')
        ->whereNotNull('ldap_dn')
        ->pluck('username')
        ->toArray();
    
    echo "Usuários LDAP já importados no banco: " . count($existingUsernames) . "\n\n";
    
    // Encontrar usuários que NÃO estão no banco
    $missingUsers = [];
    foreach ($usersData as $userData) {
        if (!in_array($userData['username'], $existingUsernames)) {
            $missingUsers[] = $userData;
        }
    }
    
    if (empty($missingUsers)) {
        echo "✅ TODOS os usuários ativos do LDAP já estão importados!\n";
    } else {
        echo "⚠️  FALTAM " . count($missingUsers) . " USUÁRIOS PARA IMPORTAR:\n\n";
        
        foreach ($missingUsers as $i => $user) {
            echo ($i + 1) . ". {$user['name']} ({$user['username']})\n";
            echo "   Email: {$user['email']}\n";
            echo "   DN: {$user['dn']}\n\n";
        }
        
        // Salvar lista para importação
        file_put_contents(
            __DIR__ . '/users-to-import.json',
            json_encode($missingUsers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        );
        
        echo "📄 Lista salva em: users-to-import.json\n";
    }
    
} catch (Exception $e) {
    echo "❌ ERRO: " . $e->getMessage() . "\n";
    echo "Trace: " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIM ===\n";
