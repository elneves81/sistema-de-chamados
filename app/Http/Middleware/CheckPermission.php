<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Se for cliente, permite acesso ao dashboard, criação e visualização de seus chamados
        if ($user->role === 'customer') {
            $allowedRoutes = [
                'home',
                'dashboard',
                'tickets.index',      // Ver lista de chamados
                'tickets.create',     // Criar chamado
                'tickets.store',      // Salvar chamado
                'tickets.show',       // Ver detalhes do chamado
                'tickets.edit',       // Editar próprio chamado (se permitido)
                'tickets.update',     // Atualizar próprio chamado
                'profile.edit',       // Editar próprio perfil
                'profile.update',     // Atualizar próprio perfil
            ];
            
            // Verifica se a rota atual está nas permitidas
            $currentRoute = $request->route()->getName();
            if (!in_array($currentRoute, $allowedRoutes) && !str_starts_with($currentRoute, 'tickets.')) {
                return redirect()->route('dashboard')->with('warning', 'Você não tem permissão para acessar esta página.');
            }
            
            // Clientes sempre podem acessar suas próprias rotas
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            // Se for requisição AJAX, retorna 403
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Acesso negado. Você não tem permissão para acessar esta funcionalidade.'], 403);
            }
            // Evita loop: se já está tentando acessar dashboard, faz logout
            if ($request->routeIs('dashboard') || $request->routeIs('home')) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Acesso negado. Entre com outro usuário.');
            }
            // Redireciona para dashboard normalmente
            return redirect()->route('dashboard')->with('error', 'Acesso negado. Você não tem permissão para acessar esta funcionalidade.');
        }

        return $next($request);
    }
}
