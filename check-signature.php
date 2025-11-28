<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$machineId = $argv[1] ?? 18;

$machine = \App\Models\Machine::find($machineId);

if (!$machine) {
    echo "Máquina #{$machineId} não encontrada\n";
    exit(1);
}

echo "=== INFORMAÇÕES DA ASSINATURA - Máquina #{$machineId} ===\n\n";
echo "Patrimônio: {$machine->patrimonio}\n";
echo "Recebedor: " . ($machine->recebedor->name ?? 'N/A') . "\n\n";

echo "Campo assinatura_digital:\n";
if (empty($machine->assinatura_digital)) {
    echo "  ❌ VAZIO ou NULL\n";
} else {
    $length = strlen($machine->assinatura_digital);
    echo "  ✓ Tamanho: {$length} caracteres\n";
    echo "  ✓ Primeiros 100 caracteres: " . substr($machine->assinatura_digital, 0, 100) . "...\n";
    
    if (strpos($machine->assinatura_digital, 'data:image') === 0) {
        echo "  ✓ Contém prefixo data:image\n";
    } else {
        echo "  ⚠️  NÃO contém prefixo data:image\n";
    }
}

echo "\nCampo assinatura_status: " . ($machine->assinatura_status ?? 'NULL') . "\n";
echo "Campo assinatura_validada_em: " . ($machine->assinatura_validada_em ?? 'NULL') . "\n";
echo "Campo nome_legivel_assinatura: " . ($machine->nome_legivel_assinatura ?? 'NULL') . "\n";

echo "\n=== FIM ===\n";
