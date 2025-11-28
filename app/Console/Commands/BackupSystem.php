<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class BackupSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:create {--full : Backup completo incluindo arquivos} {--database-only : Apenas banco de dados}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria backup completo do sistema (banco de dados e arquivos)';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('========================================');
        $this->info('INICIANDO BACKUP DO SISTEMA');
        $this->info('========================================');
        
        $timestamp = date('Y-m-d_H-i-s');
        $backupDir = storage_path('backups');
        
        // Criar diretório de backups se não existir
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
            $this->info('Diretório de backups criado: ' . $backupDir);
        }

        $databaseOnly = $this->option('database-only');
        $fullBackup = $this->option('full') || !$databaseOnly;

        try {
            // 1. Backup do Banco de Dados
            $this->info('');
            $this->info('[1/3] Fazendo backup do banco de dados...');
            $dbBackupFile = $this->backupDatabase($backupDir, $timestamp);
            
            if ($dbBackupFile) {
                $this->info('✓ Backup do banco de dados concluído: ' . basename($dbBackupFile));
                $this->info('  Tamanho: ' . $this->formatBytes(filesize($dbBackupFile)));
            } else {
                $this->error('✗ Falha no backup do banco de dados');
                return 1;
            }

            // Se for apenas database, parar aqui
            if ($databaseOnly) {
                $this->displaySummary([$dbBackupFile]);
                return 0;
            }

            // 2. Backup de Arquivos Importantes
            $this->info('');
            $this->info('[2/3] Fazendo backup de arquivos importantes...');
            $filesBackupFile = $this->backupFiles($backupDir, $timestamp);
            
            if ($filesBackupFile) {
                $this->info('✓ Backup de arquivos concluído: ' . basename($filesBackupFile));
                $this->info('  Tamanho: ' . $this->formatBytes(filesize($filesBackupFile)));
            }

            // 3. Criar backup completo (ZIP com tudo)
            if ($fullBackup) {
                $this->info('');
                $this->info('[3/3] Criando arquivo de backup completo...');
                $completeBackupFile = $this->createCompleteBackup($backupDir, $timestamp, $dbBackupFile, $filesBackupFile);
                
                if ($completeBackupFile) {
                    $this->info('✓ Backup completo criado: ' . basename($completeBackupFile));
                    $this->info('  Tamanho: ' . $this->formatBytes(filesize($completeBackupFile)));
                }
            }

            // Limpar backups antigos (manter últimos 7 dias)
            $this->cleanOldBackups($backupDir, 7);

            // Exibir resumo
            $backupFiles = array_filter([
                $dbBackupFile,
                $filesBackupFile ?? null,
                $completeBackupFile ?? null
            ]);
            
            $this->displaySummary($backupFiles);

            return 0;

        } catch (\Exception $e) {
            $this->error('Erro durante o backup: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Backup do banco de dados
     */
    protected function backupDatabase($backupDir, $timestamp)
    {
        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");
        $host = config("database.connections.{$connection}.host");
        $port = config("database.connections.{$connection}.port", 3306);

        $backupFile = "{$backupDir}/database_{$timestamp}.sql";

        // Usar mysqldump
        $command = sprintf(
            'mysqldump --user=%s --password=%s --host=%s --port=%s %s > %s 2>&1',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($backupFile)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->warn('mysqldump falhou, tentando backup via Laravel...');
            return $this->backupDatabaseLaravel($backupDir, $timestamp);
        }

        // Comprimir o arquivo SQL
        $gzipFile = $backupFile . '.gz';
        exec("gzip -c {$backupFile} > {$gzipFile}", $output, $returnVar);
        
        if ($returnVar === 0 && file_exists($gzipFile)) {
            unlink($backupFile); // Remover SQL não comprimido
            return $gzipFile;
        }

        return $backupFile;
    }

    /**
     * Backup do banco usando Laravel (fallback)
     */
    protected function backupDatabaseLaravel($backupDir, $timestamp)
    {
        $backupFile = "{$backupDir}/database_{$timestamp}.sql";
        
        $tables = DB::select('SHOW TABLES');
        $database = config('database.connections.mysql.database');
        
        $content = "-- Backup gerado em: " . date('Y-m-d H:i:s') . "\n";
        $content .= "-- Banco de dados: {$database}\n\n";
        $content .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            
            // Estrutura da tabela
            $createTable = DB::select("SHOW CREATE TABLE `{$tableName}`");
            $content .= "DROP TABLE IF EXISTS `{$tableName}`;\n";
            $content .= $createTable[0]->{'Create Table'} . ";\n\n";
            
            // Dados da tabela
            $rows = DB::table($tableName)->get();
            if ($rows->count() > 0) {
                $content .= "INSERT INTO `{$tableName}` VALUES\n";
                $values = [];
                foreach ($rows as $row) {
                    $rowData = array_map(function($value) {
                        return is_null($value) ? 'NULL' : DB::getPdo()->quote($value);
                    }, (array)$row);
                    $values[] = '(' . implode(',', $rowData) . ')';
                }
                $content .= implode(",\n", $values) . ";\n\n";
            }
        }

        $content .= "SET FOREIGN_KEY_CHECKS=1;\n";
        
        file_put_contents($backupFile, $content);
        
        return $backupFile;
    }

    /**
     * Backup de arquivos importantes
     */
    protected function backupFiles($backupDir, $timestamp)
    {
        $backupFile = "{$backupDir}/files_{$timestamp}.zip";
        
        $zip = new ZipArchive();
        if ($zip->open($backupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            $this->error('Não foi possível criar arquivo ZIP de arquivos');
            return null;
        }

        $basePath = base_path();
        
        // Diretórios e arquivos para backup
        $pathsToBackup = [
            'storage/app/public' => 'Arquivos públicos',
            'public/uploads' => 'Uploads',
            '.env' => 'Configurações',
            'composer.json' => 'Dependências PHP',
            'package.json' => 'Dependências JS',
        ];

        foreach ($pathsToBackup as $path => $description) {
            $fullPath = $basePath . '/' . $path;
            
            if (is_file($fullPath)) {
                $zip->addFile($fullPath, $path);
                $this->line("  + {$description}: {$path}");
            } elseif (is_dir($fullPath)) {
                $this->addDirectoryToZip($zip, $fullPath, $path);
                $this->line("  + {$description}: {$path}/");
            }
        }

        $zip->close();
        
        return $backupFile;
    }

    /**
     * Adicionar diretório ao ZIP recursivamente
     */
    protected function addDirectoryToZip($zip, $sourcePath, $zipPath)
    {
        if (!is_dir($sourcePath)) {
            return;
        }

        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourcePath),
            \RecursiveIteratorIterator::LEAVES_ONLY
        );

        foreach ($files as $file) {
            if (!$file->isDir()) {
                $filePath = $file->getRealPath();
                $relativePath = $zipPath . '/' . substr($filePath, strlen($sourcePath) + 1);
                $zip->addFile($filePath, $relativePath);
            }
        }
    }

    /**
     * Criar backup completo
     */
    protected function createCompleteBackup($backupDir, $timestamp, $dbBackupFile, $filesBackupFile = null)
    {
        $completeBackupFile = "{$backupDir}/sistema-completo_{$timestamp}.zip";
        
        $zip = new ZipArchive();
        if ($zip->open($completeBackupFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return null;
        }

        // Adicionar backup do banco
        if ($dbBackupFile && file_exists($dbBackupFile)) {
            $zip->addFile($dbBackupFile, 'database/' . basename($dbBackupFile));
        }

        // Adicionar backup de arquivos
        if ($filesBackupFile && file_exists($filesBackupFile)) {
            $zip->addFile($filesBackupFile, 'files/' . basename($filesBackupFile));
        }

        // Adicionar informações sobre o backup
        $info = [
            'backup_date' => date('Y-m-d H:i:s'),
            'laravel_version' => app()->version(),
            'php_version' => phpversion(),
            'database' => config('database.connections.mysql.database'),
            'app_name' => config('app.name'),
            'app_url' => config('app.url'),
        ];
        
        $zip->addFromString('backup-info.json', json_encode($info, JSON_PRETTY_PRINT));
        
        $zip->close();
        
        return $completeBackupFile;
    }

    /**
     * Limpar backups antigos
     */
    protected function cleanOldBackups($backupDir, $daysToKeep = 7)
    {
        $this->info('');
        $this->info('Limpando backups antigos (mantendo últimos ' . $daysToKeep . ' dias)...');
        
        $files = glob($backupDir . '/*');
        $now = time();
        $deleted = 0;

        foreach ($files as $file) {
            if (is_file($file)) {
                $fileAge = $now - filemtime($file);
                $daysOld = floor($fileAge / 86400);
                
                if ($daysOld > $daysToKeep) {
                    unlink($file);
                    $this->line('  - Removido: ' . basename($file) . " ({$daysOld} dias)");
                    $deleted++;
                }
            }
        }

        if ($deleted > 0) {
            $this->info("✓ {$deleted} backup(s) antigo(s) removido(s)");
        } else {
            $this->info('✓ Nenhum backup antigo para remover');
        }
    }

    /**
     * Exibir resumo
     */
    protected function displaySummary($backupFiles)
    {
        $this->info('');
        $this->info('========================================');
        $this->info('BACKUP CONCLUÍDO COM SUCESSO!');
        $this->info('========================================');
        
        $totalSize = 0;
        foreach ($backupFiles as $file) {
            if (file_exists($file)) {
                $size = filesize($file);
                $totalSize += $size;
                $this->info('Arquivo: ' . basename($file));
                $this->info('Tamanho: ' . $this->formatBytes($size));
                $this->info('Localização: ' . $file);
                $this->info('---');
            }
        }
        
        $this->info('Tamanho total: ' . $this->formatBytes($totalSize));
        $this->info('========================================');
    }

    /**
     * Formatar bytes
     */
    protected function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
