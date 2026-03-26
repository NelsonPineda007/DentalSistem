<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Maneja la petición entrante.
     * El parámetro ...$roles permite recibir una lista dinámica de roles permitidos.
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Doble verificación de que el usuario esté logueado
        if (!Auth::check()) {
            return redirect('/');
        }

        // 2. Extraemos el nombre exacto del rol desde la base de datos
        $nombreRol = DB::table('roles')->where('id', Auth::user()->rol_id)->value('nombre');

        // 3. Si su rol está en la lista de los permitidos, le abrimos la puerta
        if (in_array($nombreRol, $roles)) {
            return $next($request);
        }

        // 4. SI NO TIENE PERMISO (Ej: Recepcionista queriendo entrar al Dashboard)
        // Si es una petición de API (fetch), devolvemos un error 403 limpio
        if ($request->expectsJson() || $request->is('api/*')) {
            return response()->json(['error' => 'Acceso denegado: Privilegios insuficientes.'], 403);
        }
        
        // Si es una vista, lo redirigimos a su área principal (Pacientes)
        return redirect('/pacientes');
    }
}