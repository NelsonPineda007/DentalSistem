<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\StoreCalendarioRequest;

class CalendarioController extends Controller
{
    public function obtenerEventos()
    {
        $usuario = Auth::user();
        $rol = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');

        // 1. EVENTOS PERSONALES (Notas y Recordatorios): 
        // 100% privados. CUALQUIER usuario solo ve los suyos.
        $eventos = DB::table('calendario_eventos')
            ->where('estado', '!=', 'Inactivo')
            ->where('empleado_id', $usuario->id) // Filtro universal de privacidad
            ->select('id', 'titulo', 'fecha', 'hora', 'detalles', 'color', 'tipo')
            ->get();

        // 2. CITAS (Compartidas o filtradas por doctor)
        $queryCitas = DB::table('citas')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->where('citas.estado', '!=', 'Cancelada');
        
        // Si es dentista, solo ve sus citas. Admin y Recepcionista ven todas.
        if ($rol === 'Dentista') {
            $queryCitas->where('citas.empleado_id', $usuario->id);
        }
        
        $citas = $queryCitas->select(
                'citas.id',
                DB::raw("CONCAT('Cita: ', pacientes.nombre, ' ', pacientes.apellido) as titulo"),
                'citas.fecha_cita as fecha',
                'citas.hora_inicio as hora',
                'citas.motivo_consulta as detalles',
                DB::raw("'emerald' as color"),
                DB::raw("'Cita' as tipo")
            )->get();

        return response()->json($eventos->merge($citas));
    }

    public function guardarEvento(StoreCalendarioRequest $request)
    {
        $datos = $request->validated();
        $datos['estado'] = 'Activo';
        $datos['color'] = $datos['color'] ?? 'blue';
        $datos['empleado_id'] = Auth::id(); // Guardamos a nombre del dueño
        
        $id = DB::table('calendario_eventos')->insertGetId($datos);
        return response()->json(['success' => true, 'id' => $id]);
    }

    public function actualizarEvento(StoreCalendarioRequest $request, $id)
    {
        $datos = $request->validated();
        $datos['color'] = $datos['color'] ?? 'blue';

        // Nadie puede editar notas ajenas, ni siquiera el Admin
        DB::table('calendario_eventos')
            ->where('id', $id)
            ->where('empleado_id', Auth::id())
            ->update($datos);
            
        return response()->json(['success' => true]);
    }

    public function eliminarEvento($id)
    {
        // Nadie puede eliminar notas ajenas, ni siquiera el Admin
        DB::table('calendario_eventos')
            ->where('id', $id)
            ->where('empleado_id', Auth::id())
            ->update(['estado' => 'Inactivo']);
            
        return response()->json(['success' => true]);
    }
}