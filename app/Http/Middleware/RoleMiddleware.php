<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        if ($user->role !== $role) {
            Log::warning('Tentativa de acesso não autorizado', [
                'user' => $user->email,
                'required_role' => $role,
                'actual_role' => $user->role,
                'path' => $request->path()
            ]);
            abort(403, 'Você não tem permissão para acessar esta página.');
        }

        return $next($request);
    }
} 