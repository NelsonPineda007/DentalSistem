<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CalendarioController extends Controller
{
    // 1. Obtener Notas, Recordatorios y CITAS combinados
    public function obtenerEventos()
    {
        // Traemos eventos propios (que no estén en Soft Delete)
        $eventos = DB::table('calendario_eventos')
            ->where('estado', '!=', 'Inactivo')
            ->select('id', 'titulo', 'fecha', 'hora', 'detalles', 'color', 'tipo')
            ->get();

        // Traemos Citas y las "disfrazamos" para que encajen en el calendario
        $citas = DB::table('citas')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->where('citas.estado', '!=', 'Cancelada')
            ->select(
                'citas.id',
                DB::raw("CONCAT('Cita: ', pacientes.nombre, ' ', pacientes.apellido) as titulo"),
                'citas.fecha_cita as fecha',
                'citas.hora_inicio as hora',
                'citas.motivo_consulta as detalles',
                DB::raw("'emerald' as color"), // La cita sale color verde esmeralda
                DB::raw("'Cita' as tipo")
            )
            ->get();

        // Unimos los dos listados
        return response()->json($eventos->merge($citas));
    }

    public function guardarEvento(Request $request)
    {
        $id = DB::table('calendario_eventos')->insertGetId([
            'titulo' => $request->titulo,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'detalles' => $request->detalles,
            'color' => $request->color ?? 'blue',
            'tipo' => $request->tipo,
            'estado' => 'Activo'
        ]);
        return response()->json(['success' => true, 'id' => $id]);
    }

    public function actualizarEvento(Request $request, $id)
    {
        DB::table('calendario_eventos')->where('id', $id)->update([
            'titulo' => $request->titulo,
            'fecha' => $request->fecha,
            'hora' => $request->hora,
            'detalles' => $request->detalles,
            'color' => $request->color ?? 'blue',
            'tipo' => $request->tipo
        ]);
        return response()->json(['success' => true]);
    }

    public function eliminarEvento($id)
    {
        // Soft Delete
        DB::table('calendario_eventos')->where('id', $id)->update(['estado' => 'Inactivo']);
        return response()->json(['success' => true]);
    }
}