<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use ZipArchive;

class RestoreSystem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'backup:restore {file? : Arquivo de backup para restaurar} {--database-only : Restaurar apenas banco de dados} {--files-only : Restaurar apenas arquivos}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restaura backup do sistema';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->warn('========================================');
        $this->warn('RESTAURAÇÃO DE BACKUP DO SISTEMA');
        $this->warn('========================================');
        $this->warn('');
        $this->warn('ATENÇÃO: Esta operação irá SOBRESCREVER dados existentes!');
        
        if (!$this->confirm('Deseja continuar?')) {
            $this->info('Operação cancelada.');
            return 0;
        }

        $backupDir = storage_path('backups');
        $backupFile = $this->argument('file');

        // Se não foi fornecido arquivo, listar backups disponíveis
        if (!$backupFile) {
            $backupFile = $this->selectBackupFile($backupDir);
            if (!$backupFile) {
                return 1;
            }
        }

        // Verificar se arquivo existe
        if (!file_exists($backupFile) && file_exists($backupDir . '/' . $backupFile)) {
            $backupFile = $backupDir . '/' . $backupFile;
        }

        if (!file_exists($backupFile)) {
            $this->error('Arquivo de backup não encontrado: ' . $backupFile);
            return 1;
        }

        $this->info('');
        $this->info('Restaurando de: ' . basename($backupFile));
        $this->info('Tamanho: ' . $this->formatBytes(filesize($backupFile)));

        try {
            $databaseOnly = $this->option('database-only');
            $filesOnly = $this->option('files-only');

            // Determinar tipo de backup
            $extension = pathinfo($backupFile, PATHINFO_EXTENSION);
            
            if ($extension === 'sql' || strpos($backupFile, 'database_') !== false) {
                // Backup de banco de dados
                $this->restoreDatabase($backupFile);
            } elseif ($extension === 'gz' && strpos($backupFile, 'database_') !== false) {
                // Backup de banco de dados comprimido
                $this->restoreCompressedDatabase($backupFile);
            } elseif ($extension === 'zip') {
                // Backup ZIP (pode ser completo ou apenas arquivos)
                $this->restoreZipBackup($backupFile, $databaseOnly, $filesOnly);
            } else {
                $this->error('Tipo de arquivo de backup não reconhecido');
                return 1;
            }

            $this->info('');
            $this->info('========================================');
            $this->info('RESTAURAÇÃO CONCLUÍDA COM SUCESSO!');
            $this->info('========================================');

            return 0;

        } catch (\Exception $e) {
            $this->error('Erro durante a restauração: ' . $e->getMessage());
            $this->error($e->getTraceAsString());
            return 1;
        }
    }

    /**
     * Selecionar arquivo de backup
     */
    protected function selectBackupFile($backupDir)
    {
        if (!is_dir($backupDir)) {
            $this->error('Diretório de backups não encontrado: ' . $backupDir);
            return null;
        }

        $files = glob($backupDir . '/*');
        
        if (empty($files)) {
            $this->error('Nenhum backup encontrado em: ' . $backupDir);
            return null;
        }

        // Ordenar por data (mais recente primeiro)
        usort($files, function($a, $b) {
            return filemtime($b) - filemtime($a);
        });

        $this->info('Backups disponíveis:');
        $this->info('');
        
        $options = [];
        foreach ($files as $index => $file) {
            $number = $index + 1;
            $name = basename($file);
            $size = $this->formatBytes(filesize($file));
            $date = date('Y-m-d H:i:s', filemtime($file));
            
            $options[$number] = $file;
            $this->info("  [{$number}] {$name}");
            $this->line("      Tamanho: {$size} | Data: {$date}");
        }

        $this->info('');
        $choice = $this->ask('Selecione o número do backup para restaurar (0 para cancelar)');
        
        if ($choice == 0 || !isset($options[$choice])) {
            $this->info('Operação cancelada.');
            return null;
        }

        return $options[$choice];
    }

    /**
     * Restaurar banco de dados
     */
    protected function restoreDatabase($sqlFile)
    {
        $this->info('');
        $this->info('Restaurando banco de dados...');

        $connection = config('database.default');
        $database = config("database.connections.{$connection}.database");
        $username = config("database.connections.{$connection}.username");
        $password = config("database.connections.{$connection}.password");
        $host = config("database.connections.{$connection}.host");
        $port = config("database.connections.{$connection}.port", 3306);

        // Usar mysql command line
        $command = sprintf(
            'mysql --user=%s --password=%s --host=%s --port=%s %s < %s 2>&1',
            escapeshellarg($username),
            escapeshellarg($password),
            escapeshellarg($host),
            escapeshellarg($port),
            escapeshellarg($database),
            escapeshellarg($sqlFile)
        );

        exec($command, $output, $returnVar);

        if ($returnVar !== 0) {
            $this->warn('mysql command line falhou, tentando via Laravel...');
            $this->restoreDatabaseLaravel($sqlFile);
        } else {
            $this->info('✓ Banco de dados restaurado com sucesso');
        }
    }

    /**
     * Restaurar banco de dados comprimido
     */
    protected function restoreCompressedDatabase($gzFile)
    {
        $this->info('');
        $this->info('Descomprimindo backup do banco de dados...');
        
        $sqlFile = str_replace('.gz', '', $gzFile);
        exec("gunzip -c {$gzFile} > {$sqlFile}", $output, $returnVar);
        
        if ($returnVar !== 0) {
            throw new \Exception('Falha ao descomprimir arquivo de backup');
        }

        $this->restoreDatabase($sqlFile);
        
        // Limpar arquivo temporário
        if (file_exists($sqlFile)) {
            unlink($sqlFile);
        }
    }

    /**
     * Restaurar banco via Laravel
     */
    protected function restoreDatabaseLaravel($sqlFile)
    {
        DB::unprepared(file_get_contents($sqlFile));
        $this->info('✓ Banco de dados restaurado com sucesso');
    }

    /**
     * Restaurar backup ZIP
     */
    protected function restoreZipBackup($zipFile, $databaseOnly, $filesOnly)
    {
        $this->info('');
        $this->info('Extraindo arquivo ZIP...');

        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) {
            throw new \Exception('Não foi possível abrir arquivo ZIP');
        }

        $tempDir = storage_path('backups/temp_restore_' . time());
        mkdir($tempDir, 0755, true);

        $zip->extractTo($tempDir);
        $zip->close();

        // Verificar conteúdo
        $this->info('✓ Arquivo extraído para: ' . $tempDir);

        // Restaurar banco de dados
        if (!$filesOnly) {
            $dbFiles = glob($tempDir . '/database/*.sql*');
            if (!empty($dbFiles)) {
                foreach ($dbFiles as $dbFile) {
                    if (strpos($dbFile, '.gz') !== false) {
                        $this->restoreCompressedDatabase($dbFile);
                    } else {
                        $this->restoreDatabase($dbFile);
                    }
                }
            } else {
                $this->warn('Nenhum backup de banco de dados encontrado no ZIP');
            }
        }

        // Restaurar arquivos
        if (!$databaseOnly) {
            $filesZip = glob($tempDir . '/files/*.zip');
            if (!empty($filesZip)) {
                foreach ($filesZip as $fileZip) {
                    $this->restoreFiles($fileZip);
                }
            } else {
                $this->warn('Nenhum backup de arquivos encontrado no ZIP');
            }
        }

        // Limpar diretório temporário
        $this->deleteDirectory($tempDir);
    }

    /**
     * Restaurar arquivos
     */
    protected function restoreFiles($zipFile)
    {
        $this->info('');
        $this->info('Restaurando arquivos...');

        $zip = new ZipArchive();
        if ($zip->open($zipFile) !== true) {
            throw new \Exception('Não foi possível abrir arquivo ZIP de arquivos');
        }

        $basePath = base_path();
        $zip->extractTo($basePath);
        $zip->close();

        $this->info('✓ Arquivos restaurados com sucesso');
    }

    /**
     * Deletar diretório recursivamente
     */
    protected function deleteDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir), ['.', '..']);
        
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            is_dir($path) ? $this->deleteDirectory($path) : unlink($path);
        }

        rmdir($dir);
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
