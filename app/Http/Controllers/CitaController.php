<?php

namespace App\Http\Controllers;

use App\Models\Cita; 
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Http\Requests\StoreCitaRequest;

class CitaController extends Controller
{
    public function obtenerCitas()
    {
        $ahora = Carbon::now('America/El_Salvador');
        $usuario = Auth::user();
        $rol = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');

        $citasActivas = Cita::whereIn('estado', ['Programada', 'Confirmada', 'En progreso'])->get();

        foreach ($citasActivas as $cita) {
            $fechaHoraCita = Carbon::parse($cita->fecha_cita . ' ' . $cita->hora_inicio, 'America/El_Salvador');
            $fechaHoraFin = Carbon::parse($cita->fecha_cita . ' ' . $cita->hora_fin, 'America/El_Salvador');

            if ($ahora >= $fechaHoraFin && $cita->estado !== 'Completada') {
                $cita->update(['estado' => 'Completada']);
            } elseif ($ahora >= $fechaHoraCita && $ahora < $fechaHoraFin && $cita->estado !== 'En progreso') {
                $cita->update(['estado' => 'En progreso']);
            }
        }

        $query = Cita::join('pacientes', 'citas.paciente_id', '=', 'pacientes.id')
            ->join('empleados', 'citas.empleado_id', '=', 'empleados.id')
            ->select(
                'citas.id', 'citas.paciente_id', 'citas.empleado_id',
                'citas.fecha_cita as fecha', 'citas.hora_inicio as hora', 'citas.hora_fin', 
                DB::raw("CONCAT(pacientes.nombre, ' ', pacientes.apellido) as paciente"),
                'citas.motivo_consulta as motivo',
                DB::raw("CONCAT(empleados.nombre, ' ', empleados.apellido) as doctor"),
                'citas.estado', 'citas.notas'
            );

        // FILTRO MÁGICO: Si es dentista, solo ve las suyas
        if ($rol === 'Dentista') {
            $query->where('citas.empleado_id', $usuario->id);
        }

        $citas = $query->orderByRaw("CASE WHEN citas.estado IN ('En progreso', 'Confirmada', 'Programada') THEN 1 ELSE 2 END ASC")
            ->orderBy('citas.fecha_cita', 'asc')
            ->orderBy('citas.hora_inicio', 'asc')
            ->get();

        return response()->json($citas);
    }

    private function detectarChoqueDeHorario($fecha, $hora_inicio, $hora_fin, $empleado_id, $ignorar_id = null)
    {
        $horaInicio = Carbon::parse($hora_inicio);
        $horaFin = Carbon::parse($hora_fin);

        $query = Cita::where('fecha_cita', $fecha)
                     ->where('empleado_id', $empleado_id)
                     ->whereIn('estado', ['Programada', 'Confirmada', 'En progreso']);
        
        if ($ignorar_id) $query->where('id', '!=', $ignorar_id);

        $citasDelDia = $query->get();

        foreach ($citasDelDia as $c) {
            $cInicio = Carbon::parse($c->hora_inicio);
            $cFin = Carbon::parse($c->hora_fin);

            if ($horaInicio < $cFin && $horaFin > $cInicio) {
                return true; 
            }
        }
        return false;
    }

    public function guardarCita(StoreCitaRequest $request)
    {
        $datos = $request->validated();
        
        $horaInicioReq = Carbon::parse($datos['hora'])->format('H:i:s');
        $horaFinReq = Carbon::parse($datos['hora_fin'])->format('H:i:s');

        if ($horaInicioReq >= $horaFinReq) return response()->json(['error' => 'La hora de fin debe ser mayor a la hora de inicio.'], 422);
        if ($horaInicioReq < '06:00:00' || $horaFinReq > '18:00:00') return response()->json(['error' => 'Las citas deben programarse dentro del horario de atención (6:00 AM a 6:00 PM).'], 422);

        if (!isset($datos['forzar_guardado']) || $datos['forzar_guardado'] == false) {
            if ($this->detectarChoqueDeHorario($datos['fecha'], $datos['hora'], $datos['hora_fin'], $datos['empleado_id'])) {
                return response()->json(['warning' => true, 'mensaje' => 'Este doctor ya tiene una cita programada que interfiere. ¿Qué deseas hacer?'], 409); 
            }
        }

        $cita = Cita::create([
            'paciente_id' => $datos['paciente_id'],
            'empleado_id' => $datos['empleado_id'],
            'fecha_cita' => $datos['fecha'],
            'hora_inicio' => $datos['hora'], 
            'hora_fin' => $datos['hora_fin'], 
            'estado' => $datos['estado'] ?? 'Programada',
            'motivo_consulta' => $datos['motivo'],
            'notas' => $datos['notas']
        ]);

        return response()->json(['success' => true, 'mensaje' => 'Cita creada con éxito', 'id' => $cita->id]);
    }

    public function actualizarCita(StoreCitaRequest $request, $id)
    {
        $datos = $request->validated();

        $horaInicioReq = Carbon::parse($datos['hora'])->format('H:i:s');
        $horaFinReq = Carbon::parse($datos['hora_fin'])->format('H:i:s');

        if ($horaInicioReq >= $horaFinReq) return response()->json(['error' => 'La hora de fin debe ser mayor a la hora de inicio.'], 422);
        if ($horaInicioReq < '06:00:00' || $horaFinReq > '18:00:00') return response()->json(['error' => 'Las citas deben programarse dentro del horario de atención (6:00 AM a 6:00 PM).'], 422);

        if (!isset($datos['forzar_guardado']) || $datos['forzar_guardado'] == false) {
            if ($this->detectarChoqueDeHorario($datos['fecha'], $datos['hora'], $datos['hora_fin'], $datos['empleado_id'], $id)) {
                return response()->json(['warning' => true, 'mensaje' => 'Este doctor ya tiene una cita programada que interfiere. ¿Qué deseas hacer?'], 409); 
            }
        }

        $cita = Cita::findOrFail($id);
        $cita->update([
            'paciente_id' => $datos['paciente_id'],
            'empleado_id' => $datos['empleado_id'],
            'fecha_cita' => $datos['fecha'],
            'hora_inicio' => $datos['hora'],
            'hora_fin' => $datos['hora_fin'],
            'estado' => $datos['estado'],
            'motivo_consulta' => $datos['motivo'],
            'notas' => $datos['notas']
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
        $pacientes = DB::table('pacientes')->select('id', DB::raw("CONCAT(nombre, ' ', apellido) as nombre_completo"))->where('estado', 'Activo')->get();
        $doctores = DB::table('empleados')
            ->join('roles', 'empleados.rol_id', '=', 'roles.id')
            ->whereIn('roles.nombre', ['Admin', 'Dentista']) // Nos aseguramos que solo liste doctores, no recepcionistas
            ->where('empleados.estado', 'Activo')
            ->select('empleados.id', DB::raw("CONCAT(empleados.nombre, ' ', empleados.apellido) as nombre_completo"))
            ->get();

        return response()->json(['pacientes' => $pacientes, 'doctores' => $doctores]);
    }

    public function obtenerCitasPaciente($paciente_id)
    {
        $usuario = Auth::user();
        $rol = DB::table('roles')->where('id', $usuario->rol_id)->value('nombre');
        
        $query = Cita::where('paciente_id', $paciente_id);
        if ($rol === 'Dentista') {
            $query->where('empleado_id', $usuario->id);
        }

        $citas = $query->orderBy('fecha_cita', 'desc')->get();
        return response()->json($citas);
    }
}