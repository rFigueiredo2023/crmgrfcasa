<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            // Redireciona para login se não estiver autenticado
            return redirect()->route('login')->with('error', 'Você precisa estar logado para acessar esta área.');
        }

        if (auth()->user()->role !== 'admin') {
            // Retorna erro 403 (Forbidden) se não for administrador
            abort(403, 'Você não tem permissão para acessar esta área.');
        }

        return $next($request);
    }
}
