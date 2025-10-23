<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ForceJsonResponse
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        // Force Accept header to JSON for AJAX routes
        $request->headers->set('Accept', 'application/json');
        
        try {
            // Check authentication
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Usuário não autenticado. Faça login para continuar.',
                    'redirect' => route('login')
                ], 401);
            }
            
            $response = $next($request);
            
            // If the response is a redirect (like unauthenticated), convert to JSON
            if ($response->isRedirection()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Redirecionamento detectado. Faça login novamente.',
                    'redirect' => $response->getTargetUrl()
                ], 401);
            }
            
            // Ensure response is JSON
            $response->headers->set('Content-Type', 'application/json');
            
            return $response;
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Erro no servidor: ' . $e->getMessage(),
                'redirect' => route('login')
            ], 500);
        }
    }
}
