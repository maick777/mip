<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class VerifySession
{
    public function handle(Request $request, Closure $next)
    {
        if (auth()->check()) {
            // El usuario tiene una sesión iniciada, puedes continuar
            return $next($request);
        }

        // El usuario no tiene una sesión iniciada, puedes devolver una respuesta de error
        return response()->json(['error' => 'No autorizado'], 401);
    }
}
