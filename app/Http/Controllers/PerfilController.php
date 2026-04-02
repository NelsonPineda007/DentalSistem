<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Cita;
use Carbon\Carbon;
use Illuminate\Support\Facades\Password;

class PerfilController extends Controller
{
    // 1. Obtener los datos del usuario logueado
    public function obtenerDatos()
    {
        try {
            $user = Auth::user();
            $inicioMes = Carbon::now()->startOfMonth();
            $finMes = Carbon::now()->endOfMonth();

            // Variables por defecto en caso de que las tablas fallen
            $citasMes = 0;
            $actividad = [];
            $rol = 'Usuario';

            // INTENTO 1: Buscar citas del mes (A prueba de errores)
            try {
                $citasMes = Cita::where('empleado_id', $user->id)
                    ->whereBetween('fecha_cita', [$inicioMes, $finMes])
                    ->whereIn('estado', ['Completada', 'En progreso', 'Confirmada'])
                    ->count();
            } catch (\Exception $e) {
                // Si la tabla citas falla, se queda en 0
            }

            // INTENTO 2: Buscar logs del sistema (A prueba de errores)
            try {
                $actividad = DB::table('logs_sistema')
                    ->where('usuario_id', $user->id)
                    ->orderBy('creado_en', 'desc')
                    ->limit(5)
                    ->get()
                    ->map(function($log) {
                        return [
                            'accion' => $log->accion ?? 'Acción registrada',
                            'tabla' => $log->tabla_afectada ?? 'Sistema',
                            'tiempo' => $log->creado_en ? Carbon::parse($log->creado_en)->diffForHumans() : 'Reciente'
                        ];
                    });
            } catch (\Exception $e) {
                // Si la tabla logs_sistema no existe, manda un arreglo vacío y no rompe la página
            }

            // INTENTO 3: Buscar Rol
            try {
                $rol = DB::table('roles')->where('id', $user->rol_id)->value('nombre') ?? 'Usuario';
            } catch (\Exception $e) {}

            // Enviamos la respuesta limpia y segura
            return response()->json([
                'status' => 'success',
                'usuario' => [
                    'nombre' => $user->nombre,
                    'apellido' => $user->apellido,
                    'email' => $user->email,
                    'telefono' => $user->telefono ?? '',
                    'especialidad' => $user->especialidad ?? '',
                    'rol' => $rol
                ],
                'estadisticas' => [
                    'citas_mes' => $citasMes
                ],
                'actividad' => $actividad
            ]);

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error al cargar perfil: ' . $e->getMessage()], 500);
        }
    }

    // 2. Actualizar la información personal
    public function actualizarPerfil(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            
            $user->nombre = $request->nombre;
            $user->apellido = $request->apellido;
            $user->email = $request->email;
            $user->telefono = $request->telefono;
            $user->especialidad = $request->especialidad;
            $user->save();

            return response()->json(['status' => 'success', 'message' => 'Perfil actualizado exitosamente.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }

    // 3. Función para enviar el correo de recuperación desde el perfil
    public function solicitarCambioPassword()
    {
        try {
            // Obtenemos el correo del usuario logueado
            $email = Auth::user()->email;

            // Le pedimos a Laravel que le mande el correo mágico
            $status = Password::sendResetLink(['email' => $email]);

            if ($status === Password::RESET_LINK_SENT) {
                return response()->json([
                    'status' => 'success', 
                    'message' => '¡Correo enviado! Revisa tu bandeja de entrada para cambiar tu contraseña.'
                ]);
            } else {
                return response()->json([
                    'status' => 'error', 
                    'message' => 'No pudimos enviar el correo en este momento. Intenta de nuevo más tarde.'
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}