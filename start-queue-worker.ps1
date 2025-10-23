# Script para iniciar worker de filas Laravel
Set-Location "c:\Users\Elber\Documents\GitHub\sistema-de-chamados\sistema-de-chamados"

Write-Host "===========================================" -ForegroundColor Green
Write-Host "  Worker de Filas - Sistema de Chamados   " -ForegroundColor Green  
Write-Host "===========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Iniciando worker para processar jobs de importacao LDAP..." -ForegroundColor Yellow
Write-Host "Para parar o worker, pressione Ctrl+C" -ForegroundColor Red
Write-Host ""

try {
    # Processar fila dedicada 'ldap' com prioridade e depois 'default'
    & php artisan queue:work --queue=ldap,default --timeout=3600 --sleep=3 --tries=3 --verbose
}
catch {
    Write-Host "Erro ao iniciar worker: $_" -ForegroundColor Red
    Read-Host "Pressione Enter para sair"
}
