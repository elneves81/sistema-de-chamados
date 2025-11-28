<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Str;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== IMPORTAÇÃO EM MASSA DE USUÁRIOS LDAP ===\n\n";

// Carregar lista de usuários
$jsonFile = __DIR__ . '/users-to-import.json';

if (!file_exists($jsonFile)) {
    echo "❌ Arquivo users-to-import.json não encontrado!\n";
    echo "Execute primeiro: php check-all-ldap-users.php\n";
    exit(1);
}

$usersToImport = json_decode(file_get_contents($jsonFile), true);

if (empty($usersToImport)) {
    echo "✅ Não há usuários para importar!\n";
    exit(0);
}

echo "Total de usuários a importar: " . count($usersToImport) . "\n\n";
echo "Deseja continuar? (s/n): ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
if (trim(strtolower($line)) !== 's') {
    echo "Importação cancelada.\n";
    exit(0);
}
fclose($handle);

echo "\nIniciando importação...\n\n";

$success = 0;
$errors = 0;
$skipped = 0;

foreach ($usersToImport as $i => $userData) {
    $num = $i + 1;
    echo "[$num/" . count($usersToImport) . "] Importando: {$userData['name']} ({$userData['username']})... ";
    
    try {
        // Verificar se já existe (dupla checagem)
        $existing = User::where('username', $userData['username'])
                       ->orWhere('email', $userData['email'])
                       ->first();
        
        if ($existing) {
            echo "⊘ JÁ EXISTE\n";
            $skipped++;
            continue;
        }
        
        // Criar usuário
        $user = User::create([
            'name' => $userData['name'],
            'email' => $userData['email'],
            'username' => $userData['username'],
            'password' => bcrypt(Str::random(40)),
            'role' => 'customer', // Role correto
            'is_active' => true,
            'ldap_dn' => $userData['dn'],
            'auth_via_ldap' => true,
        ]);
        
        echo "✓ SUCESSO (ID: {$user->id})\n";
        $success++;
        
    } catch (\Exception $e) {
        echo "✗ ERRO: " . $e->getMessage() . "\n";
        $errors++;
    }
    
    // Pequena pausa para não sobrecarregar
    usleep(100000); // 0.1 segundo
}

echo "\n" . str_repeat("=", 60) . "\n";
echo "RESUMO DA IMPORTAÇÃO:\n";
echo "  ✓ Importados com sucesso: $success\n";
echo "  ⊘ Já existiam (pulados): $skipped\n";
echo "  ✗ Erros: $errors\n";
echo "  TOTAL: " . count($usersToImport) . "\n";
echo str_repeat("=", 60) . "\n";

if ($success > 0) {
    echo "\n✅ Importação concluída com sucesso!\n";
} elseif ($errors > 0) {
    echo "\n⚠️  Importação concluída com erros. Verifique os logs acima.\n";
} else {
    echo "\n ℹ️  Nenhum usuário novo foi importado.\n";
}

echo "\n=== FIM ===\n";
