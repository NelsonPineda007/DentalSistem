<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cita; 
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CitaController extends Controller
{
    // 1. Traer todas las citas (Auto-Estados Dinámicos y Ordenamiento)
    public function obtenerCitas()
    {
        $ahora = Carbon::now('America/El_Salvador');
        
        $citasActivas = Cita::whereIn('estado', ['Programada', 'Confirmada', 'En progreso'])->get();

        foreach ($citasActivas as $cita) {
            // AHORA USAMOS LA HORA FIN REAL DE LA BASE DE DATOS [cite: 2]
            $fechaHoraCita = Carbon::parse($cita->fecha_cita . ' ' . $cita->hora_inicio, 'America/El_Salvador');
            $fechaHoraFin = Carbon::parse($cita->fecha_cita . ' ' . $cita->hora_fin, 'America/El_Salvador');

            // LOGICA AUTOMATICA DE TIEMPOS
            if ($ahora >= $fechaHoraFin && $cita->estado !== 'Completada') {
                $cita->update(['estado' => 'Completada']);
            } elseif ($ahora >= $fechaHoraCita && $ahora < $fechaHoraFin && $cita->estado !== 'En progreso') {
                $cita->update(['estado' => 'En progreso']);
            }
        }

        $citas = Cita::join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('empleados', 'citas.empleado_id', '=', 'empleados.id')
            ->select(
                'citas.id',
                'citas.paciente_id',
                'citas.empleado_id',
                'citas.fecha_cita as fecha',
                'citas.hora_inicio as hora',
                'citas.hora_fin', // Traemos la hora fin para el Frontend [cite: 2]
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

    // Método Privado para detectar choques usando los rangos dinámicos [cite: 2]
    private function detectarChoqueDeHorario($fecha, $hora_inicio, $hora_fin, $empleado_id, $ignorar_id = null)
    {
        $horaInicio = Carbon::parse($hora_inicio);
        $horaFin = Carbon::parse($hora_fin);

        $query = Cita::where('fecha_cita', $fecha)
                     ->where('empleado_id', $empleado_id)
                     ->whereNotIn('estado', ['Cancelada', 'No presentado']);
        
        if ($ignorar_id) {
            $query->where('id', '!=', $ignorar_id);
        }

        $citasDelDia = $query->get();

        foreach ($citasDelDia as $c) {
            $cInicio = Carbon::parse($c->hora_inicio);
            $cFin = Carbon::parse($c->hora_fin); // Usamos la hora fin real [cite: 2]

            // Si el rango de la nueva cita se cruza con el rango de una existente
            if ($horaInicio < $cFin && $horaFin > $cInicio) {
                return true; 
            }
        }
        return false;
    }

    // 2. Guardar Cita Nueva
    public function guardarCita(Request $request)
    {
        // Validar Rango de Horario General y consistencia lógica [cite: 2]
        $horaInicioReq = Carbon::parse($request->hora)->format('H:i:s');
        $horaFinReq = Carbon::parse($request->hora_fin)->format('H:i:s');

        if ($horaInicioReq >= $horaFinReq) {
             return response()->json(['error' => 'La hora de fin debe ser mayor a la hora de inicio.'], 422);
        }

        if ($horaInicioReq < '06:00:00' || $horaFinReq > '18:00:00') {
            return response()->json(['error' => 'Las citas deben programarse dentro del horario de atención (6:00 AM a 6:00 PM).'], 422);
        }

        // Si NO forzaron el guardado, comprobamos si hay choque
        if (!$request->has('forzar_guardado') || $request->forzar_guardado == false) {
            $hayChoque = $this->detectarChoqueDeHorario($request->fecha, $request->hora, $request->hora_fin, $request->empleado_id);
            if ($hayChoque) {
                return response()->json([
                    'warning' => true, 
                    'mensaje' => 'Este doctor ya tiene una cita programada que choca con este horario. ¿Deseas encimar y agendarla de todas formas?'
                ], 409); 
            }
        }

        $cita = Cita::create([
            'paciente_id' => $request->paciente_id,
            'empleado_id' => $request->empleado_id,
            'fecha_cita' => $request->fecha,
            'hora_inicio' => $request->hora, // $request->hora trae la hora de inicio [cite: 2]
            'hora_fin' => $request->hora_fin, // Guardamos la hora de fin personalizada [cite: 2]
            'estado' => $request->estado ?? 'Programada',
            'motivo_consulta' => $request->motivo,
            'notas' => $request->notas
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Cita creada con éxito', 'id' => $cita->id]);
    }

    // 3. Actualizar Cita (Editar)
    public function actualizarCita(Request $request, $id)
    {
        $horaInicioReq = Carbon::parse($request->hora)->format('H:i:s');
        $horaFinReq = Carbon::parse($request->hora_fin)->format('H:i:s');

        if ($horaInicioReq >= $horaFinReq) {
             return response()->json(['error' => 'La hora de fin debe ser mayor a la hora de inicio.'], 422);
        }

        if ($horaInicioReq < '06:00:00' || $horaFinReq > '18:00:00') {
            return response()->json(['error' => 'Las citas deben programarse dentro del horario de atención (6:00 AM a 6:00 PM).'], 422);
        }

        if (!$request->has('forzar_guardado') || $request->forzar_guardado == false) {
            $hayChoque = $this->detectarChoqueDeHorario($request->fecha, $request->hora, $request->hora_fin, $request->empleado_id, $id);
            if ($hayChoque) {
                return response()->json([
                    'warning' => true, 
                    'mensaje' => 'Este doctor ya tiene una cita programada que choca con este horario. ¿Deseas encimar y modificarla de todas formas?'
                ], 409); 
            }
        }

        $cita = Cita::findOrFail($id);
        $cita->update([
            'paciente_id' => $request->paciente_id,
            'empleado_id' => $request->empleado_id,
            'fecha_cita' => $request->fecha,
            'hora_inicio' => $request->hora,
            'hora_fin' => $request->hora_fin, // Actualizamos la hora fin [cite: 2]
            'estado' => $request->estado,
            'motivo_consulta' => $request->motivo,
            'notas' => $request->notas
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Cita actualizada con éxito']);
    }

    public function eliminarCita($id)
    {
        $cita = Cita::findOrFail($id);
        $cita->update(['estado' => 'Cancelada']);
        return response()->json(['success' => true, 'mensaje' => 'Cita marcada como Cancelada']);
    }

    public function obtenerDatosFormulario()
    {
        $pacientes = DB::table('pacientes')->select('id', DB::raw("CONCAT(nombre, ' ', apellido) as nombre_completo"))->get();
        $doctores = DB::table('empleados')->select('id', DB::raw("CONCAT(nombre, ' ', apellido) as nombre_completo"))->get();
        return response()->json(['pacientes' => $pacientes, 'doctores' => $doctores]);
    }

    public function obtenerCitasPaciente($paciente_id)
    {
        $citas = Cita::where('paciente_id', $paciente_id)->orderBy('fecha_cita', 'desc')->get();
        return response()->json($citas);
    }
}