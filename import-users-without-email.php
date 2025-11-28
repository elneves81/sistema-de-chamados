<?php

require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Str;
use App\Models\User;

// Bootstrap Laravel
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== IMPORTAÇÃO DE USUÁRIOS SEM EMAIL (COM EMAIL GERADO) ===\n\n";

// Carregar lista de usuários sem email
$jsonFile = __DIR__ . '/users-without-email.json';

if (!file_exists($jsonFile)) {
    echo "❌ Arquivo users-without-email.json não encontrado!\n";
    echo "Execute primeiro: php check-users-without-email.php\n";
    exit(1);
}

$usersToImport = json_decode(file_get_contents($jsonFile), true);

if (empty($usersToImport)) {
    echo "✅ Não há usuários para importar!\n";
    exit(0);
}

echo "Total de usuários sem email a importar: " . count($usersToImport) . "\n";
echo "Emails serão gerados automaticamente no formato: username@guarapuava.pr.gov.br\n\n";

echo "Exemplos de emails que serão gerados:\n";
for ($i = 0; $i < min(5, count($usersToImport)); $i++) {
    $user = $usersToImport[$i];
    $generatedEmail = strtolower($user['username']) . '@guarapuava.pr.gov.br';
    echo "  - {$user['name']} → $generatedEmail\n";
}

echo "\nDeseja continuar? (s/n): ";
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
$errorDetails = [];

foreach ($usersToImport as $i => $userData) {
    $num = $i + 1;
    
    // Gerar email automático
    $generatedEmail = strtolower($userData['username']) . '@guarapuava.pr.gov.br';
    
    echo "[$num/" . count($usersToImport) . "] {$userData['name']} ({$userData['username']})... ";
    
    try {
        // Verificar se já existe (dupla checagem)
        $existingByUsername = User::where('username', $userData['username'])->first();
        $existingByEmail = User::where('email', $generatedEmail)->first();
        
        if ($existingByUsername) {
            echo "⊘ USERNAME JÁ EXISTE\n";
            $skipped++;
            continue;
        }
        
        if ($existingByEmail) {
            // Email gerado já existe, adicionar sufixo numérico
            $suffix = 1;
            $originalEmail = $generatedEmail;
            while ($existingByEmail) {
                $generatedEmail = strtolower($userData['username']) . $suffix . '@guarapuava.pr.gov.br';
                $existingByEmail = User::where('email', $generatedEmail)->first();
                $suffix++;
            }
            echo "⚠️  Email ajustado: $originalEmail → $generatedEmail... ";
        }
        
        // Criar usuário
        $user = User::create([
            'name' => $userData['name'],
            'email' => $generatedEmail,
            'username' => $userData['username'],
            'password' => bcrypt(Str::random(40)),
            'role' => 'customer',
            'is_active' => true,
            'ldap_dn' => $userData['dn'],
            'auth_via_ldap' => true,
        ]);
        
        echo "✓ SUCESSO (ID: {$user->id})\n";
        $success++;
        
    } catch (\Exception $e) {
        echo "✗ ERRO: " . $e->getMessage() . "\n";
        $errors++;
        $errorDetails[] = [
            'user' => $userData['name'],
            'username' => $userData['username'],
            'error' => $e->getMessage()
        ];
    }
    
    // Pequena pausa para não sobrecarregar
    usleep(50000); // 0.05 segundo
}

echo "\n" . str_repeat("=", 80) . "\n";
echo "RESUMO DA IMPORTAÇÃO:\n";
echo "  ✓ Importados com sucesso: $success\n";
echo "  ⊘ Já existiam (pulados): $skipped\n";
echo "  ✗ Erros: $errors\n";
echo "  TOTAL: " . count($usersToImport) . "\n";
echo str_repeat("=", 80) . "\n";

if (!empty($errorDetails)) {
    echo "\nDETALHES DOS ERROS:\n";
    foreach ($errorDetails as $error) {
        echo "  • {$error['user']} ({$error['username']}): {$error['error']}\n";
    }
}

if ($success > 0) {
    echo "\n✅ Importação concluída com sucesso!\n";
    echo "ℹ️  Os usuários podem fazer login com:\n";
    echo "   - Username LDAP (autenticação via Active Directory)\n";
    echo "   - Email gerado: username@guarapuava.pr.gov.br\n";
} elseif ($errors > 0) {
    echo "\n⚠️  Importação concluída com erros. Verifique os logs acima.\n";
} else {
    echo "\nℹ️  Nenhum usuário novo foi importado.\n";
}

echo "\n=== FIM ===\n";
