<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Str;

echo "=== IMPORTAÇÃO MANUAL: Mireille.Dussanoski ===\n\n";

$userData = [
    'dn' => 'CN=Mireille Dussanoski,OU=Vila Bela,OU=Postos de Saude,OU=Secretaria de Saude,OU=Usuarios,OU=PMG,DC=guarapuava,DC=pr,DC=gov,DC=br',
    'sAMAccountName' => 'Mireille.Dussanoski',
    'displayName' => 'Mireille Dussanoski',
    'mail' => 'mireilledussanoski@hotmail.com',
    'userPrincipalName' => 'Mireille.Dussanoski@guarapuava.pr.gov.br',
];

echo "Dados a importar:\n";
print_r($userData);

// Verificar se já existe
echo "\n1. Verificando se usuário já existe...\n";
$existing = User::where('ldap_dn', $userData['dn'])
    ->orWhere('email', $userData['mail'])
    ->orWhere('username', $userData['sAMAccountName'])
    ->first();

if ($existing) {
    echo "❌ Usuário JÁ EXISTE:\n";
    echo "   ID: {$existing->id}\n";
    echo "   Nome: {$existing->name}\n";
    echo "   Email: {$existing->email}\n";
    echo "   Username: " . ($existing->username ?? 'NULL') . "\n";
    echo "   LDAP DN: " . ($existing->ldap_dn ?? 'NULL') . "\n";
    exit(0);
}

echo "✓ Usuário não existe, pode ser criado\n\n";

echo "2. Criando usuário...\n";
try {
    $user = User::create([
        'name' => $userData['displayName'],
        'email' => $userData['mail'],
        'username' => $userData['sAMAccountName'],
        'password' => bcrypt(Str::random(40)),
        'role' => 'customer', // Corrigido: usar 'customer' em vez de 'user'
        'is_active' => true,
        'ldap_dn' => $userData['dn'],
        'ldap_upn' => $userData['userPrincipalName'],
        'auth_via_ldap' => true,
    ]);
    
    echo "✅ SUCESSO! Usuário criado:\n";
    echo "   ID: {$user->id}\n";
    echo "   Nome: {$user->name}\n";
    echo "   Email: {$user->email}\n";
    echo "   Username: {$user->username}\n";
    
} catch (\Exception $e) {
    echo "❌ ERRO ao criar usuário:\n";
    echo "   " . $e->getMessage() . "\n";
    echo "\n   Stack trace:\n";
    echo "   " . $e->getTraceAsString() . "\n";
}

echo "\n=== FIM ===\n";
