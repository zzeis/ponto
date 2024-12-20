<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class CheckAccessLevel
{
    public function handle($request, Closure $next, ...$levels)
    {
        $user = auth()->user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Você precisa estar logado');
        }

         // Garantir que $levels seja um array
        $allowedLevels = collect($levels)->flatMap(function ($level) {
            return is_string($level) ? explode(',', $level) : $level;
        })->toArray();

        // Verifica se o nível de acesso do usuário está nos níveis permitidos
        if (!in_array($user->nivel_acesso, $allowedLevels)) {
            return redirect()->back()->with('error', 'Acesso não autorizado');
        }

        return $next($request);
    }
}
