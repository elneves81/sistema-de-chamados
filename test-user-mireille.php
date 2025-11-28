<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "=== TESTE USUÁRIO ESPECÍFICO: Mireille.Dussanoski ===\n\n";

$host = '10.0.0.200';
$port = 389;
$baseDn = 'DC=guarapuava,DC=pr,DC=gov,DC=br';
$username = 'elber.pmg';
$password = 'elber@2023';

// Conectar
$ldapConn = @ldap_connect($host, $port);
ldap_set_option($ldapConn, LDAP_OPT_PROTOCOL_VERSION, 3);
ldap_set_option($ldapConn, LDAP_OPT_REFERRALS, 0);

$bindDn = $username . '@guarapuava.pr.gov.br';
$bind = @ldap_bind($ldapConn, $bindDn, $password);

if (!$bind) {
    echo "❌ Erro ao conectar\n";
    exit(1);
}

echo "✓ Conectado ao LDAP\n\n";

// Buscar especificamente esse usuário
echo "1. Buscando por samaccountname = 'Mireille.Dussanoski'...\n";
$filter1 = "(samaccountname=Mireille.Dussanoski)";
$search1 = @ldap_search($ldapConn, $baseDn, $filter1, ['*']);

if ($search1) {
    $entries1 = ldap_get_entries($ldapConn, $search1);
    echo "   Encontrados: {$entries1['count']} resultado(s)\n";
    
    if ($entries1['count'] > 0) {
        $entry = $entries1[0];
        echo "\n   DADOS DO USUÁRIO:\n";
        echo "   ================\n";
        echo "   DN: " . ($entry['dn'] ?? 'N/A') . "\n";
        echo "   samaccountname: " . ($entry['samaccountname'][0] ?? 'N/A') . "\n";
        echo "   displayname: " . ($entry['displayname'][0] ?? 'N/A') . "\n";
        echo "   cn: " . ($entry['cn'][0] ?? 'N/A') . "\n";
        echo "   mail: " . ($entry['mail'][0] ?? 'N/A') . "\n";
        echo "   userprincipalname: " . ($entry['userprincipalname'][0] ?? 'N/A') . "\n";
        echo "   userAccountControl: " . ($entry['useraccountcontrol'][0] ?? 'N/A') . "\n";
        
        // Verificar status da conta
        $uac = intval($entry['useraccountcontrol'][0] ?? 0);
        echo "\n   STATUS DA CONTA:\n";
        echo "   ================\n";
        
        if ($uac & 0x0002) {
            echo "   ❌ CONTA DESABILITADA (flag 0x0002 ativa)\n";
        } else {
            echo "   ✓ Conta habilitada\n";
        }
        
        if ($uac & 0x0010) {
            echo "   ⚠️  Conta bloqueada\n";
        }
        
        if ($uac & 0x0020) {
            echo "   ⚠️  Senha não requerida\n";
        }
        
        if ($uac & 0x10000) {
            echo "   ⚠️  Senha nunca expira\n";
        }
        
        echo "\n   UserAccountControl completo: {$uac} (0x" . dechex($uac) . ")\n";
        
        // Verificar email
        echo "\n   VALIDAÇÃO EMAIL:\n";
        echo "   ================\n";
        $email = $entry['mail'][0] ?? '';
        if (empty($email)) {
            echo "   ❌ SEM EMAIL no LDAP\n";
        } else {
            echo "   Email encontrado: {$email}\n";
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                echo "   ✓ Email válido\n";
            } else {
                echo "   ⚠️  Email inválido\n";
            }
        }
        
        // Verificar grupos
        echo "\n   GRUPOS:\n";
        echo "   =======\n";
        if (isset($entry['memberof'])) {
            $count = $entry['memberof']['count'];
            echo "   Membro de {$count} grupo(s):\n";
            for ($i = 0; $i < min(5, $count); $i++) {
                echo "   - " . $entry['memberof'][$i] . "\n";
            }
        } else {
            echo "   Nenhum grupo encontrado\n";
        }
    }
} else {
    echo "   ❌ Erro na busca: " . ldap_error($ldapConn) . "\n";
}

echo "\n2. Testando filtro de importação padrão...\n";
$filter2 = "(&(objectCategory=person)(objectClass=user)(samaccountname=Mireille.Dussanoski))";
$search2 = @ldap_search($ldapConn, $baseDn, $filter2, ['samaccountname', 'displayname', 'mail', 'useraccountcontrol']);

if ($search2) {
    $entries2 = ldap_get_entries($ldapConn, $search2);
    echo "   Encontrados com filtro de importação: {$entries2['count']} resultado(s)\n";
} else {
    echo "   ❌ Erro: " . ldap_error($ldapConn) . "\n";
}

echo "\n3. Testando filtro com contas ativas apenas...\n";
$filter3 = "(&(objectCategory=person)(objectClass=user)(samaccountname=Mireille.Dussanoski)(!(userAccountControl:1.2.840.113556.1.4.803:=2)))";
$search3 = @ldap_search($ldapConn, $baseDn, $filter3, ['samaccountname', 'displayname', 'useraccountcontrol']);

if ($search3) {
    $entries3 = ldap_get_entries($ldapConn, $search3);
    echo "   Encontrados (somente ativos): {$entries3['count']} resultado(s)\n";
    
    if ($entries3['count'] == 0) {
        echo "   ⚠️  MOTIVO: Usuário está DESABILITADO no Active Directory\n";
        echo "   Para importar, a conta precisa estar ativa no AD\n";
    }
} else {
    echo "   ❌ Erro: " . ldap_error($ldapConn) . "\n";
}

ldap_close($ldapConn);

echo "\n=== FIM DO TESTE ===\n";
