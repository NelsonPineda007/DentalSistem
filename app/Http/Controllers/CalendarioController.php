<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreCalendarioRequest;

class CalendarioController extends Controller
{
    public function obtenerEventos()
    {
        $eventos = DB::table('calendario_eventos')
            ->where('estado', '!=', 'Inactivo')
            ->select('id', 'titulo', 'fecha', 'hora', 'detalles', 'color', 'tipo')
            ->get();

        $citas = DB::table('citas')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->where('citas.estado', '!=', 'Cancelada')
            ->select(
                'citas.id',
                DB::raw("CONCAT('Cita: ', pacientes.nombre, ' ', pacientes.apellido) as titulo"),
                'citas.fecha_cita as fecha',
                'citas.hora_inicio as hora',
                'citas.motivo_consulta as detalles',
                DB::raw("'emerald' as color"),
                DB::raw("'Cita' as tipo")
            )
            ->get();

        return response()->json($eventos->merge($citas));
    }

    public function guardarEvento(StoreCalendarioRequest $request)
    {
        $datos = $request->validated();
        $datos['estado'] = 'Activo';
        $datos['color'] = $datos['color'] ?? 'blue';
        
        $id = DB::table('calendario_eventos')->insertGetId($datos);
        return response()->json(['success' => true, 'id' => $id]);
    }

    public function actualizarEvento(StoreCalendarioRequest $request, $id)
    {
        $datos = $request->validated();
        $datos['color'] = $datos['color'] ?? 'blue';

        DB::table('calendario_eventos')->where('id', $id)->update($datos);
        return response()->json(['success' => true]);
    }

    public function eliminarEvento($id)
    {
        DB::table('calendario_eventos')->where('id', $id)->update(['estado' => 'Inactivo']);
        return response()->json(['success' => true]);
    }
}