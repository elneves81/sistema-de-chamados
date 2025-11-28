<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTE DE CONEXÃO LDAP ===\n\n";

$host = '10.0.0.200';
$port = 389;
$baseDn = 'DC=guarapuava,DC=pr,DC=gov,DC=br';
$username = 'elber.pmg';
$password = 'elber@2023';

echo "Configurações:\n";
echo "Host: {$host}\n";
echo "Port: {$port}\n";
echo "Base DN: {$baseDn}\n";
echo "Username: {$username}\n\n";

// Tenta conectar
echo "1. Testando conexão...\n";
$ldapConn = @ldap_connect($host, $port);

if (!$ldapConn) {
    echo "❌ ERRO: Não foi possível conectar ao servidor LDAP\n";
    exit(1);
}

echo "✓ Conexão estabelecida\n\n";

// Configurações LDAP
ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);
ldap_set_option($ldapConn, LDAP_OPT_NETWORK_TIMEOUT, 5);

// Tenta fazer bind
echo "2. Testando autenticação (bind)...\n";
$bindDn = $username . '@guarapuava.pr.gov.br';
echo "Bind DN: {$bindDn}\n";

$bind = @ldap_bind($ldapConn, $bindDn, $password);

if (!$bind) {
    $error = ldap_error($ldapConn);
    $errno = ldap_errno($ldapConn);
    echo "❌ ERRO ao fazer bind: {$error} (Código: {$errno})\n";
    
    // Tenta com formato alternativo
    echo "\n3. Tentando formato CN...\n";
    $bindDn2 = "CN={$username},{$baseDn}";
    echo "Bind DN alternativo: {$bindDn2}\n";
    
    $bind2 = @ldap_bind($ldapConn, $bindDn2, $password);
    if (!$bind2) {
        $error2 = ldap_error($ldapConn);
        echo "❌ ERRO: {$error2}\n";
        ldap_close($ldapConn);
        exit(1);
    }
    echo "✓ Bind realizado com sucesso (formato CN)\n";
} else {
    echo "✓ Bind realizado com sucesso\n";
}

echo "\n4. Buscando usuários...\n";
$filter = "(&(objectCategory=person)(objectClass=user)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))";
echo "Filtro: {$filter}\n";
echo "Base DN: {$baseDn}\n\n";

$search = @ldap_search($ldapConn, $baseDn, $filter, ['samaccountname', 'displayname', 'mail'], 0, 10);

if (!$search) {
    $error = ldap_error($ldapConn);
    echo "❌ ERRO na busca: {$error}\n";
    ldap_close($ldapConn);
    exit(1);
}

$entries = ldap_get_entries($ldapConn, $search);
$count = $entries['count'];

echo "✓ Encontrados {$count} usuários\n\n";

if ($count > 0) {
    echo "Primeiros 10 usuários encontrados:\n";
    echo str_repeat("-", 80) . "\n";
    
    for ($i = 0; $i < min(10, $count); $i++) {
        $entry = $entries[$i];
        $username = $entry['samaccountname'][0] ?? 'N/A';
        $name = $entry['displayname'][0] ?? 'N/A';
        $email = $entry['mail'][0] ?? 'N/A';
        
        echo sprintf("%-3d | %-20s | %-30s | %s\n", 
            $i + 1, 
            $username, 
            $name, 
            $email
        );
    }
    echo str_repeat("-", 80) . "\n";
} else {
    echo "⚠️  ATENÇÃO: Nenhum usuário encontrado com o filtro especificado\n";
    echo "\nTentando busca mais simples...\n";
    
    $simpleFilter = "(objectClass=user)";
    $simpleSearch = @ldap_search($ldapConn, $baseDn, $simpleFilter, ['samaccountname'], 0, 5);
    
    if ($simpleSearch) {
        $simpleEntries = ldap_get_entries($ldapConn, $simpleSearch);
        $simpleCount = $simpleEntries['count'];
        echo "Com filtro simples: {$simpleCount} usuários encontrados\n";
        
        if ($simpleCount > 0) {
            echo "\n⚠️  PROBLEMA: O filtro complexo não está funcionando.\n";
            echo "Sugestão: Verifique a sintaxe do filtro LDAP_USER_FILTER no .env\n";
        }
    }
}

ldap_close($ldapConn);

echo "\n=== FIM DO TESTE ===\n";
