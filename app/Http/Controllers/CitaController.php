<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitaController extends Controller
{
    // 1. Traer todas las citas (Dejando las Canceladas visibles al fondo)
    public function obtenerCitas()
    {
        // REQ: Auto-completar citas pasadas basándonos en la hora de El Salvador
        $ahora = Carbon::now('America/El_Salvador');
        $fechaHoy = $ahora->format('Y-m-d');
        $horaActual = $ahora->format('H:i:s');

        // Pasa a "Completada" cualquier cita que ya haya pasado de su hora
        Cita::whereIn('estado', ['Programada', 'Confirmada'])
            ->where(function($query) use ($fechaHoy, $horaActual) {
                $query->where('fecha_cita', '<', $fechaHoy)
                      ->orWhere(function($q) use ($fechaHoy, $horaActual) {
                          $q->where('fecha_cita', $fechaHoy)
                            ->where('hora_inicio', '<', $horaActual);
                      });
            })->update(['estado' => 'Completada']);

        // REQ: Traer citas con prioridad de estado. (Confirmadas primero, Canceladas de último)
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
            ->orderByRaw("
                CASE 
                    WHEN citas.estado = 'Confirmada' THEN 1
                    WHEN citas.estado = 'Programada' THEN 2
                    WHEN citas.estado = 'En progreso' THEN 3
                    WHEN citas.estado = 'Pendiente' THEN 4
                    WHEN citas.estado = 'Completada' THEN 5
                    WHEN citas.estado = 'Cancelada' THEN 6
                    ELSE 7
                END ASC
            ")
            ->orderBy('citas.fecha_cita', 'asc') 
            ->orderBy('citas.hora_inicio', 'asc')
            ->get();

        return response()->json($citas);
    }

    // 2. Guardar Cita Nueva
    public function guardarCita(Request $request)
    {
        // Validar si ya hay cita en esa fecha, hora y con ese doctor
        $existe = Cita::where('fecha_cita', $request->fecha)
                      ->where('hora_inicio', $request->hora)
                      ->where('empleado_id', $request->empleado_id)
                      ->whereNotIn('estado', ['Cancelada', 'No presentado'])
                      ->first();

        if ($existe) {
            return response()->json(['error' => 'Ya hay una cita programada en esa fecha y hora para este doctor.'], 422);
        }

        $hora_fin = Carbon::parse($request->hora)->addHour()->format('H:i:s');

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
        // Validar si ya hay cita (excluyendo la cita que estamos editando)
        $existe = Cita::where('fecha_cita', $request->fecha)
                      ->where('hora_inicio', $request->hora)
                      ->where('empleado_id', $request->empleado_id)
                      ->where('id', '!=', $id) 
                      ->whereNotIn('estado', ['Cancelada', 'No presentado'])
                      ->first();

        if ($existe) {
            return response()->json(['error' => 'Ya hay una cita programada en esa fecha y hora para este doctor.'], 422);
        }

        $hora_fin = Carbon::parse($request->hora)->addHour()->format('H:i:s');

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

    // 4. Cancelar Cita (Solo cambia estado, no oculta de la DB)
    public function eliminarCita($id)
    {
        $cita = Cita::findOrFail($id);
        $cita->update(['estado' => 'Cancelada']);
        
        return response()->json(['success' => true, 'mensaje' => 'Cita marcada como Cancelada']);
    }

    // 5. Llenar Selects y Buscador
    public function obtenerDatosFormulario()
    {
        $pacientes = DB::table('pacientes')->select('id', DB::raw("CONCAT(nombre, ' ', apellido) as nombre_completo"))->get();
        $doctores = DB::table('empleados')->select('id', DB::raw("CONCAT(nombre, ' ', apellido) as nombre_completo"))->get();

        return response()->json(['pacientes' => $pacientes, 'doctores' => $doctores]);
    }

    // 6. Obtener citas de un paciente
    public function obtenerCitasPaciente($paciente_id)
    {
        $citas = Cita::where('paciente_id', $paciente_id)
            ->orderBy('fecha_cita', 'desc')
            ->get();
            
        return response()->json($citas);
    }
}