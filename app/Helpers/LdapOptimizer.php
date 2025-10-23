<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class LdapOptimizer
{
    /**
     * Aplicar otimizações para operações LDAP pesadas
     */
    public static function optimize()
    {
        // Aumentar limites de tempo e memória
        ini_set('max_execution_time', 600); // 10 minutos
        ini_set('memory_limit', '512M'); // 512MB de memória
        ini_set('post_max_size', '128M');
        ini_set('upload_max_filesize', '128M');
        
        // Configurações de socket e timeout
        ini_set('default_socket_timeout', 120); // 2 minutos
        ini_set('mysql.connect_timeout', 60);
        ini_set('session.gc_maxlifetime', 1440);
        
        // Otimizações de buffer
        ini_set('output_buffering', '4096');
        ini_set('implicit_flush', '0');
        
        // Configurações específicas do LDAP
        putenv('LDAPTLS_REQCERT=never'); // Ignorar certificados SSL para teste
        putenv('LDAPTLS_CACERT=/dev/null'); // Desabilitar verificação de certificado em dev
        
        // Otimizações de garbage collection
        if (function_exists('gc_enable')) {
            gc_enable();
            gc_collect_cycles();
        }
        
        // Log das configurações apenas se necessário para debug
        if (config('app.debug') && php_sapi_name() === 'cli') {
            Log::info("LDAP otimizado - Max execution time: " . ini_get('max_execution_time') . "s");
            Log::info("LDAP otimizado - Memory limit: " . ini_get('memory_limit'));
            Log::info("LDAP otimizado - Socket timeout: " . ini_get('default_socket_timeout') . "s");
        }
    }
    
    /**
     * Restaurar configurações padrão
     */
    public static function restore()
    {
        ini_set('max_execution_time', 30);
        ini_set('memory_limit', '128M');
        ini_set('default_socket_timeout', 60);
    }
}
