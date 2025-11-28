<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

echo "=== TESTE DE VALIDAÇÃO DEYSIANNE ===\n\n";

$user = User::find(5213);

if (!$user) {
    echo "❌ Usuária não encontrada!\n";
    exit;
}

echo "✓ Usuária encontrada:\n";
echo "  ID: {$user->id}\n";
echo "  Nome: {$user->name}\n";
echo "  Username: {$user->username}\n";
echo "  Email: {$user->email}\n";
echo "  Auth LDAP: " . ($user->auth_via_ldap ? 'Sim' : 'Não') . "\n";
echo "  Tem senha local: " . ($user->password ? 'Sim' : 'Não') . "\n\n";

// Pede a senha para testar
echo "Digite a senha para testar a validação: ";
$senha = trim(fgets(STDIN));

if (empty($senha)) {
    echo "❌ Senha não pode estar vazia!\n";
    exit;
}

echo "\n--- Testando autenticação local ---\n";

// Testa com username
$credentials = ['username' => $user->username, 'password' => $senha];
echo "Testando com username: {$user->username}\n";

if (Auth::attempt($credentials)) {
    Auth::logout();
    echo "✅ Autenticação local com username: SUCESSO\n\n";
} else {
    echo "❌ Autenticação local com username: FALHOU\n";
    
    // Testa com email
    $credentials = ['email' => $user->email, 'password' => $senha];
    echo "Testando com email: {$user->email}\n";
    
    if (Auth::attempt($credentials)) {
        Auth::logout();
        echo "✅ Autenticação local com email: SUCESSO\n\n";
    } else {
        echo "❌ Autenticação local com email: FALHOU\n\n";
    }
}

// Testa LDAP se configurado
if (config('ldap.connections.default') && $user->auth_via_ldap) {
    echo "--- Testando autenticação LDAP ---\n";
    
    try {
        $ldapConfig = config('ldap.connections.default');
        $settings = $ldapConfig['settings'] ?? $ldapConfig;
        unset($ldapConfig['auto_connect']);
        unset($ldapConfig['connection']);
        
        $ad = new \Adldap\Adldap();
        $ad->addProvider($settings, 'default');
        $provider = $ad->getProvider('default');
        
        $formatos = [
            $user->username . '@guarapuava.pr.gov.br',
            $user->username,
            strtolower($user->username) . '@guarapuava.pr.gov.br',
            strtolower($user->username),
            'GUARAPUAVA\\' . $user->username,
            'GUARAPUAVA\\' . strtolower($user->username),
        ];
        
        $autenticado = false;
        foreach ($formatos as $formato) {
            echo "Testando formato: $formato ... ";
            try {
                if ($provider->auth()->attempt($formato, $senha, true)) {
                    echo "✅ SUCESSO!\n";
                    $autenticado = true;
                    break;
                } else {
                    echo "❌ Falhou\n";
                }
            } catch (\Exception $e) {
                echo "❌ Erro: " . $e->getMessage() . "\n";
            }
        }
        
        if ($autenticado) {
            echo "\n✅ Autenticação LDAP: SUCESSO\n";
        } else {
            echo "\n❌ Autenticação LDAP: FALHOU em todos os formatos\n";
        }
        
    } catch (\Exception $e) {
        echo "❌ Erro ao testar LDAP: " . $e->getMessage() . "\n";
    }
} else {
    echo "--- LDAP não configurado ou usuário sem LDAP habilitado ---\n";
}

echo "\n=== FIM DO TESTE ===\n";
