<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitaController extends Controller
{
    // 1. Traer todas las citas (Ocultando las Canceladas de la vista)
    public function obtenerCitas()
    {
        $citas = DB::table('citas')
            ->join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('empleados', 'citas.empleado_id', '=', 'empleados.id')
            ->select(
                'citas.id',
                'citas.paciente_id',
                'citas.empleado_id',
                'citas.fecha_cita as fecha',
                'citas.hora_inicio as hora',
                DB::raw("CONCAT(pacientes.nombre, ' ', pacientes.apellido) as paciente"),
                'citas.motivo_consulta as motivo',
                DB::raw("CONCAT(empleados.nombre, ' ', empleados.apellido) as doctor"),
                'citas.estado',
                'citas.notas'
            )
            ->where('citas.estado', '!=', 'Cancelada') // <--- Aquí hacemos que no se vean en el navegador
            ->orderBy('citas.fecha_cita', 'desc')
            ->orderBy('citas.hora_inicio', 'desc')
            ->get();

        return response()->json($citas);
    }

    // 2. Guardar Cita Nueva
    public function guardarCita(Request $request)
    {
        $hora_fin = Carbon::parse($request->hora)->addHour()->format('H:i:s');

        $id = DB::table('citas')->insertGetId([
            'paciente_id' => $request->paciente_id,
            'empleado_id' => $request->empleado_id,
            'fecha_cita' => $request->fecha,
            'hora_inicio' => $request->hora,
            'hora_fin' => $hora_fin,
            'estado' => $request->estado ?? 'Programada',
            'motivo_consulta' => $request->motivo,
            'notas' => $request->notas
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Cita creada con éxito', 'id' => $id]);
    }

    // 3. Actualizar Cita (Editar)
    public function actualizarCita(Request $request, $id)
    {
        $hora_fin = Carbon::parse($request->hora)->addHour()->format('H:i:s');

        DB::table('citas')->where('id', $id)->update([
            'paciente_id' => $request->paciente_id,
            'empleado_id' => $request->empleado_id,
            'fecha_cita' => $request->fecha,
            'hora_inicio' => $request->hora,
            'hora_fin' => $hora_fin,
            'estado' => $request->estado,
            'motivo_consulta' => $request->motivo,
            'notas' => $request->notas
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Cita actualizada']);
    }

    // 4. Soft Delete (Cancelar para ocultar)
    public function eliminarCita($id)
    {
        DB::table('citas')->where('id', $id)->update(['estado' => 'Cancelada']);
        return response()->json(['success' => true, 'mensaje' => 'Cita ocultada correctamente']);
    }

    // 5. Llenar Selects del modal
    public function obtenerDatosFormulario()
    {
        $pacientes = DB::table('pacientes')->select('id', DB::raw("CONCAT(nombre, ' ', apellido) as nombre_completo"))->get();
        $doctores = DB::table('empleados')->select('id', DB::raw("CONCAT(nombre, ' ', apellido) as nombre_completo"))->get();

        return response()->json(['pacientes' => $pacientes, 'doctores' => $doctores]);
    }
}   