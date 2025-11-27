<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckFirstRun
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se é a primeira execução (interpretando corretamente valores booleanos do .env)
        $isFirstRun = filter_var(env('APP_FIRST_RUN', true), FILTER_VALIDATE_BOOLEAN);

        // Se for primeira execução e não estiver na rota de setup, redireciona
        if ($isFirstRun && !$request->is('setup*')) {
            return redirect()->route('setup.index');
        }

        // Se não for primeira execução e estiver tentando acessar setup, redireciona para home
        if (!$isFirstRun && $request->is('setup*')) {
            return redirect()->route('login')->with('info', 'Sistema já configurado.');
        }

        return $next($request);
    }
}
