<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;

$login = 'elber.pmg';
$senha = 'elber@2023';

echo "=== TESTE DE VALIDAÇÃO DE ASSINATURA ===\n\n";

// Busca usuário (case-insensitive)
$user = User::whereRaw('LOWER(username) = ?', [strtolower($login)])
           ->orWhereRaw('LOWER(email) = ?', [strtolower($login)])
           ->first();

if (!$user) {
    echo "❌ Usuário não encontrado\n";
    exit(1);
}

echo "✅ Usuário encontrado:\n";
echo "   ID: {$user->id}\n";
echo "   Nome: {$user->name}\n";
echo "   Username: {$user->username}\n";
echo "   Email: {$user->email}\n";
echo "   Auth LDAP: " . ($user->auth_via_ldap ? 'SIM' : 'NÃO') . "\n\n";

// Testa LDAP
if (config('ldap.connections.default') && $user->auth_via_ldap) {
    echo "📡 Testando autenticação LDAP...\n";
    
    try {
        $ldapConfig = config('ldap.connections.default');
        
        // Remove configurações incompatíveis com Adldap2 v10
        $settings = $ldapConfig['settings'] ?? $ldapConfig;
        unset($ldapConfig['auto_connect']);
        unset($ldapConfig['connection']);
        
        $ad = new \Adldap\Adldap();
        $ad->addProvider($settings, 'default');
        $provider = $ad->getProvider('default');
        
        echo "   Provider criado: " . get_class($provider) . "\n";
        echo "   Tentando autenticar com username: {$user->username}\n";
        echo "   Hosts: " . json_encode($settings['hosts'] ?? 'N/A') . "\n";
        echo "   Base DN: " . ($settings['base_dn'] ?? 'N/A') . "\n";
        
        // Tenta diferentes formatos de username
        $formatos = [
            $user->username,
            $user->username . '@guarapuava.pr.gov.br',
            'guarapuava\\' . $user->username,
            strtolower($user->username),
        ];
        
        foreach ($formatos as $formato) {
            echo "\n   Testando formato: $formato\n";
            try {
                if ($provider->auth()->attempt($formato, $senha, true)) {
                    echo "✅ Autenticação LDAP bem-sucedida com formato: $formato!\n";
                    break;
                } else {
                    echo "   ❌ Falhou com formato: $formato\n";
                }
            } catch (\Exception $e) {
                echo "   ⚠️  Erro com formato $formato: " . $e->getMessage() . "\n";
            }
        }
    } catch (\Exception $e) {
        echo "❌ Erro LDAP: " . $e->getMessage() . "\n";
        echo "   Arquivo: " . $e->getFile() . ":" . $e->getLine() . "\n";
    }
} else {
    echo "⚠️  LDAP não configurado ou usuário sem LDAP\n";
    
    // Testa autenticação local
    echo "\n🔑 Testando autenticação local...\n";
    
    $credentials = ['username' => $user->username, 'password' => $senha];
    
    if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
        echo "✅ Autenticação local bem-sucedida!\n";
        \Illuminate\Support\Facades\Auth::logout();
    } else {
        echo "❌ Autenticação local falhou\n";
        
        // Tenta com email
        $credentials = ['email' => $user->email, 'password' => $senha];
        
        if (\Illuminate\Support\Facades\Auth::attempt($credentials)) {
            echo "✅ Autenticação local com email bem-sucedida!\n";
            \Illuminate\Support\Facades\Auth::logout();
        } else {
            echo "❌ Autenticação local com email também falhou\n";
        }
    }
}

echo "\n=== FIM DO TESTE ===\n";
