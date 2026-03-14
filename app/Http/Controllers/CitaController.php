<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita; // <-- ¡Aquí estamos llamando a nuestro Modelo!
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitaController extends Controller
{
    // 1. Traer todas las citas (Ocultando las Canceladas de la vista)
    public function obtenerCitas()
    {
        // Usamos Eloquent con joins para mantener la misma estructura que esperaba el JS
        $citas = Cita::join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
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
            ->where('citas.estado', '!=', 'Cancelada')
            ->orderBy('citas.fecha_cita', 'desc')
            ->orderBy('citas.hora_inicio', 'desc')
            ->get();

        return response()->json($citas);
    }

    // 2. Guardar Cita Nueva
    public function guardarCita(Request $request)
    {
        $hora_fin = Carbon::parse($request->hora)->addHour()->format('H:i:s');

        // Eloquent 'create' devuelve el modelo recién creado
        $cita = Cita::create([
            'paciente_id' => $request->paciente_id,
            'empleado_id' => $request->empleado_id,
            'fecha_cita' => $request->fecha,
            'hora_inicio' => $request->hora,
            'hora_fin' => $hora_fin,
            'estado' => $request->estado ?? 'Programada',
            'motivo_consulta' => $request->motivo,
            'notas' => $request->notas
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Cita creada con éxito', 'id' => $cita->id]);
    }

    // 3. Actualizar Cita (Editar)
    public function actualizarCita(Request $request, $id)
    {
        $hora_fin = Carbon::parse($request->hora)->addHour()->format('H:i:s');

        // Eloquent 'findOrFail' busca o tira error 404, luego actualizamos
        $cita = Cita::findOrFail($id);
        $cita->update([
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
        $cita = Cita::findOrFail($id);
        $cita->update(['estado' => 'Cancelada']);
        
        return response()->json(['success' => true, 'mensaje' => 'Cita ocultada correctamente']);
    }

    // 5. Llenar Selects y Buscador del modal
    public function obtenerDatosFormulario()
    {
        $pacientes = DB::table('pacientes')->select('id', DB::raw("CONCAT(nombre, ' ', apellido) as nombre_completo"))->get();
        $doctores = DB::table('empleados')->select('id', DB::raw("CONCAT(nombre, ' ', apellido) as nombre_completo"))->get();

        return response()->json(['pacientes' => $pacientes, 'doctores' => $doctores]);
    }

    // 6. Obtener citas de un paciente específico (Para el Expediente)
    public function obtenerCitasPaciente($paciente_id)
    {
        $citas = Cita::where('paciente_id', $paciente_id)
            ->where('estado', '!=', 'Cancelada')
            ->orderBy('fecha_cita', 'desc')
            ->get();
            
        return response()->json($citas);
    }
}
