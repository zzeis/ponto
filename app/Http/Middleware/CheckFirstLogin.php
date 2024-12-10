<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckFirstLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário está logado e ainda no primeiro login
        if (Auth::check() && Auth::user()->first_login) {
            // Permite acessar apenas as rotas de alteração de senha
            if (!$request->routeIs('first.password.change', 'first.password.update')) {
                return redirect()->route('first.password.change');
            }
        } elseif (Auth::check() && !Auth::user()->first_login) {
            // Usuário que já alterou a senha não deve acessar as rotas de alteração inicial
            if ($request->routeIs('first.password.change', 'first.password.update')) {
                return redirect()->route('dashboard');
            }
        }
        return $next($request);
    }
}
