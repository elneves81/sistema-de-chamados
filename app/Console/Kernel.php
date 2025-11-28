<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Backup automático do sistema
        // Executa todos os dias às 3h da manhã (backup completo)
        $schedule->command('backup:create --full')
                 ->daily()
                 ->at('03:00')
                 ->withoutOverlapping()
                 ->onOneServer()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('backups/backup.log'));
        
        // Backup do banco de dados a cada 6 horas
        $schedule->command('backup:create --database-only')
                 ->everySixHours()
                 ->withoutOverlapping()
                 ->onOneServer()
                 ->runInBackground()
                 ->appendOutputTo(storage_path('backups/backup.log'));
        
        // Importação automática de usuários do LDAP
        // Executa todos os dias às 2h da manhã
        $schedule->command('ldap:import-users --limit=1000')
                 ->daily()
                 ->at('02:00')
                 ->withoutOverlapping()
                 ->onOneServer()
                 ->runInBackground();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
