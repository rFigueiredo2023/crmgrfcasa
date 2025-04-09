<?php

namespace App\Http\Middleware;

use App\Providers\RouteServiceProvider;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                $user = Auth::guard($guard)->user();
                
                switch ($user->role) {
                    case 'admin':
                        return redirect()->route('dashboard.admin');
                    case 'vendas':
                        return redirect()->route('dashboard.vendas');
                    case 'financeiro':
                        return redirect()->route('dashboard.financeiro');
                    default:
                        return redirect('/');
                }
            }
        }

        return $next($request);
    }
} 