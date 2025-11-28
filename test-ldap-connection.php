<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Teste de Conexão LDAP ===\n\n";

$host = env('LDAP_HOSTS', '10.0.0.200');
$port = env('LDAP_PORT', 389);
$username = env('LDAP_USERNAME');
$password = env('LDAP_PASSWORD');
$baseDn = env('LDAP_BASE_DN');

echo "Host: $host\n";
echo "Porta: $port\n";
echo "Usuário: $username\n";
echo "Base DN: $baseDn\n\n";

// Tentar conectar
$ldapConn = ldap_connect($host, $port);

if (!$ldapConn) {
    echo "❌ ERRO: Não foi possível conectar ao servidor LDAP\n";
    exit(1);
}

echo "✓ Conexão estabelecida\n";

// Configurar opções
ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

// Tentar fazer bind
echo "Tentando autenticar...\n";

if (@ldap_bind($ldapConn, $username, $password)) {
    echo "✅ SUCESSO: Autenticação realizada com sucesso!\n\n";
    
    // Tentar buscar usuários
    echo "Buscando usuários...\n";
    $filter = "(&(objectCategory=person)(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))";
    $attributes = ['cn', 'mail', 'samaccountname', 'displayname', 'department'];
    
    $search = @ldap_search($ldapConn, $baseDn, $filter, $attributes, 0, 10);
    
    if ($search) {
        $entries = ldap_get_entries($ldapConn, $search);
        echo "✓ Encontrados {$entries['count']} usuários (mostrando até 10):\n\n";
        
        for ($i = 0; $i < min($entries['count'], 10); $i++) {
            $entry = $entries[$i];
            $cn = $entry['cn'][0] ?? 'N/A';
            $mail = $entry['mail'][0] ?? 'N/A';
            $samaccountname = $entry['samaccountname'][0] ?? 'N/A';
            $displayname = $entry['displayname'][0] ?? 'N/A';
            $department = $entry['department'][0] ?? 'N/A';
            
            echo "  " . ($i + 1) . ". $displayname\n";
            echo "     Login: $samaccountname\n";
            echo "     Email: $mail\n";
            echo "     Departamento: $department\n\n";
        }
    } else {
        echo "⚠️ AVISO: Não foi possível buscar usuários\n";
        echo "Erro: " . ldap_error($ldapConn) . "\n";
    }
    
    ldap_close($ldapConn);
} else {
    echo "❌ ERRO: Falha na autenticação\n";
    echo "Erro: " . ldap_error($ldapConn) . "\n";
    echo "\nVerifique:\n";
    echo "  - Usuário e senha estão corretos\n";
    echo "  - O formato do usuário está correto (pode ser: user@dominio.com ou DOMINIO\\usuario ou CN completo)\n";
    ldap_close($ldapConn);
    exit(1);
}
