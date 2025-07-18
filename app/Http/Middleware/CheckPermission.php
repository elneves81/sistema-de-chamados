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

        // Se for cliente, só pode acessar /tickets/create
        if ($user->role === 'customer') {
            if (!$request->routeIs('tickets.create')) {
                Auth::logout();
                return redirect()->route('login')->with('error', 'Acesso restrito: apenas abertura de chamados permitida.');
            }
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
