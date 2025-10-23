@echo off
cd /d "c:\Users\Elber\Documents\GitHub\sistema-de-chamados\sistema-de-chamados"
echo Iniciando worker de filas Laravel...
echo Worker processara jobs de importacao LDAP em background
echo Para parar o worker, pressione Ctrl+C
echo.
php artisan queue:work --timeout=3600 --sleep=3 --tries=3 --verbose
pause
