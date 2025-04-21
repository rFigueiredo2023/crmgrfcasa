<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class CapturaErrosParaIA
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            return $next($request);
        } catch (Throwable $e) {
            // Não capturar erros na rota do assistente para evitar loops
            if ($request->is('dev-assistente') || $request->is('api/*')) {
                throw $e;
            }

            // Obter dados da requisição sem o token CSRF
            $requestData = $request->except('_token');

            // Salvar informações do erro na sessão
            session()->flash('dev_error', [
                'mensagem' => $e->getMessage(),
                'arquivo' => $e->getFile(),
                'linha' => $e->getLine(),
                'rota' => $request->method() . ' ' . $request->path(),
                'dados' => json_encode($requestData, JSON_PRETTY_PRINT),
                'trace' => $e->getTraceAsString(),
            ]);

            // Retornar a exceção para que o Laravel continue com seu fluxo normal de tratamento
            throw $e;
        }
    }
}
