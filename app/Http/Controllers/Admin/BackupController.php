<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class BackupController extends Controller
{
    /**
     * Display backup management page
     */
    public function index()
    {
        $backupDir = storage_path('backups');
        $backups = [];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $filename = basename($file);
                    
                    // Determinar tipo
                    if (strpos($filename, 'sistema-completo') !== false) {
                        $type = 'complete';
                        $typeLabel = 'Completo';
                        $icon = 'bi-archive-fill';
                        $color = 'primary';
                    } elseif (strpos($filename, 'database') !== false) {
                        $type = 'database';
                        $typeLabel = 'Banco de Dados';
                        $icon = 'bi-database-fill';
                        $color = 'success';
                    } elseif (strpos($filename, 'files') !== false) {
                        $type = 'files';
                        $typeLabel = 'Arquivos';
                        $icon = 'bi-folder-fill';
                        $color = 'warning';
                    } else {
                        $type = 'other';
                        $typeLabel = 'Outro';
                        $icon = 'bi-file-earmark';
                        $color = 'secondary';
                    }
                    
                    $backups[] = [
                        'filename' => $filename,
                        'path' => $file,
                        'size' => filesize($file),
                        'size_formatted' => $this->formatBytes(filesize($file)),
                        'date' => filemtime($file),
                        'date_formatted' => date('d/m/Y H:i:s', filemtime($file)),
                        'type' => $type,
                        'type_label' => $typeLabel,
                        'icon' => $icon,
                        'color' => $color,
                    ];
                }
            }
            
            // Ordenar por data (mais recente primeiro)
            usort($backups, function($a, $b) {
                return $b['date'] - $a['date'];
            });
        }

        // Estatísticas
        $stats = [
            'total_backups' => count($backups),
            'total_size' => array_sum(array_column($backups, 'size')),
            'last_backup' => !empty($backups) ? $backups[0]['date_formatted'] : 'Nenhum',
            'disk_usage' => disk_free_space($backupDir),
        ];

        return view('admin.backup.index', compact('backups', 'stats'));
    }

    /**
     * Create new backup
     */
    public function create(Request $request)
    {
        $request->validate([
            'type' => 'required|in:full,database,files'
        ]);

        try {
            $type = $request->type;
            
            if ($type === 'full') {
                Artisan::call('backup:create', ['--full' => true]);
                $message = 'Backup completo criado com sucesso!';
            } elseif ($type === 'database') {
                Artisan::call('backup:create', ['--database-only' => true]);
                $message = 'Backup do banco de dados criado com sucesso!';
            } else {
                return back()->with('error', 'Tipo de backup não suportado ainda.');
            }

            return back()->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao criar backup: ' . $e->getMessage());
        }
    }

    /**
     * Download backup file
     */
    public function download($filename)
    {
        $file = storage_path('backups/' . $filename);

        if (!file_exists($file)) {
            abort(404, 'Arquivo de backup não encontrado.');
        }

        return response()->download($file);
    }

    /**
     * Delete backup file
     */
    public function destroy($filename)
    {
        $file = storage_path('backups/' . $filename);

        if (!file_exists($file)) {
            return back()->with('error', 'Arquivo de backup não encontrado.');
        }

        if (unlink($file)) {
            return back()->with('success', 'Backup excluído com sucesso.');
        }

        return back()->with('error', 'Erro ao excluir backup.');
    }

    /**
     * Show restore page
     */
    public function restoreForm()
    {
        $backupDir = storage_path('backups');
        $backups = [];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*');
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $backups[] = [
                        'filename' => basename($file),
                        'path' => $file,
                        'size_formatted' => $this->formatBytes(filesize($file)),
                        'date_formatted' => date('d/m/Y H:i:s', filemtime($file)),
                    ];
                }
            }
            
            usort($backups, function($a, $b) {
                return filemtime($b['path']) - filemtime($a['path']);
            });
        }

        return view('admin.backup.restore', compact('backups'));
    }

    /**
     * Restore backup
     */
    public function restore(Request $request)
    {
        $request->validate([
            'filename' => 'required|string'
        ]);

        $file = storage_path('backups/' . $request->filename);

        if (!file_exists($file)) {
            return back()->with('error', 'Arquivo de backup não encontrado.');
        }

        try {
            // Por segurança, vamos apenas retornar instruções
            return back()->with('warning', 'Para restaurar o backup, execute no terminal: php artisan backup:restore ' . $request->filename);

        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao restaurar backup: ' . $e->getMessage());
        }
    }

    /**
     * Get backup statistics
     */
    public function stats()
    {
        $backupDir = storage_path('backups');
        
        $stats = [
            'total_backups' => 0,
            'total_size' => 0,
            'by_type' => [
                'complete' => 0,
                'database' => 0,
                'files' => 0,
            ],
            'last_7_days' => 0,
        ];

        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/*');
            $now = time();
            
            foreach ($files as $file) {
                if (is_file($file)) {
                    $stats['total_backups']++;
                    $stats['total_size'] += filesize($file);
                    
                    $filename = basename($file);
                    if (strpos($filename, 'sistema-completo') !== false) {
                        $stats['by_type']['complete']++;
                    } elseif (strpos($filename, 'database') !== false) {
                        $stats['by_type']['database']++;
                    } elseif (strpos($filename, 'files') !== false) {
                        $stats['by_type']['files']++;
                    }
                    
                    $fileAge = $now - filemtime($file);
                    if ($fileAge < (7 * 86400)) {
                        $stats['last_7_days']++;
                    }
                }
            }
        }

        $stats['total_size_formatted'] = $this->formatBytes($stats['total_size']);

        return response()->json($stats);
    }

    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
