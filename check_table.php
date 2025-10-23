<?php
require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Estrutura da tabela users:\n";
$columns = DB::select('DESCRIBE users');
foreach ($columns as $column) {
    echo "- {$column->Field}: {$column->Type} (Null: {$column->Null}, Default: {$column->Default})\n";
}
